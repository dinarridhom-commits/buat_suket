@extends('layouts.auth')

@section('title', 'Atur Ulang Kata Sandi')
@section('brand_subtitle', 'ATUR ULANG KATA SANDI')

@section('form_content')
    <p class="auth-desc">Masukkan kode OTP 6 digit yang dikirim ke email Anda beserta kata sandi baru (minimal 8 karakter, huruf dan angka).</p>

    @if(session('status'))
        <div class="alert-box alert-success">
            <svg width="17" height="17" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="flex-shrink:0; margin-top:1px;"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            <span>{{ session('status') }}</span>
        </div>
    @endif

    @if(session('dev_reset_otp'))
        <div class="dev-reset-box">
            <div><strong>Mode Lokal (MAIL_MAILER=log):</strong> Kode OTP Anda adalah <strong style="font-family:monospace;letter-spacing:3px;">{{ session('dev_reset_otp') }}</strong></div>
        </div>
    @endif

    @if($errors->any())
        <div class="alert-box alert-danger">
            <svg width="17" height="17" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="flex-shrink:0; margin-top:1px;"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            <span>{{ $errors->first() }}</span>
        </div>
    @endif

    <form method="POST" action="{{ route('password.update') }}" id="resetForm">
        @csrf
        <input type="hidden" name="g-recaptcha-response" id="recaptchaTokenReset">

        <div class="form-group">
            <div class="form-label-row">
                <label class="form-label" for="email">Email Akun</label>
            </div>
            <div class="input-wrap">
                <span class="input-icon">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><rect x="3" y="5" width="18" height="14" rx="2"/><polyline points="3 7 12 13 21 7"/></svg>
                </span>
                <input type="email" name="email" id="email" class="form-control"
                       value="{{ old('email', $email) }}"
                       placeholder="Masukkan email terdaftar" required autocomplete="email">
            </div>
        </div>

        <div class="form-group">
            <div class="form-label-row">
                <label class="form-label" for="otp">Kode OTP (6 Digit)</label>
            </div>
            <div class="input-wrap">
                <span class="input-icon">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                </span>
                <input type="text" name="otp" id="otp" class="form-control"
                       value="{{ old('otp') }}"
                       inputmode="numeric" maxlength="6" pattern="[0-9]{6}"
                       style="font-family:monospace; letter-spacing:4px; font-weight:700;"
                       placeholder="Contoh: 123456" required autofocus autocomplete="one-time-code">
            </div>
        </div>

        <div class="form-group">
            <div class="form-label-row">
                <label class="form-label" for="password">Kata Sandi Baru</label>
            </div>
            <div class="input-wrap">
                <span class="input-icon">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><rect x="4" y="11" width="16" height="9" rx="2"/><path d="M8 11V7a4 4 0 1 1 8 0v4"/></svg>
                </span>
                <input type="password" name="password" id="password" class="form-control has-toggle"
                       placeholder="Minimal 8 karakter (huruf & angka)" required autofocus autocomplete="new-password">
                <button type="button" class="toggle-password-btn" data-target="password" title="Lihat kata sandi">
                    <svg width="17" height="17" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path d="M2 12s3.5-6 10-6 10 6 10 6-3.5 6-10 6-10-6-10-6Z"/><circle cx="12" cy="12" r="2.5"/></svg>
                </button>
            </div>
        </div>

        <div class="form-group">
            <div class="form-label-row">
                <label class="form-label" for="password_confirmation">Konfirmasi Kata Sandi Baru</label>
            </div>
            <div class="input-wrap">
                <span class="input-icon">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><rect x="4" y="11" width="16" height="9" rx="2"/><path d="M8 11V7a4 4 0 1 1 8 0v4"/></svg>
                </span>
                <input type="password" name="password_confirmation" id="password_confirmation" class="form-control has-toggle"
                       placeholder="Ulangi kata sandi baru" required autocomplete="new-password">
                <button type="button" class="toggle-password-btn" data-target="password_confirmation" title="Lihat kata sandi">
                    <svg width="17" height="17" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path d="M2 12s3.5-6 10-6 10 6 10 6-3.5 6-10 6-10-6-10-6Z"/><circle cx="12" cy="12" r="2.5"/></svg>
                </button>
            </div>
        </div>

        <button type="submit" class="btn-submit-login">
            <span>Simpan Kata Sandi Baru</span>
        </button>

        <div style="margin-top: 18px; text-align: center; display: flex; flex-direction: column; gap: 8px;">
            <a href="{{ route('password.request') }}" class="back-link">Belum menerima kode? Kirim ulang OTP</a>
            <a href="{{ route('login') }}" class="back-link">&larr; Kembali ke halaman LOGIN</a>
        </div>

        <div class="recaptcha-note">
            Dilindungi oleh Google reCAPTCHA v3 ·
            <a href="https://policies.google.com/privacy" target="_blank" rel="noopener">Privasi</a> ·
            <a href="https://policies.google.com/terms" target="_blank" rel="noopener">Ketentuan</a>
        </div>
    </form>
@endsection

@push('scripts')
<script>
    bindRecaptchaV3('resetForm', 'recaptchaTokenReset', 'reset_password');
</script>
@endpush
