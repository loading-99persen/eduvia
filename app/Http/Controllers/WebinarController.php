<?php

namespace App\Http\Controllers;

use App\Models\Webinar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WebinarController extends Controller
{
    // Daftar webinar
    public function index()
    {
        $webinar = Webinar::with([
            'leader',
            'komunitas'
        ])->latest()->get();

        return view('webinar.index', compact('webinar'));
    }

    // Form tambah
    public function create()
    {
        return view('webinar.create');
    }

    // Simpan
    public function store(Request $request)
    {
        $request->validate([
            'id_komunitas' => 'required',
            'judul' => 'required|max:150',
            'deskripsi' => 'required',
            'tanggal' => 'required|date'
        ]);

        Webinar::create([
            'id_leader' => Auth::id(),
            'id_komunitas' => $request->id_komunitas,
            'judul' => $request->judul,
            'deskripsi' => $request->deskripsi,
            'tanggal' => $request->tanggal
        ]);

        return redirect('/webinar')
            ->with('success', 'Webinar berhasil dibuat.');
    }

    // Detail
    public function show($id)
    {
        $webinar = Webinar::with([
            'leader',
            'komunitas',
            'partisipasiWebinar'
        ])->findOrFail($id);

        return view('webinar.show', compact('webinar'));
    }

    // Form edit
    public function edit($id)
    {
        $webinar = Webinar::findOrFail($id);

        return view('webinar.edit', compact('webinar'));
    }

    // Update
    public function update(Request $request, $id)
    {
        $webinar = Webinar::findOrFail($id);

        $request->validate([
            'judul' => 'required|max:150',
            'deskripsi' => 'required',
            'tanggal' => 'required|date'
        ]);

        $webinar->update([
            'judul' => $request->judul,
            'deskripsi' => $request->deskripsi,
            'tanggal' => $request->tanggal
        ]);

        return redirect('/webinar')
            ->with('success', 'Webinar berhasil diperbarui.');
    }

    // Hapus
    public function destroy($id)
    {
        $webinar = Webinar::findOrFail($id);

        $webinar->delete();

        return redirect('/webinar')
            ->with('success', 'Webinar berhasil dihapus.');
    }

    // Ikut webinar
    public function ikut($id)
    {
        $webinar = Webinar::findOrFail($id);

        $sudahIkut = $webinar->partisipasiWebinar()
            ->where('id_user', Auth::id())
            ->exists();

        if (!$sudahIkut) {
            $webinar->partisipasiWebinar()->create([
                'id_user' => Auth::id(),
                'bergabung_pada' => now()
            ]);
        }

        return back()->with('success', 'Berhasil mengikuti webinar.');
    }
}