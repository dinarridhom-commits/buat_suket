<?php

namespace App\Http\Controllers;

use App\Models\SecurityLog;
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
     * Aturan kata sandi kuat yang konsisten pada reset & ubah kata sandi.
     */
    private function passwordValidationRules(): array
    {
        return [
            'required',
            'string',
            'min:8',
            'confirmed',
            function (string $attribute, mixed $value, \Closure $fail) {
                $str = (string) $value;
                if (!preg_match('/[A-Za-z]/', $str) || !preg_match('/[0-9]/', $str)) {
                    $fail('Kata sandi baru wajib mengombinasikan huruf dan angka.');
                }
            },
        ];
    }

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
            SecurityLog::record(
                'login_throttled',
                'Percobaan login diblokir sementara (melebihi 5x/menit) untuk akun: ' . Str::limit((string) $request->input('login'), 60),
                'danger',
                $request
            );
            throw ValidationException::withMessages([
                'login' => "Terlalu banyak percobaan login gagal. Silakan coba lagi dalam {$seconds} detik.",
            ]);
        }

        $loginField = filter_var($request->input('login'), FILTER_VALIDATE_EMAIL) ? 'email' : 'name';
        $remember   = $request->boolean('remember');

        if (Auth::attempt([$loginField => $request->input('login'), 'password' => $request->input('password')], $remember)) {
            RateLimiter::clear($throttleKey);
            $request->session()->regenerate();
            SecurityLog::record('login_success', 'Login berhasil ke Portal Tata Usaha.', 'info', $request, Auth::user());

            return redirect()->intended(route('dashboard'))
                ->with('success', 'Selamat datang kembali, ' . Auth::user()->name . '!');
        }

        RateLimiter::hit($throttleKey, 60);
        SecurityLog::record(
            'login_failed',
            'Percobaan login gagal untuk identitas: ' . Str::limit((string) $request->input('login'), 60),
            'warning',
            $request
        );

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
     * Kirim tautan atur ulang kata sandi (menerima email atau username)
     */
    public function sendResetLink(Request $request)
    {
        $request->validate([
            'login' => 'required|string',
        ], [
            'login.required' => 'Email atau Username wajib diisi.',
        ]);

        $this->assertRecaptchaV3($request, 'forgot_password', 'login');

        $throttleKey = 'pwreset|' . $request->ip();
        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            throw ValidationException::withMessages([
                'login' => "Terlalu banyak permintaan reset. Coba lagi dalam {$seconds} detik.",
            ]);
        }
        RateLimiter::hit($throttleKey, 60);

        $loginInput = mb_strtolower(trim((string) $request->input('login')));
        SecurityLog::record(
            'password_reset_requested',
            'Permintaan tautan atur ulang kata sandi diajukan untuk: ' . Str::limit($loginInput, 80),
            'warning',
            $request
        );
        $matched    = User::whereRaw('LOWER(email) = ? OR LOWER(name) = ?', [$loginInput, $loginInput])->first();

        if ($matched) {
            Password::sendResetLink(['email' => $matched->email], function (User $user, string $token) {
                $user->sendPasswordResetNotification($token);

                if (app()->environment('local') && config('app.debug') && config('mail.default') === 'log') {
                    session()->flash('dev_reset_url', route('password.reset', [
                        'token' => $token,
                        'email' => $user->email,
                    ]));
                }
            });
        }

        return back()->with('status', 'Jika email atau username terdaftar di sistem, tautan atur ulang kata sandi telah dikirim.');
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
            'password' => $this->passwordValidationRules(),
        ], [
            'email.required'     => 'Alamat email wajib diisi.',
            'email.email'        => 'Format alamat email tidak valid.',
            'password.required'  => 'Kata sandi baru wajib diisi.',
            'password.min'       => 'Kata sandi baru minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi kata sandi baru tidak cocok.',
        ]);

        $this->assertRecaptchaV3($request, 'reset_password', 'email');

        $throttleKey = 'pwreset-submit|' . Str::lower((string) $request->input('email')) . '|' . $request->ip();
        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            SecurityLog::record(
                'password_reset_throttled',
                'Percobaan reset kata sandi melebihi batas (5x/menit) untuk email: ' . Str::limit((string) $request->input('email'), 80),
                'danger',
                $request
            );
            throw ValidationException::withMessages([
                'email' => "Terlalu banyak percobaan atur ulang sandi. Coba lagi dalam {$seconds} detik.",
            ]);
        }

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) use ($request) {
                $user->forceFill([
                    'password'       => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();

                SecurityLog::record(
                    'password_reset_completed',
                    'Kata sandi berhasil diatur ulang melalui token reset untuk akun ' . $user->email . '.',
                    'info',
                    $request,
                    $user
                );
                event(new PasswordReset($user));
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            RateLimiter::clear($throttleKey);
            return redirect()->route('login')
                ->with('success', 'Kata sandi berhasil diperbarui. Silakan masuk dengan kata sandi baru Anda.');
        }

        RateLimiter::hit($throttleKey, 60);

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
        $user = Auth::user();
        SecurityLog::record('logout', 'Pengguna keluar dari sistem.', 'info', $request, $user);

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
        SecurityLog::record('profile_updated', 'Memperbarui profil akun (' . $user->name . ' / ' . $user->email . ').', 'info', $request, $user);

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
            'password'         => $this->passwordValidationRules(),
        ], [
            'current_password.required' => 'Kata sandi saat ini wajib diisi.',
            'password.required'         => 'Kata sandi baru wajib diisi.',
            'password.min'              => 'Kata sandi baru minimal 8 karakter.',
            'password.confirmed'        => 'Konfirmasi kata sandi baru tidak cocok.',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            SecurityLog::record('password_change_failed', 'Gagal ubah kata sandi: kata sandi saat ini tidak cocok.', 'warning', $request, $user);
            throw ValidationException::withMessages([
                'current_password' => 'Kata sandi saat ini tidak sesuai.',
            ]);
        }

        $user->forceFill([
            'password'       => Hash::make($request->password),
            'remember_token' => Str::random(60),
        ])->save();

        $request->session()->regenerate();
        SecurityLog::record('password_changed', 'Kata sandi akun berhasil diubah dari halaman Profil dan sesi diperbarui.', 'info', $request, $user);

        return redirect()->route('profil.index')
            ->with('success', 'Kata sandi berhasil diubah dan sesi login diperbarui.');
    }
}
