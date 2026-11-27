@extends('layouts.auth')

@section('title', 'Lupa Kata Sandi')
@section('brand_subtitle', 'LUPA KATA SANDI')

@section('form_content')
    <p class="auth-desc">Masukkan alamat email aktif yang terdaftar pada akun Anda untuk menerima kode OTP 6 digit melalui email.</p>

    @if(session('status'))
        <div class="alert-box alert-success">
            <svg width="17" height="17" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="flex-shrink:0; margin-top:1px;"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            <span>{{ session('status') }}</span>
        </div>
    @endif

    @if($errors->any())
        <div class="alert-box alert-danger">
            <svg width="17" height="17" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="flex-shrink:0; margin-top:1px;"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            <span>{{ $errors->first() }}</span>
        </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}" id="forgotForm">
        @csrf
        <input type="hidden" name="g-recaptcha-response" id="recaptchaTokenForgot">

        <div class="form-group">
            <div class="form-label-row">
                <label class="form-label" for="login">Alamat Email</label>
            </div>
            <div class="input-wrap">
                <span class="input-icon">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><rect x="3" y="5" width="18" height="14" rx="2"/><polyline points="3 7 12 13 21 7"/></svg>
                </span>
                <input type="email" name="login" id="login" class="form-control"
                       value="{{ old('login') }}"
                       placeholder="Masukkan alamat email terdaftar" required autofocus autocomplete="email">
            </div>
        </div>

        <button type="submit" class="btn-submit-login">
            <span>Kirim Kode OTP ke Email</span>
        </button>

        <div style="margin-top: 18px; text-align: center;">
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
    bindRecaptchaV3('forgotForm', 'recaptchaTokenForgot', 'forgot_password');
</script>
@endpush
