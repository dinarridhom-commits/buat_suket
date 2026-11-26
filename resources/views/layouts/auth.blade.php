@php
    $recaptchaSiteKey = \App\Services\RecaptchaV3Service::siteKey();
    $recaptchaActive  = \App\Services\RecaptchaV3Service::isActive();
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'LOGIN') — BWS Kalimantan I Pontianak</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    @if($recaptchaActive)
        <script src="https://www.google.com/recaptcha/api.js?render={{ urlencode($recaptchaSiteKey) }}"></script>
    @endif

    <style>
        :root {
            --pu-navy: #13243d;
            --pu-navy-hover: #0d1a2e;
            --pu-blue: #1d4ed8;
            --pu-gold: #f59e0b;
            --border: #cbd5e1;
            --border-soft: #e2e8f0;
            --text-main: #0f172a;
            --text-muted: #64748b;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 28px 18px;
            color: var(--text-main);
            background: linear-gradient(155deg, #eef3f8 0%, #dbe5f1 100%);
            position: relative;
            overflow-x: hidden;
        }

        /* Latar belakang motion gelombang aliran sungai */
        .motion-bg-canvas {
            position: fixed;
            inset: 0;
            width: 100%;
            height: 100%;
            z-index: 0;
            pointer-events: none;
        }

        .top-pu-accent {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            background: linear-gradient(90deg, var(--pu-navy) 0%, var(--pu-navy) 70%, var(--pu-gold) 100%);
            z-index: 20;
        }

        /* Kartu terpusat Opsi 1 (proporsional & lega) */
        .auth-card {
            position: relative;
            z-index: 10;
            width: 100%;
            max-width: 440px;
            background: rgba(255, 255, 255, 0.96);
            backdrop-filter: blur(8px);
            border: 1px solid rgba(203, 213, 225, 0.85);
            border-radius: 18px;
            box-shadow: 0 20px 45px -12px rgba(15, 23, 42, 0.14), 0 4px 12px rgba(15, 23, 42, 0.05);
            padding: 36px 38px 30px;
        }

        .auth-brand {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            padding-bottom: 20px;
            margin-bottom: 24px;
            border-bottom: 1px solid var(--border-soft);
        }

        .auth-logo {
            width: 64px;
            height: 64px;
            object-fit: contain;
            border-radius: 12px;
            margin-bottom: 14px;
            filter: drop-shadow(0 4px 10px rgba(19, 36, 61, 0.12));
        }

        .auth-brand-title {
            font-size: 19px;
            font-weight: 800;
            letter-spacing: 0.4px;
            color: var(--pu-navy);
            line-height: 1.25;
        }

        .auth-brand-subtitle {
            font-size: 13.5px;
            font-weight: 700;
            letter-spacing: 2px;
            color: #475569;
            margin-top: 6px;
            text-transform: uppercase;
        }

        .auth-desc {
            font-size: 13px;
            color: var(--text-muted);
            text-align: center;
            line-height: 1.55;
            margin: -10px 0 20px;
        }

        .form-group { margin-bottom: 18px; }

        .form-label-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            margin-bottom: 7px;
        }

        .form-label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: #334155;
        }

        .input-wrap {
            position: relative;
            display: flex;
            align-items: center;
        }

        .input-icon {
            position: absolute;
            left: 14px;
            color: #94a3b8;
            display: inline-flex;
            align-items: center;
            pointer-events: none;
        }

        .form-control {
            width: 100%;
            height: 44px;
            padding: 10px 14px 10px 42px;
            border: 1.5px solid var(--border);
            border-radius: 10px;
            font-size: 13.5px;
            font-family: inherit;
            color: var(--text-main);
            background: #f8fafc;
            outline: none;
            transition: border-color 0.15s ease, background 0.15s ease, box-shadow 0.15s ease;
        }

        .form-control.has-toggle {
            padding-right: 42px;
        }

        .form-control:focus {
            background: #ffffff;
            border-color: var(--pu-blue);
            box-shadow: 0 0 0 3.5px rgba(29, 78, 216, 0.11);
        }

        .toggle-password-btn {
            position: absolute;
            right: 10px;
            background: transparent;
            border: none;
            color: #64748b;
            cursor: pointer;
            padding: 6px;
            border-radius: 6px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: color 0.15s ease, background 0.15s ease;
        }

        .toggle-password-btn:hover {
            color: var(--pu-navy);
            background: #f1f5f9;
        }

        .form-options {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin: 4px 0 22px;
        }

        .remember-checkbox {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            color: #475569;
            cursor: pointer;
            font-weight: 500;
            user-select: none;
        }

        .remember-checkbox input {
            width: 16px;
            height: 16px;
            accent-color: var(--pu-navy);
            cursor: pointer;
        }

        .forgot-link, .back-link {
            color: var(--pu-blue);
            font-weight: 600;
            text-decoration: none;
            font-size: 12.5px;
        }

        .forgot-link:hover, .back-link:hover {
            text-decoration: underline;
            color: var(--pu-navy);
        }

        .btn-submit-login {
            width: 100%;
            height: 46px;
            background: var(--pu-navy);
            color: #ffffff;
            border: none;
            border-radius: 10px;
            font-size: 14.5px;
            font-weight: 700;
            letter-spacing: 0.2px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: background 0.15s ease, transform 0.15s ease, box-shadow 0.15s ease;
            box-shadow: 0 4px 12px rgba(19, 36, 61, 0.22);
            font-family: inherit;
        }

        .btn-submit-login:hover {
            background: var(--pu-navy-hover);
            transform: translateY(-1px);
            box-shadow: 0 6px 16px rgba(19, 36, 61, 0.28);
        }

        .btn-submit-login:disabled {
            opacity: 0.7;
            cursor: wait;
            transform: none;
        }

        .alert-box {
            padding: 11px 14px;
            border-radius: 10px;
            font-size: 13px;
            margin-bottom: 18px;
            display: flex;
            align-items: flex-start;
            gap: 10px;
            line-height: 1.45;
        }

        .alert-danger { background: #fef2f2; border: 1px solid #fecaca; color: #b91c1c; }
        .alert-success { background: #ecfdf5; border: 1px solid #a7f3d0; color: #065f46; }

        .recaptcha-note {
            margin-top: 18px;
            font-size: 11.5px;
            color: #94a3b8;
            text-align: center;
            line-height: 1.5;
        }

        .recaptcha-note a {
            color: #64748b;
            text-decoration: underline;
        }

        .dev-reset-box {
            margin-bottom: 18px;
            padding: 12px 14px;
            border-radius: 10px;
            background: #fffbeb;
            border: 1px solid #fde68a;
            color: #92400e;
            font-size: 12.5px;
            line-height: 1.5;
        }

        .dev-reset-box a {
            display: inline-block;
            margin-top: 8px;
            padding: 7px 13px;
            background: var(--pu-navy);
            color: #ffffff;
            border-radius: 7px;
            font-weight: 600;
            text-decoration: none;
            font-size: 12px;
        }

        @media (max-width: 480px) {
            .auth-card { padding: 28px 22px 24px; }
            .auth-logo { width: 56px; height: 56px; }
        }
    </style>
</head>
<body>
    <div class="top-pu-accent"></div>
    <canvas id="authMotionCanvas" class="motion-bg-canvas" aria-hidden="true"></canvas>

    <main class="auth-card">
        <div class="auth-brand">
            <img src="{{ asset('images/logo_pu.png') }}" alt="Logo PU" class="auth-logo">
            <h1 class="auth-brand-title">BWS KALIMANTAN I</h1>
            <div class="auth-brand-subtitle">@yield('brand_subtitle', 'LOGIN')</div>
        </div>

        @yield('form_content')
    </main>

    <script>
        // Animasi latar belakang gelombang aliran sungai yang halus (ringan & responsif)
        (function () {
            const canvas = document.getElementById('authMotionCanvas');
            if (!canvas || !canvas.getContext) return;
            const ctx = canvas.getContext('2d');
            const reduceMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
            let w = 0, h = 0, dpr = 1, t = 0;

            const layers = [
                { amp: 32, freq: 0.0038, spd: 0.65, yOff: 0.62, color: 'rgba(19, 36, 61, 0.10)' },
                { amp: 26, freq: 0.0048, spd: -0.82, yOff: 0.68, color: 'rgba(29, 78, 216, 0.08)' },
                { amp: 20, freq: 0.0056, spd: 1.05, yOff: 0.74, color: 'rgba(14, 165, 233, 0.09)' }
            ];

            function resize() {
                dpr = Math.min(window.devicePixelRatio || 1, 2);
                w = window.innerWidth;
                h = window.innerHeight;
                canvas.width = w * dpr;
                canvas.height = h * dpr;
                ctx.setTransform(dpr, 0, 0, dpr, 0, 0);
            }

            function draw() {
                const bg = ctx.createLinearGradient(0, 0, w, h);
                bg.addColorStop(0, '#eef3f8');
                bg.addColorStop(1, '#d7e2ef');
                ctx.fillStyle = bg;
                ctx.fillRect(0, 0, w, h);

                layers.forEach(function (L) {
                    ctx.beginPath();
                    ctx.moveTo(0, h);
                    for (let x = 0; x <= w; x += 12) {
                        const y = h * L.yOff
                            + Math.sin(x * L.freq + t * L.spd) * L.amp
                            + Math.cos(x * L.freq * 0.55 - t * 0.45) * 10;
                        ctx.lineTo(x, y);
                    }
                    ctx.lineTo(w, h);
                    ctx.closePath();
                    ctx.fillStyle = L.color;
                    ctx.fill();
                });

                ctx.beginPath();
                for (let x = 0; x <= w; x += 12) {
                    const y = h * 0.54 + Math.sin(x * 0.0032 + t * 0.58) * 30;
                    if (x === 0) ctx.moveTo(x, y); else ctx.lineTo(x, y);
                }
                ctx.strokeStyle = 'rgba(245, 158, 11, 0.34)';
                ctx.lineWidth = 1.6;
                ctx.stroke();

                if (!reduceMotion) {
                    t += 0.014;
                    requestAnimationFrame(draw);
                }
            }

            window.addEventListener('resize', resize);
            resize();
            draw();
        })();

        // Toggle lihat / sembunyikan kata sandi dengan ikon SVG
        const eyeShowSvg = '<svg width="17" height="17" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path d="M2 12s3.5-6 10-6 10 6 10 6-3.5 6-10 6-10-6-10-6Z"/><circle cx="12" cy="12" r="2.5"/></svg>';
        const eyeHideSvg = '<svg width="17" height="17" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path d="M17.94 17.94A10.94 10.94 0 0 1 12 18c-6.5 0-10-6-10-6a18.8 18.8 0 0 1 5.06-5.94"/><path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c6.5 0 10 6 10 6a18.5 18.5 0 0 1-2.16 3.19"/><line x1="2" y1="2" x2="22" y2="22"/></svg>';

        document.querySelectorAll('.toggle-password-btn[data-target]').forEach(function (btn) {
            btn.addEventListener('click', function () {
                const input = document.getElementById(btn.getAttribute('data-target'));
                if (!input) return;
                const isPass = input.type === 'password';
                input.type = isPass ? 'text' : 'password';
                btn.innerHTML = isPass ? eyeHideSvg : eyeShowSvg;
            });
        });

        // Integrasi Google reCAPTCHA v3 saat submit formulir
        window.bindRecaptchaV3 = function (formId, inputId, action) {
            const siteKey = @json($recaptchaActive ? $recaptchaSiteKey : '');
            const form = document.getElementById(formId);
            const tokenInput = document.getElementById(inputId);
            if (!form || !tokenInput || !siteKey) return;

            let submitting = false;
            form.addEventListener('submit', function (e) {
                if (submitting) return;
                if (typeof grecaptcha === 'undefined') {
                    e.preventDefault();
                    alert('Layanan Google reCAPTCHA v3 gagal dimuat. Periksa koneksi internet Anda lalu muat ulang halaman.');
                    return;
                }
                e.preventDefault();
                submitting = true;
                const btn = form.querySelector('button[type="submit"]');
                if (btn) btn.disabled = true;

                grecaptcha.ready(function () {
                    grecaptcha.execute(siteKey, { action: action })
                        .then(function (token) {
                            tokenInput.value = token;
                            form.submit();
                        })
                        .catch(function () {
                            submitting = false;
                            if (btn) btn.disabled = false;
                            alert('Verifikasi reCAPTCHA v3 gagal diproses. Silakan coba lagi.');
                        });
                });
            });
        };
    </script>
    @stack('scripts')
</body>
</html>
