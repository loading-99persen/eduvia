<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    // =========================
    // LOGIN
    // =========================

    public function login()
    {
        return view('auth.login');
    }

    public function prosesLogin(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $data = [
            'email' => $request->email,
            'password' => $request->password,
            'status' => 'aktif'
        ];

        if (Auth::attempt($data)) {
            $request->session()->regenerate();

            if (Auth::user()->id_role == 1) {
                return redirect('/admin');
            }

            return redirect('/beranda');
        }

        return back()->with('error', 'Email atau password salah!');
    }

    // =========================
    // REGISTER
    // =========================

    public function register()
    {
        return view('auth.register');
    }

    public function prosesRegister(Request $request)
    {
        $request->validate([
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'password_confirmation' => 'required|same:password'
        ]);

        $user = User::create([
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'id_role' => 2,
            'status' => 'aktif'
        ]);

        $user->profil()->create([
            'nama_lengkap' => 'Pengguna Baru'
        ]);

        return redirect('/login')
            ->with('success', 'Registrasi berhasil! Silakan login.');
    }

    // =========================
    // GOOGLE LOGIN
    // =========================

    public function redirectGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function callbackGoogle()
    {
        try {
            $googleUser = Socialite::driver('google')->user();

            $user = User::where('google_id', $googleUser->id)
                ->orWhere('email', $googleUser->email)
                ->first();

            if (!$user) {
                $user = User::create([
                    'email' => $googleUser->email,
                    'password' => Hash::make(uniqid()),
                    'google_id' => $googleUser->id,
                    'id_role' => 2,
                    'status' => 'aktif'
                ]);

                $user->profil()->create([
                    'nama_lengkap' => $googleUser->name ?? 'Pengguna Google'
                ]);
            } else {
                $user->update([
                    'google_id' => $googleUser->id
                ]);
            }

            if ($user->status !== 'aktif') {
                return redirect('/login')
                    ->with('error', 'Akun kamu tidak aktif.');
            }

            Auth::login($user);

            request()->session()->regenerate();

            if ($user->id_role == 1) {
                return redirect('/admin');
            }

            return redirect('/beranda');

        } catch (\Exception $e) {
            return redirect('/login')
                ->with('error', 'Login Google gagal. Silakan coba lagi.');
        }
    }

    // =========================
    // LOGOUT
    // =========================

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login')
            ->with('success', 'Berhasil logout.');
    }
}