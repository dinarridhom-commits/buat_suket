<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>500 — Kendala Server | BWS Kalimantan I</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo_pu.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&family=JetBrains+Mono:wght@700&display=swap" rel="stylesheet">
    <style>
        :root {
            --pu-navy: #13243d;
            --pu-gold: #f59e0b;
            --pu-blue: #1d4ed8;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: linear-gradient(135deg, #0b1526 0%, #13243d 50%, #1a3456 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
            color: #ffffff;
            text-align: center;
        }
        .error-card {
            background: #ffffff;
            color: #0f172a;
            max-width: 520px;
            width: 100%;
            padding: 44px 36px;
            border-radius: 20px;
            box-shadow: 0 25px 50px -12px rgba(0,0,0,0.5);
        }
        .error-logo {
            width: 54px;
            height: 54px;
            border-radius: 10px;
            border: 2px solid var(--pu-gold);
            padding: 4px;
            background: #ffffff;
            margin: 0 auto 16px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.15);
        }
        .error-code {
            font-family: 'JetBrains Mono', monospace;
            font-size: 72px;
            font-weight: 800;
            color: #dc2626;
            line-height: 1;
            margin-bottom: 8px;
            letter-spacing: -2px;
        }
        .error-title {
            font-size: 20px;
            font-weight: 800;
            color: #1e293b;
            margin-bottom: 10px;
        }
        .error-desc {
            font-size: 13.5px;
            color: #64748b;
            line-height: 1.6;
            margin-bottom: 28px;
        }
        .btn-home {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: var(--pu-navy);
            color: #ffffff;
            padding: 12px 24px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 700;
            font-size: 13.5px;
            transition: all 0.2s ease;
        }
        .btn-home:hover {
            background: #1e3a63;
            transform: translateY(-2px);
        }
    </style>
</head>
<body>
    <div class="error-card">
        <img src="{{ asset('images/logo_pu.png') }}" class="error-logo" alt="Logo PU">
        <div class="error-code">500</div>
        <h1 class="error-title">Terjadi Kendala Server</h1>
        <p class="error-desc">
            Sistem sedang memproses permintaan namun menemui kendala internal. Silakan muat ulang halaman atau hubungi pengelola sistem tata usaha.
        </p>
        <a href="{{ route('dashboard') }}" class="btn-home">
            <span>🏠</span> Kembali ke Dashboard
        </a>
    </div>
</body>
</html>
