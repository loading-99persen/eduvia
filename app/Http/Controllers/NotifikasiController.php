<?php

namespace App\Http\Controllers;

use App\Models\Notifikasi;
use Illuminate\Http\Request;

class NotifikasiController extends Controller
{
    public function index(Request $request)
    {
        $filter = $request->query('filter', 'semua');

        $notifikasi = Notifikasi::where('id_user', auth()->id())
            ->when($filter === 'belum', fn ($q) => $q->where('dibaca', false))
            ->orderByDesc('dibuat_pada')
            ->orderByDesc('id_notifikasi')
            ->paginate(20)
            ->withQueryString();

        $belum = Notifikasi::where('id_user', auth()->id())
            ->where('dibaca', false)
            ->count();

        return view('notifikasi.index', compact('notifikasi', 'belum', 'filter'));
    }

    public function baca($id)
    {
        $notifikasi = $this->milikSaya($id);
        $notifikasi->update(['dibaca' => true]);

        return back();
    }

    /** Tandai dibaca lalu langsung buka tautan terkait. */
    public function buka($id)
    {
        $notifikasi = $this->milikSaya($id);
        $notifikasi->update(['dibaca' => true]);

        return redirect($notifikasi->tautan ?: route('notifikasi.index'));
    }

    public function bacaSemua()
    {
        Notifikasi::where('id_user', auth()->id())
            ->where('dibaca', false)
            ->update(['dibaca' => true]);

        return back()->with('success', 'Semua notifikasi ditandai sudah dibaca.');
    }

    public function destroy($id)
    {
        $this->milikSaya($id)->delete();

        return back()->with('success', 'Notifikasi dihapus.');
    }

    public function hapusSemua()
    {
        Notifikasi::where('id_user', auth()->id())->delete();

        return back()->with('success', 'Semua notifikasi dihapus.');
    }

    protected function milikSaya($id): Notifikasi
    {
        return Notifikasi::where('id_notifikasi', $id)
            ->where('id_user', auth()->id())
            ->firstOrFail();
    }
}
