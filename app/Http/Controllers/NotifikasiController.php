<?php

namespace App\Http\Controllers;

use App\Models\Notifikasi;
use Illuminate\Support\Facades\Auth;

class NotifikasiController extends Controller
{
    // Semua notifikasi
    public function index()
    {
        $notifikasi = Notifikasi::where('id_user', Auth::id())
            ->latest()
            ->get();

        return view('notifikasi.index', compact('notifikasi'));
    }

    // Tandai sudah dibaca
    public function baca($id)
    {
        $notifikasi = Notifikasi::where('id_notifikasi', $id)
            ->where('id_user', Auth::id())
            ->firstOrFail();

        $notifikasi->update([
            'status' => 'dibaca'
        ]);

        return back();
    }

    // Tandai semua sudah dibaca
    public function bacaSemua()
    {
        Notifikasi::where('id_user', Auth::id())
            ->update([
                'status' => 'dibaca'
            ]);

        return back();
    }

    // Hapus notifikasi
    public function destroy($id)
    {
        $notifikasi = Notifikasi::where('id_notifikasi', $id)
            ->where('id_user', Auth::id())
            ->firstOrFail();

        $notifikasi->delete();

        return back();
    }
}