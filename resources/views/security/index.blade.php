@extends('layouts.app')

@section('title', 'Keamanan & Backup')
@section('header_title', 'Keamanan & Backup Database')
@section('header_subtitle', 'Pantau status proteksi website, log aktivitas keamanan, dan unduh cadangan data SQL')

@push('styles')
<style>
    .sec-top-actions {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        flex-wrap: wrap;
        margin-bottom: 18px;
    }
    .sec-kpi-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 14px;
        margin-bottom: 18px;
    }
    .sec-kpi-card {
        background: var(--bg-surface);
        border: 1px solid var(--border);
        border-radius: var(--radius-md);
        padding: 16px 18px;
        box-shadow: var(--shadow-card);
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .sec-kpi-label {
        font-size: 12px;
        font-weight: 600;
        color: var(--text-muted);
    }
    .sec-kpi-val {
        font-size: 26px;
        font-weight: 800;
        color: var(--pu-navy);
        margin-top: 2px;
    }
    .sec-grid-2 {
        display: grid;
        grid-template-columns: 1.35fr 1fr;
        gap: 16px;
        margin-bottom: 18px;
    }
    .check-list {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }
    .check-row {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 12px;
        padding: 10px 12px;
        border: 1px solid var(--border);
        border-radius: var(--radius-sm);
        background: var(--bg-subtle);
    }
    .check-title {
        font-size: 13px;
        font-weight: 700;
        color: var(--text-main);
    }
    .check-detail {
        font-size: 11.5px;
        color: var(--text-muted);
        margin-top: 2px;
    }
    .badge-sec {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 3px 10px;
        border-radius: 999px;
        font-size: 11px;
        font-weight: 700;
        white-space: nowrap;
        flex-shrink: 0;
    }
    .badge-ok { background: #dcfce7; color: #166534; }
    .badge-warn { background: #fef3c7; color: #92400e; }
    .badge-danger { background: #fee2e2; color: #b91c1c; }
    .badge-info { background: #eff6ff; color: #1d4ed8; }
    .env-snippet {
        background: #0f172a;
        color: #e2e8f0;
        padding: 12px 14px;
        border-radius: var(--radius-sm);
        font-family: 'JetBrains Mono', monospace;
        font-size: 11.5px;
        line-height: 1.65;
        overflow-x: auto;
        margin-top: 10px;
    }
    .sec-filter-row {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
    }
    .sec-input, .sec-select {
        padding: 7px 11px;
        border: 1px solid var(--border);
        border-radius: 8px;
        font-size: 12.5px;
        font-family: inherit;
        background: #ffffff;
        color: var(--text-main);
    }
    .btn-sec-primary {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        padding: 9px 16px;
        border-radius: 50px;
        background: var(--pu-navy);
        color: #ffffff;
        text-decoration: none;
        font-size: 13px;
        font-weight: 600;
        border: none;
        cursor: pointer;
        box-shadow: 0 3px 10px rgba(19, 36, 61, 0.2);
    }
    .btn-sec-primary:hover { background: var(--pu-navy-light); }
    .btn-sec-outline {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 7px 13px;
        border-radius: 8px;
        background: #ffffff;
        color: var(--text-main);
        border: 1px solid var(--border);
        font-size: 12px;
        font-weight: 600;
        font-family: inherit;
        cursor: pointer;
        text-decoration: none;
    }
    .btn-sec-outline:hover { background: var(--bg-subtle); }
    @media (max-width: 1024px) {
        .sec-kpi-grid { grid-template-columns: 1fr; }
        .sec-grid-2 { grid-template-columns: 1fr; }
    }
</style>
@endpush

@section('content')

<div class="sec-top-actions">
    <div style="font-size:13px; color:var(--text-muted);">
        Pantau status proteksi produksi dan buat salinan cadangan SQL secara berkala sebelum ada perubahan data besar.
    </div>
    <div style="display:flex; align-items:center; gap:8px; flex-wrap:wrap;">
        <a href="{{ route('security.backup') }}" class="btn-sec-primary">
            <x-icon name="database" class="ui-icon-sm" /> Unduh Backup Database (.sql)
        </a>
    </div>
</div>

<div class="sec-kpi-grid">
    <div class="sec-kpi-card">
        <div>
            <div class="sec-kpi-label">Total Catatan Keamanan</div>
            <div class="sec-kpi-val">{{ $summary['total'] }}</div>
        </div>
        <span class="badge-sec badge-info">Aktif</span>
    </div>
    <div class="sec-kpi-card">
        <div>
            <div class="sec-kpi-label">Peringatan (Gagal Login / Hapus Permanen)</div>
            <div class="sec-kpi-val" style="color:#b45309;">{{ $summary['warning'] }}</div>
        </div>
        <span class="badge-sec badge-warn">Warning</span>
    </div>
    <div class="sec-kpi-card">
        <div>
            <div class="sec-kpi-label">Blokir Throttle (Brute-Force)</div>
            <div class="sec-kpi-val" style="color:#b91c1c;">{{ $summary['danger'] }}</div>
        </div>
        <span class="badge-sec badge-danger">Kritis</span>
    </div>
</div>

<div class="sec-grid-2">
    <div class="panel-card">
        <div class="panel-head">
            <h3><x-icon name="shield-check" /> Status Proteksi Sistem</h3>
        </div>
        <div class="panel-body">
            <div class="check-list">
                @foreach($checks as $chk)
                    <div class="check-row">
                        <div>
                            <div class="check-title">{{ $chk['label'] }}</div>
                            <div class="check-detail">{{ $chk['detail'] }}</div>
                        </div>
                        <span class="badge-sec {{ $chk['ok'] ? 'badge-ok' : 'badge-warn' }}">
                            {{ $chk['ok'] ? 'Aktif' : 'Perlu Cek' }}
                        </span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="panel-card">
        <div class="panel-head">
            <h3><x-icon name="key" /> Konfigurasi reCAPTCHA v3 &amp; SMTP</h3>
        </div>
        <div class="panel-body">
            <p style="font-size:12.5px; color:var(--text-muted); line-height:1.6;">
                Untuk mengaktifkan verifikasi anti-bot Google reCAPTCHA v3 serta pengiriman email <strong>Lupa Kata Sandi</strong> via SMTP, tambahkan kunci milik instansi ke file <code>htdocs/.env</code> via File Manager:
            </p>
            <pre class="env-snippet">SESSION_SECURE_COOKIE=true
SESSION_HTTP_ONLY=true
SESSION_SAME_SITE=lax

RECAPTCHA_ENABLED=true
RECAPTCHA_SITE_KEY="ISI_SITE_KEY_GOOGLE"
RECAPTCHA_SECRET_KEY="ISI_SECRET_KEY_GOOGLE"

MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_SCHEME=tls
MAIL_USERNAME="emailanda@gmail.com"
MAIL_PASSWORD="APP_PASSWORD_GMAIL_16_DIGIT"</pre>
            <p style="font-size:11.5px; color:var(--text-subtle); margin-top:10px; line-height:1.5;">
                Tip: Gunakan App Password Google (bukan sandi akun Google utama). Jangan lupa klik <strong>Unduh Backup Database (.sql)</strong> setiap minggu.
            </p>
        </div>
    </div>
</div>

<div class="panel-card">
    <div class="panel-head" style="flex-wrap:wrap; gap:10px;">
        <h3><x-icon name="history" /> Log Aktivitas Keamanan</h3>
        <div class="sec-filter-row">
            <form method="GET" action="{{ route('security.index') }}" class="sec-filter-row">
                <input type="text" name="q" value="{{ request('q') }}" class="sec-input" placeholder="Cari IP, akun, atau kejadian...">
                <select name="level" class="sec-select">
                    <option value="">Semua Level</option>
                    <option value="info" {{ request('level') === 'info' ? 'selected' : '' }}>Info</option>
                    <option value="warning" {{ request('level') === 'warning' ? 'selected' : '' }}>Warning</option>
                    <option value="danger" {{ request('level') === 'danger' ? 'selected' : '' }}>Kritis / Blokir</option>
                </select>
                <button type="submit" class="btn-sec-outline">Filter</button>
            </form>
            <form method="POST" action="{{ route('security.prune') }}" onsubmit="return confirm('Hapus catatan log keamanan yang berusia lebih dari 30 hari?')">
                @csrf
                @method('DELETE')
                <input type="hidden" name="scope" value="30days">
                <button type="submit" class="btn-sec-outline">Bersihkan &gt;30 Hari</button>
            </form>
        </div>
    </div>
    <div class="panel-body" style="padding:0;">
        @if($logs->count() > 0)
            <table class="table-modern">
                <thead>
                    <tr>
                        <th>Waktu</th>
                        <th>Level</th>
                        <th>Aktivitas</th>
                        <th>Akun</th>
                        <th>Alamat IP</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($logs as $log)
                        @php
                            $lvlClass = match ($log->level) {
                                'danger'  => 'badge-danger',
                                'warning' => 'badge-warn',
                                default   => 'badge-info',
                            };
                        @endphp
                        <tr>
                            <td style="white-space:nowrap; font-size:12px; color:var(--text-muted);">
                                {{ $log->created_at ? $log->created_at->format('d/m/Y H:i:s') : '-' }}
                            </td>
                            <td>
                                <span class="badge-sec {{ $lvlClass }}">{{ strtoupper($log->level) }}</span>
                            </td>
                            <td style="font-family:'JetBrains Mono',monospace; font-size:11.5px; font-weight:600; color:var(--pu-navy);">
                                {{ $log->event }}
                            </td>
                            <td style="font-weight:600;">{{ $log->actor_name ?? '-' }}</td>
                            <td style="font-family:'JetBrains Mono',monospace; font-size:11.5px;">{{ $log->ip_address ?? '-' }}</td>
                            <td style="font-size:12.5px;">{{ $log->description }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            @include('partials.pagination-bar', ['paginator' => $logs, 'label' => 'catatan log'])
        @else
            <div class="empty-state">
                <span class="empty-icon"><x-icon name="shield-check" class="ui-icon-lg" /></span>
                <p style="font-weight:600;">Belum ada catatan keamanan yang sesuai filter.</p>
            </div>
        @endif
    </div>
</div>

@endsection
