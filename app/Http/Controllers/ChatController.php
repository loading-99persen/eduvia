<?php

namespace App\Http\Controllers;

use App\Models\Komunitas;
use App\Models\Pesan;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    /** Group chat komunitas, hanya untuk anggota. */
    public function show($id)
    {
        $komunitas = Komunitas::findOrFail($id);

        if (!$this->bolehAkses($komunitas)) {
            return redirect()->route('komunitas.show', $komunitas->id_komunitas)
                ->with('error', 'Gabung komunitas dulu untuk membuka group chat.');
        }

        $chatroom = $komunitas->chatroomAtauBuat();

        $pesan = Pesan::with('user.profil')
            ->where('id_rc', $chatroom->id_rc)
            ->orderBy('dikirim_pada')
            ->take(200)
            ->get();

        return view('komunitas.chat', compact('komunitas', 'chatroom', 'pesan'));
    }

    /** Kirim pesan baru. */
    public function kirim(Request $request, $id)
    {
        $komunitas = Komunitas::findOrFail($id);

        if (!$this->bolehAkses($komunitas)) {
            return back()->with('error', 'Kamu tidak punya akses ke group chat ini.');
        }

        $request->validate([
            'pesan' => ['required', 'string', 'max:2000'],
        ]);

        $chatroom = $komunitas->chatroomAtauBuat();

        $pesan = Pesan::create([
            'id_rc'   => $chatroom->id_rc,
            'id_user' => auth()->id(),
            'pesan'   => $request->input('pesan'),
        ]);

        if ($request->wantsJson()) {
            return response()->json(['ok' => true, 'id_pesan' => $pesan->id_pesan]);
        }

        return back();
    }

    /** Dipakai polling JavaScript supaya chat terasa hidup. */
    public function json(Request $request, $id)
    {
        $komunitas = Komunitas::findOrFail($id);

        if (!$this->bolehAkses($komunitas)) {
            return response()->json(['pesan' => []], 403);
        }

        $chatroom = $komunitas->chatroomAtauBuat();

        $pesan = Pesan::with('user.profil')
            ->where('id_rc', $chatroom->id_rc)
            ->when($request->query('sejak'), fn ($q, $sejak) => $q->where('id_pesan', '>', (int) $sejak))
            ->orderBy('id_pesan')
            ->take(100)
            ->get()
            ->map(fn (Pesan $p) => [
                'id_pesan' => $p->id_pesan,
                'id_user'  => $p->id_user,
                'nama'     => $p->user->profil->nama_lengkap ?? 'Anggota',
                'pesan'    => $p->pesan,
                'jam'      => optional($p->dikirim_pada)->format('H:i'),
                'utc'      => optional($p->dikirim_pada)->utc()->format('Y-m-d H:i'),
                'saya'     => (int) $p->id_user === (int) auth()->id(),
            ]);

        return response()->json(['pesan' => $pesan]);
    }

    protected function bolehAkses(Komunitas $komunitas): bool
    {
        return auth()->user()->isAdmin() || $komunitas->sudah_gabung;
    }
}
