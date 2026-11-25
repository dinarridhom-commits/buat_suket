<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Tampilkan halaman login
     */
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.login');
    }

    /**
     * Proses autentikasi login
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'login'    => 'required|string',
            'password' => 'required|string',
        ], [
            'login.required'    => 'Email atau Username wajib diisi.',
            'password.required' => 'Kata sandi wajib diisi.',
        ]);

        // Rate limiting throttle untuk keamanan hosting publik (5x percobaan / menit)
        $throttleKey = Str::lower($request->input('login')) . '|' . $request->ip();
        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            throw ValidationException::withMessages([
                'login' => "Terlalu banyak percobaan login gagal. Silakan coba lagi dalam {$seconds} detik.",
            ]);
        }

        $loginField = filter_var($request->input('login'), FILTER_VALIDATE_EMAIL) ? 'email' : 'name';
        $remember = $request->boolean('remember');

        if (Auth::attempt([$loginField => $request->input('login'), 'password' => $request->input('password')], $remember)) {
            RateLimiter::clear($throttleKey);
            $request->session()->regenerate();
            ActivityLog::record('login', 'Keamanan & Autentikasi', 'Berhasil masuk ke portal (' . Auth::user()->email . ').');

            return redirect()->intended(route('dashboard'))
                ->with('success', 'Selamat datang kembali, ' . Auth::user()->name . '!');
        }

        RateLimiter::hit($throttleKey, 60);

        throw ValidationException::withMessages([
            'login' => 'Email/Username atau kata sandi yang Anda masukkan tidak sesuai.',
        ]);
    }

    /**
     * Proses keluar (logout)
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('success', 'Anda telah berhasil keluar dari sistem.');
    }

    /**
     * Tampilkan halaman profil & ubah kata sandi
     */
    public function showProfile()
    {
        $user = Auth::user();
        return view('auth.profil', compact('user'));
    }

    /**
     * Update nama & email profil
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name'  => 'required|string|max:120',
            'email' => 'required|email|max:150|unique:users,email,' . $user->id,
        ], [
            'name.required'  => 'Nama lengkap wajib diisi.',
            'email.required' => 'Alamat email wajib diisi.',
            'email.unique'   => 'Email sudah digunakan oleh akun lain.',
        ]);

        $user->update($validated);

        return redirect()->route('profil.index')
            ->with('success', 'Profil akun berhasil diperbarui.');
    }

    /**
     * Ubah kata sandi akun
     */
    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|string',
            'password'         => 'required|string|min:6|confirmed',
        ], [
            'current_password.required' => 'Kata sandi saat ini wajib diisi.',
            'password.required'         => 'Kata sandi baru wajib diisi.',
            'password.min'              => 'Kata sandi baru minimal 6 karakter.',
            'password.confirmed'        => 'Konfirmasi kata sandi baru tidak cocok.',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => 'Kata sandi saat ini tidak sesuai.',
            ]);
        }

        $user->update([
            'password' => Hash::make($request->password),
        ]);
        ActivityLog::record('update', 'Keamanan & Autentikasi', 'Mengubah kata sandi akun (' . $user->email . ').');

        return redirect()->route('profil.index')
            ->with('success', 'Kata sandi berhasil diubah.');
    }
}
