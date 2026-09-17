<?php

namespace App\Http\Controllers;

use App\Models\Pengajuan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PengajuanController extends Controller
{
    // Daftar pengajuan user
    public function index()
    {
        $pengajuan = Pengajuan::with([
            'user',
            'komunitas'
        ])
        ->where('id_user', Auth::id())
        ->latest()
        ->get();

        return view('pengajuan.index', compact('pengajuan'));
    }

    // Buat pengajuan
    public function store(Request $request)
    {
        $request->validate([
            'id_komunitas' => 'required',
            'alasan' => 'required'
        ]);

        Pengajuan::create([
            'id_user' => Auth::id(),
            'id_komunitas' => $request->id_komunitas,
            'alasan' => $request->alasan,
            'status' => 'menunggu'
        ]);

        return back()
            ->with('success', 'Pengajuan berhasil dikirim.');
    }

    // Daftar pengajuan untuk admin/leader
    public function admin()
    {
        $pengajuan = Pengajuan::with([
            'user',
            'komunitas'
        ])->latest()->get();

        return view('admin.pengajuan.index', compact('pengajuan'));
    }

    // Terima
    public function terima($id)
    {
        $pengajuan = Pengajuan::findOrFail($id);

        $pengajuan->update([
            'status' => 'diterima'
        ]);

        return back()
            ->with('success', 'Pengajuan diterima.');
    }

    // Tolak
    public function tolak($id)
    {
        $pengajuan = Pengajuan::findOrFail($id);

        $pengajuan->update([
            'status' => 'ditolak'
        ]);

        return back()
            ->with('success', 'Pengajuan ditolak.');
    }
}