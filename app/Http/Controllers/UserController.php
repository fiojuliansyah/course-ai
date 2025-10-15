<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Menampilkan daftar semua pengguna (Index Page).
     */
    public function index()
    {
        $users = User::orderBy('name')->paginate(10); 
        
        return view('admin.users.index', compact('users'));
    }

    /**
     * Menampilkan formulir untuk membuat pengguna baru.
     */
    public function create()
    {
        $roles = ['siswa', 'instructor', 'admin']; 
        return view('admin.users.create', compact('roles'));
    }

    /**
     * Menyimpan pengguna baru ke database (Store).
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => ['required', Rule::in(['siswa', 'instructor', 'admin'])],
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        return redirect()->route('admin.users.index')
                         ->with('success', 'Pengguna berhasil ditambahkan!');
    }

    /**
     * Menampilkan formulir untuk mengedit pengguna (Edit).
     */
    public function edit(User $user)
    {
        $roles = ['siswa', 'instructor', 'admin'];
        return view('admin.users.edit', compact('user', 'roles'));
    }

    /**
     * Memperbarui pengguna yang ada di database (Update).
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)], 
            'password' => 'nullable|string|min:8|confirmed',
            'role' => ['required', Rule::in(['siswa', 'instructor', 'admin'])],
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password ? Hash::make($request->password) : $user->password,
            'role' => $request->role,
        ]);

        return redirect()->route('admin.users.index')
                         ->with('success', 'Data pengguna berhasil diperbarui!');
    }

    /**
     * Menghapus pengguna dari database (Destroy).
     */
    public function destroy(User $user)
    {
        $user->delete();
        
        return redirect()->route('admin.users.index')
                         ->with('success', 'Pengguna berhasil dihapus!');
    }
}