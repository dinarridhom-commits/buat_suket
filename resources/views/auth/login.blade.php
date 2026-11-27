@extends('layouts.auth')

@section('title', 'LOGIN')
@section('brand_subtitle', 'LOGIN')

@section('form_content')
    @if(session('success'))
        <div class="alert-box alert-success">
            <svg width="17" height="17" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="flex-shrink:0; margin-top:1px;"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if($errors->any())
        <div class="alert-box alert-danger">
            <svg width="17" height="17" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="flex-shrink:0; margin-top:1px;"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            <span>{{ $errors->first() }}</span>
        </div>
    @endif

    <form method="POST" action="{{ route('login.post') }}" id="loginForm">
        @csrf
        <input type="hidden" name="g-recaptcha-response" id="recaptchaTokenLogin">

        <div class="form-group">
            <div class="form-label-row">
                <label class="form-label" for="login">Email atau Username</label>
            </div>
            <div class="input-wrap">
                <span class="input-icon">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 4-6 8-6s8 2 8 6"/></svg>
                </span>
                <input type="text" name="login" id="login" class="form-control"
                       value="{{ old('login') }}"
                       placeholder="Masukkan email atau username" required autofocus autocomplete="username">
            </div>
        </div>

        <div class="form-group">
            <div class="form-label-row">
                <label class="form-label" for="password">Kata Sandi</label>
            </div>
            <div class="input-wrap">
                <span class="input-icon">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><rect x="4" y="11" width="16" height="9" rx="2"/><path d="M8 11V7a4 4 0 1 1 8 0v4"/></svg>
                </span>
                <input type="password" name="password" id="password" class="form-control has-toggle"
                       placeholder="••••••••" required autocomplete="current-password">
                <button type="button" class="toggle-password-btn" data-target="password" title="Lihat kata sandi">
                    <svg width="17" height="17" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path d="M2 12s3.5-6 10-6 10 6 10 6-3.5 6-10 6-10-6-10-6Z"/><circle cx="12" cy="12" r="2.5"/></svg>
                </button>
            </div>
        </div>

        <div class="form-options">
            <label class="remember-checkbox">
                <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                <span>Ingat saya</span>
            </label>
            <a href="{{ route('password.request') }}" class="forgot-link">Lupa kata sandi?</a>
        </div>

        <button type="submit" class="btn-submit-login">
            <span>Masuk</span>
        </button>

        <div class="recaptcha-note">
            Dilindungi oleh Google reCAPTCHA v3 ·
            <a href="https://policies.google.com/privacy" target="_blank" rel="noopener">Privasi</a> ·
            <a href="https://policies.google.com/terms" target="_blank" rel="noopener">Ketentuan</a>
        </div>
    </form>
@endsection

@push('scripts')
<script>
    bindRecaptchaV3('loginForm', 'recaptchaTokenLogin', 'login');
</script>
@endpush
