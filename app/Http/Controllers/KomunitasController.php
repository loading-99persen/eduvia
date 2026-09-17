<?php

namespace App\Http\Controllers;

use App\Models\Komunitas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KomunitasController extends Controller
{
    // Daftar komunitas
    public function index()
    {
        $komunitas = Komunitas::with('leader')->get();

        return view('komunitas.index', compact('komunitas'));
    }

    // Form tambah komunitas
    public function create()
    {
        return view('komunitas.create');
    }

    // Simpan komunitas
    public function store(Request $request)
    {
        $request->validate([
            'nama_komunitas' => 'required|max:100',
            'deskripsi' => 'required'
        ]);

        Komunitas::create([
            'nama_komunitas' => $request->nama_komunitas,
            'deskripsi' => $request->deskripsi,
            'id_leader' => Auth::id()
        ]);

        return redirect('/komunitas')
            ->with('success', 'Komunitas berhasil dibuat.');
    }

    // Detail komunitas
    public function show($id)
    {
        $komunitas = Komunitas::with([
            'leader',
            'memberKomunitas',
            'posts',
            'webinar'
        ])->findOrFail($id);

        return view('komunitas.show', compact('komunitas'));
    }

    // Form edit
    public function edit($id)
    {
        $komunitas = Komunitas::findOrFail($id);

        return view('komunitas.edit', compact('komunitas'));
    }

    // Update
    public function update(Request $request, $id)
    {
        $komunitas = Komunitas::findOrFail($id);

        $request->validate([
            'nama_komunitas' => 'required|max:100',
            'deskripsi' => 'required'
        ]);

        $komunitas->update([
            'nama_komunitas' => $request->nama_komunitas,
            'deskripsi' => $request->deskripsi
        ]);

        return redirect('/komunitas')
            ->with('success', 'Komunitas berhasil diperbarui.');
    }

    // Hapus
    public function destroy($id)
    {
        $komunitas = Komunitas::findOrFail($id);

        $komunitas->delete();

        return redirect('/komunitas')
            ->with('success', 'Komunitas berhasil dihapus.');
    }

    // Gabung komunitas
    public function gabung($id)
    {
        $komunitas = Komunitas::findOrFail($id);

        $sudahGabung = $komunitas->memberKomunitas()
            ->where('id_user', Auth::id())
            ->exists();

        if (!$sudahGabung) {
            $komunitas->memberKomunitas()->create([
                'id_user' => Auth::id(),
                'role' => 'member',
                'bergabung_pada' => now()
            ]);
        }

        return back()->with('success', 'Berhasil bergabung ke komunitas.');
    }

    // Keluar komunitas
    public function keluar($id)
    {
        $komunitas = Komunitas::findOrFail($id);

        $komunitas->memberKomunitas()
            ->where('id_user', Auth::id())
            ->delete();

        return back()->with('success', 'Berhasil keluar dari komunitas.');
    }
}