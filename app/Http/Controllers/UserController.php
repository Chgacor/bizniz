<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    // 1. Tampilkan Daftar Staf
    public function index()
    {
        // Ambil semua user beserta role-nya, kecuali diri sendiri (agar tidak sengaja terhapus)
        $users = User::with('roles')->where('id', '!=', auth()->id())->get();
        return view('users.index', compact('users'));
    }

    // 2. Tampilkan Form Tambah Staf
    public function create()
    {
        $roles = Role::all();
        return view('users.create', compact('roles'));
    }

    // 3. Simpan Staf Baru
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', 'exists:roles,name'],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Tetapkan Role (Jabatan)
        $user->assignRole($request->role);

        return redirect()->route('users.index')->with('success', 'Staf baru berhasil ditambahkan.');
    }

    // 4. Hapus Staf (Pecat)
    public function destroy(User $user)
    {
        $user->delete();
        return back()->with('success', 'Data staf berhasil dihapus.');
    }
}
