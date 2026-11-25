@extends('layouts.app')

@section('title', 'Profil Akun & Kata Sandi')
@section('header_title', 'Profil Akun')
@section('header_subtitle', 'Kelola informasi akun dan keamanan kata sandi Anda')

@push('styles')
<style>
    .profil-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
        max-width: 980px;
    }
    .form-group {
        margin-bottom: 16px;
    }
    .form-group label {
        display: block;
        font-size: 12.5px;
        font-weight: 700;
        color: var(--text-main);
        margin-bottom: 6px;
    }
    .form-control {
        width: 100%;
        padding: 10px 14px;
        border: 1.5px solid var(--border);
        border-radius: var(--radius-md);
        font-size: 13.5px;
        font-family: inherit;
        color: var(--text-main);
        background: var(--bg-subtle);
        transition: border-color 0.2s, background 0.2s;
    }
    .form-control:focus {
        outline: none;
        border-color: var(--pu-navy);
        background: #ffffff;
    }
    .error-text {
        color: var(--danger);
        font-size: 11.5px;
        font-weight: 600;
        margin-top: 4px;
        display: block;
    }
    .btn-save {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: var(--pu-navy);
        color: #ffffff;
        border: none;
        padding: 10px 20px;
        border-radius: 50px;
        font-size: 13.5px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
        box-shadow: 0 4px 10px rgba(19, 36, 61, 0.2);
    }
    .btn-save:hover {
        background: var(--pu-navy-light);
        transform: translateY(-1px);
    }
    .profil-banner {
        display: flex;
        align-items: center;
        gap: 16px;
        padding: 20px 24px;
        background: linear-gradient(135deg, var(--pu-navy) 0%, #1e3a66 100%);
        color: #ffffff;
        border-radius: var(--radius-lg);
        margin-bottom: 20px;
        max-width: 980px;
        box-shadow: var(--shadow-card);
    }
    .profil-avatar-lg {
        width: 58px;
        height: 58px;
        border-radius: 50%;
        background: var(--pu-gold);
        color: var(--pu-navy);
        font-size: 20px;
        font-weight: 800;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 3px solid #ffffff;
        flex-shrink: 0;
    }
    @media (max-width: 820px) {
        .profil-grid { grid-template-columns: 1fr; }
    }
</style>
@endpush

@section('content')

    <div class="profil-banner">
        <div class="profil-avatar-lg">
            {{ strtoupper(substr($user->name ?? 'AD', 0, 2)) }}
        </div>
        <div>
            <h3 style="font-size:18px; font-weight:800; margin-bottom:2px;">{{ $user->name }}</h3>
            <p style="font-size:13px; color:rgba(255,255,255,0.75);">{{ $user->email }} • Operator Tata Usaha BWS Kalimantan I</p>
        </div>
    </div>

    <div class="profil-grid">
        <!-- Informasi Profil -->
        <div class="panel-card">
            <div class="panel-head">
                <h3>👤 Informasi Akun</h3>
            </div>
            <div class="panel-body">
                <form method="POST" action="{{ route('profil.update') }}">
                    @csrf
                    @method('PUT')

                    <div class="form-group">
                        <label for="name">Nama Lengkap / Username</label>
                        <input type="text" id="name" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
                        @error('name')
                            <span class="error-text">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="email">Alamat Email</label>
                        <input type="email" id="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
                        @error('email')
                            <span class="error-text">{{ $message }}</span>
                        @enderror
                    </div>

                    <button type="submit" class="btn-save">💾 Simpan Profil</button>
                </form>
            </div>
        </div>

        <!-- Ubah Kata Sandi -->
        <div class="panel-card">
            <div class="panel-head">
                <h3>🔒 Ubah Kata Sandi</h3>
            </div>
            <div class="panel-body">
                <form method="POST" action="{{ route('profil.password') }}">
                    @csrf
                    @method('PUT')

                    <div class="form-group">
                        <label for="current_password">Kata Sandi Saat Ini</label>
                        <input type="password" id="current_password" name="current_password" class="form-control" required>
                        @error('current_password')
                            <span class="error-text">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="password">Kata Sandi Baru (Minimal 6 karakter)</label>
                        <input type="password" id="password" name="password" class="form-control" required>
                        @error('password')
                            <span class="error-text">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="password_confirmation">Konfirmasi Kata Sandi Baru</label>
                        <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" required>
                    </div>

                    <button type="submit" class="btn-save">🔑 Perbarui Kata Sandi</button>
                </form>
            </div>
        </div>
    </div>

@endsection
