<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Support\Notif;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    // =========================================================
    // LOGIN
    // =========================================================

    public function login()
    {
        if (auth()->check()) {
            return $this->kePeranMasing();
        }

        return view('auth.login');
    }

    public function prosesLogin(Request $request)
    {
        $kredensial = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ], [], [
            'email'    => 'email',
            'password' => 'kata sandi',
        ]);

        $user = User::where('email', $kredensial['email'])->first();

        if (!$user || !Hash::check($kredensial['password'], $user->password)) {
            return back()
                ->withInput($request->only('email'))
                ->with('error', 'Email atau kata sandi salah.');
        }

        if (!$user->aktif()) {
            return back()
                ->withInput($request->only('email'))
                ->with('error', 'Akun ini sedang dinonaktifkan. Hubungi admin untuk mengaktifkannya kembali.');
        }

        Auth::login($user, $request->boolean('ingat'));
        $request->session()->regenerate();

        return $this->kePeranMasing();
    }

    // =========================================================
    // REGISTER
    // =========================================================

    public function register()
    {
        if (auth()->check()) {
            return $this->kePeranMasing();
        }

        return view('auth.register');
    }

    public function prosesRegister(Request $request)
    {
        $data = $request->validate([
            'nama_lengkap' => ['required', 'string', 'max:100'],
            'email'        => ['required', 'email', 'max:100', 'unique:users,email'],
            'password'     => ['required', 'string', 'min:6', 'confirmed'],
        ], [
            'password.confirmed' => 'Konfirmasi kata sandi tidak sama.',
            'email.unique'       => 'Email ini sudah terdaftar.',
        ]);

        $user = User::create([
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
            'id_role'  => 2,          // registrasi publik selalu menjadi user
            'status'   => 'aktif',
        ]);

        $user->profil()->create([
            'nama_lengkap' => $data['nama_lengkap'],
        ]);

        Notif::kirim(
            $user->id_user,
            'sistem',
            'Selamat datang di Eduvia',
            'Lengkapi profil dan pilih minat belajarmu supaya rekomendasi lebih pas.',
            route('user.profil')
        );

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('user.profil')
            ->with('success', 'Registrasi berhasil. Lengkapi profilmu dulu, ya.');
    }

    // =========================================================
    // GOOGLE
    // =========================================================

    public function redirectGoogle()
    {
        try {
            return Socialite::driver('google')->redirect();
        } catch (\Throwable $e) {
            return redirect()->route('login')
                ->with('error', 'Login Google belum dapat digunakan. Silakan masuk memakai email.');
        }
    }

    public function callbackGoogle(Request $request)
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (\Throwable $e) {
            return redirect()->route('login')
                ->with('error', 'Login Google gagal. Silakan coba lagi.');
        }

        $user = User::where('google_id', $googleUser->getId())
            ->orWhere('email', $googleUser->getEmail())
            ->first();

        if (!$user) {
            $user = User::create([
                'email'     => $googleUser->getEmail(),
                'password'  => Hash::make(str()->random(32)),
                'google_id' => $googleUser->getId(),
                'id_role'   => 2,
                'status'    => 'aktif',
            ]);

            $user->profil()->create([
                'nama_lengkap' => $googleUser->getName() ?: 'Pengguna Google',
            ]);
        } elseif (!$user->google_id) {
            $user->update(['google_id' => $googleUser->getId()]);
        }

        if (!$user->aktif()) {
            return redirect()->route('login')
                ->with('error', 'Akun ini sedang dinonaktifkan.');
        }

        Auth::login($user);
        $request->session()->regenerate();

        return $this->kePeranMasing();
    }

    // =========================================================
    // LOGOUT
    // =========================================================

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Kamu sudah keluar dari akun.');
    }

    // =========================================================

    protected function kePeranMasing()
    {
        $user = auth()->user();

        if ($user->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }

        if (!$user->profilLengkap()) {
            return redirect()->route('user.profil')
                ->with('success', 'Lengkapi profilmu dulu, ya.');
        }

        if (!$user->onboardingSelesai()) {
            return redirect()->route('user.onboarding');
        }

        return redirect()->route('beranda');
    }
}
