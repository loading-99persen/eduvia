<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    // Menampilkan semua user
    public function index()
    {
        $users = User::with(['role', 'profil'])->get();

        return view('admin.users.index', compact('users'));
    }

    // Form tambah user
    public function create()
    {
        return view('admin.users.create');
    }

    // Simpan user
    public function store(Request $request)
    {
        $request->validate([
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'id_role' => 'required',
            'status' => 'required'
        ]);

        User::create([
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'id_role' => $request->id_role,
            'status' => $request->status
        ]);

        return redirect('/admin/users')
            ->with('success', 'User berhasil ditambahkan.');
    }

    // Detail user
    public function show($id)
    {
        $user = User::with([
            'role',
            'profil',
            'minat',
            'komunitas',
            'posts'
        ])->findOrFail($id);

        return view('admin.users.show', compact('user'));
    }

    // Form edit
    public function edit($id)
    {
        $user = User::findOrFail($id);

        return view('admin.users.edit', compact('user'));
    }

    // Update user
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'email' => 'required|email|unique:users,email,' . $id . ',id_user',
            'id_role' => 'required',
            'status' => 'required'
        ]);

        $user->update([
            'email' => $request->email,
            'id_role' => $request->id_role,
            'status' => $request->status
        ]);

        if ($request->filled('password')) {
            $user->update([
                'password' => Hash::make($request->password)
            ]);
        }

        return redirect('/admin/users')
            ->with('success', 'User berhasil diperbarui.');
    }

    // Hapus user
    public function destroy($id)
    {
        $user = User::findOrFail($id);

        $user->delete();

        return redirect('/admin/users')
            ->with('success', 'User berhasil dihapus.');
    }
}