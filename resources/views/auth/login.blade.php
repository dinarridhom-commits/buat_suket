@extends('layouts.auth')

@section('title', 'Masuk Sistem')

@section('form_content')
    <div class="form-heading">
        <h3>Masuk ke Akun Anda</h3>
        <p>Gunakan kredensial resmi pengelola tata usaha untuk melanjutkan.</p>
    </div>

    @if(session('success'))
        <div class="alert-box alert-success">
            <span>✅</span>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if($errors->any())
        <div class="alert-box alert-danger">
            <span>⚠️</span>
            <span>{{ $errors->first() }}</span>
        </div>
    @endif

    <form method="POST" action="{{ route('login.post') }}" id="loginForm">
        @csrf
        <input type="hidden" name="g-recaptcha-response" id="recaptchaTokenLogin">

        <div class="form-group">
            <label class="form-label" for="login">Email atau Username</label>
            <div class="input-wrap">
                <span class="input-icon">👤</span>
                <input type="text" name="login" id="login" class="form-control"
                       value="{{ old('login') }}"
                       placeholder="Masukkan email atau username" required autofocus autocomplete="username">
            </div>
        </div>

        <div class="form-group">
            <label class="form-label" for="password">Kata Sandi</label>
            <div class="input-wrap">
                <span class="input-icon">🔒</span>
                <input type="password" name="password" id="password" class="form-control"
                       placeholder="••••••••" required autocomplete="current-password">
                <button type="button" class="toggle-password-btn" data-target="password" title="Lihat kata sandi">
                    👁️
                </button>
            </div>
        </div>

        <div class="form-options">
            <label class="remember-checkbox">
                <input type="checkbox" name="remember" id="remember">
                <span>Ingat saya di perangkat ini</span>
            </label>
            <a href="{{ route('password.request') }}" class="forgot-link">Lupa kata sandi?</a>
        </div>

        <button type="submit" class="btn-submit-login">
            <span>Masuk ke Portal</span>
            <span>→</span>
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
