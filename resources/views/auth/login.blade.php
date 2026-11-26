<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk Sistem — Portal Tata Usaha BWS Kalimantan I Pontianak</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">

    <style>
        :root {
            --pu-navy: #13243d;
            --pu-navy-dark: #0a1422;
            --pu-navy-light: #1e3a63;
            --pu-blue: #1d4ed8;
            --pu-gold: #f59e0b;
            --pu-gold-dark: #d97706;
            --bg-surface: #ffffff;
            --border: #e2e8f0;
            --text-main: #0f172a;
            --text-muted: #64748b;
            --radius-md: 10px;
            --radius-lg: 16px;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
            background: linear-gradient(135deg, #0b1526 0%, #13243d 50%, #1a3456 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
            color: var(--text-main);
            position: relative;
            overflow-x: hidden;
        }

        /* Background Watermark Geometric */
        body::before {
            content: "";
            position: absolute;
            width: 600px;
            height: 600px;
            background: radial-gradient(circle, rgba(245, 158, 11, 0.08) 0%, rgba(245, 158, 11, 0) 70%);
            top: -100px;
            right: -100px;
            border-radius: 50%;
            pointer-events: none;
        }

        body::after {
            content: "";
            position: absolute;
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, rgba(37, 99, 235, 0.12) 0%, rgba(37, 99, 235, 0) 70%);
            bottom: -150px;
            left: -150px;
            border-radius: 50%;
            pointer-events: none;
        }

        .login-container {
            width: 100%;
            max-width: 960px;
            background: #ffffff;
            border-radius: 20px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.45);
            overflow: hidden;
            display: grid;
            grid-template-columns: 1.15fr 1fr;
            position: relative;
            z-index: 10;
            border: 1px solid rgba(255, 255, 255, 0.15);
        }

        /* LEFT BRAND PANEL */
        .brand-panel {
            background: linear-gradient(145deg, #0f1c30 0%, #172d4d 100%);
            color: #ffffff;
            padding: 48px 44px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
            border-right: 1px solid rgba(255, 255, 255, 0.08);
        }

        .brand-header {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .brand-logo-img {
            width: 52px;
            height: 52px;
            background: #ffffff;
            border-radius: 10px;
            padding: 5px;
            object-fit: contain;
            box-shadow: 0 4px 10px rgba(0,0,0,0.3);
            border: 2px solid var(--pu-gold);
        }

        .brand-title h1 {
            font-size: 15.5px;
            font-weight: 800;
            letter-spacing: 0.5px;
            color: #ffffff;
            line-height: 1.25;
        }

        .brand-title span {
            font-size: 11.5px;
            color: var(--pu-gold);
            font-weight: 600;
            display: block;
            margin-top: 2px;
        }

        .brand-hero {
            margin: 40px 0;
        }

        .brand-hero h2 {
            font-size: 26px;
            font-weight: 800;
            line-height: 1.3;
            margin-bottom: 14px;
            color: #ffffff;
            letter-spacing: -0.4px;
        }

        .brand-hero p {
            font-size: 13.5px;
            color: #94a3b8;
            line-height: 1.6;
        }

        .feature-bullets {
            list-style: none;
            margin-top: 22px;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .feature-bullet {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 12.5px;
            color: #cbd5e1;
            font-weight: 500;
        }

        .bullet-icon {
            width: 22px;
            height: 22px;
            border-radius: 50%;
            background: rgba(245, 158, 11, 0.18);
            color: var(--pu-gold);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: bold;
            flex-shrink: 0;
        }

        .brand-footer {
            font-size: 11.5px;
            color: #64748b;
            border-top: 1px solid rgba(255, 255, 255, 0.08);
            padding-top: 18px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        /* RIGHT FORM PANEL */
        .form-panel {
            padding: 48px 42px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            background: #ffffff;
        }

        .form-heading {
            margin-bottom: 28px;
        }

        .form-heading h3 {
            font-size: 22px;
            font-weight: 800;
            color: var(--pu-navy);
            letter-spacing: -0.3px;
        }

        .form-heading p {
            font-size: 13px;
            color: var(--text-muted);
            margin-top: 4px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            font-size: 12.5px;
            font-weight: 700;
            color: #334155;
            margin-bottom: 6px;
        }

        .input-wrap {
            position: relative;
            display: flex;
            align-items: center;
        }

        .input-icon {
            position: absolute;
            left: 14px;
            font-size: 16px;
            color: #94a3b8;
            pointer-events: none;
        }

        .form-control {
            width: 100%;
            padding: 12px 14px 12px 42px;
            border: 1.5px solid #cbd5e1;
            border-radius: var(--radius-md);
            font-size: 14px;
            font-family: inherit;
            color: var(--text-main);
            outline: none;
            transition: all 0.15s ease;
            background: #fafbfc;
        }

        .form-control:focus {
            background: #ffffff;
            border-color: var(--pu-blue);
            box-shadow: 0 0 0 3.5px rgba(37, 99, 235, 0.12);
        }

        .toggle-password-btn {
            position: absolute;
            right: 12px;
            background: transparent;
            border: none;
            color: #64748b;
            cursor: pointer;
            font-size: 16px;
            padding: 4px;
            border-radius: 4px;
        }

        .form-options {
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 12.5px;
            margin-bottom: 24px;
        }

        .remember-checkbox {
            display: flex;
            align-items: center;
            gap: 8px;
            color: #475569;
            cursor: pointer;
            font-weight: 500;
        }

        .remember-checkbox input {
            width: 16px;
            height: 16px;
            accent-color: var(--pu-navy);
            cursor: pointer;
        }

        .btn-submit-login {
            width: 100%;
            padding: 13px;
            background: linear-gradient(135deg, var(--pu-navy) 0%, var(--pu-navy-light) 100%);
            color: #ffffff;
            border: none;
            border-radius: var(--radius-md);
            font-size: 14.5px;
            font-weight: 700;
            letter-spacing: 0.3px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: all 0.2s ease;
            box-shadow: 0 4px 14px rgba(19, 36, 61, 0.35);
        }

        .btn-submit-login:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 18px rgba(19, 36, 61, 0.45);
            background: linear-gradient(135deg, #0b1526 0%, var(--pu-navy) 100%);
        }

        /* ALERT MESSAGES */
        .alert-box {
            padding: 11px 14px;
            border-radius: var(--radius-md);
            font-size: 13px;
            margin-bottom: 18px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .alert-danger {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #b91c1c;
        }

        .alert-success {
            background: #ecfdf5;
            border: 1px solid #a7f3d0;
            color: #065f46;
        }

        /* DEMO CREDENTIAL CHIP */
        .demo-box {
            margin-top: 24px;
            padding: 12px 14px;
            background: #f8fafc;
            border: 1px dashed #cbd5e1;
            border-radius: var(--radius-md);
            font-size: 12px;
            color: #475569;
        }

        .demo-header {
            font-weight: 700;
            color: var(--pu-navy);
            margin-bottom: 6px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .demo-btn-autofill {
            background: #e2e8f0;
            border: none;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: 700;
            color: #1e293b;
            cursor: pointer;
        }

        .demo-btn-autofill:hover {
            background: #cbd5e1;
        }

        @media (max-width: 820px) {
            .login-container {
                grid-template-columns: 1fr;
                max-width: 480px;
            }
            .brand-panel {
                display: none;
            }
            .form-panel {
                padding: 36px 28px;
            }
        }
    </style>
</head>
<body>

    <div class="login-container">
        <!-- LEFT PANEL: BRANDING -->
        <div class="brand-panel">
            <div class="brand-header">
                <img src="{{ asset('images/logo_pu.png') }}" alt="Logo PU" class="brand-logo-img">
                <div class="brand-title">
                    <h1>BWS KALIMANTAN I</h1>
                    <span>Kementerian Pekerjaan Umum</span>
                </div>
            </div>

            <div class="brand-hero">
                <h2>Portal Administrasi & Tata Usaha Kedinasan</h2>
                <p>Sistem manajemen arsip Surat Undangan, Surat Keterangan Lupa Absen, serta Master Database 148 Pegawai BWS Kalimantan I.</p>

                <ul class="feature-bullets">
                    <li class="feature-bullet">
                        <span class="bullet-icon">✓</span>
                        <span>Format Naskah Resmi Standar Balai (A4 Siap Cetak)</span>
                    </li>
                    <li class="feature-bullet">
                        <span class="bullet-icon">✓</span>
                        <span>Sinkronisasi Jabatan & Pejabat Penandatangan</span>
                    </li>
                    <li class="feature-bullet">
                        <span class="bullet-icon">✓</span>
                        <span>Keamanan Akses Terpusat Berbasis Kredensial</span>
                    </li>
                </ul>
            </div>

            <div class="brand-footer">
                <span>© 2026 BWS Kalimantan I Pontianak</span>
                <span>Ditjen Sumber Daya Air</span>
            </div>
        </div>

        <!-- RIGHT PANEL: LOGIN FORM -->
        <div class="form-panel">
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

                <div class="form-group">
                    <label class="form-label" for="login">Email atau Username</label>
                    <div class="input-wrap">
                        <span class="input-icon">👤</span>
                        <input type="text" name="login" id="login" class="form-control" 
                               value="{{ old('login') }}" 
                               placeholder="Masukkan email atau username instansi" required autofocus autocomplete="username">
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="password">Kata Sandi</label>
                    <div class="input-wrap">
                        <span class="input-icon">🔒</span>
                        <input type="password" name="password" id="password" class="form-control" 
                               placeholder="••••••••" required autocomplete="current-password">
                        <button type="button" class="toggle-password-btn" id="togglePasswordBtn" title="Lihat kata sandi">
                            👁️
                        </button>
                    </div>
                </div>

                <div class="form-options">
                    <label class="remember-checkbox">
                        <input type="checkbox" name="remember" id="remember">
                        <span>Ingat saya di perangkat ini</span>
                    </label>
                </div>

                <button type="submit" class="btn-submit-login">
                    <span>Masuk ke Portal</span>
                    <span>→</span>
                </button>
            </form>

            <!-- SECURITY NOTICE -->
            <div class="demo-box" style="border-style:solid; border-color:#e2e8f0; color:#475569; font-size:12px; line-height:1.5;">
                <div style="font-weight:700; color:var(--pu-navy); margin-bottom:2px;">🛡️ Akses Terbatas Pegawai BWS Kalimantan I</div>
                <div>Gunakan akun resmi yang terdaftar. Sistem membatasi percobaan masuk demi keamanan dokumen kedinasan.</div>
            </div>
        </div>
    </div>

    <script>
        // Toggle Show / Hide Password
        const toggleBtn = document.getElementById('togglePasswordBtn');
        const passInput = document.getElementById('password');

        if (toggleBtn && passInput) {
            toggleBtn.addEventListener('click', function() {
                const isPass = passInput.type === 'password';
                passInput.type = isPass ? 'text' : 'password';
                toggleBtn.textContent = isPass ? '🙈' : '👁️';
            });
        }
    </script>
</body>
</html>
