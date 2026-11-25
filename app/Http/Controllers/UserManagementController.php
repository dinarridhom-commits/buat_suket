<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserManagementController extends Controller
{
    public function index()
    {
        $users = User::orderBy('id', 'asc')->get();
        return view('users.index', compact('users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:120',
            'email'    => 'required|email|max:150|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $user = User::create([
            'name'              => $validated['name'],
            'email'             => $validated['email'],
            'password'          => Hash::make($validated['password']),
            'email_verified_at' => now(),
        ]);

        ActivityLog::record('create', 'Manajemen Pengguna', "Menambahkan akun pengguna baru: {$user->name} ({$user->email})");

        return redirect()->route('users.index')->with('success', "Akun {$user->name} berhasil ditambahkan.");
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'name'     => 'required|string|max:120',
            'email'    => ['required', 'email', 'max:150', Rule::unique('users', 'email')->ignore($user->id)],
            'password' => 'nullable|string|min:6|confirmed',
        ]);

        $data = [
            'name'  => $validated['name'],
            'email' => $validated['email'],
        ];

        if (!empty($validated['password'])) {
            $data['password'] = Hash::make($validated['password']);
        }

        $user->update($data);
        ActivityLog::record('update', 'Manajemen Pengguna', "Memperbarui akun pengguna: {$user->name} ({$user->email})");

        return redirect()->route('users.index')->with('success', "Data akun {$user->name} berhasil diperbarui.");
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);

        if ($user->id === Auth::id()) {
            return redirect()->route('users.index')->with('error', 'Akun yang sedang digunakan tidak dapat dihapus.');
        }

        $info = "{$user->name} ({$user->email})";
        $user->delete();
        ActivityLog::record('delete', 'Manajemen Pengguna', "Menghapus akun pengguna: {$info}");

        return redirect()->route('users.index')->with('success', "Akun {$info} berhasil dihapus.");
    }
}
