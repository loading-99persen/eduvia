<?php

namespace App\Http\Controllers;

use App\Models\Komunitas;
use App\Models\Pengajuan;
use App\Support\Notif;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PengajuanController extends Controller
{
    /** Daftar pengajuan milik user. */
    public function index()
    {
        $pengajuan = Pengajuan::with('komunitas')
            ->where('id_user', auth()->id())
            ->orderByDesc('tanggal_pengajuan')
            ->get();

        return view('pengajuan.index', compact('pengajuan'));
    }

    /** Form pengajuan komunitas baru atau webinar. */
    public function create(Request $request)
    {
        $tipe = $request->query('tipe') === 'webinar' ? 'webinar' : 'komunitas';

        $komunitasDipimpin = auth()->user()
            ->memberKomunitas()
            ->where('role', 'leader')
            ->with('komunitas')
            ->get()
            ->pluck('komunitas')
            ->filter()
            ->values();

        $kategori = [
            'Programming', 'Database', 'AI', 'Matematika', 'Bahasa',
            'Sains', 'Desain', 'Psikologi', 'Bisnis', 'Pengembangan diri',
        ];

        return view('pengajuan.create', compact('tipe', 'komunitasDipimpin', 'kategori'));
    }

    /** Simpan pengajuan. */
    public function store(Request $request)
    {
        $tipe = $request->input('tipe') === 'webinar' ? 'webinar' : 'komunitas';

        return $tipe === 'webinar'
            ? $this->simpanWebinar($request)
            : $this->simpanKomunitas($request);
    }

    protected function simpanKomunitas(Request $request)
    {
        $data = $request->validate([
            'nama_komunitas' => ['required', 'string', 'max:100'],
            'kategori'       => ['required', 'string', 'max:50'],
            'deskripsi'      => ['required', 'string', 'max:2000'],
            'alasan'         => ['required', 'string', 'max:2000'],
            'gambar'         => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        $adaKomunitasSama = Komunitas::where('nama_komunitas', $data['nama_komunitas'])->exists();

        if ($adaKomunitasSama) {
            return back()->withInput()
                ->with('error', 'Sudah ada komunitas dengan nama tersebut. Coba nama lain.');
        }

        $adaPengajuanSama = Pengajuan::where('id_user', auth()->id())
            ->where('tipe', 'komunitas')
            ->where('status', 'proses')
            ->where('nama_komunitas', $data['nama_komunitas'])
            ->exists();

        if ($adaPengajuanSama) {
            return back()->withInput()
                ->with('error', 'Pengajuan komunitas dengan nama itu masih diproses admin.');
        }

        $gambar = $request->hasFile('gambar')
            ? $request->file('gambar')->store('pengajuan', 'public')
            : null;

        Pengajuan::create([
            'id_user'        => auth()->id(),
            'tipe'           => 'komunitas',
            'judul'          => $data['nama_komunitas'],
            'nama_komunitas' => $data['nama_komunitas'],
            'kategori'       => $data['kategori'],
            'deskripsi'      => $data['deskripsi'],
            'alasan'         => $data['alasan'],
            'gambar'         => $gambar,
            'status'         => 'proses',
        ]);

        Notif::kirim(
            auth()->id(),
            'pengajuan',
            'Pengajuan komunitas terkirim',
            'Pengajuan "' . $data['nama_komunitas'] . '" sedang ditinjau admin.',
            route('pengajuan.index')
        );

        return redirect()->route('pengajuan.index')
            ->with('success', 'Pengajuan komunitas berhasil dikirim. Tunggu peninjauan admin.');
    }

    protected function simpanWebinar(Request $request)
    {
        $data = $request->validate([
            'id_komunitas' => ['required', 'integer', 'exists:komunitas,id_komunitas'],
            'judul'        => ['required', 'string', 'max:100'],
            'pembicara'    => ['required', 'string', 'max:100'],
            'kategori'     => ['required', 'string', 'max:50'],
            'tanggal'      => ['required', 'date', 'after_or_equal:today'],
            'waktu'        => ['required', 'date_format:H:i'],
            'deskripsi'    => ['required', 'string', 'max:2000'],
            'link_meeting' => ['nullable', 'url', 'max:255'],
            'foto'         => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ], [
            'tanggal.after_or_equal' => 'Tanggal webinar tidak boleh di masa lalu.',
        ]);

        $komunitas = Komunitas::findOrFail($data['id_komunitas']);

        if (!$komunitas->saya_leader) {
            return back()->withInput()
                ->with('error', 'Hanya leader komunitas yang dapat mengajukan webinar.');
        }

        $foto = $request->hasFile('foto')
            ? $request->file('foto')->store('pengajuan', 'public')
            : null;

        Pengajuan::create([
            'id_user'      => auth()->id(),
            'id_komunitas' => $komunitas->id_komunitas,
            'tipe'         => 'webinar',
            'judul'        => $data['judul'],
            'deskripsi'    => $data['deskripsi'],
            'kategori'     => $data['kategori'],
            'pembicara'    => $data['pembicara'],
            'tanggal'      => $data['tanggal'],
            'waktu'        => $data['waktu'],
            'link_meeting' => $data['link_meeting'] ?? null,
            'foto'         => $foto,
            'status'       => 'proses',
        ]);

        Notif::kirim(
            auth()->id(),
            'pengajuan',
            'Pengajuan webinar terkirim',
            'Pengajuan webinar "' . $data['judul'] . '" sedang ditinjau admin.',
            route('pengajuan.index')
        );

        return redirect()->route('pengajuan.index')
            ->with('success', 'Pengajuan webinar berhasil dikirim.');
    }

    /** Batalkan pengajuan selama masih berstatus proses. */
    public function destroy($id)
    {
        $pengajuan = Pengajuan::where('id_pengajuan', $id)
            ->where('id_user', auth()->id())
            ->firstOrFail();

        if ($pengajuan->status !== 'proses') {
            return back()->with('error', 'Pengajuan yang sudah diproses tidak dapat dibatalkan.');
        }

        foreach (array_filter([$pengajuan->gambar, $pengajuan->foto]) as $berkas) {
            if (Storage::disk('public')->exists($berkas)) {
                Storage::disk('public')->delete($berkas);
            }
        }

        $pengajuan->delete();

        return back()->with('success', 'Pengajuan dibatalkan.');
    }
}
