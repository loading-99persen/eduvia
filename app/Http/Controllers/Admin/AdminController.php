<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Komentar;
use App\Models\Komunitas;
use App\Models\MemberKomunitas;
use App\Models\Pengajuan;
use App\Models\Post;
use App\Models\Report;
use App\Models\User;
use App\Models\Webinar;
use App\Support\Notif;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    // =====================================================
    // DASHBOARD
    // =====================================================

    public function dashboard()
    {
        $statistik = (object) [
            'user'             => User::where('id_role', 2)->count(),
            'komunitas'        => Komunitas::count(),
            'webinar'          => Webinar::count(),
            'postingan'        => Post::count(),
            'pengajuanKomunitas' => Pengajuan::proses()->where('tipe', 'komunitas')->count(),
            'pengajuanWebinar' => Pengajuan::proses()->where('tipe', 'webinar')->count(),
            'laporan'          => Report::where('status', 'proses')->count(),
        ];

        $pengajuanTerbaru = Pengajuan::with('user.profil')
            ->orderByDesc('tanggal_pengajuan')
            ->take(5)
            ->get();

        $laporanTerbaru = Report::with('user.profil')
            ->orderByDesc('dibuat_pada')
            ->take(5)
            ->get();

        $userTerbaru = User::with('profil')
            ->where('id_role', 2)
            ->orderByDesc('id_user')
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'statistik',
            'pengajuanTerbaru',
            'laporanTerbaru',
            'userTerbaru'
        ));
    }

    // =====================================================
    // MANAJEMEN USER
    // =====================================================

    public function users(Request $request)
    {
        $cari   = trim((string) $request->query('cari', ''));
        $status = $request->query('status', 'semua');
        $role   = $request->query('role', 'semua');

        $users = User::with(['profil', 'role'])
            ->withCount(['posts', 'komunitas'])
            ->when($cari !== '', function ($q) use ($cari) {
                $q->where(function ($sub) use ($cari) {
                    $sub->where('email', 'like', "%{$cari}%")
                        ->orWhereHas('profil', fn ($p) => $p->where('nama_lengkap', 'like', "%{$cari}%"));
                });
            })
            ->when($status !== 'semua', fn ($q) => $q->where('status', $status))
            ->when($role !== 'semua', fn ($q) => $q->where('id_role', $role === 'admin' ? 1 : 2))
            ->orderByDesc('id_user')
            ->paginate(15)
            ->withQueryString();

        return view('admin.users', compact('users', 'cari', 'status', 'role'));
    }

    public function userUpdate(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $data = $request->validate([
            'id_role' => ['nullable', 'in:1,2'],
            'status'  => ['nullable', 'in:aktif,nonaktif'],
        ]);

        if ((int) $user->id_user === (int) auth()->id()) {
            return back()->with('error', 'Kamu tidak dapat mengubah akunmu sendiri.');
        }

        $user->update(array_filter($data, fn ($v) => $v !== null));

        if ($request->filled('status')) {
            Notif::kirim(
                $user->id_user,
                'sistem',
                $data['status'] === 'aktif' ? 'Akun diaktifkan' : 'Akun dinonaktifkan',
                'Status akunmu diperbarui oleh admin.'
            );
        }

        return back()->with('success', 'Data user diperbarui.');
    }

    public function userDestroy($id)
    {
        $user = User::findOrFail($id);

        if ((int) $user->id_user === (int) auth()->id()) {
            return back()->with('error', 'Kamu tidak dapat menghapus akunmu sendiri.');
        }

        if ($user->komunitasDipimpin()->exists()) {
            return back()->with('error', 'User ini masih memimpin komunitas. Pindahkan atau hapus komunitasnya dulu.');
        }

        $user->delete();

        return back()->with('success', 'Akun user dihapus.');
    }

    // =====================================================
    // MANAJEMEN KOMUNITAS
    // =====================================================

    public function komunitas(Request $request)
    {
        $cari = trim((string) $request->query('cari', ''));

        $komunitas = Komunitas::with('leader.profil')
            ->withCount(['memberKomunitas', 'posts', 'webinar'])
            ->when($cari !== '', fn ($q) => $q->where('nama_komunitas', 'like', "%{$cari}%"))
            ->orderByDesc('id_komunitas')
            ->paginate(15)
            ->withQueryString();

        return view('admin.komunitas', compact('komunitas', 'cari'));
    }

    public function komunitasUpdate(Request $request, $id)
    {
        $komunitas = Komunitas::findOrFail($id);

        $data = $request->validate([
            'status' => ['required', 'in:aktif,nonaktif'],
        ]);

        $komunitas->update($data);

        Notif::kirim(
            $komunitas->id_leader,
            'aktivitas_komunitas',
            'Status komunitas diperbarui',
            'Komunitas "' . $komunitas->nama_komunitas . '" kini berstatus ' . $data['status'] . '.',
            route('komunitas.show', $komunitas->id_komunitas)
        );

        return back()->with('success', 'Status komunitas diperbarui.');
    }

    public function komunitasDestroy($id)
    {
        $komunitas = Komunitas::findOrFail($id);
        $nama      = $komunitas->nama_komunitas;
        $leader    = $komunitas->id_leader;

        $komunitas->delete();

        Notif::kirim(
            $leader,
            'aktivitas_komunitas',
            'Komunitas dihapus',
            'Komunitas "' . $nama . '" dihapus oleh admin.'
        );

        return back()->with('success', 'Komunitas dihapus.');
    }

    // =====================================================
    // MANAJEMEN WEBINAR
    // =====================================================

    public function webinar(Request $request)
    {
        $cari = trim((string) $request->query('cari', ''));

        $webinar = Webinar::with(['komunitas', 'leader.profil'])
            ->withCount('partisipasi')
            ->when($cari !== '', fn ($q) => $q->where('judul', 'like', "%{$cari}%"))
            ->orderByDesc('tanggal')
            ->paginate(15)
            ->withQueryString();

        return view('admin.webinar', compact('webinar', 'cari'));
    }

    public function webinarUpdate(Request $request, $id)
    {
        $webinar = Webinar::findOrFail($id);

        $data = $request->validate([
            'status' => ['required', 'in:akan_datang,berlangsung,selesai,dibatalkan'],
        ]);

        $webinar->update($data);

        if ($data['status'] === 'dibatalkan') {
            Notif::kirimBanyak(
                $webinar->partisipasi()->pluck('id_user')->push($webinar->id_leader)->all(),
                'webinar',
                'Webinar dibatalkan',
                'Webinar "' . $webinar->judul . '" dibatalkan oleh admin.'
            );
        }

        return back()->with('success', 'Status webinar diperbarui.');
    }

    public function webinarDestroy($id)
    {
        $webinar = Webinar::findOrFail($id);
        $webinar->delete();

        return back()->with('success', 'Webinar dihapus.');
    }

    // =====================================================
    // PENGAJUAN
    // =====================================================

    public function pengajuan(Request $request)
    {
        $status = $request->query('status', 'proses');
        $tipe   = $request->query('tipe', 'semua');

        $pengajuan = Pengajuan::with(['user.profil', 'komunitas'])
            ->when($status !== 'semua', fn ($q) => $q->where('status', $status))
            ->when($tipe !== 'semua', fn ($q) => $q->where('tipe', $tipe))
            ->orderByDesc('tanggal_pengajuan')
            ->paginate(15)
            ->withQueryString();

        return view('admin.pengajuan', compact('pengajuan', 'status', 'tipe'));
    }

    /** Menyetujui pengajuan: membuat komunitas atau webinar sungguhan. */
    public function terima($id)
    {
        $pengajuan = Pengajuan::with('user')->findOrFail($id);

        if ($pengajuan->status !== 'proses') {
            return back()->with('error', 'Pengajuan ini sudah diproses sebelumnya.');
        }

        DB::transaction(function () use ($pengajuan) {
            if ($pengajuan->tipe === 'komunitas') {
                $komunitas = Komunitas::create([
                    'id_leader'      => $pengajuan->id_user,
                    'nama_komunitas' => $pengajuan->nama_komunitas ?: $pengajuan->judul,
                    'deskripsi'      => $pengajuan->deskripsi,
                    'kategori'       => $pengajuan->kategori,
                    'gambar'         => $pengajuan->gambar,
                    'status'         => 'aktif',
                ]);

                // Pengaju otomatis menjadi leader komunitas tersebut.
                MemberKomunitas::firstOrCreate(
                    ['id_komunitas' => $komunitas->id_komunitas, 'id_user' => $pengajuan->id_user],
                    ['role' => 'leader']
                );

                $komunitas->chatroomAtauBuat();

                $pengajuan->update([
                    'status'        => 'disetujui',
                    'id_komunitas'  => $komunitas->id_komunitas,
                    'diproses_pada' => now(),
                ]);

                Notif::kirim(
                    $pengajuan->id_user,
                    'pengajuan',
                    'Pengajuan komunitas disetujui',
                    'Komunitas "' . $komunitas->nama_komunitas . '" sudah aktif dan kamu menjadi leader-nya.',
                    route('komunitas.show', $komunitas->id_komunitas)
                );

                return;
            }

            // Pengajuan webinar
            $webinar = Webinar::create([
                'id_leader'    => $pengajuan->id_user,
                'id_komunitas' => $pengajuan->id_komunitas,
                'judul'        => $pengajuan->judul,
                'deskripsi'    => $pengajuan->deskripsi,
                'pembicara'    => $pengajuan->pembicara,
                'tanggal'      => $pengajuan->tanggal,
                'waktu'        => $pengajuan->waktu,
                'foto'         => $pengajuan->foto,
                'kategori'     => $pengajuan->kategori,
                'link_meeting' => $pengajuan->link_meeting,
                'status'       => 'akan_datang',
            ]);

            $pengajuan->update([
                'status'        => 'disetujui',
                'diproses_pada' => now(),
            ]);

            Notif::kirim(
                $pengajuan->id_user,
                'pengajuan',
                'Pengajuan webinar disetujui',
                'Webinar "' . $webinar->judul . '" sudah tayang di komunitasmu.',
                route('webinar.show', $webinar->id_webinar)
            );

            // Beri tahu anggota komunitas penyelenggara.
            $anggota = MemberKomunitas::where('id_komunitas', $webinar->id_komunitas)
                ->where('id_user', '!=', $pengajuan->id_user)
                ->pluck('id_user')
                ->all();

            Notif::kirimBanyak(
                $anggota,
                'webinar',
                'Webinar baru di komunitasmu',
                '"' . $webinar->judul . '" dijadwalkan pada '
                    . $webinar->mulai->locale('id')->isoFormat('D MMMM Y, HH:mm') . ' WIB.',
                route('webinar.show', $webinar->id_webinar)
            );
        });

        return back()->with('success', 'Pengajuan disetujui.');
    }

    public function tolak(Request $request, $id)
    {
        $pengajuan = Pengajuan::findOrFail($id);

        if ($pengajuan->status !== 'proses') {
            return back()->with('error', 'Pengajuan ini sudah diproses sebelumnya.');
        }

        $data = $request->validate([
            'catatan_admin' => ['required', 'string', 'min:10', 'max:1000'],
        ], [
            'catatan_admin.required' => 'Tuliskan alasan penolakan supaya pengaju tahu yang harus diperbaiki.',
        ]);

        $pengajuan->update([
            'status'        => 'ditolak',
            'catatan_admin' => $data['catatan_admin'],
            'diproses_pada' => now(),
        ]);

        Notif::kirim(
            $pengajuan->id_user,
            'pengajuan',
            'Pengajuan ditolak',
            'Pengajuan "' . $pengajuan->judul . '" ditolak. Alasan: ' . $data['catatan_admin'],
            route('pengajuan.index')
        );

        return back()->with('success', 'Pengajuan ditolak dan alasannya sudah dikirim.');
    }

    // =====================================================
    // LAPORAN & MODERASI
    // =====================================================

    public function laporan(Request $request)
    {
        $status = $request->query('status', 'proses');

        $reports = Report::with('user.profil')
            ->when($status !== 'semua', fn ($q) => $q->where('status', $status))
            ->orderByDesc('dibuat_pada')
            ->paginate(15)
            ->withQueryString();

        return view('admin.laporan', compact('reports', 'status'));
    }

    public function laporanUpdate(Request $request, $id)
    {
        $report = Report::findOrFail($id);

        $data = $request->validate([
            'status'          => ['required', 'in:proses,diterima,ditolak'],
            'tanggapan_admin' => ['nullable', 'string', 'max:1000'],
        ]);

        $report->update([
            'status'          => $data['status'],
            'tanggapan_admin' => $data['tanggapan_admin'] ?? $report->tanggapan_admin,
            'diproses_pada'   => now(),
        ]);

        Notif::kirim(
            $report->id_user,
            'sistem',
            'Laporanmu sudah ditinjau',
            'Status laporan: ' . $data['status'] . '. ' . ($data['tanggapan_admin'] ?? ''),
            route('report.index')
        );

        return back()->with('success', 'Status laporan diperbarui.');
    }

    public function moderasi(Request $request)
    {
        $posts = Post::with(['user.profil', 'komunitas'])
            ->withCount(['likes', 'komentar'])
            ->orderByDesc('dibuat_pada')
            ->paginate(10, ['*'], 'post')
            ->withQueryString();

        $komentar = Komentar::with(['user.profil', 'post'])
            ->orderByDesc('dibuat_pada')
            ->paginate(10, ['*'], 'komentar')
            ->withQueryString();

        return view('admin.moderasi', compact('posts', 'komentar'));
    }

    public function hapusPost($id)
    {
        $post = Post::findOrFail($id);
        $pemilik = $post->id_user;
        $post->delete();

        Notif::kirim(
            $pemilik,
            'sistem',
            'Postingan dihapus admin',
            'Salah satu postinganmu dihapus karena melanggar aturan komunitas.'
        );

        return back()->with('success', 'Postingan dihapus.');
    }

    public function hapusKomentar($id)
    {
        $komentar = Komentar::findOrFail($id);
        $pemilik  = $komentar->id_user;
        $komentar->delete();

        Notif::kirim(
            $pemilik,
            'sistem',
            'Komentar dihapus admin',
            'Salah satu komentarmu dihapus karena melanggar aturan komunitas.'
        );

        return back()->with('success', 'Komentar dihapus.');
    }
}
