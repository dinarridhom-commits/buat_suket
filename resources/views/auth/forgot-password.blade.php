@extends('layouts.auth')

@section('title', 'Lupa Kata Sandi')

@section('form_content')
    <div class="form-heading">
        <h3>Lupa Kata Sandi</h3>
        <p>Masukkan alamat email akun Anda untuk menerima tautan atur ulang kata sandi.</p>
    </div>

    @if(session('status'))
        <div class="alert-box alert-success">
            <span>✅</span>
            <span>{{ session('status') }}</span>
        </div>
    @endif

    @if(session('dev_reset_url'))
        <div class="dev-reset-box">
            <div><strong>Mode Lokal (MAIL_MAILER=log):</strong> Server email belum dikonfigurasi. Gunakan tautan langsung berikut untuk mengatur ulang kata sandi:</div>
            <a href="{{ session('dev_reset_url') }}">Buka Halaman Reset Kata Sandi</a>
        </div>
    @endif

    @if($errors->any())
        <div class="alert-box alert-danger">
            <span>⚠️</span>
            <span>{{ $errors->first() }}</span>
        </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}" id="forgotForm" style="margin-top: 12px;">
        @csrf
        <input type="hidden" name="g-recaptcha-response" id="recaptchaTokenForgot">

        <div class="form-group">
            <label class="form-label" for="email">Email Akun</label>
            <div class="input-wrap">
                <span class="input-icon">✉️</span>
                <input type="email" name="email" id="email" class="form-control"
                       value="{{ old('email') }}"
                       placeholder="Masukkan email terdaftar Anda" required autofocus autocomplete="email">
            </div>
        </div>

        <button type="submit" class="btn-submit-login">
            <span>Kirim Tautan Atur Ulang</span>
            <span>→</span>
        </button>

        <div style="margin-top: 18px; text-align: center;">
            <a href="{{ route('login') }}" class="back-link">← Kembali ke halaman Masuk</a>
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
