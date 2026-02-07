<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index()
    {
        // Admin & Owner boleh lihat semua user
        $users = User::with('roles')->latest()->paginate(10);
        return view('users.index', compact('users'));
    }

    public function create()
    {
        if (auth()->user()->hasRole('Owner')) {
            $roles = Role::all();
        } else {
            // Admin hanya boleh bikin Staff atau Admin lain
            $roles = Role::where('name', '!=', 'Owner')->get();
        }

        return view('users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
            'role' => 'required|exists:roles,name'
        ]);


        if ($request->role === 'Owner' && !auth()->user()->hasRole('Owner')) {
            abort(403, 'Admin tidak boleh membuat akun Owner.');
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $user->assignRole($request->role);

        return redirect()->route('users.index')->with('success', 'Staf berhasil ditambahkan.');
    }

    public function destroy(User $user)
    {
        $user->delete();
        return back()->with('success', 'Data staf berhasil dihapus.');
    }
}
