@extends('layouts.app')

@section('title', 'Dashboard Utama')
@section('header_title', 'Dashboard')
@section('header_subtitle', 'Portal Tata Usaha & Dokumen Digital BWS Kalimantan I Pontianak')

@push('styles')
<style>
    /* ── PAGE HEADER (Donezo style) ── */
    .dash-header {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        gap: 16px;
        margin-bottom: 24px;
        flex-wrap: wrap;
    }
    .dash-header-title h1 {
        font-size: 28px;
        font-weight: 800;
        color: var(--text-main);
        letter-spacing: -0.6px;
        line-height: 1.15;
    }
    .dash-header-title p {
        font-size: 13.5px;
        color: var(--text-muted);
        margin-top: 4px;
    }
    .dash-header-actions {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
    }
    .btn-pill-primary {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        background: var(--pu-navy);
        color: #ffffff;
        padding: 10px 18px;
        border-radius: 50px;
        text-decoration: none;
        font-size: 13px;
        font-weight: 600;
        transition: all 0.2s;
        box-shadow: 0 4px 12px rgba(19, 36, 61, 0.22);
    }
    .btn-pill-primary:hover {
        background: var(--pu-navy-light);
        transform: translateY(-1px);
    }
    .btn-pill-outline {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        background: var(--bg-surface);
        color: var(--text-main);
        border: 1px solid var(--border);
        padding: 10px 18px;
        border-radius: 50px;
        text-decoration: none;
        font-size: 13px;
        font-weight: 600;
        transition: all 0.2s;
        box-shadow: var(--shadow-sm);
    }
    .btn-pill-outline:hover {
        border-color: var(--border-hover);
        background: var(--bg-subtle);
    }

    /* ── 4 KPI CARDS (First card solid PU Navy) ── */
    .bento-kpi-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 16px;
        margin-bottom: 20px;
    }
    .bento-kpi-card {
        background: var(--bg-surface);
        border-radius: var(--radius-lg);
        padding: 20px 22px;
        box-shadow: var(--shadow-card);
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        transition: transform 0.2s, box-shadow 0.2s;
        text-decoration: none;
        color: inherit;
        position: relative;
    }
    .bento-kpi-card:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-md);
    }
    .bento-kpi-card.featured {
        background: linear-gradient(145deg, var(--pu-navy) 0%, #1c355c 100%);
        color: #ffffff;
        box-shadow: 0 8px 20px rgba(19, 36, 61, 0.24);
    }
    .kpi-top-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 14px;
    }
    .kpi-title {
        font-size: 14px;
        font-weight: 600;
        color: var(--text-muted);
    }
    .bento-kpi-card.featured .kpi-title {
        color: rgba(255, 255, 255, 0.85);
    }
    .kpi-arrow-btn {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        border: 1px solid var(--border);
        background: var(--bg-surface);
        color: var(--text-main);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        font-weight: 700;
        transition: all 0.2s;
    }
    .bento-kpi-card.featured .kpi-arrow-btn {
        background: #ffffff;
        color: var(--pu-navy);
        border-color: #ffffff;
    }
    .bento-kpi-card:hover .kpi-arrow-btn {
        transform: scale(1.06);
    }
    .kpi-number {
        font-size: 36px;
        font-weight: 800;
        line-height: 1.05;
        letter-spacing: -1px;
        font-variant-numeric: tabular-nums;
        margin-bottom: 10px;
    }
    .kpi-trend {
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 11.5px;
        font-weight: 600;
        color: var(--text-muted);
    }
    .bento-kpi-card.featured .kpi-trend {
        color: rgba(255, 255, 255, 0.78);
    }
    .trend-pill {
        display: inline-flex;
        align-items: center;
        gap: 3px;
        padding: 2px 7px;
        border-radius: 6px;
        font-size: 11px;
        font-weight: 700;
        background: var(--pu-navy-50);
        color: var(--pu-navy);
    }
    .bento-kpi-card.featured .trend-pill {
        background: rgba(245, 158, 11, 0.22);
        color: #fcd34d;
    }

    /* ── BENTO ROW 1 (2/3 Analytics + 1/3 Reminder) ── */
    .bento-row-1 {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 16px;
        margin-bottom: 20px;
    }
    .bento-card {
        background: var(--bg-surface);
        border-radius: var(--radius-lg);
        padding: 22px;
        box-shadow: var(--shadow-card);
        display: flex;
        flex-direction: column;
    }
    .bento-card-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 18px;
    }
    .bento-card-head h3 {
        font-size: 16px;
        font-weight: 700;
        color: var(--text-main);
    }
    .bento-card-head .btn-mini-pill {
        font-size: 12px;
        font-weight: 600;
        color: var(--pu-navy);
        background: var(--bg-subtle);
        border: 1px solid var(--border);
        padding: 5px 12px;
        border-radius: 50px;
        text-decoration: none;
        transition: all 0.15s;
    }
    .bento-card-head .btn-mini-pill:hover {
        background: var(--pu-navy);
        color: #ffffff;
        border-color: var(--pu-navy);
    }

    /* Pill / Capsule Bar Chart */
    .capsule-chart {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        gap: 14px;
        height: 175px;
        padding-top: 24px;
        flex: 1;
    }
    .capsule-col {
        flex: 1;
        display: flex;
        flex-direction: column;
        align-items: center;
        height: 100%;
        justify-content: flex-end;
        gap: 8px;
    }
    .capsule-bar-wrap {
        width: 100%;
        max-width: 44px;
        flex: 1;
        display: flex;
        align-items: flex-end;
        position: relative;
    }
    .capsule-bar {
        width: 100%;
        border-radius: 50px;
        background: repeating-linear-gradient(
            -45deg,
            #e5e7eb,
            #e5e7eb 4px,
            #f3f4f6 4px,
            #f3f4f6 8px
        );
        transition: all 0.3s ease;
        position: relative;
        min-height: 28px;
    }
    .capsule-bar.active-bar {
        background: linear-gradient(180deg, var(--pu-navy) 0%, #274775 100%);
        box-shadow: 0 4px 12px rgba(19, 36, 61, 0.22);
    }
    .capsule-bar.highlight-gold {
        background: linear-gradient(180deg, var(--pu-gold) 0%, var(--pu-gold-dark) 100%);
        box-shadow: 0 4px 12px rgba(245, 158, 11, 0.25);
    }
    .capsule-tooltip {
        position: absolute;
        top: -24px;
        left: 50%;
        transform: translateX(-50%);
        background: var(--pu-navy);
        color: #ffffff;
        font-size: 10.5px;
        font-weight: 700;
        padding: 2px 7px;
        border-radius: 6px;
        white-space: nowrap;
        opacity: 0;
        transition: opacity 0.2s;
        pointer-events: none;
    }
    .capsule-col:hover .capsule-tooltip,
    .capsule-bar.active-bar .capsule-tooltip {
        opacity: 1;
    }
    .capsule-label {
        font-size: 12px;
        font-weight: 600;
        color: var(--text-muted);
    }

    /* Reminder / Undangan Terbaru Card */
    .reminder-box {
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        flex: 1;
    }
    .reminder-title {
        font-size: 18px;
        font-weight: 700;
        color: var(--pu-navy);
        line-height: 1.35;
        margin-bottom: 8px;
    }
    .reminder-meta {
        font-size: 12.5px;
        color: var(--text-muted);
        margin-bottom: 18px;
        display: flex;
        flex-direction: column;
        gap: 4px;
    }
    .btn-cta-reminder {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        width: 100%;
        background: var(--pu-navy);
        color: #ffffff;
        padding: 12px 16px;
        border-radius: 50px;
        text-decoration: none;
        font-size: 13.5px;
        font-weight: 600;
        transition: all 0.2s;
        box-shadow: 0 4px 12px rgba(19, 36, 61, 0.2);
    }
    .btn-cta-reminder:hover {
        background: var(--pu-navy-light);
        transform: translateY(-1px);
    }

    /* ── BENTO ROW 2 (3 Equal Columns) ── */
    .bento-row-2 {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 16px;
        margin-bottom: 24px;
    }

    /* Team / Pegawai List */
    .team-list {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }
    .team-item {
        display: flex;
        align-items: center;
        gap: 11px;
    }
    .team-avatar {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: var(--pu-navy-50);
        color: var(--pu-navy);
        font-weight: 800;
        font-size: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        border: 1.5px solid rgba(19, 36, 61, 0.12);
    }
    .team-info {
        flex: 1;
        min-width: 0;
    }
    .team-name {
        font-size: 13px;
        font-weight: 700;
        color: var(--text-main);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .team-role {
        font-size: 11px;
        color: var(--text-muted);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .team-badge {
        font-size: 10.5px;
        font-weight: 600;
        padding: 3px 9px;
        border-radius: 20px;
        background: var(--pu-gold-light);
        color: #92400e;
        flex-shrink: 0;
    }

    /* Donut Progress Card */
    .donut-wrap {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        flex: 1;
        padding-top: 6px;
    }
    .donut-ring {
        width: 140px;
        height: 140px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
        margin-bottom: 16px;
    }
    .donut-inner {
        width: 102px;
        height: 102px;
        border-radius: 50%;
        background: var(--bg-surface);
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }
    .donut-pct {
        font-size: 26px;
        font-weight: 800;
        color: var(--text-main);
        line-height: 1;
    }
    .donut-lbl {
        font-size: 11px;
        color: var(--text-muted);
        margin-top: 3px;
    }
    .donut-legend {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 16px;
        font-size: 12px;
        color: var(--text-muted);
    }
    .legend-dot {
        display: inline-block;
        width: 9px;
        height: 9px;
        border-radius: 50%;
        margin-right: 5px;
    }

    /* Jam Dinas Widget (like Time Tracker) */
    .clock-widget {
        background: linear-gradient(145deg, #0b1626 0%, var(--pu-navy) 100%);
        color: #ffffff;
        position: relative;
        overflow: hidden;
        justify-content: space-between;
    }
    .clock-widget::before {
        content: '';
        position: absolute;
        top: -40px;
        right: -40px;
        width: 160px;
        height: 160px;
        background: radial-gradient(circle, rgba(245, 158, 11, 0.25) 0%, transparent 70%);
        border-radius: 50%;
        pointer-events: none;
    }
    .clock-widget h3 {
        color: rgba(255, 255, 255, 0.85);
        font-size: 14px;
        font-weight: 600;
    }
    .clock-time {
        font-family: 'JetBrains Mono', monospace;
        font-size: 36px;
        font-weight: 700;
        letter-spacing: 1px;
        margin: 14px 0 6px;
        color: #ffffff;
    }
    .clock-date {
        font-size: 12.5px;
        color: var(--pu-gold);
        font-weight: 600;
    }
    .clock-actions {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-top: 16px;
    }
    .clock-quick-btn {
        flex: 1;
        text-align: center;
        padding: 8px 12px;
        border-radius: 50px;
        font-size: 12px;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.2s;
    }
    .clock-btn-gold {
        background: var(--pu-gold);
        color: var(--pu-navy);
    }
    .clock-btn-gold:hover { background: #fbbf24; }
    .clock-btn-ghost {
        background: rgba(255, 255, 255, 0.12);
        color: #ffffff;
    }
    .clock-btn-ghost:hover { background: rgba(255, 255, 255, 0.2); }

    /* ── BOTTOM ARSIP TABLES (2 columns) ── */
    .bento-tables-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
    }
    .btn-view-sm {
        padding: 4px 10px;
        border-radius: 50px;
        background: var(--pu-navy-50);
        color: var(--pu-navy);
        font-size: 11.5px;
        font-weight: 600;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        transition: all 0.15s;
    }
    .btn-view-sm:hover {
        background: var(--pu-navy);
        color: #ffffff;
    }

    @media (max-width: 1150px) {
        .bento-kpi-grid { grid-template-columns: repeat(2, 1fr); }
        .bento-row-1 { grid-template-columns: 1fr; }
        .bento-row-2 { grid-template-columns: 1fr; }
        .bento-tables-row { grid-template-columns: 1fr; }
    }
    @media (max-width: 600px) {
        .bento-kpi-grid { grid-template-columns: 1fr; }
    }
</style>
@endpush

@section('content')

    <!-- PAGE HEADER -->
    <div class="dash-header">
        <div class="dash-header-title">
            <h1>Dashboard</h1>
            <p>Pantau aktivitas persuratan, database pegawai, dan utilitas dokumen BWS Kalimantan I.</p>
        </div>
        <div class="dash-header-actions">
            <a href="{{ route('surat.undangan.create') }}" class="btn-pill-primary">
                <span>+</span> Buat Surat Undangan
            </a>
            <a href="{{ route('surat.suket.create') }}" class="btn-pill-outline">
                <span>📝</span> Buat Suket Absen
            </a>
            <a href="{{ route('pegawai.export.csv') }}" class="btn-pill-outline">
                <span>📥</span> Ekspor CSV
            </a>
        </div>
    </div>

    <!-- 4 KPI CARDS -->
    <div class="bento-kpi-grid">
        <a href="{{ route('surat.undangan.index') }}" class="bento-kpi-card featured">
            <div class="kpi-top-row">
                <span class="kpi-title">Total Surat Keluar</span>
                <span class="kpi-arrow-btn">↗</span>
            </div>
            <div class="kpi-number">{{ $totalSuratKeluar }}</div>
            <div class="kpi-trend">
                <span class="trend-pill">{{ $totalSuratUndangan }} Undangan</span>
                <span>+ {{ $totalSuket }} Suket Absen</span>
            </div>
        </a>

        <a href="{{ route('surat.undangan.index') }}" class="bento-kpi-card">
            <div class="kpi-top-row">
                <span class="kpi-title">Surat Undangan</span>
                <span class="kpi-arrow-btn">↗</span>
            </div>
            <div class="kpi-number">{{ $totalSuratUndangan }}</div>
            <div class="kpi-trend">
                <span class="trend-pill">{{ $suratFinalCount }} Final</span>
                <span>Tercatat di sistem</span>
            </div>
        </a>

        <a href="{{ route('surat.suket.index') }}" class="bento-kpi-card">
            <div class="kpi-top-row">
                <span class="kpi-title">Suket Lupa Absen</span>
                <span class="kpi-arrow-btn">↗</span>
            </div>
            <div class="kpi-number">{{ $totalSuket }}</div>
            <div class="kpi-trend">
                <span class="trend-pill">Aktif</span>
                <span>Surat keterangan pegawai</span>
            </div>
        </a>

        <a href="{{ route('pegawai.index') }}" class="bento-kpi-card">
            <div class="kpi-top-row">
                <span class="kpi-title">Master Pegawai & QR</span>
                <span class="kpi-arrow-btn">↗</span>
            </div>
            <div class="kpi-number">{{ $totalPegawai }}</div>
            <div class="kpi-trend">
                <span class="trend-pill">{{ $unitKerjaCount }} Satker</span>
                <span>• {{ $totalQr }} QR tersimpan</span>
            </div>
        </a>
    </div>

    <!-- BENTO ROW 1: ANALYTICS + REMINDER -->
    <div class="bento-row-1">
        <!-- Left (2/3): Capsule Bar Chart -->
        <div class="bento-card">
            <div class="bento-card-head">
                <h3>Statistik Persuratan (7 Bulan Terakhir)</h3>
                <a href="{{ route('surat.undangan.index') }}" class="btn-mini-pill">Lihat Arsip</a>
            </div>
            @php
                $maxVal = max(array_merge($chartValues, [1]));
            @endphp
            <div class="capsule-chart">
                @foreach($chartLabels as $idx => $label)
                    @php
                        $val = $chartValues[$idx];
                        $heightPct = max(20, round(($val / $maxVal) * 100));
                        $isLast = ($idx === count($chartLabels) - 1);
                        $isPeak = ($val > 0 && $val === $maxVal);
                    @endphp
                    <div class="capsule-col">
                        <div class="capsule-bar-wrap">
                            <div class="capsule-bar {{ $isLast ? 'highlight-gold' : ($isPeak ? 'active-bar' : '') }}" style="height: {{ $heightPct }}%;">
                                <span class="capsule-tooltip">{{ $val }} Surat</span>
                            </div>
                        </div>
                        <span class="capsule-label">{{ $label }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Right (1/3): Reminder / Surat Undangan Terbaru -->
        <div class="bento-card">
            <div class="bento-card-head">
                <h3>Agenda / Surat Terbaru</h3>
                <span style="font-size:18px;">📌</span>
            </div>
            <div class="reminder-box">
                @if($suratMendatang)
                    <div>
                        <div class="reminder-title">{{ Str::limit($suratMendatang->hal, 48) }}</div>
                        <div class="reminder-meta">
                            <span>📄 No: <b style="color:var(--text-main);">{{ $suratMendatang->nomor_surat }}</b></span>
                            <span>📅 Tanggal: {{ $suratMendatang->tanggal_surat->format('d M Y') }}</span>
                            <span>✍️ TTD: {{ $suratMendatang->penandatangan_nama }}</span>
                        </div>
                    </div>
                    <a href="{{ route('surat.undangan.show', $suratMendatang->id) }}" class="btn-cta-reminder">
                        <span>📋</span> Buka & Cetak Surat
                    </a>
                @else
                    <div>
                        <div class="reminder-title">Belum Ada Agenda Surat</div>
                        <div class="reminder-meta">
                            <span>Buat surat undangan rapat atau dinas pertama Anda sekarang.</span>
                        </div>
                    </div>
                    <a href="{{ route('surat.undangan.create') }}" class="btn-cta-reminder">
                        <span>+</span> Buat Surat Undangan
                    </a>
                @endif
            </div>
        </div>
    </div>

    <!-- BENTO ROW 2: 3 EQUAL COLUMNS -->
    <div class="bento-row-2">
        <!-- Col 1: Daftar Pegawai -->
        <div class="bento-card">
            <div class="bento-card-head">
                <h3>Pegawai Terdaftar</h3>
                <a href="{{ route('pegawai.index') }}" class="btn-mini-pill">+ Kelola</a>
            </div>
            <div class="team-list">
                @forelse($pegawaiTerbaru as $p)
                    <div class="team-item">
                        <div class="team-avatar">
                            {{ strtoupper(substr($p->nama, 0, 2)) }}
                        </div>
                        <div class="team-info">
                            <div class="team-name">{{ $p->nama }}</div>
                            <div class="team-role">{{ $p->jabatan ?? 'Pegawai BWS K1' }}</div>
                        </div>
                        <span class="team-badge">{{ Str::limit($p->unit ?? 'Balai', 10) }}</span>
                    </div>
                @empty
                    <p style="font-size:13px; color:var(--text-muted);">Belum ada data pegawai.</p>
                @endforelse
            </div>
        </div>

        <!-- Col 2: Progress / Status Arsip Surat -->
        @php
            $totalUnd = max($totalSuratUndangan, 1);
            $finalPct = $totalSuratUndangan > 0 ? round(($suratFinalCount / $totalUnd) * 100) : 0;
        @endphp
        <div class="bento-card">
            <div class="bento-card-head">
                <h3>Status Arsip Surat</h3>
                <a href="{{ route('qrcode.index') }}" class="btn-mini-pill">QR Studio</a>
            </div>
            <div class="donut-wrap">
                <div class="donut-ring" style="background: conic-gradient(var(--pu-navy) 0% {{ $finalPct }}%, var(--pu-gold) {{ $finalPct }}% 100%);">
                    <div class="donut-inner">
                        <span class="donut-pct">{{ $finalPct }}%</span>
                        <span class="donut-lbl">Surat Final</span>
                    </div>
                </div>
                <div class="donut-legend">
                    <span><i class="legend-dot" style="background:var(--pu-navy);"></i>Final ({{ $suratFinalCount }})</span>
                    <span><i class="legend-dot" style="background:var(--pu-gold);"></i>Draft ({{ $suratDraftCount }})</span>
                </div>
            </div>
        </div>

        <!-- Col 3: Jam Dinas Widget -->
        <div class="bento-card clock-widget">
            <div>
                <h3>Jam Dinas Pontianak (WIB)</h3>
                <div class="clock-time" id="liveClockWib">--:--:--</div>
                <div class="clock-date">{{ now()->locale('id')->translatedFormat('l, d F Y') }}</div>
            </div>
            <div class="clock-actions">
                <a href="{{ route('pdf.index') }}" class="clock-quick-btn clock-btn-gold">📑 PDF Studio</a>
                <a href="{{ route('qrcode.index') }}" class="clock-quick-btn clock-btn-ghost">📱 QR PU</a>
            </div>
        </div>
    </div>

    <!-- BOTTOM: RECENT TABLES -->
    <div class="bento-tables-row">
        <!-- Recent Surat Undangan -->
        <div class="panel-card">
            <div class="panel-head">
                <h3>✉️ Arsip Surat Undangan Terbaru</h3>
                <a href="{{ route('surat.undangan.index') }}" style="font-size:12px; font-weight:600; color:var(--pu-navy); text-decoration:none;">Semua →</a>
            </div>
            <div class="panel-body" style="padding:0;">
                @if($suratTerbaru->count() > 0)
                    <table class="table-modern">
                        <thead>
                            <tr>
                                <th>Nomor</th>
                                <th>Perihal</th>
                                <th>Status</th>
                                <th style="text-align:center;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($suratTerbaru as $s)
                                <tr>
                                    <td style="font-family:'JetBrains Mono',monospace; font-size:11.5px; font-weight:600; color:var(--pu-navy);">
                                        {{ $s->nomor_surat }}
                                    </td>
                                    <td>{{ Str::limit($s->hal, 26) }}</td>
                                    <td>
                                        <span class="badge-status badge-{{ $s->status == 'final' ? 'final' : 'draft' }}">
                                            {{ ucfirst($s->status) }}
                                        </span>
                                    </td>
                                    <td style="text-align:center;">
                                        <a href="{{ route('surat.undangan.show', $s->id) }}" class="btn-view-sm">👁️ Lihat</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="empty-state">
                        <span class="empty-icon">📭</span>
                        <p>Belum ada Surat Undangan yang dibuat.</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Recent Suket Absen -->
        <div class="panel-card">
            <div class="panel-head">
                <h3>📝 Arsip Suket Lupa Absen Terbaru</h3>
                <a href="{{ route('surat.suket.index') }}" style="font-size:12px; font-weight:600; color:var(--pu-navy); text-decoration:none;">Semua →</a>
            </div>
            <div class="panel-body" style="padding:0;">
                @if($suketTerbaru->count() > 0)
                    <table class="table-modern">
                        <thead>
                            <tr>
                                <th>Nomor</th>
                                <th>Pegawai</th>
                                <th>Jenis</th>
                                <th style="text-align:center;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($suketTerbaru as $sk)
                                <tr>
                                    <td style="font-family:'JetBrains Mono',monospace; font-size:11.5px; font-weight:600; color:var(--pu-navy);">
                                        {{ $sk->nomor_surat }}
                                    </td>
                                    <td style="font-weight:600;">{{ Str::limit($sk->nama_pegawai, 22) }}</td>
                                    <td>
                                        <span class="badge-status {{ $sk->jenis_absen == 'masuk' ? 'badge-final' : 'badge-draft' }}">
                                            {{ ucfirst($sk->jenis_absen) }}
                                        </span>
                                    </td>
                                    <td style="text-align:center;">
                                        <a href="{{ route('surat.suket.show', $sk->id) }}" class="btn-view-sm">👁️ Lihat</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="empty-state">
                        <span class="empty-icon">📝</span>
                        <p>Belum ada Suket Lupa Absen yang dibuat.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

@endsection

@push('scripts')
<script>
    function updateLiveClock() {
        const el = document.getElementById('liveClockWib');
        if (!el) return;
        const now = new Date();
        const hh = String(now.getHours()).padStart(2, '0');
        const mm = String(now.getMinutes()).padStart(2, '0');
        const ss = String(now.getSeconds()).padStart(2, '0');
        el.textContent = `${hh}:${mm}:${ss}`;
    }
    updateLiveClock();
    setInterval(updateLiveClock, 1000);
</script>
@endpush
