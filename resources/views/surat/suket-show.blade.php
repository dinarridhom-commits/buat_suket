@extends('layouts.app')

@section('title', 'Detail Suket Lupa Absen')
@section('header_title', 'Detail & Cetak Suket Lupa Absen')
@section('header_subtitle', 'Arsip Resmi BWS Kalimantan I Pontianak — Lembar A4 Siap Cetak')

@push('styles')
<style>
    .action-toolbar {
        display: flex; align-items: center; justify-content: space-between;
        background: var(--bg-surface); border: 1px solid var(--border); border-radius: var(--radius-lg);
        padding: 12px 18px; margin-bottom: 20px; box-shadow: var(--shadow-sm); flex-wrap: wrap; gap: 12px;
    }
    .toolbar-left  { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; }
    .toolbar-right { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }
    .toolbar-right form { display: inline-flex; margin: 0; }
    .toolbar-divider {
        width: 1px; height: 22px; background: var(--border); margin: 0 2px; flex-shrink: 0;
    }
    .btn-toolbar {
        height: 36px; padding: 0 13px; border-radius: var(--radius-sm); font-size: 12.5px; font-weight: 600;
        font-family: inherit; line-height: 1; white-space: nowrap;
        display: inline-flex; align-items: center; justify-content: center; gap: 6px; cursor: pointer;
        border: 1px solid transparent; text-decoration: none; transition: all 0.15s ease;
    }
    .btn-back   { background: #f8fafc; color: #334155; border-color: #cbd5e1; }
    .btn-back:hover { background: #f1f5f9; border-color: #94a3b8; }
    .btn-edit   { background: #f8fafc; color: #1e293b; border-color: #cbd5e1; }
    .btn-edit:hover { background: #f1f5f9; border-color: #94a3b8; }
    .btn-docx   { background: #0284c7; color: #ffffff; }
    .btn-docx:hover { background: #0369a1; }
    .btn-print  { background: var(--pu-navy); color: #ffffff; }
    .btn-print:hover { background: var(--pu-navy-light); }
    .btn-delete { background: #fff5f5; color: #dc2626; border-color: #fecaca; }
    .btn-delete:hover { background: #fee2e2; }
    .meta-chip  {
        background: #f1f5f9; color: #475569; padding: 5px 11px; border-radius: 20px;
        font-size: 12px; font-weight: 600; display: inline-flex; align-items: center; gap: 6px;
    }
    @media (max-width: 900px) {
        .toolbar-divider { display: none; }
    }

    .preview-scroll-wrapper {
        background: #cbd5e1; border-radius: var(--radius-lg); padding: 32px 24px;
        overflow-y: auto; display: flex; justify-content: center;
    }

    /* ===== A4 SHEET RESMI (SAMA DENGAN FORMAT HTML/DOCX) ===== */
    .sheet-a4 {
        width: 794px; min-height: 1123px; background: #ffffff;
        box-shadow: 0 4px 15px rgba(0,0,0,0.15);
        padding: 38px 56px 46px 56px;
        font-family: Arial, Helvetica, sans-serif; color: #000;
        font-size: 14px; line-height: 1.5;
    }
    .kop-table { width:100%; border-collapse:collapse; margin-bottom:24px; }
    .kop-table td { vertical-align:bottom; }
    .kop-logo-cell { width:92px; text-align:left; padding-right:10px; }
    .kop-logo-img  { width:82px; height:82px; object-fit:contain; display:block; }
    .kop-text-cell { text-align:center; border-bottom:2px solid #000; padding-bottom:5px; }
    .kop-line-1 { font-size:17px; font-weight:400; letter-spacing:0.2px; line-height:1.2; }
    .kop-line-2 { font-size:12px; font-weight:400; line-height:1.25; }
    .kop-line-3 { font-size:9.5px; font-weight:700; line-height:1.25; }
    .kop-line-address { font-size:8.5px; margin-top:2px; line-height:1.25; }

    .suket-title { text-align:center; margin-bottom:20px; }
    .suket-title h3 { margin:0 0 2px 0; font-size:14.5px; font-weight:700; letter-spacing:0.3px; }

    .suket-bio-table { width:100%; font-size:14px; margin:6px 0 14px 0; border-collapse:collapse; }
    .suket-bio-table td { padding:2px 0; vertical-align:top; }
    .suket-bio-table td:first-child { width:150px; }
    .suket-bio-table td:nth-child(2) { width:14px; }

    .ttd-dual-table { width:100%; margin-top:24px; font-size:14px; border-collapse:collapse; }
    .ttd-dual-table td { width:50%; vertical-align:top; text-align:center; }

    .catatan-dots { border-bottom:1.5px dotted #000; height:22px; margin-bottom:4px; }

    @page { size: A4 portrait; margin: 0; }

    @media print {
        html, body {
            background: #fff !important; margin: 0 !important; padding: 0 !important;
            -webkit-print-color-adjust: exact; print-color-adjust: exact;
        }
        .sidebar, .topbar, .subpage-header, .action-toolbar, .alert-toast { display: none !important; }
        .wrapper { margin-left: 0 !important; }
        .content { padding: 0 !important; }
        .preview-scroll-wrapper {
            background: transparent !important; padding: 0 !important; box-shadow: none !important;
            max-height: none !important; overflow: visible !important; display: block !important;
        }
        .sheet-a4 {
            box-shadow: none !important;
            padding: 10mm 17.5mm 12.5mm 10mm !important;
            width: 100% !important; min-height: auto !important; margin: 0 !important;
        }
    }
</style>
@endpush

@section('content')

@php
    $opsiSuket = [
        'terlambat'  => 'saya terlambat masuk kerja',
        'pulangawal' => 'pulang sebelum waktunya',
        'masuk'      => 'tidak mengisi daftar hadir kedatangan',
        'pulang'     => 'tidak mengisi daftar hadir kepulangan',
    ];
    $jenisLabel = [
        'masuk'      => 'Lupa Absen Masuk',
        'pulang'     => 'Lupa Absen Pulang',
        'terlambat'  => 'Terlambat Masuk',
        'pulangawal' => 'Pulang Sebelum Waktunya',
    ];
@endphp

<div class="action-toolbar">
    <div class="toolbar-left">
        <a href="{{ route('surat.suket.index') }}" class="btn-toolbar btn-back">← Kembali ke Arsip</a>
        <span class="meta-chip"><x-icon name="file-text" class="ui-icon-sm" /> {{ $suket->nomor_surat }}</span>
        <span class="meta-chip"><x-icon name="calendar" class="ui-icon-sm" /> {{ $suket->tanggal_kejadian->format('d/m/Y') }}</span>
        <span class="meta-chip" style="background:#eff6ff; color:#1d4ed8;">
            {{ $jenisLabel[$suket->jenis_absen] ?? 'Lupa Absen ' . ucfirst($suket->jenis_absen) }}
        </span>
    </div>
    <div class="toolbar-right">
        <a href="{{ route('surat.suket.edit', $suket->id) }}" class="btn-toolbar btn-edit">
            <x-icon name="pen-tool" class="ui-icon-sm" /> Edit
        </a>
        <span class="toolbar-divider" aria-hidden="true"></span>
        <button type="button" id="btnExportDocx" class="btn-toolbar btn-docx">
            <x-icon name="file-text" class="ui-icon-sm" /> Unduh Word (.docx)
        </button>
        <button type="button" onclick="window.print()" class="btn-toolbar btn-print">
            <x-icon name="printer" class="ui-icon-sm" /> Cetak / PDF
        </button>
        <span class="toolbar-divider" aria-hidden="true"></span>
        <form method="POST" action="{{ route('surat.suket.destroy', $suket->id) }}"
              onsubmit="return confirm('Pindahkan arsip suket ini ke Riwayat Penghapusan?')">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn-toolbar btn-delete">
                <x-icon name="trash" class="ui-icon-sm" /> Hapus
            </button>
        </form>
    </div>
</div>

<div class="preview-scroll-wrapper">
    <div class="sheet-a4">
        <div style="padding-left: 28px;">
            {{-- KOP SURAT RESMI --}}
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
                    </td>
                </tr>
            </table>

            {{-- JUDUL --}}
            <div class="suket-title">
                <h3>SURAT PERMOHONAN IZIN / PEMBERITAHUAN</h3>
                <div style="font-size:13.5px;">NOMOR {{ $suket->nomor_surat }}</div>
            </div>

            {{-- PEMBUKA --}}
            <div style="margin-bottom:6px;">Yang bertandatangan di bawah ini:</div>

            {{-- DATA PEGAWAI --}}
            <table class="suket-bio-table">
                <tr>
                    <td>Nama</td>
                    <td>:</td>
                    <td>{{ $suket->nama_pegawai }}</td>
                </tr>
                <tr>
                    <td>NIP</td>
                    <td>:</td>
                    <td>{{ $suket->nip_pegawai ?? '-' }}</td>
                </tr>
                <tr>
                    <td>Pangkat/Golongan</td>
                    <td>:</td>
                    <td>{{ $suket->pangkat_pegawai ?? '-' }}</td>
                </tr>
                <tr>
                    <td>Jabatan</td>
                    <td>:</td>
                    <td>{{ $suket->jabatan_pegawai }}</td>
                </tr>
            </table>

            {{-- ISI PERNYATAAN DENGAN OPSI CORET RESMI --}}
            <div style="text-align:justify; line-height:1.6; margin:14px 0 18px 0;">
                Dengan ini menerangkan bahwa pada hari {{ $suket->tanggal_kejadian->locale('id')->translatedFormat('l') }} tanggal {{ $suket->tanggal_kejadian->locale('id')->translatedFormat('d F Y') }},
                @foreach($opsiSuket as $kode => $teks)
                    @php
                        $on = $suket->jenis_absen === $kode;
                        $isLast = $loop->last;
                    @endphp
                    @if($on)
                        <span>{{ $teks }}</span>{{ $isLast ? '' : ' ' }}
                    @elseif($isLast)
                        <del>/ {{ $teks }}</del>
                    @else
                        <del>{{ $teks }} /</del>
                    @endif
                @endforeach
                (*) karena: {{ $suket->alasan }}
            </div>

            {{-- TANDA TANGAN DUAL --}}
            <table class="ttd-dual-table">
                <tr>
                    <td></td>
                    <td>
                        <div>Pontianak, {{ $suket->tanggal_kejadian->locale('id')->translatedFormat('d F Y') }}</div>
                        <div>Disetujui/Tidak disetujui (*) oleh</div>
                    </td>
                </tr>
                <tr>
                    <td style="vertical-align:bottom; padding-top:4px;">Pegawai yang bersangkutan,</td>
                    <td style="vertical-align:top; padding-top:4px;">{{ $suket->atasan_jabatan }}</td>
                </tr>
                <tr>
                    <td style="height:78px;"></td>
                    <td style="height:78px;"></td>
                </tr>
                <tr>
                    <td>
                        <div style="text-decoration:underline;">{{ $suket->nama_pegawai }}</div>
                        @if($suket->nip_pegawai)
                            <div>NIP. {{ $suket->nip_pegawai }}</div>
                        @endif
                    </td>
                    <td>
                        <div style="text-decoration:underline;">{{ $suket->atasan_nama }}</div>
                        @if($suket->atasan_nip)
                            <div>NIP. {{ $suket->atasan_nip }}</div>
                        @endif
                    </td>
                </tr>
            </table>

            {{-- CATATAN ALASAN TIDAK DISETUJUI --}}
            <div style="margin-top: 34px;">
                <div style="margin-bottom: 6px;">Catatan Alasan Tidak Disetujui:</div>
                <div class="catatan-dots"></div>
                <div class="catatan-dots"></div>
                <div class="catatan-dots"></div>
                <div style="margin-top: 14px; font-size: 13px;">(*) Coret yang tidak perlu</div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="{{ asset('js/jszip.min.js') }}"></script>
<script src="{{ asset('js/surat_constants.js') }}"></script>
<script src="{{ asset('js/docgen.js') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        document.getElementById('btnExportDocx').addEventListener('click', function() {
            const btn = this;
            const orig = btn.innerHTML;
            btn.textContent = 'Membuat .docx...';
            btn.disabled = true;

            const docData = {
                mode: 'suket',
                sk_nomor: @json($suket->nomor_surat),
                sk_nama: @json($suket->nama_pegawai),
                sk_nip: @json($suket->nip_pegawai ?? ''),
                sk_pangkat: @json($suket->pangkat_pegawai ?? ''),
                sk_jabatan: @json($suket->jabatan_pegawai),
                sk_tglAbsen: @json($suket->tanggal_kejadian->format('Y-m-d')),
                sk_tglSurat: @json($suket->tanggal_kejadian->format('Y-m-d')),
                sk_kota: 'Pontianak',
                sk_jenis: @json($suket->jenis_absen),
                sk_alasan: @json($suket->alasan),
                sk_appJab: @json($suket->atasan_jabatan),
                sk_appNama: @json($suket->atasan_nama),
                sk_appNip: @json($suket->atasan_nip ?? '')
            };

            const kopSpec = {
                line1: @json($kop['kementerian']),
                line2: @json($kop['ditjen']),
                line3: [@json($kop['balai'])],
                line4: @json($kop['alamat'] . ' | ' . $kop['kontak'])
            };

            DocGen.build(docData, {
                kop: kopSpec,
                logo: { b64: typeof LOGO !== 'undefined' ? LOGO : '' },
                arialW: typeof ARIALW !== 'undefined' ? ARIALW : null,
                JSZip: JSZip,
                outType: 'blob'
            }).then(function(blob) {
                const url = URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = @json('Suket_' . preg_replace('/[^a-zA-Z0-9_-]/', '_', $suket->nama_pegawai) . '.docx');
                document.body.appendChild(a);
                a.click();
                document.body.removeChild(a);
                URL.revokeObjectURL(url);
                btn.innerHTML = orig;
                btn.disabled = false;
            }).catch(function(err) {
                alert('Gagal ekspor Word: ' + (err.message || err));
                btn.innerHTML = orig;
                btn.disabled = false;
            });
        });
    });
</script>
@endpush
