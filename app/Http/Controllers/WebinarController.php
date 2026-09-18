<?php

namespace App\Http\Controllers;

use App\Models\PartisipasiWebinar;
use App\Models\Webinar;
use App\Support\Notif;
use Illuminate\Http\Request;

class WebinarController extends Controller
{
    /** Daftar webinar yang sudah disetujui admin. */
    public function index(Request $request)
    {
        $user   = auth()->user();
        $filter = $request->query('filter', 'mendatang');
        $cari   = trim((string) $request->query('cari', ''));

        $query = Webinar::with(['komunitas', 'leader.profil'])
            ->withCount('partisipasi')
            ->withCount(['partisipasi as daftar_count' => fn ($q) => $q->where('id_user', $user->id_user)])
            ->where('status', '!=', 'dibatalkan')
            ->when($cari !== '', function ($q) use ($cari) {
                $q->where(function ($sub) use ($cari) {
                    $sub->where('judul', 'like', "%{$cari}%")
                        ->orWhere('deskripsi', 'like', "%{$cari}%")
                        ->orWhere('kategori', 'like', "%{$cari}%");
                });
            });

        if ($filter === 'diikuti') {
            $query->whereHas('partisipasi', fn ($q) => $q->where('id_user', $user->id_user));
        } elseif ($filter === 'komunitas') {
            $query->whereIn('id_komunitas', $user->komunitas()->pluck('komunitas.id_komunitas'));
        } elseif ($filter === 'selesai') {
            $query->whereDate('tanggal', '<', now()->toDateString());
        } else {
            $query->whereDate('tanggal', '>=', now()->toDateString());
        }

        $webinar = $query
            ->orderBy('tanggal', $filter === 'selesai' ? 'desc' : 'asc')
            ->orderBy('waktu')
            ->paginate(10)
            ->withQueryString();

        return view('webinar.index', compact('webinar', 'filter', 'cari'));
    }

    /** Detail webinar. */
    public function show($id)
    {
        $webinar = Webinar::with(['komunitas', 'leader.profil'])
            ->withCount('partisipasi')
            ->findOrFail($id);

        $sudahDaftar = $webinar->diikutiOleh();
        $aksesLink   = $webinar->bolehAksesLink();

        $peserta = $webinar->peserta()
            ->with('profil')
            ->orderByPivot('bergabung_pada')
            ->take(12)
            ->get();

        $jumlahPeserta = $webinar->jumlah_peserta;
        $sayaLeader    = (int) $webinar->id_leader === (int) auth()->id();

        return view('webinar.show', compact(
            'webinar',
            'sudahDaftar',
            'aksesLink',
            'peserta',
            'jumlahPeserta',
            'sayaLeader'
        ));
    }

    /**
     * Daftar webinar tanpa form tambahan:
     * data peserta diambil dari akun dan profil user.
     */
    public function ikut($id)
    {
        $webinar = Webinar::findOrFail($id);
        $user    = auth()->user();

        if ($webinar->status === 'dibatalkan') {
            return back()->with('error', 'Webinar ini sudah dibatalkan.');
        }

        if ($webinar->sudahLewat()) {
            return back()->with('error', 'Webinar ini sudah selesai.');
        }

        if (!$user->profilLengkap()) {
            return redirect()->route('user.profil')
                ->with('error', 'Lengkapi profil dulu, karena data peserta diambil dari profilmu.');
        }

        $sudah = PartisipasiWebinar::where('id_webinar', $webinar->id_webinar)
            ->where('id_user', $user->id_user)
            ->exists();

        if ($sudah) {
            return back()->with('error', 'Kamu sudah terdaftar di webinar ini.');
        }

        PartisipasiWebinar::create([
            'id_webinar' => $webinar->id_webinar,
            'id_user'    => $user->id_user,
        ]);

        Notif::kirim(
            $user->id_user,
            'webinar',
            'Pendaftaran webinar berhasil',
            'Kamu terdaftar di "' . $webinar->judul . '" pada '
                . $webinar->mulai->locale('id')->isoFormat('D MMMM Y, HH:mm') . ' WIB.',
            route('user.jadwal')
        );

        if ((int) $webinar->id_leader !== (int) $user->id_user) {
            Notif::kirim(
                $webinar->id_leader,
                'webinar',
                'Peserta baru',
                $user->nama . ' mendaftar di webinar "' . $webinar->judul . '".',
                route('webinar.show', $webinar->id_webinar)
            );
        }

        return redirect()->route('user.jadwal')
            ->with('success', 'Berhasil terdaftar. Webinar sudah masuk ke Jadwal saya.');
    }

    /** Batalkan pendaftaran. */
    public function batal($id)
    {
        $webinar = Webinar::findOrFail($id);

        PartisipasiWebinar::where('id_webinar', $webinar->id_webinar)
            ->where('id_user', auth()->id())
            ->delete();

        return back()->with('success', 'Pendaftaran webinar dibatalkan.');
    }

    /** Leader boleh memperbarui tautan meeting webinarnya. */
    public function updateLink(Request $request, $id)
    {
        $webinar = Webinar::findOrFail($id);

        abort_unless((int) $webinar->id_leader === (int) auth()->id() || auth()->user()->isAdmin(), 403);

        $data = $request->validate([
            'link_meeting' => ['required', 'url', 'max:255'],
        ]);

        $webinar->update($data);

        Notif::kirimBanyak(
            $webinar->partisipasi()->pluck('id_user')->all(),
            'webinar',
            'Tautan meeting diperbarui',
            'Tautan webinar "' . $webinar->judul . '" sudah diperbarui oleh leader.',
            route('webinar.show', $webinar->id_webinar)
        );

        return back()->with('success', 'Tautan meeting diperbarui.');
    }
}
