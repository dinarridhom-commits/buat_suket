@extends('layouts.auth')

@section('title', 'Atur Ulang Kata Sandi')

@section('form_content')
    <div class="form-heading">
        <h3>Atur Ulang Kata Sandi</h3>
        <p>Masukkan kata sandi baru minimal 6 karakter untuk akun Anda.</p>
    </div>

    @if($errors->any())
        <div class="alert-box alert-danger">
            <span>⚠️</span>
            <span>{{ $errors->first() }}</span>
        </div>
    @endif

    <form method="POST" action="{{ route('password.update') }}" id="resetForm">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">
        <input type="hidden" name="g-recaptcha-response" id="recaptchaTokenReset">

        <div class="form-group">
            <label class="form-label" for="email">Email Akun</label>
            <div class="input-wrap">
                <span class="input-icon">✉️</span>
                <input type="email" name="email" id="email" class="form-control"
                       value="{{ old('email', $email) }}"
                       placeholder="Masukkan email terdaftar" required autocomplete="email">
            </div>
        </div>

        <div class="form-group">
            <label class="form-label" for="password">Kata Sandi Baru</label>
            <div class="input-wrap">
                <span class="input-icon">🔒</span>
                <input type="password" name="password" id="password" class="form-control"
                       placeholder="Minimal 6 karakter" required autofocus autocomplete="new-password">
                <button type="button" class="toggle-password-btn" data-target="password" title="Lihat kata sandi">
                    👁️
                </button>
            </div>
        </div>

        <div class="form-group">
            <label class="form-label" for="password_confirmation">Konfirmasi Kata Sandi Baru</label>
            <div class="input-wrap">
                <span class="input-icon">🔒</span>
                <input type="password" name="password_confirmation" id="password_confirmation" class="form-control"
                       placeholder="Ulangi kata sandi baru" required autocomplete="new-password">
                <button type="button" class="toggle-password-btn" data-target="password_confirmation" title="Lihat kata sandi">
                    👁️
                </button>
            </div>
        </div>

        <button type="submit" class="btn-submit-login">
            <span>Simpan Kata Sandi Baru</span>
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
    bindRecaptchaV3('resetForm', 'recaptchaTokenReset', 'reset_password');
</script>
@endpush
