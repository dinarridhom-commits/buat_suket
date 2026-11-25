@extends('layouts.app')

@section('title', 'Buat Surat Undangan')
@section('header_title', 'Generator Surat Undangan Kedinasan')
@section('header_subtitle', 'Format Resmi Balai Wilayah Sungai Kalimantan I Pontianak — Multi-Lembar (Asli, Paraf, Lampiran, Konsep)')

@push('styles')
<style>
    .editor-layout {
        display: grid;
        grid-template-columns: minmax(400px, 490px) 1fr;
        gap: 22px;
        align-items: start;
    }

    .form-card {
        background: var(--bg-surface);
        border: 1px solid var(--border);
        border-radius: var(--radius-lg);
        overflow: hidden;
        margin-bottom: 16px;
        box-shadow: var(--shadow-sm);
    }

    .form-card-head {
        padding: 12px 18px;
        background: #f8fafc;
        border-bottom: 1px solid var(--border);
        font-size: 13px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: var(--pu-navy);
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .form-card-body {
        padding: 16px 18px;
    }

    .form-group {
        margin-bottom: 14px;
    }

    .form-group:last-child {
        margin-bottom: 0;
    }

    .form-label {
        display: block;
        font-size: 12.5px;
        font-weight: 600;
        color: #334155;
        margin-bottom: 5px;
    }

    .form-control {
        width: 100%;
        padding: 8px 12px;
        border: 1px solid #cbd5e1;
        border-radius: var(--radius-sm);
        font-size: 13.5px;
        font-family: inherit;
        background: #ffffff;
        outline: none;
        transition: all 0.15s ease;
    }

    .form-control:focus {
        border-color: var(--pu-blue);
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15);
    }

    .grid-2 {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 12px;
    }

    .grid-3 {
        display: grid;
        grid-template-columns: 1fr 1fr 1fr;
        gap: 8px;
    }

    /* PREVIEW CONTAINER */
    .preview-pane {
        position: sticky;
        top: 86px;
    }

    .preview-header-bar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 12px;
        background: #ffffff;
        padding: 10px 16px;
        border: 1px solid var(--border);
        border-radius: var(--radius-md);
        box-shadow: var(--shadow-sm);
        flex-wrap: wrap;
        gap: 10px;
    }

    .preview-scroll-wrapper {
        background: #cbd5e1;
        border-radius: var(--radius-lg);
        padding: 24px;
        max-height: calc(100vh - 170px);
        overflow-y: auto;
        box-shadow: inset 0 2px 4px rgba(0,0,0,0.1);
        display: flex;
        justify-content: center;
    }

    /* SHEET A4 PREVIEW */
    .sheet-a4 {
        width: 794px;
        min-height: 1123px;
        background: #ffffff;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.15);
        padding: 50px 60px;
        font-family: Arial, Helvetica, sans-serif;
        color: #000000;
        font-size: 14.5px;
        line-height: 1.45;
        position: relative;
        zoom: 0.76;
    }
    @media (min-width: 1560px) {
        .sheet-a4 { zoom: 0.92; }
    }

    .kop-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 6px;
    }

    .kop-table td {
        vertical-align: middle;
    }

    .kop-logo-cell {
        width: 90px;
        text-align: center;
    }

    .kop-logo-img {
        width: 80px;
        height: auto;
    }

    .kop-text-cell {
        text-align: center;
        padding-right: 20px;
    }

    .kop-line-1 { font-size: 14.5px; font-weight: bold; letter-spacing: 0.3px; }
    .kop-line-2 { font-size: 15.5px; font-weight: bold; letter-spacing: 0.3px; }
    .kop-line-3 { font-size: 16px; font-weight: bold; letter-spacing: 0.3px; }
    .kop-line-address { font-size: 10.5px; margin-top: 3px; line-height: 1.3; }
    .kop-border-bottom {
        border-bottom: 2.5px solid #000000;
        margin-top: 6px;
        margin-bottom: 16px;
    }

    .letter-meta-table {
        width: 100%;
        margin-bottom: 14px;
        font-size: 14.5px;
    }

    .letter-meta-table td {
        vertical-align: top;
        padding: 1.5px 0;
    }

    .undangan-tujuan-box {
        margin: 14px 0;
        font-size: 14.5px;
        line-height: 1.5;
    }

    .undangan-list {
        padding-left: 22px;
        margin-top: 4px;
    }

    .schedule-table-preview {
        width: 100%;
        border-collapse: collapse;
        margin: 14px 0;
        font-size: 13.5px;
    }

    .schedule-table-preview th, .schedule-table-preview td {
        border: 1px solid #333333;
        padding: 6px 10px;
    }

    .schedule-table-preview th {
        background: #f1f5f9;
        font-weight: 700;
        text-align: center;
    }

    .tanda-tangan-wrap {
        float: right;
        width: 320px;
        text-align: center;
        margin-top: 28px;
        font-size: 14.5px;
    }

    .qr-verify-seal {
        margin: 8px auto;
        padding: 6px;
        border: 1px dashed #94a3b8;
        border-radius: 6px;
        display: inline-flex;
        align-items: center;
        gap: 10px;
        background: #f8fafc;
        max-width: 280px;
        text-align: left;
    }

    .qr-verify-seal .qr-canvas-holder {
        flex-shrink: 0;
    }

    .qr-verify-seal .seal-desc {
        font-size: 9.5px;
        color: #475569;
        line-height: 1.25;
    }

    /* KOTAK PARAF */
    .paraf-box-table {
        border-collapse: collapse;
        border: 1.5px solid #000;
        font-size: 11px;
        width: 300px;
        margin-top: 24px;
        float: left;
    }

    .paraf-box-table th, .paraf-box-table td {
        border: 1px solid #000;
        padding: 4px 6px;
    }

    /* KONSEP TABLE */
    .konsep-table {
        width: 100%;
        border-collapse: collapse;
        border: 1.5px solid #000;
        font-size: 12px;
        margin-top: 10px;
    }

    .konsep-table td, .konsep-table th {
        border: 1px solid #000;
        padding: 8px 10px;
        vertical-align: top;
    }

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
        .sidebar, .topbar, .editor-form-col, .preview-header-bar, .alert-toast { display: none !important; }
        .wrapper { margin-left: 0 !important; }
        .content { padding: 0 !important; }
        .editor-layout { display: block !important; }
        .preview-pane { position: static !important; }
        .preview-scroll-wrapper {
            background: transparent !important;
            padding: 0 !important;
            box-shadow: none !important;
            max-height: none !important;
            overflow: visible !important;
            display: block !important;
        }
        .sheet-a4 {
            zoom: 1 !important;
            box-shadow: none !important;
            padding: 15mm 20mm !important;
            width: 100% !important;
            min-height: auto !important;
            margin: 0 !important;
        }
    }

    @media (max-width: 1200px) {
        .editor-layout { grid-template-columns: 1fr; }
        .preview-pane { position: static; }
    }
</style>
@endpush

@section('content')

<div class="editor-layout">
    <!-- LEFT PANE: FORM -->
    <div class="editor-form-col">
        <form method="POST" action="{{ route('surat.undangan.store') }}" id="formSurat">
            @csrf

            <!-- CARD 1: KEPALA SURAT -->
            <div class="form-card">
                <div class="form-card-head">
                    <span>📑</span> 1. Informasi & Penomoran Surat
                </div>
                <div class="form-card-body">
                    <div class="form-group">
                        <label class="form-label">Nomor Surat Dinas</label>
                        <input type="text" name="nomor_surat" id="inNomor" value="{{ $autoNomor ?? 'UM.01.02/Bws1/001/2026' }}" class="form-control" required>
                    </div>

                    <div class="grid-2">
                        <div class="form-group">
                            <label class="form-label">Sifat</label>
                            <input type="text" name="sifat" id="inSifat" value="Biasa / Segera" class="form-control">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Lampiran</label>
                            <input type="text" name="lampiran" id="inLampiran" value="1 (satu) Berkas" class="form-control">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Hal / Perihal</label>
                        <input type="text" name="hal" id="inHal" value="Undangan Rapat Koordinasi Teknis" class="form-control" required>
                    </div>

                    <div class="grid-2">
                        <div class="form-group">
                            <label class="form-label">Kota Surat</label>
                            <input type="text" id="inKota" value="Pontianak" class="form-control">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Tanggal Surat</label>
                            <input type="date" name="tanggal_surat" id="inTanggalSurat" value="{{ date('Y-m-d') }}" class="form-control" required>
                        </div>
                    </div>
                </div>
            </div>

            <!-- CARD 2: JADWAL & TEMPAT ACARA -->
            <div class="form-card">
                <div class="form-card-head">
                    <span>📅</span> 2. Jadwal & Tempat Acara
                </div>
                <div class="form-card-body">
                    <div class="form-group">
                        <label class="form-label">Hari / Tanggal Acara</label>
                        <input type="text" name="hari_tanggal_acara" id="inHariTanggalAcara" value="Senin / 30 November 2026" class="form-control">
                    </div>

                    <div class="grid-2">
                        <div class="form-group">
                            <label class="form-label">Waktu Pelaksanaan</label>
                            <input type="text" name="waktu_acara" id="inWaktuAcara" value="09.00 WIB s.d Selesai" class="form-control">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Tempat Pertemuan</label>
                            <input type="text" name="tempat" id="inTempat" value="Ruang Rapat BWS Kalimantan I Pontianak" class="form-control" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Agenda / Pembahasan Pokok</label>
                        <textarea name="agenda" id="inAgenda" rows="2" class="form-control">Rapat Pembahasan Evaluasi Kinerja Triwulan Program Pengelolaan SDA</textarea>
                    </div>
                </div>
            </div>

            <!-- CARD 3: MULTI-SESSION AGENDA BUILDER -->
            <div class="form-card">
                <div class="form-card-head" style="justify-content: space-between;">
                    <div style="display:flex; align-items:center; gap:8px;">
                        <span>📋</span> 3. Susunan Acara / Sesi Rapat (Opsional)
                    </div>
                    <button type="button" id="btnAddScheduleRow" style="background:#e2e8f0; border:none; padding:3px 10px; border-radius:4px; font-size:11px; font-weight:700; cursor:pointer;">+ Tambah Sesi</button>
                </div>
                <div class="form-card-body">
                    <div style="margin-bottom: 10px; font-size:12px; color:#64748b;">
                        Tambahkan rincian sesi jadwal bila undangan memiliki susunan acara terjadwal.
                    </div>
                    <div id="scheduleRowsContainer">
                        <div class="grid-3 schedule-row" style="margin-bottom:8px;">
                            <input type="text" class="form-control sc-waktu" value="09.00 - 09.30" placeholder="Waktu (e.g. 09.00-09.30)">
                            <input type="text" class="form-control sc-acara" value="Pembukaan & Arahan Kepala Balai" placeholder="Kegiatan / Acara">
                            <div style="display:flex; gap:4px;">
                                <input type="text" class="form-control sc-pic" value="Plt. Kepala Balai" placeholder="Narasumber / PIC">
                                <button type="button" style="border:1px solid #fca5a5; background:#fff1f2; color:#b91c1c; border-radius:4px; padding:0 8px; cursor:pointer;" onclick="removeScheduleRow(this)">✕</button>
                            </div>
                        </div>
                        <div class="grid-3 schedule-row" style="margin-bottom:8px;">
                            <input type="text" class="form-control sc-waktu" value="09.30 - 11.30" placeholder="Waktu">
                            <input type="text" class="form-control sc-acara" value="Pemaparan Progres Fisik & Keuangan" placeholder="Kegiatan / Acara">
                            <div style="display:flex; gap:4px;">
                                <input type="text" class="form-control sc-pic" value="Para PPK & Satker" placeholder="Narasumber / PIC">
                                <button type="button" style="border:1px solid #fca5a5; background:#fff1f2; color:#b91c1c; border-radius:4px; padding:0 8px; cursor:pointer;" onclick="removeScheduleRow(this)">✕</button>
                            </div>
                        </div>
                    </div>
                    <label style="font-size:12px; color:#475569; display:flex; align-items:center; gap:6px; margin-top:8px; cursor:pointer;">
                        <input type="checkbox" id="chkShowSchedule" checked> Tampilkan tabel susunan acara ini pada lembar surat
                    </label>
                </div>
            </div>

            <!-- CARD 4: DAFTAR TUJUAN PENERIMA -->
            <div class="form-card">
                <div class="form-card-head" style="justify-content: space-between;">
                    <div style="display:flex; align-items:center; gap:8px;">
                        <span>👥</span> 4. Daftar Penerima Undangan
                    </div>
                    <button type="button" id="btnAddPenerima" style="background:#e2e8f0; border:none; padding:3px 10px; border-radius:4px; font-size:11px; font-weight:700; cursor:pointer;">+ Tambah Penerima</button>
                </div>
                <div class="form-card-body">
                    <div id="penerimaContainer">
                        <div class="form-group" style="display:flex; gap:6px;">
                            <input type="text" name="penerimas[]" class="form-control in-penerima" value="Para Pejabat Administrator BWS Kalimantan I">
                            <button type="button" style="border:1px solid #fca5a5; background:#fff1f2; color:#b91c1c; border-radius:6px; padding:0 10px; cursor:pointer;" onclick="this.parentElement.remove(); syncPenerimaList();">✕</button>
                        </div>
                        <div class="form-group" style="display:flex; gap:6px;">
                            <input type="text" name="penerimas[]" class="form-control in-penerima" value="Para Kepala Satuan Kerja di Lingkungan BWS Kalimantan I">
                            <button type="button" style="border:1px solid #fca5a5; background:#fff1f2; color:#b91c1c; border-radius:6px; padding:0 10px; cursor:pointer;" onclick="this.parentElement.remove(); syncPenerimaList();">✕</button>
                        </div>
                        <div class="form-group" style="display:flex; gap:6px;">
                            <input type="text" name="penerimas[]" class="form-control in-penerima" value="Para Pejabat Pembuat Komitmen (PPK) di Lingkungan BWS Kalimantan I">
                            <button type="button" style="border:1px solid #fca5a5; background:#fff1f2; color:#b91c1c; border-radius:6px; padding:0 10px; cursor:pointer;" onclick="this.parentElement.remove(); syncPenerimaList();">✕</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- CARD 5: PENANDA TANGAN & PARAF HIERARKI -->
            <div class="form-card">
                <div class="form-card-head">
                    <span>✍️</span> 5. Penanda Tangan & Paraf Hierarki
                </div>
                <div class="form-card-body">
                    <div class="form-group">
                        <label class="form-label">Pilih dari Master Pegawai (148 Pegawai BWS Kalimantan I)</label>
                        <select id="selectPegawaiDb" class="form-control" style="background:#f1f5f9; font-weight:600;">
                            <option value="">-- Pilih Pejabat Penandatangan --</option>
                            @foreach($pegawais as $p)
                                <option value="{{ $p->id }}" 
                                    data-nama="{{ $p->nama_gelar }}" 
                                    data-nip="{{ $p->nip }}" 
                                    data-jabatan="{{ $p->jabatan_definitif ?? $p->jabatan_struktural }}"
                                    {{ str_contains($p->nama, 'Donni') ? 'selected' : '' }}>
                                    {{ $p->nama_gelar }} — {{ $p->jabatan_definitif ?? $p->jabatan_struktural }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Jabatan Penandatangan</label>
                        <input type="text" name="penandatangan_jabatan" id="inTtdJabatan" value="Plt. Kepala Balai Wilayah Sungai Kalimantan I Pontianak" class="form-control" required>
                    </div>

                    <div class="grid-2">
                        <div class="form-group">
                            <label class="form-label">Nama Lengkap & Gelar</label>
                            <input type="text" name="penandatangan_nama" id="inTtdNama" value="Donni Saputra, S.T, M.Si., M.T." class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">NIP</label>
                            <input type="text" name="penandatangan_nip" id="inTtdNip" value="197611012005021003" class="form-control">
                        </div>
                    </div>

                    <div style="margin-top:10px; padding:10px; background:#f0fdf4; border:1px solid #bbf7d0; border-radius:6px;">
                        <label style="font-size:12.5px; font-weight:600; color:#166534; display:flex; align-items:center; gap:8px; cursor:pointer;">
                            <input type="checkbox" id="chkQrStamp" checked>
                            <span>Sertakan Stempel Digital & QR Code e-Sign BWS Kalimantan I</span>
                        </label>
                    </div>

                    <div class="grid-2" style="margin-top: 14px;">
                        <div class="form-group">
                            <label class="form-label">Paraf 1 (Kasubbag TU)</label>
                            <input type="text" id="inParaf1" value="Iswandi, S.E., S.T." class="form-control">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Paraf 2 (Kasi / Penelaah)</label>
                            <input type="text" id="inParaf2" value="Kepala Seksi Terkait" class="form-control">
                        </div>
                    </div>
                </div>
            </div>

            <!-- SUBMIT BUTTONS -->
            <div style="display:flex; gap:12px; margin-bottom: 24px;">
                <button type="submit" class="btn-quick-create" style="flex:1; justify-content:center; padding:12px; font-size:14px;">
                    💾 Simpan ke Database Arsip
                </button>
            </div>
        </form>
    </div>

    <!-- RIGHT PANE: LIVE A4 PAPER PREVIEW -->
    <div class="preview-pane">
        <div class="preview-header-bar">
            <!-- PAGE SWITCHER -->
            <div style="display:flex; align-items:center; gap:8px;">
                <span style="font-size:12.5px; font-weight:700; color:var(--pu-navy);">Lembar:</span>
                <select id="selPreviewPage" style="padding:6px 10px; border-radius:6px; border:1px solid #cbd5e1; font-size:12.5px; font-weight:600; background:#f8fafc; color:#1e293b; outline:none; cursor:pointer;">
                    <option value="asli">📄 Lembar 1: Asli Surat Undangan</option>
                    <option value="paraf">✍️ Lembar 2: Lembar Paraf Koordinasi</option>
                    <option value="lampiran">📑 Lembar 3: Lampiran I (Daftar Penerima)</option>
                    <option value="konsep">📋 Lembar 4: Konsep Naskah Dinas</option>
                </select>
            </div>

            <div style="display:flex; gap:8px;">
                <button type="button" id="btnExportDocx" class="btn-action" style="background:#0284c7; color:#ffffff; font-weight:600; padding:6px 12px; font-size:12.5px; display:flex; align-items:center; gap:5px; border-radius:6px; border:none; cursor:pointer;">
                    <span>📄</span> Unduh Word (.docx)
                </button>
                <button type="button" onclick="window.print()" class="btn-action" style="background:var(--pu-navy); color:#ffffff; font-weight:600; padding:6px 12px; font-size:12.5px; display:flex; align-items:center; gap:5px; border-radius:6px; border:none; cursor:pointer;">
                    <span>🖨️</span> Cetak / PDF
                </button>
            </div>
        </div>

        <div class="preview-scroll-wrapper">
            <!-- 1. VIEW LEMBAR ASLI & PARAF -->
            <div class="sheet-a4" id="viewSheetAsli">
                <!-- KOP SURAT RESMI -->
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

                <!-- SURAT META (NOMOR & TANGGAL) -->
                <table class="letter-meta-table">
                    <tr>
                        <td style="width: 80px;">Nomor</td>
                        <td style="width: 15px;">:</td>
                        <td id="pvNomor">UM.01.02/Bws1/      /2026</td>
                        <td style="text-align: right;" id="pvTanggal">Pontianak, {{ now()->locale('id')->translatedFormat('d F Y') }}</td>
                    </tr>
                    <tr>
                        <td>Sifat</td>
                        <td>:</td>
                        <td id="pvSifat">Biasa / Segera</td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>Lampiran</td>
                        <td>:</td>
                        <td id="pvLampiran">1 (satu) Berkas</td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>Hal</td>
                        <td>:</td>
                        <td id="pvHal" style="font-weight: bold;">Undangan Rapat Koordinasi Teknis</td>
                        <td></td>
                    </tr>
                </table>

                <!-- TUJUAN PENERIMA -->
                <div class="undangan-tujuan-box">
                    <div>Yth.</div>
                    <ol class="undangan-list" id="pvListPenerima">
                        <li>Para Pejabat Administrator BWS Kalimantan I</li>
                        <li>Para Kepala Satker di Lingkungan BWS Kalimantan I</li>
                        <li>Para PPK di Lingkungan BWS Kalimantan I</li>
                    </ol>
                    <div style="margin-top: 4px;">di -</div>
                    <div style="padding-left: 20px; font-weight: bold;">Tempat</div>
                </div>

                <!-- ISI SURAT -->
                <div style="text-align: justify; text-indent: 36px; margin-top: 14px;">
                    Sehubungan dengan pelaksanaan program kegiatan pada Balai Wilayah Sungai Kalimantan I Pontianak, dengan ini kami mengharapkan kehadiran Bapak/Ibu pada rapat yang akan dilaksanakan pada:
                </div>

                <table style="width: 100%; margin: 12px 0 12px 36px; font-size: 14.5px;">
                    <tr>
                        <td style="width: 130px; padding: 2px 0;">Hari / Tanggal</td>
                        <td style="width: 15px;">:</td>
                        <td id="pvHariTanggal" style="font-weight: 500;">Senin / 30 November 2026</td>
                    </tr>
                    <tr>
                        <td style="padding: 2px 0;">Waktu</td>
                        <td>:</td>
                        <td id="pvWaktu">09.00 WIB s.d Selesai</td>
                    </tr>
                    <tr>
                        <td style="padding: 2px 0;">Tempat</td>
                        <td>:</td>
                        <td id="pvTempat">Ruang Rapat BWS Kalimantan I Pontianak</td>
                    </tr>
                    <tr>
                        <td style="padding: 2px 0;">Agenda</td>
                        <td>:</td>
                        <td id="pvAgenda" style="font-weight: bold;">Rapat Pembahasan Evaluasi Kinerja Triwulan Program Pengelolaan SDA</td>
                    </tr>
                </table>

                <!-- PREVIEW SUSUNAN ACARA / TABEL JADWAL -->
                <div id="pvScheduleBox" style="margin: 12px 0;">
                    <table class="schedule-table-preview" id="pvScheduleTable">
                        <thead>
                            <tr>
                                <th style="width:140px;">Waktu</th>
                                <th>Agenda / Acara</th>
                                <th style="width:180px;">Narasumber / PIC</th>
                            </tr>
                        </thead>
                        <tbody id="pvScheduleTbody">
                            <tr>
                                <td style="text-align:center;">09.00 - 09.30</td>
                                <td>Pembukaan & Arahan Kepala Balai</td>
                                <td>Plt. Kepala Balai</td>
                            </tr>
                            <tr>
                                <td style="text-align:center;">09.30 - 11.30</td>
                                <td>Pemaparan Progres Fisik & Keuangan</td>
                                <td>Para PPK & Satker</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div style="text-align: justify; text-indent: 36px; margin-top: 14px;">
                    Mengingat pentingnya acara tersebut di atas, diharapkan hadir tepat pada waktunya. Demikian kami sampaikan, atas perhatian dan kehadirannya diucapkan terima kasih.
                </div>

                <!-- LEMBAR PARAF BOX (HANYA MUNCUL DI MODE PARAF) -->
                <div id="pvParafHierarkiBox" style="display:none; clear:both; padding-top:20px;">
                    <table class="paraf-box-table">
                        <tr>
                            <th colspan="2" style="background:#f1f5f9; text-align:center; font-weight:bold;">PARAF HIERARKI KOORDINASI</th>
                        </tr>
                        <tr>
                            <td style="width:160px; font-weight:600;">Kasubbag Umum & TU</td>
                            <td style="height:32px; text-align:center; color:#64748b; font-size:10px;" id="pvParafVal1">Iswandi, S.E., S.T.</td>
                        </tr>
                        <tr>
                            <td style="font-weight:600;">Kasi / Penelaah Teknis</td>
                            <td style="height:32px; text-align:center; color:#64748b; font-size:10px;" id="pvParafVal2">Kepala Seksi Terkait</td>
                        </tr>
                        <tr>
                            <td style="font-weight:600;">Konseptor Naskah</td>
                            <td style="height:32px; text-align:center; color:#64748b; font-size:10px;">Staf Pelaksana TU</td>
                        </tr>
                    </table>
                </div>

                <!-- TANDA TANGAN & VERIFIKASI QR -->
                <div class="tanda-tangan-wrap">
                    <div id="pvTtdJabatan">Plt. Kepala Balai Wilayah Sungai Kalimantan I Pontianak</div>
                    
                    <!-- DYNAMIC QR VERIFICATION STAMP -->
                    <div id="pvQrSealWrapper" style="padding: 10px 0;">
                        <div class="qr-verify-seal">
                            <div class="qr-canvas-holder" id="qrCanvasContainer"></div>
                            <div class="seal-desc">
                                <b style="color:var(--pu-navy);">KEMENTERIAN PU</b><br>
                                Ditjen Sumber Daya Air<br>
                                BWS Kalimantan I<br>
                                <span style="color:#0284c7; font-weight:600;">Ditandatangani Elektronik</span>
                            </div>
                        </div>
                    </div>

                    <div id="pvTtdNama" style="font-weight: bold; text-decoration: underline;">Donni Saputra, S.T, M.Si., M.T.</div>
                    <div id="pvTtdNip">NIP. 197611012005021003</div>
                </div>
                <div style="clear: both;"></div>
            </div>

            <!-- 2. VIEW LEMBAR LAMPIRAN I -->
            <div class="sheet-a4" id="viewSheetLampiran" style="display:none;">
                <div style="margin-left: 50%; font-size: 13.5px; line-height: 1.4; margin-bottom: 24px;">
                    <div>Lampiran I Surat Plt. Kepala BWS Kalimantan I</div>
                    <div>Nomor : <span id="pvLpNomor">UM.01.02/Bws1/      /2026</span></div>
                    <div>Tanggal : <span id="pvLpTanggal">Pontianak, {{ now()->locale('id')->translatedFormat('d F Y') }}</span></div>
                    <div>Hal : <span id="pvLpHal">Undangan Rapat Koordinasi Teknis</span></div>
                </div>

                <div style="text-align: center; font-weight: bold; font-size: 16px; margin: 30px 0 20px 0; letter-spacing: 0.5px; text-decoration: underline;">
                    DAFTAR PENERIMA UNDANGAN
                </div>

                <ol id="pvLpListContainer" style="line-height: 1.8; font-size: 14.5px; padding-left: 28px;">
                    <!-- Synchronized dynamically -->
                </ol>

                <div class="tanda-tangan-wrap" style="margin-top: 60px;">
                    <div id="pvLpTtdJabatan">Plt. Kepala Balai Wilayah Sungai Kalimantan I Pontianak</div>
                    <div style="height: 75px;"></div>
                    <div id="pvLpTtdNama" style="font-weight: bold; text-decoration: underline;">Donni Saputra, S.T, M.Si., M.T.</div>
                    <div id="pvLpTtdNip">NIP. 197611012005021003</div>
                </div>
                <div style="clear:both;"></div>
            </div>

            <!-- 3. VIEW LEMBAR KONSEP NASKAH DINAS -->
            <div class="sheet-a4" id="viewSheetKonsep" style="display:none;">
                <!-- KOP SURAT KECIL -->
                <table class="kop-table">
                    <tr>
                        <td class="kop-logo-cell" style="width:70px;">
                            <img src="{{ asset('images/logo_pu.png') }}" alt="Logo Kementerian PU" style="width:65px;">
                        </td>
                        <td class="kop-text-cell">
                            <div style="font-size:13px; font-weight:bold;">KEMENTERIAN PEKERJAAN UMUM</div>
                            <div style="font-size:14px; font-weight:bold;">DIREKTORAT JENDERAL SUMBER DAYA AIR</div>
                            <div style="font-size:14.5px; font-weight:bold;">BALAI WILAYAH SUNGAI KALIMANTAN I PONTIANAK</div>
                        </td>
                    </tr>
                </table>
                <div style="border-bottom: 2px solid #000; margin: 4px 0 16px 0;"></div>

                <div style="text-align: center; font-weight: bold; font-size: 15px; margin-bottom: 12px; letter-spacing: 0.5px;">
                    LEMBAR KONSEP NASKAH DINAS
                </div>

                <table class="konsep-table">
                    <tr>
                        <td style="width: 50%;">
                            <b>Nomor Konsep / Klasifikasi:</b><br>
                            <span id="pvKpNomor">UM.01.02/Bws1/      /2026</span>
                        </td>
                        <td style="width: 50%;">
                            <b>Tanggal Konsep Dibuat:</b><br>
                            <span>{{ now()->locale('id')->translatedFormat('d F Y') }}</span>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <b>Perihal Naskah:</b><br>
                            <span id="pvKpHal" style="font-weight: 600;">Undangan Rapat Koordinasi Teknis</span>
                        </td>
                    </tr>
                    <tr>
                        <td style="height: 110px;">
                            <b>Konseptor / Penata Redaksi:</b><br><br>
                            Nama: Staf Pelaksana Subbag TU<br>
                            Paraf & Tanggal: ...................................
                        </td>
                        <td style="height: 110px;">
                            <b>Pemeriksa Awal (Kasi / Penelaah):</b><br><br>
                            Nama: Kepala Seksi Terkait<br>
                            Paraf & Tanggal: ...................................
                        </td>
                    </tr>
                    <tr>
                        <td style="height: 110px;">
                            <b>Pemeriksa Akhir (Kasubbag Umum & TU):</b><br><br>
                            Nama: Iswandi, S.E., S.T.<br>
                            Paraf & Tanggal: ...................................
                        </td>
                        <td style="height: 110px;">
                            <b>Ditetapkan / Disetujui Oleh:</b><br><br>
                            Nama: Donni Saputra, S.T, M.Si., M.T.<br>
                            Tanda Tangan & Tanggal: ...................................
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" style="height: 120px;">
                            <b>Catatan Revisi / Disposisi Pimpinan:</b><br>
                            <div style="color: #64748b; font-size: 11px; margin-top: 6px;">
                                [ ] Setuju cetak bersih & penandatanganan elektronik<br>
                                [ ] Perbaiki susunan jadwal acara<br>
                                [ ] Sesuaikan daftar instansi penerima undangan
                            </div>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<!-- Core Word (.docx) & QR Engine Assets -->
<script src="{{ asset('js/jszip.min.js') }}"></script>
<script src="{{ asset('js/surat_constants.js') }}"></script>
<script src="{{ asset('js/docgen.js') }}"></script>
<script src="{{ asset('js/qrcode.min.js') }}"></script>

<script>
    // Live synchronization between form inputs and A4 preview
    const inNomor = document.getElementById('inNomor');
    const inSifat = document.getElementById('inSifat');
    const inLampiran = document.getElementById('inLampiran');
    const inHal = document.getElementById('inHal');
    const inKota = document.getElementById('inKota');
    const inTanggalSurat = document.getElementById('inTanggalSurat');
    const inHariTanggal = document.getElementById('inHariTanggalAcara');
    const inWaktu = document.getElementById('inWaktuAcara');
    const inTempat = document.getElementById('inTempat');
    const inAgenda = document.getElementById('inAgenda');
    const inTtdNama = document.getElementById('inTtdNama');
    const inTtdNip = document.getElementById('inTtdNip');
    const inTtdJabatan = document.getElementById('inTtdJabatan');
    const inParaf1 = document.getElementById('inParaf1');
    const inParaf2 = document.getElementById('inParaf2');
    const selectPegawai = document.getElementById('selectPegawaiDb');
    const chkShowSchedule = document.getElementById('chkShowSchedule');
    const chkQrStamp = document.getElementById('chkQrStamp');
    const selPreviewPage = document.getElementById('selPreviewPage');

    function updateTanggalPreview() {
        const d = inTanggalSurat.value ? new Date(inTanggalSurat.value) : new Date();
        const months = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
        const formatted = d.getDate() + ' ' + months[d.getMonth()] + ' ' + d.getFullYear();
        const fullDate = (inKota.value ? inKota.value + ', ' : '') + formatted;
        document.getElementById('pvTanggal').innerText = fullDate;
        document.getElementById('pvLpTanggal').innerText = fullDate;
        renderQrSeal();
    }

    inNomor.addEventListener('input', () => {
        document.getElementById('pvNomor').innerText = inNomor.value;
        document.getElementById('pvLpNomor').innerText = inNomor.value;
        document.getElementById('pvKpNomor').innerText = inNomor.value;
        renderQrSeal();
    });
    inSifat.addEventListener('input', () => document.getElementById('pvSifat').innerText = inSifat.value);
    inLampiran.addEventListener('input', () => document.getElementById('pvLampiran').innerText = inLampiran.value);
    inHal.addEventListener('input', () => {
        document.getElementById('pvHal').innerText = inHal.value;
        document.getElementById('pvLpHal').innerText = inHal.value;
        document.getElementById('pvKpHal').innerText = inHal.value;
    });
    inKota.addEventListener('input', updateTanggalPreview);
    inTanggalSurat.addEventListener('input', updateTanggalPreview);
    inHariTanggal.addEventListener('input', () => document.getElementById('pvHariTanggal').innerText = inHariTanggal.value);
    inWaktu.addEventListener('input', () => document.getElementById('pvWaktu').innerText = inWaktu.value);
    inTempat.addEventListener('input', () => document.getElementById('pvTempat').innerText = inTempat.value);
    inAgenda.addEventListener('input', () => document.getElementById('pvAgenda').innerText = inAgenda.value);
    inTtdNama.addEventListener('input', () => {
        document.getElementById('pvTtdNama').innerText = inTtdNama.value;
        document.getElementById('pvLpTtdNama').innerText = inTtdNama.value;
        renderQrSeal();
    });
    inTtdNip.addEventListener('input', () => {
        const nipVal = inTtdNip.value ? 'NIP. ' + inTtdNip.value : '';
        document.getElementById('pvTtdNip').innerText = nipVal;
        document.getElementById('pvLpTtdNip').innerText = nipVal;
    });
    inTtdJabatan.addEventListener('input', () => {
        document.getElementById('pvTtdJabatan').innerText = inTtdJabatan.value;
        document.getElementById('pvLpTtdJabatan').innerText = inTtdJabatan.value;
    });

    inParaf1.addEventListener('input', () => document.getElementById('pvParafVal1').innerText = inParaf1.value);
    inParaf2.addEventListener('input', () => document.getElementById('pvParafVal2').innerText = inParaf2.value);

    // Page switcher logic
    selPreviewPage.addEventListener('change', function() {
        const val = this.value;
        const sheetAsli = document.getElementById('viewSheetAsli');
        const sheetLampiran = document.getElementById('viewSheetLampiran');
        const sheetKonsep = document.getElementById('viewSheetKonsep');
        const parafBox = document.getElementById('pvParafHierarkiBox');

        if (val === 'asli') {
            sheetAsli.style.display = 'block';
            sheetLampiran.style.display = 'none';
            sheetKonsep.style.display = 'none';
            parafBox.style.display = 'none';
        } else if (val === 'paraf') {
            sheetAsli.style.display = 'block';
            sheetLampiran.style.display = 'none';
            sheetKonsep.style.display = 'none';
            parafBox.style.display = 'block';
        } else if (val === 'lampiran') {
            sheetAsli.style.display = 'none';
            sheetLampiran.style.display = 'block';
            sheetKonsep.style.display = 'none';
        } else if (val === 'konsep') {
            sheetAsli.style.display = 'none';
            sheetLampiran.style.display = 'none';
            sheetKonsep.style.display = 'block';
        }
    });

    // Auto-fill from DB dropdown
    selectPegawai.addEventListener('change', function() {
        const opt = this.options[this.selectedIndex];
        if (opt.value) {
            inTtdNama.value = opt.getAttribute('data-nama');
            inTtdNip.value = opt.getAttribute('data-nip') || '';
            inTtdJabatan.value = opt.getAttribute('data-jabatan');
            
            document.getElementById('pvTtdNama').innerText = inTtdNama.value;
            document.getElementById('pvLpTtdNama').innerText = inTtdNama.value;
            document.getElementById('pvTtdNip').innerText = inTtdNip.value ? 'NIP. ' + inTtdNip.value : '';
            document.getElementById('pvLpTtdNip').innerText = inTtdNip.value ? 'NIP. ' + inTtdNip.value : '';
            document.getElementById('pvTtdJabatan').innerText = inTtdJabatan.value;
            document.getElementById('pvLpTtdJabatan').innerText = inTtdJabatan.value;
            renderQrSeal();
        }
    });

    // Dynamic recipients sync
    function syncPenerimaList() {
        const inputs = document.querySelectorAll('.in-penerima');
        const pvList = document.getElementById('pvListPenerima');
        const pvLpList = document.getElementById('pvLpListContainer');
        pvList.innerHTML = '';
        pvLpList.innerHTML = '';
        inputs.forEach(inp => {
            if (inp.value.trim()) {
                const li = document.createElement('li');
                li.innerText = inp.value.trim();
                pvList.appendChild(li);

                const li2 = document.createElement('li');
                li2.innerText = inp.value.trim();
                pvLpList.appendChild(li2);
            }
        });
    }

    document.querySelectorAll('.in-penerima').forEach(inp => {
        inp.addEventListener('input', syncPenerimaList);
    });

    document.getElementById('btnAddPenerima').addEventListener('click', function() {
        const container = document.getElementById('penerimaContainer');
        const div = document.createElement('div');
        div.className = 'form-group';
        div.style.display = 'flex';
        div.style.gap = '6px';
        div.innerHTML = `
            <input type="text" name="penerimas[]" class="form-control in-penerima" placeholder="Nama instansi / jabatan penerima...">
            <button type="button" style="border:1px solid #fca5a5; background:#fff1f2; color:#b91c1c; border-radius:6px; padding:0 10px; cursor:pointer;" onclick="this.parentElement.remove(); syncPenerimaList();">✕</button>
        `;
        container.appendChild(div);
        div.querySelector('input').addEventListener('input', syncPenerimaList);
    });

    // Multi-session Schedule Builder
    function syncSchedulePreview() {
        const rows = document.querySelectorAll('.schedule-row');
        const tbody = document.getElementById('pvScheduleTbody');
        tbody.innerHTML = '';
        let hasData = false;
        rows.forEach(r => {
            const w = r.querySelector('.sc-waktu').value.trim();
            const a = r.querySelector('.sc-acara').value.trim();
            const p = r.querySelector('.sc-pic').value.trim();
            if (w || a || p) {
                hasData = true;
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td style="text-align:center;">${w}</td>
                    <td>${a}</td>
                    <td>${p}</td>
                `;
                tbody.appendChild(tr);
            }
        });

        document.getElementById('pvScheduleBox').style.display = (chkShowSchedule.checked && hasData) ? 'block' : 'none';
    }

    window.removeScheduleRow = function(btn) {
        btn.closest('.schedule-row').remove();
        syncSchedulePreview();
    };

    document.getElementById('btnAddScheduleRow').addEventListener('click', function() {
        const container = document.getElementById('scheduleRowsContainer');
        const div = document.createElement('div');
        div.className = 'grid-3 schedule-row';
        div.style.marginBottom = '8px';
        div.innerHTML = `
            <input type="text" class="form-control sc-waktu" placeholder="Waktu (e.g. 11.30-12.00)">
            <input type="text" class="form-control sc-acara" placeholder="Kegiatan / Acara">
            <div style="display:flex; gap:4px;">
                <input type="text" class="form-control sc-pic" placeholder="Narasumber / PIC">
                <button type="button" style="border:1px solid #fca5a5; background:#fff1f2; color:#b91c1c; border-radius:4px; padding:0 8px; cursor:pointer;" onclick="removeScheduleRow(this)">✕</button>
            </div>
        `;
        container.appendChild(div);
        div.querySelectorAll('input').forEach(inp => inp.addEventListener('input', syncSchedulePreview));
    });

    document.querySelectorAll('.schedule-row input').forEach(inp => inp.addEventListener('input', syncSchedulePreview));
    chkShowSchedule.addEventListener('change', syncSchedulePreview);

    // QR Verification Seal
    function renderQrSeal() {
        const wrapper = document.getElementById('pvQrSealWrapper');
        if (!chkQrStamp.checked) {
            wrapper.style.display = 'none';
            return;
        }
        wrapper.style.display = 'block';
        const container = document.getElementById('qrCanvasContainer');
        container.innerHTML = '';
        
        const qrText = 'VERIFIKASI SURAT DINAS BWS KALIMANTAN I\n' +
                       'No: ' + inNomor.value + '\n' +
                       'Hal: ' + inHal.value + '\n' +
                       'Tgl: ' + inTanggalSurat.value + '\n' +
                       'Penandatangan: ' + inTtdNama.value + '\n' +
                       'Otorisasi: Kementerian PUPR RI';
        
        if (typeof QRCode !== 'undefined') {
            new QRCode(container, {
                text: qrText,
                width: 60,
                height: 60,
                colorDark: '#0f172a',
                colorLight: '#ffffff',
                correctLevel: QRCode.CorrectLevel.M
            });
        }
    }

    chkQrStamp.addEventListener('change', renderQrSeal);
    renderQrSeal();
    syncPenerimaList();

    // Export DOCX (.docx) via DocGen & JSZip
    document.getElementById('btnExportDocx').addEventListener('click', function() {
        const btn = this;
        const originalText = btn.innerHTML;
        btn.innerHTML = '<span>⏳</span> Membuat .docx...';
        btn.disabled = true;

        try {
            const penerimas = [];
            document.querySelectorAll('.in-penerima').forEach(inp => {
                if (inp.value.trim()) penerimas.push(inp.value.trim());
            });

            const docData = {
                mode: 'undangan',
                nomor: inNomor.value,
                sifat: inSifat.value,
                lampiran: inLampiran.value,
                hal: inHal.value,
                kota: inKota.value,
                tanggal: inTanggalSurat.value,
                ythMode: 'langsung',
                ythText: penerimas.join('\n'),
                ttd_jab: inTtdJabatan.value,
                ttd_nama: inTtdNama.value,
                ttd_nip: inTtdNip.value,
                body: [
                    {
                        type: 'para',
                        indent: true,
                        text: 'Sehubungan dengan pelaksanaan program kegiatan pada Balai Wilayah Sungai Kalimantan I Pontianak, dengan ini kami mengharapkan kehadiran Bapak/Ibu pada rapat yang akan dilaksanakan pada:'
                    },
                    {
                        type: 'jadwal',
                        hari: inHariTanggal.value,
                        waktu: inWaktu.value,
                        tempat: inTempat.value,
                        agenda: inAgenda.value
                    },
                    {
                        type: 'para',
                        indent: true,
                        text: 'Mengingat pentingnya acara tersebut di atas, diharapkan hadir tepat pada waktunya. Demikian kami sampaikan, atas perhatian dan kehadirannya diucapkan terima kasih.'
                    }
                ],
                pages: { asli: true, paraf: true, konsep: true, lampiran: true },
                margin: { top: 2.5, bottom: 2.5, left: 2.5, right: 2.5 }
            };

            const kopSpec = {
                line1: 'KEMENTERIAN PEKERJAAN UMUM',
                line2: 'DIREKTORAT JENDERAL SUMBER DAYA AIR',
                line3: ['BALAI WILAYAH SUNGAI KALIMANTAN I PONTIANAK'],
                line4: 'Jl. Ahmad Yani No. 1 Pontianak, Kalimantan Barat'
            };

            if (typeof DocGen !== 'undefined' && typeof JSZip !== 'undefined') {
                DocGen.build(docData, {
                    kop: kopSpec,
                    logo: { b64: typeof LOGO !== 'undefined' ? LOGO : '' },
                    arialW: typeof ARIALW !== 'undefined' ? ARIALW : null,
                    JSZip: JSZip,
                    outType: 'blob'
                }).then(function(blob) {
                    const url = URL.createObjectURL(blob);
                    const a = document.createElement('a');
                    const cleanName = ('Surat_Undangan_' + (inHal.value || 'Dinas')).replace(/[^a-zA-Z0-9_-]/g, '_') + '.docx';
                    a.href = url;
                    a.download = cleanName;
                    document.body.appendChild(a);
                    a.click();
                    document.body.removeChild(a);
                    URL.revokeObjectURL(url);
                    btn.innerHTML = originalText;
                    btn.disabled = false;
                }).catch(function(err) {
                    console.error('DocGen build failed:', err);
                    alert('Catatan: Ekspor Word mengalami kendala (' + (err.message || err) + '). Anda tetap dapat mencetak atau menyimpan PDF dengan tombol Cetak / PDF.');
                    btn.innerHTML = originalText;
                    btn.disabled = false;
                });
            } else {
                alert('Pustaka pembuat Word sedang diinisialisasi. Silakan gunakan tombol Cetak / PDF.');
                btn.innerHTML = originalText;
                btn.disabled = false;
            }
        } catch (e) {
            console.error('Export error:', e);
            btn.innerHTML = originalText;
            btn.disabled = false;
        }
    });
</script>
@endpush
