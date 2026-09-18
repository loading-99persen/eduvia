<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Support\Notif;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    protected array $tipeValid = ['post', 'komentar', 'user', 'komunitas', 'webinar'];

    /** Laporan yang pernah dikirim user. */
    public function index()
    {
        $reports = Report::where('id_user', auth()->id())
            ->orderByDesc('dibuat_pada')
            ->get();

        return view('report.index', compact('reports'));
    }

    /** Form laporan, dibuka dari tombol "Laporkan" pada konten. */
    public function create(Request $request)
    {
        $tipe = in_array($request->query('tipe'), $this->tipeValid)
            ? $request->query('tipe')
            : 'post';

        $idTarget = (int) $request->query('id', 0);
        $kembali  = $request->query('kembali', url()->previous());

        return view('report.create', compact('tipe', 'idTarget', 'kembali'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'tipe_target' => ['required', 'in:' . implode(',', $this->tipeValid)],
            'id_target'   => ['required', 'integer', 'min:1'],
            'alasan'      => ['required', 'string', 'min:10', 'max:1000'],
        ], [
            'alasan.min' => 'Jelaskan alasannya minimal 10 karakter.',
        ]);

        $sudah = Report::where('id_user', auth()->id())
            ->where('tipe_target', $data['tipe_target'])
            ->where('id_target', $data['id_target'])
            ->where('status', 'proses')
            ->exists();

        if ($sudah) {
            return redirect()->route('report.index')
                ->with('error', 'Kamu sudah melaporkan konten ini dan masih ditinjau admin.');
        }

        Report::create([
            'id_user'     => auth()->id(),
            'tipe_target' => $data['tipe_target'],
            'id_target'   => $data['id_target'],
            'alasan'      => $data['alasan'],
            'status'      => 'proses',
        ]);

        Notif::kirim(
            auth()->id(),
            'sistem',
            'Laporan terkirim',
            'Terima kasih. Laporanmu sedang ditinjau admin.',
            route('report.index')
        );

        return redirect()->route('report.index')
            ->with('success', 'Laporan berhasil dikirim.');
    }
}
