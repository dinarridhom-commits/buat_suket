@extends('layouts.app')

@section('title', 'Detail Suket Lupa Absen')
@section('header_title', 'Detail & Cetak Suket Lupa Absen')
@section('header_subtitle', 'Arsip Resmi BWS Kalimantan I Pontianak — Lembar A4 Siap Cetak')

@push('styles')
<style>
    .action-toolbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        background: var(--bg-surface);
        border: 1px solid var(--border);
        border-radius: var(--radius-lg);
        padding: 12px 20px;
        margin-bottom: 20px;
        box-shadow: var(--shadow-sm);
        flex-wrap: wrap;
        gap: 10px;
    }
    .toolbar-left  { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; }
    .toolbar-right { display: flex; gap: 8px; flex-wrap: wrap; }
    .btn-toolbar   { padding: 7px 14px; border-radius: var(--radius-sm); font-size: 12.5px; font-weight: 600; display: inline-flex; align-items: center; gap: 6px; cursor: pointer; border: none; text-decoration: none; transition: all 0.15s ease; }
    .btn-back   { background: #f1f5f9; color: #334155; border: 1px solid #cbd5e1; }
    .btn-print  { background: var(--pu-navy); color: #fff; }
    .btn-delete { background: #fff5f5; color: #dc2626; border: 1px solid #fecaca; }
    .meta-chip  { background: #f1f5f9; color: #475569; padding: 4px 10px; border-radius: 20px; font-size: 12.5px; font-weight: 500; }

    .preview-scroll-wrapper {
        background: #cbd5e1;
        border-radius: var(--radius-lg);
        padding: 32px 24px;
        overflow-y: auto;
        display: flex;
        justify-content: center;
    }

    /* ===== A4 SHEET ===== */
    .sheet-a4 {
        width: 794px;
        min-height: 1123px;
        background: #ffffff;
        box-shadow: 0 4px 15px rgba(0,0,0,0.15);
        padding: 50px 60px;
        font-family: Arial, Helvetica, sans-serif;
        color: #000;
        font-size: 14.5px;
        line-height: 1.5;
    }
    .kop-table { width:100%; border-collapse:collapse; margin-bottom:6px; }
    .kop-table td { vertical-align:middle; }
    .kop-logo-cell { width:90px; text-align:center; }
    .kop-logo-img  { width:80px; height:auto; }
    .kop-text-cell { text-align:center; padding-right:20px; }
    .kop-line-1 { font-size:14.5px; font-weight:bold; }
    .kop-line-2 { font-size:15.5px; font-weight:bold; }
    .kop-line-3 { font-size:16px; font-weight:bold; }
    .kop-line-address { font-size:10.5px; margin-top:3px; line-height:1.3; }
    .kop-border-bottom { border-bottom:2.5px solid #000; margin-top:6px; margin-bottom:20px; }

    .suket-title { text-align:center; margin-bottom:22px; }
    .suket-title h3 { margin:0 0 4px 0; font-size:17px; font-weight:700; text-decoration:underline; letter-spacing:0.5px; }

    .data-table { width:100%; font-size:14.5px; margin:16px 0; }
    .data-table td { padding:3px 0; vertical-align:top; }
    .data-table td:first-child { width:220px; }
    .data-table td:nth-child(2) { width:16px; }

    .ttd-dual-table { width:100%; margin-top:40px; font-size:14px; }
    .ttd-dual-table td { width:50%; vertical-align:top; text-align:center; }

    @page {
        size: A4 portrait;
        margin: 0;
    }

    @media print {
        html, body {
            background: #fff !important;
            margin: 0 !important;
            padding: 0 !important;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        .sidebar, .topbar, .action-toolbar, .alert-toast { display: none !important; }
        .wrapper { margin-left: 0 !important; }
        .content { padding: 0 !important; }
        .preview-scroll-wrapper {
            background: transparent !important;
            padding: 0 !important;
            box-shadow: none !important;
            max-height: none !important;
            overflow: visible !important;
            display: block !important;
        }
        .sheet-a4 {
            box-shadow: none !important;
            padding: 15mm 20mm !important;
            width: 100% !important;
            min-height: auto !important;
            margin: 0 !important;
        }
    }
</style>
@endpush

@section('content')

<div class="action-toolbar">
    <div class="toolbar-left">
        <a href="{{ route('surat.suket.index') }}" class="btn-toolbar btn-back">← Kembali ke Arsip</a>
        <span class="meta-chip">📋 {{ $suket->nomor_surat }}</span>
        <span class="meta-chip">📅 {{ $suket->tanggal_kejadian->format('d/m/Y') }}</span>
        <span class="meta-chip" style="background:{{ $suket->jenis_absen == 'masuk' ? '#eff6ff' : '#fffbeb' }}; color:{{ $suket->jenis_absen == 'masuk' ? '#1d4ed8' : '#b45309' }};">
            Lupa Absen {{ ucfirst($suket->jenis_absen) }}
        </span>
    </div>
    <div class="toolbar-right">
        <button type="button" onclick="window.print()" class="btn-toolbar btn-print">🖨️ Cetak / PDF</button>
        <form method="POST" action="{{ route('surat.suket.destroy', $suket->id) }}"
              onsubmit="return confirm('Hapus arsip suket ini secara permanen?')">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn-toolbar btn-delete">🗑️ Hapus Arsip</button>
        </form>
    </div>
</div>

<div class="preview-scroll-wrapper">
    <div class="sheet-a4">
        {{-- KOP SURAT --}}
        <table class="kop-table">
            <tr>
                <td class="kop-logo-cell">
                    <img src="{{ asset('images/logo_pu.png') }}" alt="Logo Kementerian PU" class="kop-logo-img">
                </td>
                <td class="kop-text-cell">
                    <div class="kop-line-1">{{ $kop['kementerian'] }}</div>
                    <div class="kop-line-2">{{ $kop['ditjen'] }}</div>
                    <div class="kop-line-3">{{ $kop['balai'] }}</div>
                    <div class="kop-line-address">{{ $kop['alamat'] }}</div>
                    <div class="kop-line-address">{{ $kop['kontak'] }}</div>
                </td>
            </tr>
        </table>
        <div class="kop-border-bottom"></div>

        {{-- JUDUL --}}
        <div class="suket-title">
            <h3>SURAT KETERANGAN</h3>
            <div style="font-size:13.5px;">Nomor: {{ $suket->nomor_surat }}</div>
        </div>

        {{-- PEMBUKA --}}
        <div style="text-align:justify; margin-bottom:18px;">
            Yang bertanda tangan di bawah ini, {{ $suket->atasan_jabatan }} pada {{ $kop['balai'] }},
            dengan ini menerangkan bahwa:
        </div>

        {{-- DATA PEGAWAI --}}
        <table class="data-table" style="margin-left:36px;">
            <tr>
                <td>Nama</td>
                <td>:</td>
                <td style="font-weight:700;">{{ $suket->nama_pegawai }}</td>
            </tr>
            <tr>
                <td>NIP</td>
                <td>:</td>
                <td>{{ $suket->nip_pegawai ?? '-' }}</td>
            </tr>
            <tr>
                <td>Jabatan</td>
                <td>:</td>
                <td>{{ $suket->jabatan_pegawai }}</td>
            </tr>
            <tr>
                <td>Unit Kerja</td>
                <td>:</td>
                <td>{{ $suket->unit_kerja ?? $kop['balai'] }}</td>
            </tr>
        </table>

        {{-- ISI KETERANGAN --}}
        <div style="text-align:justify; margin:18px 0;">
            Adalah benar-benar Pegawai Negeri Sipil di lingkungan {{ $kop['balai'] }} yang pada:
        </div>

        <table class="data-table" style="margin-left:36px;">
            <tr>
                <td>Hari / Tanggal</td>
                <td>:</td>
                <td style="font-weight:600;">{{ $suket->tanggal_kejadian->locale('id')->translatedFormat('l, d F Y') }}</td>
            </tr>
            <tr>
                <td>Waktu</td>
                <td>:</td>
                <td style="font-weight:600;">Pukul {{ $suket->waktu_kejadian }} WIB</td>
            </tr>
            <tr>
                <td>Jenis Ketidakhadiran</td>
                <td>:</td>
                <td style="font-weight:600;">Lupa Absen {{ ucfirst($suket->jenis_absen) }}</td>
            </tr>
        </table>

        <div style="text-align:justify; margin:18px 0;">
            Dengan alasan: <strong>{{ $suket->alasan }}</strong>
        </div>

        @if(!empty($suket->keterangan_tambahan))
            <div style="text-align:justify; margin-bottom:18px;">{{ $suket->keterangan_tambahan }}</div>
        @endif

        <div style="text-align:justify; margin-top:18px;">
            Demikian Surat Keterangan ini dibuat dengan sebenarnya untuk dapat dipergunakan sebagaimana mestinya.
        </div>

        {{-- TANDA TANGAN DUAL --}}
        <table class="ttd-dual-table">
            <tr>
                <td>
                    <div>Pontianak, {{ $suket->tanggal_kejadian->locale('id')->translatedFormat('d F Y') }}</div>
                    <div style="margin-top:4px; font-weight:500;">Yang Bersangkutan,</div>
                    <div style="height:70px;"></div>
                    <div style="font-weight:bold; text-decoration:underline;">{{ $suket->nama_pegawai }}</div>
                    <div>NIP. {{ $suket->nip_pegawai ?? '-' }}</div>
                </td>
                <td>
                    <div>Pontianak, {{ $suket->tanggal_kejadian->locale('id')->translatedFormat('d F Y') }}</div>
                    <div style="margin-top:4px; font-weight:500;">{{ $suket->atasan_jabatan }},</div>
                    <div style="height:70px;"></div>
                    <div style="font-weight:bold; text-decoration:underline;">{{ $suket->atasan_nama }}</div>
                    <div>NIP. {{ $suket->atasan_nip ?? '-' }}</div>
                </td>
            </tr>
        </table>

        {{-- QR VERIFIKASI --}}
        <div style="margin-top:26px; display:inline-flex; align-items:center; gap:10px; border:1px dashed #94a3b8; border-radius:6px; padding:6px 10px; background:#f8fafc;">
            <div id="qrVerifySuketCanvas"></div>
            <div style="font-size:9.5px; color:#334155; line-height:1.35; max-width:200px;">
                <div style="font-weight:700; color:#13243d;">VERIFIKASI SUKET RESMI</div>
                <div>Pindai QR untuk cek keaslian arsip digital BWS Kalimantan I.</div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="{{ asset('js/qrcode.min.js') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const el = document.getElementById('qrVerifySuketCanvas');
        if (el && typeof QRCode !== 'undefined') {
            new QRCode(el, {
                text: @json($verifyUrl),
                width: 60,
                height: 60,
                colorDark: '#13243d',
                colorLight: '#ffffff',
                correctLevel: QRCode.CorrectLevel.M
            });
        }
    });
</script>
@endpush
