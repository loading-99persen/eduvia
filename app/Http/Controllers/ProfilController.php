<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProfilController extends Controller
{
    /** Profil milik sendiri (sekaligus form ubah data diri). */
    public function index()
    {
        $user = auth()->user()->load(['profil', 'minat']);

        $profil = $user->profil ?: $user->profil()->create([
            'nama_lengkap' => 'Pengguna Baru',
        ]);

        $statistik = (object) [
            'komunitas' => $user->komunitas()->count(),
            'postingan' => $user->posts()->count(),
            'webinar'   => $user->partisipasiWebinar()->count(),
        ];

        return view('user.profil', [
            'user'      => $user,
            'profil'    => $profil,
            'minat'     => $user->minat,
            'statistik' => $statistik,
        ]);
    }

    public function update(Request $request)
    {
        $user = auth()->user();

        $data = $request->validate([
            'nama_lengkap'       => ['required', 'string', 'max:100'],
            'tingkat_pendidikan' => ['required', 'string', 'max:50'],
            'institusi'          => ['nullable', 'string', 'max:100'],
            'tanggal_lahir'      => ['nullable', 'date', 'before:today'],
            'jenis_kelamin'      => ['nullable', 'in:L,P'],
            'bio'                => ['nullable', 'string', 'max:1000'],
            'media_sosial'       => ['nullable', 'string', 'max:255'],
            'photo'              => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ], [
            'photo.image' => 'Foto profil harus berupa gambar.',
            'photo.max'   => 'Ukuran foto maksimal 2 MB.',
        ]);

        $profil = $user->profil ?: $user->profil()->create(['nama_lengkap' => $data['nama_lengkap']]);

        if ($request->hasFile('photo')) {
            if ($profil->photo && Storage::disk('public')->exists($profil->photo)) {
                Storage::disk('public')->delete($profil->photo);
            }

            $data['photo'] = $request->file('photo')->store('profil', 'public');
        } else {
            unset($data['photo']);
        }

        $profil->update($data);

        if (!$user->onboardingSelesai()) {
            return redirect()->route('user.onboarding')
                ->with('success', 'Profil tersimpan. Sekarang pilih minat belajarmu.');
        }

        return redirect()->route('user.profil')
            ->with('success', 'Profil berhasil diperbarui.');
    }

    /** Profil publik pengguna lain. */
    public function show($id)
    {
        $user = User::with(['profil', 'minat'])->findOrFail($id);

        if ((int) $id === (int) auth()->id()) {
            return redirect()->route('user.profil');
        }

        $komunitas = $user->komunitas()->get();

        $posts = $user->posts()
            ->with(['user.profil', 'komunitas'])
            ->withCount(['likes', 'komentar'])
            ->withCount(['likes as disukai_count' => fn ($q) => $q->where('id_user', auth()->id())])
            ->orderByDesc('dibuat_pada')
            ->take(10)
            ->get();

        return view('user.profil-publik', compact('user', 'komunitas', 'posts'));
    }
}
