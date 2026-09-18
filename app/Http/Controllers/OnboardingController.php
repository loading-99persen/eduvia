<?php

namespace App\Http\Controllers;

use App\Models\Minat;
use Illuminate\Http\Request;

class OnboardingController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        return view('user.onboarding', [
            'semuaMinat'    => Minat::orderBy('nama_minat')->get(),
            'minatTerpilih' => $user->minat()->pluck('minat.id_minat')->all(),
            'preferensi'    => $user->profil->preferensi_belajar ?? 'diskusi',
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'minat'      => ['required', 'array', 'min:1'],
            'minat.*'    => ['integer', 'exists:minat,id_minat'],
            'preferensi' => ['nullable', 'in:diskusi,webinar,praktik'],
        ], [
            'minat.required' => 'Pilih minimal satu minat belajar.',
            'minat.min'      => 'Pilih minimal satu minat belajar.',
        ]);

        $user = auth()->user();

        $user->minat()->sync($data['minat']);

        $profil = $user->profil ?: $user->profil()->create(['nama_lengkap' => 'Pengguna Baru']);
        $profil->update(['preferensi_belajar' => $data['preferensi'] ?? 'diskusi']);

        return redirect()->route('beranda')
            ->with('success', 'Minat belajar tersimpan. Berandamu sudah disesuaikan.');
    }
}
