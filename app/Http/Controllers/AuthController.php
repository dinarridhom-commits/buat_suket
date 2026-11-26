<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\RecaptchaV3Service;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Validasi Google reCAPTCHA v3 bila Site Key & Secret Key sudah dikonfigurasi.
     */
    private function assertRecaptchaV3(Request $request, string $action, string $errorField = 'login'): void
    {
        $check = RecaptchaV3Service::verify(
            $request->input('g-recaptcha-response'),
            $action,
            (string) $request->ip()
        );

        if (!$check['ok']) {
            throw ValidationException::withMessages([
                $errorField => $check['message'] ?? 'Verifikasi reCAPTCHA v3 gagal.',
            ]);
        }
    }

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
        $request->validate([
            'login'    => 'required|string',
            'password' => 'required|string',
        ], [
            'login.required'    => 'Email atau Username wajib diisi.',
            'password.required' => 'Kata sandi wajib diisi.',
        ]);

        $this->assertRecaptchaV3($request, 'login', 'login');

        // Rate limiting throttle untuk keamanan (5x percobaan / menit)
        $throttleKey = Str::lower($request->input('login')) . '|' . $request->ip();
        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            throw ValidationException::withMessages([
                'login' => "Terlalu banyak percobaan login gagal. Silakan coba lagi dalam {$seconds} detik.",
            ]);
        }

        $loginField = filter_var($request->input('login'), FILTER_VALIDATE_EMAIL) ? 'email' : 'name';
        $remember   = $request->boolean('remember');

        if (Auth::attempt([$loginField => $request->input('login'), 'password' => $request->input('password')], $remember)) {
            RateLimiter::clear($throttleKey);
            $request->session()->regenerate();

            return redirect()->intended(route('dashboard'))
                ->with('success', 'Selamat datang kembali, ' . Auth::user()->name . '!');
        }

        RateLimiter::hit($throttleKey, 60);

        throw ValidationException::withMessages([
            'login' => 'Email/Username atau kata sandi yang Anda masukkan tidak sesuai.',
        ]);
    }

    /**
     * Tampilkan halaman Lupa Kata Sandi
     */
    public function showForgotPassword()
    {
        return view('auth.forgot-password');
    }

    /**
     * Kirim tautan atur ulang kata sandi
     */
    public function sendResetLink(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ], [
            'email.required' => 'Alamat email wajib diisi.',
            'email.email'    => 'Format alamat email tidak valid.',
        ]);

        $this->assertRecaptchaV3($request, 'forgot_password', 'email');

        $throttleKey = 'pwreset|' . $request->ip();
        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            throw ValidationException::withMessages([
                'email' => "Terlalu banyak permintaan reset. Coba lagi dalam {$seconds} detik.",
            ]);
        }
        RateLimiter::hit($throttleKey, 60);

        $user = null;
        Password::sendResetLink($request->only('email'), function (User $matchedUser, string $token) use (&$user) {
            $user = $matchedUser;
            $matchedUser->sendPasswordResetNotification($token);

            if (app()->environment('local') && config('app.debug') && config('mail.default') === 'log') {
                session()->flash('dev_reset_url', route('password.reset', [
                    'token' => $token,
                    'email' => $matchedUser->email,
                ]));
            }
        });

        return back()->with('status', 'Jika alamat email terdaftar di sistem, tautan atur ulang kata sandi telah dikirim ke email Anda.');
    }

    /**
     * Tampilkan halaman Atur Ulang Kata Sandi
     */
    public function showResetPassword(Request $request, string $token)
    {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => (string) $request->query('email', ''),
        ]);
    }

    /**
     * Proses penyimpanan kata sandi baru
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token'    => 'required|string',
            'email'    => 'required|email',
            'password' => 'required|string|min:6|confirmed',
        ], [
            'email.required'     => 'Alamat email wajib diisi.',
            'email.email'        => 'Format alamat email tidak valid.',
            'password.required'  => 'Kata sandi baru wajib diisi.',
            'password.min'       => 'Kata sandi baru minimal 6 karakter.',
            'password.confirmed' => 'Konfirmasi kata sandi baru tidak cocok.',
        ]);

        $this->assertRecaptchaV3($request, 'reset_password', 'email');

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill([
                    'password'       => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return redirect()->route('login')
                ->with('success', 'Kata sandi berhasil diperbarui. Silakan masuk dengan kata sandi baru Anda.');
        }

        $message = match ($status) {
            Password::INVALID_USER  => 'Akun dengan alamat email tersebut tidak ditemukan.',
            Password::INVALID_TOKEN => 'Tautan atur ulang kata sandi tidak valid atau sudah kedaluwarsa.',
            default                 => 'Gagal mengatur ulang kata sandi. Silakan ajukan permintaan baru.',
        };

        throw ValidationException::withMessages([
            'email' => $message,
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

        return redirect()->route('profil.index')
            ->with('success', 'Kata sandi berhasil diubah.');
    }
}
