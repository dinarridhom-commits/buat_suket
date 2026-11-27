<?php

namespace App\Http\Controllers;

use App\Models\SecurityLog;
use App\Models\User;
use App\Services\RecaptchaV3Service;
use Carbon\Carbon;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
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
            $otp = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

            DB::table('password_reset_tokens')->updateOrInsert(
                ['email' => $matched->email],
                [
                    'token'      => Hash::make($otp),
                    'created_at' => now(),
                ]
            );

            try {
                $htmlBody = '<div style="font-family:Arial,sans-serif;max-width:520px;margin:0 auto;padding:24px;border:1px solid #e5e7eb;border-radius:12px;">'
                    . '<h2 style="color:#13243d;margin-top:0;">Kode OTP Reset Kata Sandi</h2>'
                    . '<p style="color:#374151;font-size:14px;line-height:1.6;">Halo <strong>' . e($matched->name) . '</strong>,</p>'
                    . '<p style="color:#374151;font-size:14px;line-height:1.6;">Berikut adalah kode OTP 6 digit untuk mengatur ulang kata sandi akun Portal Tata Usaha BWS Kalimantan I:</p>'
                    . '<div style="margin:20px 0;padding:16px;background:#eef2f8;border-radius:10px;text-align:center;font-size:28px;font-weight:800;letter-spacing:8px;color:#13243d;font-family:monospace;">' . $otp . '</div>'
                    . '<p style="color:#6b7280;font-size:13px;line-height:1.5;">Kode ini berlaku selama <strong>10 menit</strong>. Jangan bagikan kode OTP ini kepada siapa pun.</p>'
                    . '</div>';

                Mail::html($htmlBody, function ($message) use ($matched) {
                    $message->to($matched->email, $matched->name)
                        ->subject('Kode OTP Reset Kata Sandi — BWS Kalimantan I');
                });
            } catch (\Throwable $e) {
                SecurityLog::record(
                    'password_reset_mail_failed',
                    'Gagal mengirim email OTP reset sandi: ' . Str::limit($e->getMessage(), 150),
                    'danger',
                    $request,
                    $matched
                );
                return back()->withErrors([
                    'login' => 'Gagal mengirim email OTP. Pastikan konfigurasi SMTP email di server sudah benar.',
                ])->withInput();
            }

            if (app()->environment('local') && config('app.debug') && config('mail.default') === 'log') {
                session()->flash('dev_reset_otp', $otp);
            }

            return redirect()->route('password.reset', ['email' => $matched->email])
                ->with('status', 'Kode OTP 6 digit telah dikirim ke email ' . $matched->email . '. Berlaku selama 10 menit.');
        }

        return back()->with('status', 'Jika email atau username terdaftar di sistem, kode OTP 6 digit telah dikirim ke email terdaftar.');
    }

    /**
     * Tampilkan halaman Atur Ulang Kata Sandi dengan OTP
     */
    public function showResetPassword(Request $request, ?string $token = null)
    {
        return view('auth.reset-password', [
            'email' => (string) $request->query('email', session('otp_reset_email', '')),
        ]);
    }

    /**
     * Proses verifikasi OTP & penyimpanan kata sandi baru
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'otp'      => 'required|digits:6',
            'password' => $this->passwordValidationRules(),
        ], [
            'email.required'     => 'Alamat email wajib diisi.',
            'email.email'        => 'Format alamat email tidak valid.',
            'otp.required'       => 'Kode OTP 6 digit wajib diisi.',
            'otp.digits'         => 'Kode OTP harus berupa 6 digit angka.',
            'password.required'  => 'Kata sandi baru wajib diisi.',
            'password.min'       => 'Kata sandi baru minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi kata sandi baru tidak cocok.',
        ]);

        $this->assertRecaptchaV3($request, 'reset_password', 'otp');

        $emailInput  = Str::lower(trim((string) $request->input('email')));
        $throttleKey = 'pwreset-submit|' . $emailInput . '|' . $request->ip();
        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            SecurityLog::record(
                'password_reset_throttled',
                'Percobaan reset kata sandi melebihi batas (5x/menit) untuk email: ' . Str::limit($emailInput, 80),
                'danger',
                $request
            );
            throw ValidationException::withMessages([
                'otp' => "Terlalu banyak percobaan atur ulang sandi. Coba lagi dalam {$seconds} detik.",
            ]);
        }

        $record = DB::table('password_reset_tokens')
            ->whereRaw('LOWER(email) = ?', [$emailInput])
            ->first();

        $user = User::whereRaw('LOWER(email) = ?', [$emailInput])->first();

        $isValidOtp = $record
            && $user
            && $record->created_at
            && Carbon::parse($record->created_at)->addMinutes(10)->isFuture()
            && Hash::check((string) $request->input('otp'), $record->token);

        if (!$isValidOtp) {
            RateLimiter::hit($throttleKey, 60);
            SecurityLog::record(
                'password_reset_failed',
                'Kode OTP reset kata sandi tidak valid atau kedaluwarsa untuk email: ' . Str::limit($emailInput, 80),
                'warning',
                $request
            );
            throw ValidationException::withMessages([
                'otp' => 'Kode OTP tidak valid atau sudah kedaluwarsa.',
            ]);
        }

        $user->forceFill([
            'password'       => Hash::make((string) $request->input('password')),
            'remember_token' => Str::random(60),
        ])->save();

        DB::table('password_reset_tokens')->whereRaw('LOWER(email) = ?', [$emailInput])->delete();
        RateLimiter::clear($throttleKey);

        SecurityLog::record(
            'password_reset_completed',
            'Kata sandi berhasil diatur ulang melalui OTP email untuk akun ' . $user->email . '.',
            'info',
            $request,
            $user
        );
        event(new PasswordReset($user));

        return redirect()->route('login')
            ->with('success', 'Kata sandi berhasil diperbarui. Silakan masuk dengan kata sandi baru Anda.');
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
