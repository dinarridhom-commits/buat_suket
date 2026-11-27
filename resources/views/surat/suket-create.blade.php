@extends('layouts.app')

@php
    $isEdit = isset($suket);
    $jenisVal = old('jenis_absen', $isEdit ? $suket->jenis_absen : 'masuk');
    $tglVal = old('tanggal_kejadian', $isEdit ? $suket->tanggal_kejadian->format('Y-m-d') : date('Y-m-d'));
@endphp

@section('title', $isEdit ? 'Edit Suket Lupa Absen' : 'Buat Suket Lupa Absen')
@section('header_title', $isEdit ? 'Edit Surat Keterangan / Permohonan Izin' : 'Surat Permohonan Izin / Lupa Absen')
@section('header_subtitle', 'Format Resmi Balai Wilayah Sungai Kalimantan I Pontianak — Standar PDF & Word (.docx)')

@push('styles')
<style>
    .editor-layout {
        display: grid;
        grid-template-columns: minmax(400px, 480px) 1fr;
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

    .form-card-body { padding: 16px 18px; }
    .form-group { margin-bottom: 14px; }
    .form-label { display: block; font-size: 12.5px; font-weight: 600; color: #334155; margin-bottom: 5px; }
    .form-control {
        width: 100%; padding: 8px 12px; border: 1px solid #cbd5e1; border-radius: var(--radius-sm);
        font-size: 13.5px; font-family: inherit; background: #ffffff; outline: none; transition: all 0.15s ease;
    }
    .form-control:focus { border-color: var(--pu-blue); box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15); }
    .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }

    .radio-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 8px;
        margin-top: 6px;
    }
    .radio-pill {
        display: flex; align-items: center; gap: 7px; font-size: 12.5px; font-weight: 500;
        padding: 8px 10px; border: 1px solid #cbd5e1; border-radius: var(--radius-sm); cursor: pointer; background: #fff;
    }
    .radio-pill:has(input:checked) { border-color: var(--pu-navy); background: var(--pu-navy-50); font-weight: 600; color: var(--pu-navy); }

    .preview-pane { position: sticky; top: 86px; }
    .preview-header-bar {
        display: flex; align-items: center; justify-content: space-between; margin-bottom: 12px;
        background: #ffffff; padding: 10px 16px; border: 1px solid var(--border);
        border-radius: var(--radius-md); box-shadow: var(--shadow-sm); flex-wrap: wrap; gap: 8px;
    }
    .preview-scroll-wrapper {
        background: #cbd5e1; border-radius: var(--radius-lg); padding: 24px;
        max-height: calc(100vh - 170px); overflow-y: auto; display: flex; justify-content: center;
    }

    /* ===== A4 SHEET RESMI (SAMA DENGAN FORMAT HTML/DOCX) ===== */
    .sheet-a4 {
        width: 794px;
        min-height: 1123px;
        background: #ffffff;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.15);
        padding: 38px 56px 46px 56px;
        font-family: Arial, Helvetica, sans-serif;
        color: #000000;
        font-size: 14px;
        line-height: 1.5;
        zoom: 0.76;
    }
    @media (min-width: 1560px) { .sheet-a4 { zoom: 0.92; } }

    .kop-table { width: 100%; border-collapse: collapse; margin-bottom: 24px; }
    .kop-table td { vertical-align: bottom; }
    .kop-logo-cell { width: 92px; text-align: left; padding-right: 10px; }
    .kop-logo-img { width: 82px; height: 82px; object-fit: contain; display: block; }
    .kop-text-cell { text-align: center; border-bottom: 2px solid #000000; padding-bottom: 5px; }
    .kop-line-1 { font-size: 17px; font-weight: 400; letter-spacing: 0.2px; line-height: 1.2; }
    .kop-line-2 { font-size: 12px; font-weight: 400; line-height: 1.25; }
    .kop-line-3 { font-size: 9.5px; font-weight: 700; line-height: 1.25; }
    .kop-line-address { font-size: 8.5px; margin-top: 2px; line-height: 1.25; }

    .suket-title { text-align: center; margin-bottom: 20px; }
    .suket-title h3 { margin: 0 0 2px 0; font-size: 14.5px; font-weight: 700; letter-spacing: 0.3px; }

    .suket-bio-table { width: 100%; margin: 6px 0 14px 0; font-size: 14px; border-collapse: collapse; }
    .suket-bio-table td { padding: 2px 0; vertical-align: top; }
    .suket-bio-table td:first-child { width: 150px; }
    .suket-bio-table td:nth-child(2) { width: 14px; }

    .ttd-dual-table { width: 100%; margin-top: 24px; font-size: 14px; border-collapse: collapse; }
    .ttd-dual-table td { width: 50%; vertical-align: top; text-align: center; }

    .catatan-dots {
        border-bottom: 1.5px dotted #000;
        height: 22px;
        margin-bottom: 4px;
    }

    @page { size: A4 portrait; margin: 0; }

    @media print {
        html, body {
            background: #fff !important; margin: 0 !important; padding: 0 !important;
            -webkit-print-color-adjust: exact; print-color-adjust: exact;
        }
        .sidebar, .topbar, .subpage-header, .editor-form-col, .preview-header-bar, .alert-toast { display: none !important; }
        .wrapper { margin-left: 0 !important; }
        .content { padding: 0 !important; }
        .editor-layout { display: block !important; }
        .preview-pane { position: static !important; }
        .preview-scroll-wrapper {
            background: transparent !important; padding: 0 !important; box-shadow: none !important;
            max-height: none !important; overflow: visible !important; display: block !important;
        }
        .sheet-a4 {
            zoom: 1 !important; box-shadow: none !important;
            padding: 10mm 17.5mm 12.5mm 10mm !important;
            width: 100% !important; min-height: auto !important; margin: 0 !important;
        }
    }

    @media (max-width: 1200px) {
        .editor-layout { grid-template-columns: 1fr; }
        .editor-form-col, .preview-pane { min-width: 0; max-width: 100%; }
        .preview-pane { position: static; }
    }
    @media (max-width: 1024px) {
        .preview-scroll-wrapper { padding: 18px 12px; overflow-x: auto; align-items: flex-start; }
        .sheet-a4 { zoom: 0.78; margin: 0 auto; }
    }
    @media (max-width: 640px) {
        .preview-scroll-wrapper { padding: 10px 6px; }
        .sheet-a4 { zoom: 0.44; margin: 0 auto; }
        .grid-2 { grid-template-columns: 1fr; }
        .grid-2 > *, .form-group { min-width: 0; }
        .form-control, input[type="date"], input[type="time"], select.form-control {
            width: 100%; max-width: 100%; min-width: 0; box-sizing: border-box;
        }
    }
</style>
@endpush

@section('content')

<div class="editor-layout">
    <!-- LEFT PANE: FORM -->
    <div class="editor-form-col">
        <form method="POST" action="{{ $isEdit ? route('surat.suket.update', $suket->id) : route('surat.suket.store') }}" id="formSuket">
            @csrf
            @if($isEdit) @method('PUT') @endif

            <!-- CARD 1: DATA PEGAWAI -->
            <div class="form-card">
                <div class="form-card-head"><x-icon name="user" class="ui-icon-sm" /> 1. Pegawai yang Bersangkutan</div>
                <div class="form-card-body">
                    <div class="form-group">
                        <label class="form-label">Pilih dari Master Pegawai (148 Pegawai BWS Kalimantan I)</label>
                        <select id="selectPegawai" class="form-control" style="background:#f1f5f9; font-weight:600;">
                            <option value="">-- Pilih Nama Pegawai --</option>
                            @foreach($pegawais as $p)
                                <option value="{{ $p->id }}"
                                    data-nama="{{ $p->nama_gelar }}"
                                    data-nip="{{ $p->nip }}"
                                    data-pangkat="{{ $p->pangkat }}"
                                    data-jabatan="{{ $p->jabatan_definitif ?? $p->jabatan_struktural ?? 'Staf Pelaksana' }}"
                                    data-raw-jabatan="{{ $p->jabatan }}"
                                    data-rangkap="{{ $p->rangkap }}">
                                    {{ $p->nama_gelar }} — {{ $p->nip }} ({{ $p->unit ?? 'BWS Kalimantan I' }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid-2">
                        <div class="form-group">
                            <label class="form-label">Nama Pegawai</label>
                            <input type="text" name="nama_pegawai" id="inNamaPegawai"
                                   value="{{ old('nama_pegawai', $isEdit ? $suket->nama_pegawai : 'Juananda Dwi Satria, S.T.') }}"
                                   class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">NIP</label>
                            <input type="text" name="nip_pegawai" id="inNipPegawai"
                                   value="{{ old('nip_pegawai', $isEdit ? $suket->nip_pegawai : '199407072025061009') }}"
                                   class="form-control">
                        </div>
                    </div>

                    <div class="grid-2">
                        <div class="form-group">
                            <label class="form-label">Pangkat / Golongan</label>
                            <input type="text" name="pangkat_pegawai" id="inPangkatPegawai"
                                   value="{{ old('pangkat_pegawai', $isEdit ? $suket->pangkat_pegawai : 'Penata Muda / III/a') }}"
                                   class="form-control" placeholder="Penata Muda / III/a">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Jabatan</label>
                            <input type="text" name="jabatan_pegawai" id="inJabatanPegawai"
                                   value="{{ old('jabatan_pegawai', $isEdit ? $suket->jabatan_pegawai : 'Penata Kelola Penyehatan Lingkungan Ahli Pertama') }}"
                                   class="form-control" required>
                        </div>
                    </div>
                </div>
            </div>

            <!-- CARD 2: RINCIAN KENDALA ABSEN -->
            <div class="form-card">
                <div class="form-card-head"><x-icon name="clock" class="ui-icon-sm" /> 2. Rincian Izin / Lupa Absen</div>
                <div class="form-card-body">
                    <div class="form-group">
                        <label class="form-label">Nomor Surat Keterangan</label>
                        <input type="text" name="nomor_surat" id="inNomor"
                               value="{{ old('nomor_surat', $autoNomor ?? 'KP.07.03/Bws1/001/2026') }}"
                               class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Jenis Lupa Absen</label>
                        <div class="radio-grid">
                            <label class="radio-pill">
                                <input type="radio" name="jenis_absen" value="masuk" {{ $jenisVal !== 'pulang' ? 'checked' : '' }}>
                                Lupa Absen Masuk
                            </label>
                            <label class="radio-pill">
                                <input type="radio" name="jenis_absen" value="pulang" {{ $jenisVal === 'pulang' ? 'checked' : '' }}>
                                Lupa Absen Pulang
                            </label>
                        </div>
                    </div>

                    <div class="grid-2">
                        <div class="form-group">
                            <label class="form-label">Tanggal Kejadian</label>
                            <input type="date" name="tanggal_kejadian" id="inTanggalAbsen" value="{{ $tglVal }}" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Waktu Kejadian Presensi</label>
                            <input type="text" name="waktu_kejadian" id="inWaktuKejadian"
                                   value="{{ old('waktu_kejadian', $isEdit ? $suket->waktu_kejadian : '07:30') }}"
                                   class="form-control" placeholder="07:30" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Alasan Kendala</label>
                        <textarea name="alasan" id="inAlasan" rows="2" class="form-control" required>{{ old('alasan', $isEdit ? $suket->alasan : 'Lupa absensi masuk di Satu Bravo') }}</textarea>
                    </div>
                </div>
            </div>

            <!-- CARD 3: ATASAN LANGSUNG -->
            <div class="form-card">
                <div class="form-card-head" style="justify-content:space-between;">
                    <span>3. Pejabat yang Menyetujui</span>
                    <span id="skAppRuleBadge" style="font-size:11px; background:#e0f2fe; color:#0369a1; padding:2px 8px; border-radius:20px; text-transform:none;">Otomatis</span>
                </div>
                <div class="form-card-body">
                    <div class="form-group">
                        <label class="form-label">Ganti Manual dari Master Pegawai (Opsional)</label>
                        <select id="selectAtasan" class="form-control" style="background:#f8fafc;">
                            <option value="">-- Pilih Pejabat Penyetuju --</option>
                            @foreach($pegawais as $p)
                                <option value="{{ $p->id }}"
                                    data-nama="{{ $p->nama_gelar }}"
                                    data-nip="{{ $p->nip }}"
                                    data-jabatan="{{ $p->jabatan_definitif ?? $p->jabatan_struktural }}">
                                    {{ $p->nama_gelar }} — {{ $p->jabatan_definitif ?? $p->jabatan_struktural }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Jabatan Atasan Langsung</label>
                        <input type="text" name="atasan_jabatan" id="inAtasanJabatan"
                               value="{{ old('atasan_jabatan', $isEdit ? $suket->atasan_jabatan : 'Kepala Subbagian Umum dan Tata Usaha') }}"
                               class="form-control" required>
                    </div>

                    <div class="grid-2">
                        <div class="form-group">
                            <label class="form-label">Nama Atasan Langsung</label>
                            <input type="text" name="atasan_nama" id="inAtasanNama"
                                   value="{{ old('atasan_nama', $isEdit ? $suket->atasan_nama : 'Iswandi, S.E., S.T.') }}"
                                   class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">NIP Atasan</label>
                            <input type="text" name="atasan_nip" id="inAtasanNip"
                                   value="{{ old('atasan_nip', $isEdit ? $suket->atasan_nip : '197603222009111001') }}"
                                   class="form-control">
                        </div>
                    </div>

                    <div style="font-size:11.5px; color:#64748b; line-height:1.45; margin-bottom:10px;">
                        Aturan V5: pegawai yang menjabat <b>PPK / Kepala Satker / Kepala Seksi</b> (termasuk jabatan rangkap) disetujui oleh <b>Kepala Balai</b>; selain itu oleh <b>Kepala Subbagian Umum dan Tata Usaha</b>.
                        <a href="#" id="btnReapplyRule" style="color:var(--pu-blue); font-weight:600;">Terapkan aturan lagi</a>
                    </div>

                </div>
            </div>

            <!-- SUBMIT BUTTONS -->
            <div style="display:flex; gap:12px; margin-bottom: 24px;">
                @if($isEdit)
                    <a href="{{ route('surat.suket.show', $suket->id) }}" class="btn-quick-create" style="background:#f1f5f9; color:#334155; justify-content:center; padding:12px 18px;">
                        ← Batal
                    </a>
                @endif
                <button type="submit" class="btn-quick-create" style="flex:1; justify-content:center; padding:12px; font-size:14px;">
                    {{ $isEdit ? 'Simpan Perubahan & Lanjut Cetak' : 'Simpan & Lanjut Cetak' }}
                </button>
            </div>
        </form>
    </div>

    <!-- RIGHT PANE: LIVE A4 PREVIEW -->
    <div class="preview-pane">
        <div class="preview-header-bar">
            <span style="font-size:13px; font-weight:700; color:var(--text-main); display:flex; align-items:center; gap:6px;">
                <x-icon name="eye" class="ui-icon-sm" /> Pratinjau A4 Resmi (Sesuai Format Standar BWS)
            </span>
            <div style="display:flex; gap:8px;">
                <button type="button" id="btnExportSuketDocx" class="btn-action" style="background:#0284c7; color:#ffffff; font-weight:600; padding:6px 12px; font-size:12.5px; display:flex; align-items:center; gap:6px; border-radius:6px; border:none; cursor:pointer;">
                    <x-icon name="file-text" class="ui-icon-sm" /> Unduh Word (.docx)
                </button>
                <button type="button" onclick="window.print()" class="btn-action" style="background:var(--pu-navy); color:#ffffff; font-weight:600; padding:6px 14px; font-size:12.5px; display:flex; align-items:center; gap:6px; border-radius:6px; border:none; cursor:pointer;">
                    <x-icon name="printer" class="ui-icon-sm" /> Cetak / PDF
                </button>
            </div>
        </div>

        <div class="preview-scroll-wrapper">
            <div class="sheet-a4" id="sheetPrintArea">
                <div style="padding-left: 28px;">
                    <!-- KOP RESMI (GARIS BAWAH DI KOLOM TEKS SEJAJAR LOGO) -->
                    <table class="kop-table">
                        <tr>
                            <td class="kop-logo-cell">
                                <img src="{{ asset('images/logo_pu.png') }}" alt="Logo PU" class="kop-logo-img">
                            </td>
                            <td class="kop-text-cell">
                                <div class="kop-line-1">{{ $kop['kementerian'] }}</div>
                                <div class="kop-line-2">{{ $kop['ditjen'] }}</div>
                                <div class="kop-line-3">{{ $kop['balai'] }}</div>
                                <div class="kop-line-address">{{ $kop['alamat'] }}</div>
                            </td>
                        </tr>
                    </table>

                    <!-- TITLE -->
                    <div class="suket-title">
                        <h3>SURAT PERMOHONAN IZIN / PEMBERITAHUAN</h3>
                        <div style="font-size:13.5px;" id="pvNomor">NOMOR {{ $autoNomor ?? 'KP.07.03/Bws1/001/2026' }}</div>
                    </div>

                    <div style="margin-bottom:6px;">Yang bertandatangan di bawah ini:</div>

                    <table class="suket-bio-table">
                        <tr>
                            <td>Nama</td>
                            <td>:</td>
                            <td id="pvNama">{{ old('nama_pegawai', $isEdit ? $suket->nama_pegawai : 'Juananda Dwi Satria, S.T.') }}</td>
                        </tr>
                        <tr>
                            <td>NIP</td>
                            <td>:</td>
                            <td id="pvNip">{{ old('nip_pegawai', $isEdit ? $suket->nip_pegawai : '199407072025061009') }}</td>
                        </tr>
                        <tr>
                            <td>Pangkat/Golongan</td>
                            <td>:</td>
                            <td id="pvPangkat">{{ old('pangkat_pegawai', $isEdit ? $suket->pangkat_pegawai : 'Penata Muda / III/a') }}</td>
                        </tr>
                        <tr>
                            <td>Jabatan</td>
                            <td>:</td>
                            <td id="pvJabatan">{{ old('jabatan_pegawai', $isEdit ? $suket->jabatan_pegawai : 'Penata Kelola Penyehatan Lingkungan Ahli Pertama') }}</td>
                        </tr>
                    </table>

                    <div style="text-align:justify; line-height:1.6; margin: 14px 0 18px 0;" id="pvPernyataan">
                        Dengan ini menerangkan bahwa pada hari <span id="pvHariTgl">{{ \Carbon\Carbon::parse($tglVal)->locale('id')->translatedFormat('l') }} tanggal {{ \Carbon\Carbon::parse($tglVal)->locale('id')->translatedFormat('d F Y') }}</span>,
                        <span id="pvOpsiKalimat"></span>
                        (*) karena: <span id="pvAlasan">{{ old('alasan', $isEdit ? $suket->alasan : 'Lupa absensi masuk di Satu Bravo') }}</span>
                    </div>

                    <!-- TTD DUAL -->
                    <table class="ttd-dual-table">
                        <tr>
                            <td></td>
                            <td>
                                <div id="pvTglSurat">Pontianak, {{ \Carbon\Carbon::parse($tglVal)->locale('id')->translatedFormat('d F Y') }}</div>
                                <div>Disetujui/Tidak disetujui (*) oleh</div>
                            </td>
                        </tr>
                        <tr>
                            <td style="vertical-align:bottom; padding-top:4px;">Pegawai yang bersangkutan,</td>
                            <td style="vertical-align:top; padding-top:4px;" id="pvTtdAtasanJabatan">{{ old('atasan_jabatan', $isEdit ? $suket->atasan_jabatan : 'Kepala Sub Bagian Umum dan Tata Usaha') }}</td>
                        </tr>
                        <tr>
                            <td style="height:78px;"></td>
                            <td style="height:78px;"></td>
                        </tr>
                        <tr>
                            <td>
                                <div id="pvTtdPegawai" style="text-decoration:underline;">{{ old('nama_pegawai', $isEdit ? $suket->nama_pegawai : 'Juananda Dwi Satria, S.T.') }}</div>
                                <div id="pvTtdNipPegawai">NIP. {{ old('nip_pegawai', $isEdit ? $suket->nip_pegawai : '199407072025061009') }}</div>
                            </td>
                            <td>
                                <div id="pvTtdAtasanNama" style="text-decoration:underline;">{{ old('atasan_nama', $isEdit ? $suket->atasan_nama : 'Iswandi, S.E., S.T.') }}</div>
                                <div id="pvTtdAtasanNip">NIP. {{ old('atasan_nip', $isEdit ? $suket->atasan_nip : '197603222009111001') }}</div>
                            </td>
                        </tr>
                    </table>

                    <!-- CATATAN ALASAN TIDAK DISETUJUI -->
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
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/jszip.min.js') }}"></script>
<script src="{{ asset('js/surat_constants.js') }}"></script>
<script src="{{ asset('js/docgen.js') }}"></script>
<script>
    const inNomor = document.getElementById('inNomor');
    const inNama = document.getElementById('inNamaPegawai');
    const inNip = document.getElementById('inNipPegawai');
    const inPangkat = document.getElementById('inPangkatPegawai');
    const inJabatan = document.getElementById('inJabatanPegawai');
    const inAlasan = document.getElementById('inAlasan');
    const inWaktu = document.getElementById('inWaktuKejadian');
    const inTanggalAbsen = document.getElementById('inTanggalAbsen');
    const inAtasanNama = document.getElementById('inAtasanNama');
    const inAtasanNip = document.getElementById('inAtasanNip');
    const inAtasanJabatan = document.getElementById('inAtasanJabatan');
    const selectPegawai = document.getElementById('selectPegawai');

    const SUKET_OPSI = [
        ['terlambat', 'saya terlambat masuk kerja'],
        ['pulangawal', 'pulang sebelum waktunya'],
        ['masuk', 'tidak mengisi daftar hadir kedatangan'],
        ['pulang', 'tidak mengisi daftar hadir kepulangan']
    ];
    const BULAN = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
    const HARI  = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];

    function fmtPangkat(s) {
        s = String(s || '').trim();
        const m = /^(.*?)\s*\/?\s*\(\s*([IV]+)\s*\/\s*([a-e])\s*\)\s*$/i.exec(s);
        return m ? m[1].trim() + ' / ' + m[2].toUpperCase() + '/' + m[3].toLowerCase() : s;
    }

    function getSelectedJenis() {
        const chk = document.querySelector('input[name="jenis_absen"]:checked');
        return chk ? chk.value : 'masuk';
    }

    function renderOpsiKalimat() {
        const pick = getSelectedJenis();
        let html = '';
        SUKET_OPSI.forEach((o, i) => {
            const on = o[0] === pick;
            const last = i === SUKET_OPSI.length - 1;
            if (on) {
                html += (i ? ' ' : '') + '<span>' + o[1] + '</span>' + (last ? '' : ' ');
            } else if (last) {
                html += '<del>/ ' + o[1] + '</del>';
            } else {
                html += '<del>' + (i ? ' ' : '') + o[1] + ' /</del>';
            }
        });
        document.getElementById('pvOpsiKalimat').innerHTML = html;
    }

    function renderTanggal() {
        const val = inTanggalAbsen.value;
        const p = val ? val.split('-') : [];
        const d = p.length === 3 ? new Date(+p[0], +p[1] - 1, +p[2]) : new Date();
        const tglStr = d.getDate() + ' ' + BULAN[d.getMonth()] + ' ' + d.getFullYear();
        document.getElementById('pvHariTgl').innerText = HARI[d.getDay()] + ' tanggal ' + tglStr;
        document.getElementById('pvTglSurat').innerText = 'Pontianak, ' + tglStr;
    }

    inNomor.addEventListener('input', () => { document.getElementById('pvNomor').innerText = 'NOMOR ' + inNomor.value; });
    inNama.addEventListener('input', () => {
        document.getElementById('pvNama').innerText = inNama.value;
        document.getElementById('pvTtdPegawai').innerText = inNama.value;
    });
    inNip.addEventListener('input', () => {
        document.getElementById('pvNip').innerText = inNip.value;
        document.getElementById('pvTtdNipPegawai').innerText = inNip.value ? 'NIP. ' + inNip.value : '';
    });
    inPangkat.addEventListener('input', () => document.getElementById('pvPangkat').innerText = inPangkat.value);
    inJabatan.addEventListener('input', () => document.getElementById('pvJabatan').innerText = inJabatan.value);
    inAlasan.addEventListener('input', () => document.getElementById('pvAlasan').innerText = inAlasan.value);
    inTanggalAbsen.addEventListener('input', renderTanggal);
    inAtasanNama.addEventListener('input', () => { document.getElementById('pvTtdAtasanNama').innerText = inAtasanNama.value; });
    inAtasanNip.addEventListener('input', () => document.getElementById('pvTtdAtasanNip').innerText = inAtasanNip.value ? 'NIP. ' + inAtasanNip.value : '');
    inAtasanJabatan.addEventListener('input', () => document.getElementById('pvTtdAtasanJabatan').innerText = inAtasanJabatan.value);

    document.querySelectorAll('input[name="jenis_absen"]').forEach(r => r.addEventListener('change', function() {
        renderOpsiKalimat();
        const cur = inAlasan.value.trim();
        if (cur === 'Lupa absensi masuk di Satu Bravo' || cur === 'Lupa absensi pulang di Satu Bravo') {
            inAlasan.value = this.value === 'pulang' ? 'Lupa absensi pulang di Satu Bravo' : 'Lupa absensi masuk di Satu Bravo';
            document.getElementById('pvAlasan').innerText = inAlasan.value;
        }
    }));

    const RE_PIMPINAN = /(^|[\s.(])PPK\b|Pejabat Pembuat Komitmen|Kepala Satker|Kepala Satuan Kerja|Kepala SNVT|Kepala Seksi/i;
    let skAppManual = {{ $isEdit ? 'true' : 'false' }};
    let selectedRawJabatan = '';
    let selectedRangkap = '';

    function applyAtasanRule(force) {
        if (!force && skAppManual) return;
        const texts = [inJabatan.value, selectedRawJabatan, selectedRangkap];
        const isPimpinan = texts.some(t => RE_PIMPINAN.test(String(t || '')));
        if (isPimpinan) {
            inAtasanJabatan.value = 'Plt. Kepala Balai Wilayah Sungai Kalimantan I Pontianak';
            inAtasanNama.value    = 'Donni Saputra, S.T, M.Si., M.T.';
            inAtasanNip.value     = '197611012005021003';
        } else {
            inAtasanJabatan.value = 'Kepala Subbagian Umum dan Tata Usaha';
            inAtasanNama.value    = 'Iswandi, S.E., S.T.';
            inAtasanNip.value     = '197603222009111001';
        }
        skAppManual = false;
        document.getElementById('skAppRuleBadge').innerText = 'Otomatis';
        document.getElementById('pvTtdAtasanJabatan').innerText = inAtasanJabatan.value;
        document.getElementById('pvTtdAtasanNama').innerText = inAtasanNama.value;
        document.getElementById('pvTtdAtasanNip').innerText = inAtasanNip.value ? 'NIP. ' + inAtasanNip.value : '';
    }

    document.getElementById('btnReapplyRule').addEventListener('click', function(e) {
        e.preventDefault();
        applyAtasanRule(true);
    });

    document.getElementById('selectAtasan').addEventListener('change', function() {
        const opt = this.options[this.selectedIndex];
        if (opt.value) {
            inAtasanNama.value = opt.getAttribute('data-nama') || '';
            inAtasanNip.value = opt.getAttribute('data-nip') || '';
            inAtasanJabatan.value = opt.getAttribute('data-jabatan') || '';
            skAppManual = true;
            document.getElementById('skAppRuleBadge').innerText = 'Manual';
            document.getElementById('pvTtdAtasanJabatan').innerText = inAtasanJabatan.value;
            document.getElementById('pvTtdAtasanNama').innerText = inAtasanNama.value;
            document.getElementById('pvTtdAtasanNip').innerText = inAtasanNip.value ? 'NIP. ' + inAtasanNip.value : '';
        }
    });

    selectPegawai.addEventListener('change', function() {
        const opt = this.options[this.selectedIndex];
        if (opt.value) {
            inNama.value = opt.getAttribute('data-nama') || '';
            inNip.value = opt.getAttribute('data-nip') || '';
            inPangkat.value = fmtPangkat(opt.getAttribute('data-pangkat') || '');
            inJabatan.value = opt.getAttribute('data-jabatan') || '';
            selectedRawJabatan = opt.getAttribute('data-raw-jabatan') || '';
            selectedRangkap = opt.getAttribute('data-rangkap') || '';

            document.getElementById('pvNama').innerText = inNama.value;
            document.getElementById('pvTtdPegawai').innerText = inNama.value;
            document.getElementById('pvNip').innerText = inNip.value;
            document.getElementById('pvTtdNipPegawai').innerText = inNip.value ? 'NIP. ' + inNip.value : '';
            document.getElementById('pvPangkat').innerText = inPangkat.value;
            document.getElementById('pvJabatan').innerText = inJabatan.value;
            applyAtasanRule(false);
        }
    });

    renderOpsiKalimat();
    renderTanggal();

    // Export DOCX Suket via DocGen
    document.getElementById('btnExportSuketDocx').addEventListener('click', function() {
        const btn = this;
        const orig = btn.innerHTML;
        btn.textContent = 'Membuat .docx...';
        btn.disabled = true;

        try {
            const pick = getSelectedJenis();
            const docData = {
                mode: 'suket',
                sk_nomor: inNomor.value,
                sk_nama: inNama.value,
                sk_nip: inNip.value,
                sk_pangkat: inPangkat.value,
                sk_jabatan: inJabatan.value,
                sk_tglAbsen: inTanggalAbsen.value,
                sk_tglSurat: inTanggalAbsen.value,
                sk_kota: 'Pontianak',
                sk_jenis: pick,
                sk_alasan: inAlasan.value,
                sk_appJab: inAtasanJabatan.value,
                sk_appNama: inAtasanNama.value,
                sk_appNip: inAtasanNip.value
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
                a.download = ('Suket_' + (inNama.value || 'Pegawai')).replace(/[^a-zA-Z0-9_-]/g, '_') + '.docx';
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
        } catch (e) {
            btn.innerHTML = orig;
            btn.disabled = false;
        }
    });
</script>
@endpush
