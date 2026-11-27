@extends('layouts.app')

@section('title', 'Pengaturan Sistem')
@section('header_title', 'Pengaturan Instansi & Kop Balai')
@section('header_subtitle', 'Konfigurasi identitas resmi BWS Kalimantan I untuk seluruh dokumen dan surat dinas')

@push('styles')
<style>
    .settings-layout {
        display: grid;
        grid-template-columns: 280px 1fr;
        gap: 24px;
        align-items: start;
    }

    /* SIDEBAR NAV */
    .settings-nav {
        background: var(--bg-surface);
        border: 1px solid var(--border);
        border-radius: var(--radius-lg);
        overflow: hidden;
        box-shadow: var(--shadow-card);
        position: sticky;
        top: 86px;
        padding-bottom: 6px;
    }

    .settings-nav-head {
        padding: 14px 18px;
        background: var(--bg-surface);
        border-bottom: 1px solid var(--border);
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: var(--text-subtle);
    }

    .settings-nav-item {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 10px 14px;
        margin: 4px 8px;
        border-radius: var(--radius-sm);
        font-size: 13.5px;
        font-weight: 500;
        color: var(--text-muted);
        cursor: pointer;
        transition: all 0.15s ease;
        text-decoration: none;
    }

    .settings-nav-item:hover {
        background: var(--bg-subtle);
        color: var(--text-main);
    }

    .settings-nav-item.active {
        background: var(--pu-navy);
        color: #ffffff;
        font-weight: 600;
        box-shadow: 0 2px 8px rgba(19, 36, 61, 0.2);
    }

    /* CONTENT CARDS */
    .settings-card {
        background: var(--bg-surface);
        border: 1px solid var(--border);
        border-radius: var(--radius-lg);
        overflow: hidden;
        box-shadow: var(--shadow-card);
        margin-bottom: 20px;
    }

    .settings-card-head {
        padding: 16px 22px;
        background: var(--bg-surface);
        border-bottom: 1px solid var(--border);
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .settings-card-head-left {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .settings-card-icon {
        width: 36px;
        height: 36px;
        border-radius: var(--radius-sm);
        display: flex;
        align-items: center;
        justify-content: center;
        background: #eff6ff;
        color: var(--pu-navy);
        flex-shrink: 0;
    }

    .settings-card-title {
        font-size: 14.5px;
        font-weight: 700;
        color: var(--text-main);
    }

    .settings-card-desc {
        font-size: 12px;
        color: var(--text-muted);
        margin-top: 2px;
    }

    .settings-card-body {
        padding: 22px;
    }

    .form-group {
        margin-bottom: 18px;
    }

    .form-group:last-child {
        margin-bottom: 0;
    }

    .form-label {
        display: block;
        font-size: 12.5px;
        font-weight: 600;
        color: #334155;
        margin-bottom: 6px;
    }

    .form-label span.required {
        color: #dc2626;
        margin-left: 2px;
    }

    .form-control {
        width: 100%;
        padding: 9px 13px;
        border: 1px solid #cbd5e1;
        border-radius: var(--radius-sm);
        font-size: 13.5px;
        font-family: inherit;
        background: #ffffff;
        outline: none;
        transition: all 0.15s ease;
        color: var(--text-main);
    }

    .form-control:focus {
        border-color: var(--pu-blue);
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12);
    }

    .form-hint {
        font-size: 11.5px;
        color: var(--text-subtle);
        margin-top: 5px;
        line-height: 1.4;
    }

    /* KOP PREVIEW */
    .kop-preview-box {
        background: #ffffff;
        border: 1.5px solid var(--border);
        border-radius: var(--radius-md);
        padding: 20px 24px 16px;
        font-family: Arial, Helvetica, sans-serif;
        margin-bottom: 20px;
        position: relative;
        overflow: hidden;
    }

    .kop-preview-box::before {
        content: "PREVIEW KOP SURAT";
        position: absolute;
        top: 8px;
        right: 10px;
        font-size: 10px;
        font-weight: 700;
        color: #94a3b8;
        letter-spacing: 1px;
        font-family: 'Plus Jakarta Sans', sans-serif;
    }

    .kop-preview-inner {
        display: flex;
        align-items: center;
        gap: 16px;
    }

    .kop-preview-logo {
        width: 60px;
        height: 60px;
        object-fit: contain;
        flex-shrink: 0;
    }

    .kop-preview-text {
        text-align: center;
        flex: 1;
        padding-right: 20px;
    }

    .kop-preview-line1 { font-size: 11px; font-weight: bold; }
    .kop-preview-line2 { font-size: 11.5px; font-weight: bold; }
    .kop-preview-line3 { font-size: 12px; font-weight: bold; }
    .kop-preview-addr  { font-size: 8.5px; color: #374151; margin-top: 3px; }

    .kop-preview-border {
        border-bottom: 2px solid #000;
        margin-top: 8px;
    }

    /* SUBMIT BAR */
    .settings-submit-bar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        background: var(--bg-surface);
        border: 1px solid var(--border);
        border-radius: var(--radius-lg);
        padding: 14px 22px;
        box-shadow: var(--shadow-sm);
        margin-top: 4px;
    }

    .btn-save-settings {
        padding: 10px 24px;
        background: var(--pu-blue);
        color: #fff;
        border: none;
        border-radius: var(--radius-md);
        font-weight: 700;
        font-size: 13.5px;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.15s ease;
    }

    .btn-save-settings:hover {
        background: var(--pu-navy);
        transform: translateY(-1px);
        box-shadow: var(--shadow-md);
    }

    /* NOMOR SURAT CONFIG */
    .nomor-preview-chip {
        background: #f8fafc;
        border: 1px solid var(--border);
        border-radius: var(--radius-sm);
        padding: 10px 14px;
        font-family: 'JetBrains Mono', monospace;
        font-size: 14px;
        font-weight: 600;
        color: var(--pu-navy);
        margin-top: 10px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .info-box {
        background: #fffbeb;
        border: 1px solid #fde68a;
        border-radius: var(--radius-sm);
        padding: 12px 16px;
        font-size: 12.5px;
        color: #78350f;
        line-height: 1.6;
        margin-top: 16px;
        display: flex;
        align-items: flex-start;
        gap: 9px;
    }

    @media (max-width: 1024px) {
        .settings-layout { grid-template-columns: 1fr; }
        .settings-nav { position: static; }
    }
</style>
@endpush

@section('content')

<form method="POST" action="{{ route('settings.update') }}" id="settingsForm">
@csrf
@method('PUT')

<div class="settings-layout">

    {{-- LEFT: NAV --}}
    <div class="settings-nav">
        <div class="settings-nav-head">Pengaturan</div>
        <a href="#kop" class="settings-nav-item active" onclick="scrollToSection(this, '#kop')">
            <x-icon name="building" class="ui-icon-sm" /> Kop Surat & Instansi
        </a>
        <a href="#nomor" class="settings-nav-item" onclick="scrollToSection(this, '#nomor')">
            <x-icon name="hash" class="ui-icon-sm" /> Format Nomor Surat
        </a>
    </div>

    {{-- RIGHT: CONTENT --}}
    <div>

        {{-- ===== SECTION 1: KOP SURAT ===== --}}
        <div class="settings-card" id="kop">
            <div class="settings-card-head">
                <div class="settings-card-head-left">
                    <div class="settings-card-icon"><x-icon name="building" class="ui-icon-md" /></div>
                    <div>
                        <div class="settings-card-title">Kop Surat & Profil Instansi</div>
                        <div class="settings-card-desc">Data ini digunakan pada semua surat dinas yang dihasilkan sistem</div>
                    </div>
                </div>
            </div>
            <div class="settings-card-body">

                {{-- LIVE PREVIEW KOP --}}
                <div class="kop-preview-box">
                    <div class="kop-preview-inner">
                        <img src="{{ asset('images/logo_pu.png') }}" class="kop-preview-logo" alt="Logo PU">
                        <div class="kop-preview-text">
                            <div class="kop-preview-line1" id="pvKementerian">{{ $settings['kementerian'] }}</div>
                            <div class="kop-preview-line2" id="pvDitjen">{{ $settings['ditjen'] }}</div>
                            <div class="kop-preview-line3" id="pvBalai">{{ $settings['balai'] }}</div>
                            <div class="kop-preview-addr" id="pvAlamat">{{ $settings['alamat'] }}</div>
                            <div class="kop-preview-addr" id="pvKontak">{{ $settings['kontak'] }}</div>
                        </div>
                    </div>
                    <div class="kop-preview-border"></div>
                </div>

                <div class="form-group">
                    <label class="form-label">Nama Kementerian <span class="required">*</span></label>
                    <input type="text" name="instansi_kementerian" id="inKementerian"
                           class="form-control" value="{{ $settings['kementerian'] }}" required>
                    <div class="form-hint">Contoh: KEMENTERIAN PEKERJAAN UMUM</div>
                </div>

                <div class="form-group">
                    <label class="form-label">Direktorat Jenderal <span class="required">*</span></label>
                    <input type="text" name="instansi_ditjen" id="inDitjen"
                           class="form-control" value="{{ $settings['ditjen'] }}" required>
                    <div class="form-hint">Contoh: DIREKTORAT JENDERAL SUMBER DAYA AIR</div>
                </div>

                <div class="form-group">
                    <label class="form-label">Nama Balai / UPT <span class="required">*</span></label>
                    <input type="text" name="instansi_balai" id="inBalai"
                           class="form-control" value="{{ $settings['balai'] }}" required>
                    <div class="form-hint">Contoh: BALAI WILAYAH SUNGAI KALIMANTAN I PONTIANAK</div>
                </div>

                <div class="form-group">
                    <label class="form-label">Alamat Kantor <span class="required">*</span></label>
                    <input type="text" name="instansi_alamat" id="inAlamat"
                           class="form-control" value="{{ $settings['alamat'] }}" required>
                    <div class="form-hint">Contoh: Jalan Ahmad Yani, Pontianak, Kalimantan Barat 78111</div>
                </div>

                <div class="form-group">
                    <label class="form-label">Kontak (Telepon & Email) <span class="required">*</span></label>
                    <input type="text" name="instansi_kontak" id="inKontak"
                           class="form-control" value="{{ $settings['kontak'] }}" required>
                    <div class="form-hint">Contoh: Telp. (0561) 732292 | Email: bws.kalimantan1@pu.go.id</div>
                </div>
            </div>
        </div>

        {{-- ===== SECTION 2: FORMAT NOMOR SURAT ===== --}}
        <div class="settings-card" id="nomor">
            <div class="settings-card-head">
                <div class="settings-card-head-left">
                    <div class="settings-card-icon"><x-icon name="hash" class="ui-icon-md" /></div>
                    <div>
                        <div class="settings-card-title">Format & Prefix Nomor Surat</div>
                        <div class="settings-card-desc">Konfigurasi kode klasifikasi surat dinas otomatis</div>
                    </div>
                </div>
            </div>
            <div class="settings-card-body">
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">
                    <div class="form-group">
                        <label class="form-label">Prefix Surat Undangan</label>
                        <input type="text" name="prefix_undangan" class="form-control"
                               value="{{ $settings['prefix_undangan'] ?? 'UM.01.02/Bws1' }}"
                               id="inPrefixUndangan">
                        <div class="form-hint">Kode klasifikasi surat undangan</div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Prefix Suket Lupa Absen</label>
                        <input type="text" name="prefix_suket" class="form-control"
                               value="{{ $settings['prefix_suket'] ?? 'KP.07.03/Bws1' }}"
                               id="inPrefixSuket">
                        <div class="form-hint">Kode klasifikasi suket absen</div>
                    </div>
                </div>

                <div class="nomor-preview-chip">
                    <x-icon name="file-text" class="ui-icon-sm" />
                    <span id="pvNomorUndangan">{{ $settings['prefix_undangan'] ?? 'UM.01.02/Bws1' }}/001/{{ date('Y') }}</span>
                    <span style="color:#94a3b8; font-size:11px; font-weight:400; font-family:sans-serif;">← Contoh format surat undangan</span>
                </div>

                <div class="info-box">
                    <x-icon name="info" class="ui-icon-sm" style="margin-top:3px; color:#b45309;" />
                    <div>
                        <strong>Nomor Otomatis:</strong> Sistem akan menghitung urutan nomor surat secara otomatis berdasarkan jumlah surat yang telah dibuat dalam tahun berjalan. Format: <code style="background:#fef3c7; padding:1px 5px; border-radius:3px;">PREFIX/XXX/TAHUN</code>
                    </div>
                </div>
            </div>
        </div>

        {{-- SUBMIT BAR --}}
        <div class="settings-submit-bar">
            <div style="font-size:12.5px; color:var(--text-muted); display:inline-flex; align-items:center; gap:7px;">
                <x-icon name="info" class="ui-icon-sm" />
                <span>Perubahan akan langsung diterapkan ke seluruh dokumen baru yang dibuat.</span>
            </div>
            <button type="submit" class="btn-save-settings">
                <x-icon name="save" class="ui-icon-sm" /> Simpan Semua Pengaturan
            </button>
        </div>

    </div>
</div>

</form>

@endsection

@push('scripts')
<script>
    // Live preview kop surat
    const fields = {
        inKementerian: 'pvKementerian',
        inDitjen:      'pvDitjen',
        inBalai:       'pvBalai',
        inAlamat:      'pvAlamat',
        inKontak:      'pvKontak',
    };

    Object.entries(fields).forEach(([inputId, previewId]) => {
        const input = document.getElementById(inputId);
        const preview = document.getElementById(previewId);
        if (input && preview) {
            input.addEventListener('input', () => {
                preview.textContent = input.value || '—';
            });
        }
    });

    // Live preview nomor surat
    const inPrefixUndangan = document.getElementById('inPrefixUndangan');
    const pvNomorUndangan  = document.getElementById('pvNomorUndangan');
    if (inPrefixUndangan) {
        inPrefixUndangan.addEventListener('input', () => {
            pvNomorUndangan.textContent = (inPrefixUndangan.value || 'PREFIX') + '/001/{{ date("Y") }}';
        });
    }

    // Smooth scroll for settings nav
    window.scrollToSection = function(el, target) {
        document.querySelectorAll('.settings-nav-item').forEach(i => i.classList.remove('active'));
        el.classList.add('active');
        const t = document.querySelector(target);
        if (t) t.scrollIntoView({ behavior: 'smooth', block: 'start' });
    };
</script>
@endpush
