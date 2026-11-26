@extends('layouts.app')

@php
    $isEdit = isset($surat);
    $src = $surat ?? ($duplicateFrom ?? null);
    $extra = $src ? ($src->extra_data ?? []) : [];
    $kopTypeVal = old('extra_data.kop_type', $extra['kop_type'] ?? 'balai');
    $ythModeVal = old('extra_data.yth_mode', $extra['yth_mode'] ?? 'langsung');
    $tembusanVal = old('extra_data.tembusan', $extra['tembusan'] ?? '');
    $diTempatVal = old('extra_data.di_tempat', $extra['di_tempat'] ?? 'Tempat');
    $showNipVal = old('extra_data.show_nip', $extra['show_nip'] ?? '0');
    $gapYthVal = old('extra_data.gap_yth', $extra['gap_yth'] ?? '240');
    $urutanVal = old('extra_data.urutan', $extra['urutan'] ?? 'paraf-dulu');
    $showScheduleVal = old('extra_data.show_schedule', $extra['show_schedule'] ?? '0');
    $konsep = $extra['konsep'] ?? [];
    $margin = $extra['margin'] ?? ['top' => 2.5, 'bottom' => 2.5, 'left' => 2.5, 'right' => 2.5, 'lamp_top' => 1, 'lamp_bottom' => 1];
    $tglSuratVal = old('tanggal_surat', $isEdit ? $surat->tanggal_surat->format('Y-m-d') : date('Y-m-d'));

    $penerimaList = ($src && $src->penerimas->count() > 0)
        ? $src->penerimas->pluck('nama_penerima')->toArray()
        : [
            'Para Pejabat Administrator BWS Kalimantan I',
            'Para Kepala Satuan Kerja di Lingkungan BWS Kalimantan I',
            'Para Pejabat Pembuat Komitmen (PPK) di Lingkungan BWS Kalimantan I',
        ];

    $defaultBodySections = [
        [
            'type' => 'paragraph',
            'indent' => true,
            'text' => 'Sehubungan dengan pelaksanaan program kegiatan pada Balai Wilayah Sungai Kalimantan I Pontianak, dengan ini kami mengharapkan kehadiran Bapak/Ibu pada rapat yang akan dilaksanakan pada:',
            'items' => [],
        ],
        [
            'type' => 'points',
            'indent' => true,
            'text' => '',
            'items' => [
                ['label' => 'Hari / Tanggal', 'value' => $src ? ($src->hari_tanggal_acara ?? 'Senin / 30 November 2026') : 'Senin / 30 November 2026'],
                ['label' => 'Jam', 'value' => $src ? ($src->waktu_acara ?? '09.00 WIB s.d. selesai') : '09.00 WIB s.d. selesai'],
                ['label' => 'Tempat', 'value' => $src ? ($src->tempat ?? 'Ruang Rapat BWS Kalimantan I Pontianak') : 'Ruang Rapat BWS Kalimantan I Pontianak'],
                ['label' => 'Agenda', 'value' => $src ? ($src->agenda ?? 'Rapat Pembahasan Evaluasi Kinerja Program Pengelolaan SDA') : 'Rapat Pembahasan Evaluasi Kinerja Program Pengelolaan SDA'],
            ],
        ],
        [
            'type' => 'paragraph',
            'indent' => false,
            'text' => 'Mengingat pentingnya acara tersebut di atas, diharapkan hadir tepat pada waktunya. Demikian disampaikan, atas perhatian dan kehadirannya diucapkan terima kasih.',
            'items' => [],
        ],
    ];
    $bodySections = !empty($extra['body']) && is_array($extra['body']) ? $extra['body'] : $defaultBodySections;

    $scheduleRows = !empty($extra['schedule_rows']) && is_array($extra['schedule_rows']) ? $extra['schedule_rows'] : [];
@endphp

@section('title', $isEdit ? 'Edit Surat Undangan' : 'Buat Surat Undangan')
@section('header_title', $isEdit ? 'Edit Surat Undangan Kedinasan' : 'Generator Surat Undangan Kedinasan')
@section('header_subtitle', 'Format Resmi Tata Naskah Dinas BWS Kalimantan I Pontianak')

@push('styles')
<style>
    .editor-layout {
        display: grid;
        grid-template-columns: minmax(420px, 510px) 1fr;
        gap: 22px;
        align-items: start;
    }
    .form-card {
        background: var(--bg-surface); border: 1px solid var(--border); border-radius: var(--radius-lg);
        overflow: hidden; margin-bottom: 16px; box-shadow: var(--shadow-sm);
    }
    .form-card-head {
        padding: 12px 18px; background: #f8fafc; border-bottom: 1px solid var(--border);
        font-size: 12.5px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;
        color: var(--pu-navy); display: flex; align-items: center; gap: 8px;
    }
    .form-card-body { padding: 16px 18px; }
    .form-group { margin-bottom: 14px; }
    .form-group:last-child { margin-bottom: 0; }
    .form-label { display: block; font-size: 12.5px; font-weight: 600; color: #334155; margin-bottom: 5px; }
    .form-control {
        width: 100%; padding: 8px 12px; border: 1px solid #cbd5e1; border-radius: var(--radius-sm);
        font-size: 13.5px; font-family: inherit; background: #ffffff; outline: none; transition: all 0.15s ease;
    }
    .form-control:focus { border-color: var(--pu-blue); box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15); }
    .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
    .grid-3 { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 8px; }
    .grid-4 { display: grid; grid-template-columns: repeat(4, 1fr); gap: 8px; }

    /* Dynamic Body Builder (V5 style) */
    .sec-box { border: 1px solid #d6dbd8; border-radius: 8px; background: #fbfcfb; margin-bottom: 10px; overflow: hidden; }
    .sec-head {
        display: flex; align-items: center; gap: 6px; padding: 6px 10px;
        border-bottom: 1px solid #e6e9e7; background: #f1f5f9;
    }
    .sec-head select { padding: 3px 8px; font-size: 12px; border-radius: 4px; border: 1px solid #cbd5e1; }
    .sec-head-btn {
        border: 1px solid #cbd5e1; background: #fff; border-radius: 4px; width: 24px; height: 24px;
        cursor: pointer; font-size: 11px; color: #334155; padding: 0;
    }
    .sec-head-btn.del { color: #dc2626; border-color: #fecaca; }
    .sec-body { padding: 10px; }
    .rin-row { display: grid; grid-template-columns: 130px 1fr 26px; gap: 6px; margin-bottom: 6px; align-items: start; }

    .btn-mini {
        padding: 4px 10px; font-size: 11.5px; font-weight: 600; border: 1px solid #cbd5e1;
        background: #f8fafc; color: #334155; border-radius: 5px; cursor: pointer;
    }
    .btn-mini:hover { background: #e2e8f0; }

    .preview-pane { position: sticky; top: 86px; }
    .preview-header-bar {
        display: flex; align-items: center; justify-content: space-between; margin-bottom: 12px;
        background: #ffffff; padding: 10px 14px; border: 1px solid var(--border);
        border-radius: var(--radius-md); box-shadow: var(--shadow-sm); flex-wrap: wrap; gap: 8px;
    }
    .btn-toolbar-sm {
        font-weight: 600; padding: 6px 12px; font-size: 12.5px; display: inline-flex; align-items: center;
        gap: 6px; border-radius: 6px; border: none; cursor: pointer; text-decoration: none;
    }
    .preview-scroll-wrapper {
        background: #cbd5e1; border-radius: var(--radius-lg); padding: 24px;
        max-height: calc(100vh - 170px); overflow-y: auto; box-shadow: inset 0 2px 4px rgba(0,0,0,0.1);
        display: flex; flex-direction: column; align-items: center; gap: 20px;
    }

    /* ===== A4 SHEET RESMI ===== */
    .sheet-a4 {
        width: 794px; min-height: 1123px; background: #ffffff;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.15); padding: 48px 56px;
        font-family: Arial, Helvetica, sans-serif; color: #000000;
        font-size: 14.5px; line-height: 1.45; position: relative; zoom: 0.76;
    }
    @media (min-width: 1560px) { .sheet-a4 { zoom: 0.92; } }

    .kop-table { width: 100%; border-collapse: collapse; margin-bottom: 16px; }
    .kop-table td { vertical-align: bottom; }
    .kop-logo-cell { width: 92px; text-align: left; padding-right: 10px; }
    .kop-logo-img { width: 82px; height: 82px; object-fit: contain; display: block; }
    .kop-text-cell { text-align: center; border-bottom: 2px solid #000000; padding-bottom: 5px; }
    .kop-line-1 { font-size: 17px; font-weight: 400; letter-spacing: 0.2px; line-height: 1.2; }
    .kop-line-2 { font-size: 12px; font-weight: 400; line-height: 1.25; }
    .kop-line-3 { font-size: 9.5px; font-weight: 700; line-height: 1.25; }
    .kop-line-address { font-size: 8.5px; margin-top: 2px; line-height: 1.25; }

    .letter-meta-table { width: 100%; margin-bottom: 14px; font-size: 14.5px; border-collapse: collapse; }
    .letter-meta-table td { vertical-align: top; padding: 1.5px 0; }

    .undangan-tujuan-box { margin: 14px 0; font-size: 14.5px; line-height: 1.5; }
    .undangan-list { padding-left: 22px; margin-top: 4px; }

    .pv-points-table { width: 100%; margin: 10px 0 10px 36px; font-size: 14.5px; border-collapse: collapse; }
    .pv-points-table td { vertical-align: top; padding: 2px 0; }

    .schedule-table-preview { width: 100%; border-collapse: collapse; margin: 14px 0; font-size: 13.5px; }
    .schedule-table-preview th, .schedule-table-preview td { border: 1px solid #333; padding: 6px 10px; }
    .schedule-table-preview th { background: #f1f5f9; font-weight: 700; text-align: center; }

    .ttd-paraf-grid { width: 100%; margin-top: 28px; border-collapse: collapse; font-size: 14.5px; }
    .ttd-paraf-grid td { vertical-align: top; }
    .paraf-mini-box {
        border: 1.5px solid #000; width: 165px; min-height: 78px; padding: 4px 6px;
        text-align: center; font-size: 10.5px; display: flex; flex-direction: column; justify-content: space-between;
    }

    .konsep-table { width: 100%; border-collapse: collapse; border: 1.5px solid #000; font-size: 11.5px; margin-top: 8px; }
    .konsep-table td, .konsep-table th { border: 1px solid #000; padding: 7px 9px; vertical-align: top; }

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
            padding: 15mm 20mm !important; width: 100% !important; min-height: 297mm !important; margin: 0 !important;
            page-break-after: always;
        }
        .sheet-a4:last-child { page-break-after: auto; }
        body.print-all-sheets .sheet-a4.printable-sheet { display: block !important; }
    }

    @media (max-width: 1200px) {
        .editor-layout { grid-template-columns: 1fr; }
        .preview-pane { position: static; }
    }
</style>
@endpush

@section('content')

<datalist id="dl-pegawai-roles">
    @foreach($pegawais as $p)
        <option value="{{ $p->nama_gelar }} — {{ $p->jabatan_definitif ?? $p->jabatan }}"
                data-nama="{{ $p->nama_gelar }}"
                data-nip="{{ $p->nip }}"
                data-jab="{{ $p->jabatan_definitif ?? $p->jabatan }}"></option>
        @if($p->rangkap)
            <option value="{{ $p->nama_gelar }} — [Rangkap] {{ $p->rangkap_surat ?: $p->rangkap }}"
                    data-nama="{{ $p->nama_gelar }}"
                    data-nip="{{ $p->nip }}"
                    data-jab="{{ $p->rangkap_surat ?: $p->rangkap }}"></option>
        @endif
    @endforeach
</datalist>

<div class="editor-layout">
    <!-- LEFT PANE: FORM -->
    <div class="editor-form-col">
        <form method="POST" action="{{ $isEdit ? route('surat.undangan.update', $surat->id) : route('surat.undangan.store') }}" id="formSurat">
            @csrf
            @if($isEdit) @method('PUT') @endif
            <input type="hidden" name="extra_data[schedule_json]" id="inScheduleJson" value="">
            <input type="hidden" name="extra_data[body_json]" id="inBodyJson" value="">
            <input type="hidden" name="hari_tanggal_acara" id="inHariTanggalHidden" value="{{ old('hari_tanggal_acara', $src ? $src->hari_tanggal_acara : '') }}">
            <input type="hidden" name="waktu_acara" id="inWaktuHidden" value="{{ old('waktu_acara', $src ? $src->waktu_acara : '') }}">
            <input type="hidden" name="tempat" id="inTempatHidden" value="{{ old('tempat', $src ? $src->tempat : 'Ruang Rapat BWS Kalimantan I Pontianak') }}">
            <input type="hidden" name="agenda" id="inAgendaHidden" value="{{ old('agenda', $src ? $src->agenda : '') }}">

            <!-- CARD 1: PENGATURAN DOKUMEN & KOP -->
            <div class="form-card">
                <div class="form-card-head">
                    <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                    1. Pengaturan Dokumen &amp; Kop Surat
                </div>
                <div class="form-card-body">
                    <div class="form-group">
                        <label class="form-label">Pilihan Kop Resmi (Balai / Satker)</label>
                        <select name="extra_data[kop_type]" id="inKopType" class="form-control" style="font-weight:600;">
                            @foreach($kopPresets as $key => $preset)
                                <option value="{{ $key }}" {{ $kopTypeVal === $key ? 'selected' : '' }}>{{ $preset['label'] }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid-2">
                        <div class="form-group">
                            <label class="form-label">Urutan Halaman (.docx)</label>
                            <select name="extra_data[urutan]" id="inUrutan" class="form-control">
                                <option value="paraf-dulu" {{ $urutanVal === 'paraf-dulu' ? 'selected' : '' }}>Konsep &rarr; Paraf &rarr; Asli</option>
                                <option value="asli-dulu" {{ $urutanVal === 'asli-dulu' ? 'selected' : '' }}>Konsep &rarr; Asli &rarr; Paraf</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Jarak Hal &rarr; Yth.</label>
                            <select name="extra_data[gap_yth]" id="inGapYth" class="form-control">
                                <option value="120" {{ $gapYthVal == '120' ? 'selected' : '' }}>Rapat (1/2 baris)</option>
                                <option value="240" {{ $gapYthVal == '240' ? 'selected' : '' }}>Normal (1 baris)</option>
                                <option value="480" {{ $gapYthVal == '480' ? 'selected' : '' }}>Lebar (2 baris)</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label style="font-size:12.5px; font-weight:600; color:#334155; display:flex; align-items:center; gap:8px; cursor:pointer;">
                            <input type="hidden" name="extra_data[show_nip]" value="0">
                            <input type="checkbox" name="extra_data[show_nip]" value="1" id="chkShowNip" {{ $showNipVal ? 'checked' : '' }}>
                            <span>Tampilkan NIP di bawah nama penanda tangan</span>
                        </label>
                    </div>

                    <details style="border:1px solid var(--border); border-radius:8px; padding:8px 12px; background:#f8fafc;">
                        <summary style="cursor:pointer; font-size:12px; font-weight:700; color:var(--pu-navy);">Pengaturan Margin &amp; Halaman (.docx)</summary>
                        <div class="grid-4" style="margin-top:10px;">
                            <div><span style="font-size:11px; color:#64748b;">Atas (cm)</span><input type="number" step="0.05" min="0.5" name="extra_data[margin][top]" id="inMcTop" value="{{ $margin['top'] ?? 2.5 }}" class="form-control"></div>
                            <div><span style="font-size:11px; color:#64748b;">Bawah</span><input type="number" step="0.05" min="0.5" name="extra_data[margin][bottom]" id="inMcBottom" value="{{ $margin['bottom'] ?? 2.5 }}" class="form-control"></div>
                            <div><span style="font-size:11px; color:#64748b;">Kiri</span><input type="number" step="0.05" min="0.5" name="extra_data[margin][left]" id="inMcLeft" value="{{ $margin['left'] ?? 2.5 }}" class="form-control"></div>
                            <div><span style="font-size:11px; color:#64748b;">Kanan</span><input type="number" step="0.05" min="0.5" name="extra_data[margin][right]" id="inMcRight" value="{{ $margin['right'] ?? 2.5 }}" class="form-control"></div>
                        </div>
                        <div class="grid-2" style="margin-top:8px;">
                            <div><span style="font-size:11px; color:#64748b;">Margin Lampiran Atas</span><input type="number" step="0.05" min="0.5" name="extra_data[margin][lamp_top]" id="inMlcTop" value="{{ $margin['lamp_top'] ?? 1 }}" class="form-control"></div>
                            <div><span style="font-size:11px; color:#64748b;">Margin Lampiran Bawah</span><input type="number" step="0.05" min="0.5" name="extra_data[margin][lamp_bottom]" id="inMlcBottom" value="{{ $margin['lamp_bottom'] ?? 1 }}" class="form-control"></div>
                        </div>
                        <div style="display:flex; gap:14px; flex-wrap:wrap; margin-top:10px; font-size:12px;">
                            <label><input type="checkbox" id="pgKonsep" checked> Konsep</label>
                            <label><input type="checkbox" id="pgParaf" checked> Paraf</label>
                            <label><input type="checkbox" id="pgAsli" checked> Surat Asli</label>
                            <label><input type="checkbox" id="pgLampiran" checked> Lampiran</label>
                            <label><input type="checkbox" id="chkKonsepMargin" checked> Margin sempit konsep</label>
                        </div>
                    </details>
                </div>
            </div>

            <!-- CARD 2: SURAT ASLI (META & ISI DINAMIS) -->
            <div class="form-card">
                <div class="form-card-head">
                    <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                    2. Kepala &amp; Isi Surat Asli
                </div>
                <div class="form-card-body">
                    <div class="form-group">
                        <label class="form-label">Nomor Surat Dinas</label>
                        <input type="text" name="nomor_surat" id="inNomor" value="{{ old('nomor_surat', $autoNomor ?? 'UM.01.02/Bws1/001/2026') }}" class="form-control" required>
                    </div>

                    <div class="grid-2">
                        <div class="form-group">
                            <label class="form-label">Kota Surat</label>
                            <input type="text" name="extra_data[kota]" id="inKota" value="{{ old('extra_data.kota', $extra['kota'] ?? 'Pontianak') }}" class="form-control">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Tanggal Surat</label>
                            <input type="date" name="tanggal_surat" id="inTanggalSurat" value="{{ $tglSuratVal }}" class="form-control" required>
                        </div>
                    </div>

                    <div class="grid-2">
                        <div class="form-group">
                            <label class="form-label">Sifat</label>
                            <input type="text" name="sifat" id="inSifat" value="{{ old('sifat', $src ? $src->sifat : 'Biasa') }}" class="form-control">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Lampiran</label>
                            <input type="text" name="lampiran" id="inLampiran" value="{{ old('lampiran', $src ? $src->lampiran : '1 Lembar') }}" class="form-control">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Hal / Perihal</label>
                        <textarea name="hal" id="inHal" rows="2" class="form-control" required>{{ old('hal', $src ? $src->hal : 'Undangan Rapat Koordinasi Teknis') }}</textarea>
                    </div>

                    <div class="form-group" style="margin-top:14px;">
                        <label class="form-label">Susunan Isi Surat (Bagian Paragraf &amp; Poin)</label>
                        <div id="bodySectionsContainer"></div>
                        <div style="display:flex; gap:8px; margin-top:8px;">
                            <button type="button" class="btn-mini" id="btnAddPara">+ Bagian Paragraf</button>
                            <button type="button" class="btn-mini" id="btnAddPoints">+ Bagian Poin (Label : Isi)</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- CARD 3: PENERIMA, DI TEMPAT & TEMBUSAN -->
            <div class="form-card">
                <div class="form-card-head" style="justify-content: space-between;">
                    <span>3. Penerima Undangan (Yth.) &amp; Tembusan</span>
                    <button type="button" id="btnAddPenerima" class="btn-mini">+ Tambah</button>
                </div>
                <div class="form-card-body">
                    <div class="form-group">
                        <label class="form-label">Mode Penampilan Penerima</label>
                        <select name="extra_data[yth_mode]" id="inYthMode" class="form-control">
                            <option value="langsung" {{ $ythModeVal === 'langsung' ? 'selected' : '' }}>Tampilkan Langsung di Badan Surat</option>
                            <option value="terlampir" {{ $ythModeVal === 'terlampir' ? 'selected' : '' }}>Daftar Terlampir (Pindah ke Lampiran I)</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Tambah Cepat dari Master Pegawai / Jabatan</label>
                        <input type="search" id="pickerAddPenerima" list="dl-pegawai-roles" class="form-control" placeholder="Ketik nama atau jabatan lalu pilih...">
                    </div>

                    <div id="penerimaContainer">
                        @foreach($penerimaList as $pNama)
                            <div class="form-group" style="display:flex; gap:5px; margin-bottom:6px;">
                                <input type="text" name="penerimas[]" class="form-control in-penerima" value="{{ $pNama }}">
                                <button type="button" class="btn-mini" onclick="movePenerima(this, -1)" title="Naik">&#9650;</button>
                                <button type="button" class="btn-mini" onclick="movePenerima(this, 1)" title="Turun">&#9660;</button>
                                <button type="button" style="border:1px solid #fca5a5; background:#fff1f2; color:#b91c1c; border-radius:6px; padding:0 9px; cursor:pointer;" onclick="this.parentElement.remove(); syncPenerimaList();">&times;</button>
                            </div>
                        @endforeach
                    </div>

                    <div style="display:flex; gap:6px; flex-wrap:wrap; margin-bottom:12px;">
                        <button type="button" class="btn-mini" id="btnSortPenerima">Urutkan A–Z</button>
                        <button type="button" class="btn-mini" id="btnRevPenerima">Balik Urutan</button>
                    </div>

                    <div class="form-group">
                        <label class="form-label">di- (Tujuan Tempat)</label>
                        <input type="text" name="extra_data[di_tempat]" id="inDiTempat" value="{{ $diTempatVal }}" class="form-control">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Tembusan (1 baris = 1 nomor, kosongkan jika tidak ada)</label>
                        <textarea name="extra_data[tembusan]" id="inTembusan" rows="2" class="form-control" placeholder="Direktur Jenderal Sumber Daya Air">{{ $tembusanVal }}</textarea>
                    </div>
                </div>
            </div>

            <!-- CARD 4: PENANDA TANGAN, PARAF & KONSEP -->
            <div class="form-card">
                <div class="form-card-head">
                    <span>4. Penanda Tangan, Paraf &amp; Lembar Konsep</span>
                </div>
                <div class="form-card-body">
                    <div class="form-group">
                        <label class="form-label">Pilih Penanda Tangan dari Master Pegawai</label>
                        <select id="selectPegawaiDb" class="form-control" style="background:#f1f5f9; font-weight:600;">
                            <option value="">-- Pilih Pejabat Penandatangan --</option>
                            @foreach($pegawais as $p)
                                <option value="{{ $p->id }}"
                                    data-nama="{{ $p->nama_gelar }}"
                                    data-nip="{{ $p->nip }}"
                                    data-jabatan="{{ $p->jabatan_definitif ?? $p->jabatan }}"
                                    {{ (!$src && str_contains($p->nama, 'Donni')) ? 'selected' : '' }}>
                                    {{ $p->nama_gelar }} — {{ $p->jabatan_definitif ?? $p->jabatan }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Jabatan Penandatangan</label>
                        <input type="text" name="penandatangan_jabatan" id="inTtdJabatan" value="{{ old('penandatangan_jabatan', $src ? $src->penandatangan_jabatan : 'Plt. Kepala Balai Wilayah Sungai Kalimantan I Pontianak') }}" class="form-control" required>
                    </div>

                    <div class="grid-2">
                        <div class="form-group">
                            <label class="form-label">Nama Lengkap &amp; Gelar</label>
                            <input type="text" name="penandatangan_nama" id="inTtdNama" value="{{ old('penandatangan_nama', $src ? $src->penandatangan_nama : 'Donni Saputra, S.T, M.Si., M.T.') }}" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">NIP</label>
                            <input type="text" name="penandatangan_nip" id="inTtdNip" value="{{ old('penandatangan_nip', $src ? $src->penandatangan_nip : '197611012005021003') }}" class="form-control">
                        </div>
                    </div>

                    <details open style="border-top:1px dashed var(--border); padding-top:12px;">
                        <summary style="cursor:pointer; font-size:12px; font-weight:700; color:var(--pu-navy); margin-bottom:10px; text-transform:uppercase;">Lembar Konsep (Hal. 1) &amp; Paraf (Hal. 2)</summary>
                        <div class="grid-2">
                            <div class="form-group">
                                <label class="form-label">Konfirmasi / Paraf (Jabatan)</label>
                                <input type="text" name="extra_data[konsep][konf_jab]" id="inKonfJab" value="{{ old('extra_data.konsep.konf_jab', $konsep['konf_jab'] ?? 'Kasubbag Umum & TU') }}" class="form-control">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Konfirmasi / Paraf (Nama)</label>
                                <input type="text" name="extra_data[konsep][konf_nama]" id="inKonfNama" value="{{ old('extra_data.konsep.konf_nama', $konsep['konf_nama'] ?? 'Iswandi, S.E., S.T.') }}" class="form-control">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Jabatan Singkat di Kotak Paraf (Opsional)</label>
                            <input type="text" name="extra_data[konsep][konf_jab_paraf]" id="inKonfJabParaf" value="{{ old('extra_data.konsep.konf_jab_paraf', $konsep['konf_jab_paraf'] ?? '') }}" class="form-control" placeholder="Kosongkan untuk memakai jabatan Konfirmasi">
                        </div>
                        <div class="grid-2">
                            <div class="form-group">
                                <label class="form-label">Pemeriksa Akhir (Jabatan)</label>
                                <input type="text" name="extra_data[konsep][pem_jab]" id="inPemJab" value="{{ old('extra_data.konsep.pem_jab', $konsep['pem_jab'] ?? 'Kepala Subbagian TU') }}" class="form-control">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Pemeriksa Akhir (Nama)</label>
                                <input type="text" name="extra_data[konsep][pem_nama]" id="inPemNama" value="{{ old('extra_data.konsep.pem_nama', $konsep['pem_nama'] ?? 'Iswandi, S.E., S.T.') }}" class="form-control">
                            </div>
                        </div>
                        <div class="grid-2">
                            <div class="form-group">
                                <label class="form-label">Penulis Redaksi (Jabatan)</label>
                                <input type="text" name="extra_data[konsep][pen_jab]" id="inPenJab" value="{{ old('extra_data.konsep.pen_jab', $konsep['pen_jab'] ?? 'Staf Pelaksana TU') }}" class="form-control">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Penulis Redaksi (Nama)</label>
                                <input type="text" name="extra_data[konsep][pen_nama]" id="inPenNama" value="{{ old('extra_data.konsep.pen_nama', $konsep['pen_nama'] ?? 'Staf Pelaksana TU') }}" class="form-control">
                            </div>
                        </div>
                        <div class="grid-2">
                            <div class="form-group">
                                <label class="form-label">Diketik Oleh</label>
                                <input type="text" name="extra_data[konsep][ketik_nama]" id="inKetikNama" value="{{ old('extra_data.konsep.ketik_nama', $konsep['ketik_nama'] ?? 'Operator TU') }}" class="form-control">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Ditetapkan (Nama)</label>
                                <input type="text" name="extra_data[konsep][tetap_nama]" id="inTetapNama" value="{{ old('extra_data.konsep.tetap_nama', $konsep['tetap_nama'] ?? '') }}" class="form-control" placeholder="Otomatis ikut Penanda Tangan jika kosong">
                            </div>
                        </div>
                        <div class="grid-3">
                            <div class="form-group">
                                <label class="form-label">Ajukan Lagi Tgl</label>
                                <input type="date" name="extra_data[konsep][ajukan]" id="inAjukan" value="{{ old('extra_data.konsep.ajukan', $konsep['ajukan'] ?? '') }}" class="form-control">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Deadline</label>
                                <input type="date" name="extra_data[konsep][deadline]" id="inDeadline" value="{{ old('extra_data.konsep.deadline', $konsep['deadline'] ?? '') }}" class="form-control">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Jumlah Lembar</label>
                                <input type="number" min="1" name="extra_data[konsep][jumlah_lembar]" id="inJmlLembar" value="{{ old('extra_data.konsep.jumlah_lembar', $konsep['jumlah_lembar'] ?? 1) }}" class="form-control">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Konsep Dikembalikan Kepada</label>
                            <input type="text" name="extra_data[konsep][kembali]" id="inKembali" value="{{ old('extra_data.konsep.kembali', $konsep['kembali'] ?? 'Subbag Umum & TU') }}" class="form-control">
                        </div>
                    </details>
                </div>
            </div>

            <!-- CARD 5: SUSUNAN ACARA TABEL (OPSIONAL) -->
            <div class="form-card">
                <div class="form-card-head" style="justify-content: space-between;">
                    <span>5. Lampiran Tabel Susunan Acara (Opsional)</span>
                    <button type="button" id="btnAddScheduleRow" class="btn-mini">+ Tambah Sesi</button>
                </div>
                <div class="form-card-body">
                    <div id="scheduleRowsContainer">
                        @foreach($scheduleRows as $sr)
                            <div class="grid-3 schedule-row" style="margin-bottom:8px;">
                                <input type="text" class="form-control sc-waktu" value="{{ $sr['waktu'] ?? '' }}" placeholder="Waktu">
                                <input type="text" class="form-control sc-acara" value="{{ $sr['acara'] ?? '' }}" placeholder="Kegiatan / Acara">
                                <div style="display:flex; gap:4px;">
                                    <input type="text" class="form-control sc-pic" value="{{ $sr['pic'] ?? '' }}" placeholder="Narasumber / PIC">
                                    <button type="button" style="border:1px solid #fca5a5; background:#fff1f2; color:#b91c1c; border-radius:4px; padding:0 8px; cursor:pointer;" onclick="removeScheduleRow(this)">&times;</button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <label style="font-size:12px; color:#475569; display:flex; align-items:center; gap:6px; cursor:pointer;">
                        <input type="hidden" name="extra_data[show_schedule]" value="0">
                        <input type="checkbox" name="extra_data[show_schedule]" value="1" id="chkShowSchedule" {{ $showScheduleVal ? 'checked' : '' }}>
                        Tampilkan tabel susunan acara pada surat
                    </label>
                </div>
            </div>

            <!-- SUBMIT BUTTONS -->
            <div style="display:flex; gap:12px; margin-bottom: 24px;">
                @if($isEdit)
                    <a href="{{ route('surat.undangan.show', $surat->id) }}" class="btn-quick-create" style="background:#f1f5f9; color:#334155; justify-content:center; padding:12px 18px;">Batal</a>
                @endif
                <button type="submit" class="btn-quick-create" style="flex:1; justify-content:center; padding:12px; font-size:14px;">
                    {{ $isEdit ? 'Simpan Perubahan Surat' : 'Simpan ke Database Arsip' }}
                </button>
            </div>
        </form>
    </div>

    <!-- RIGHT PANE: LIVE A4 PAPER PREVIEW -->
    <div class="preview-pane">
        <div class="preview-header-bar">
            <div style="display:flex; align-items:center; gap:8px;">
                <span style="font-size:12.5px; font-weight:700; color:var(--pu-navy);">Lembar:</span>
                <select id="selPreviewPage" style="padding:6px 10px; border-radius:6px; border:1px solid #cbd5e1; font-size:12.5px; font-weight:600; background:#f8fafc; color:#1e293b; outline:none; cursor:pointer;">
                    <option value="asli">Lembar 1: Asli Surat Undangan</option>
                    <option value="paraf">Lembar 2: Lembar Paraf Koordinasi</option>
                    <option value="lampiran">Lembar 3: Lampiran I (Daftar Penerima)</option>
                    <option value="konsep">Lembar 4: Konsep Naskah Dinas</option>
                </select>
            </div>

            <div style="display:flex; gap:6px; flex-wrap:wrap;">
                <button type="button" id="btnExportDocx" class="btn-toolbar-sm" style="background:#0284c7; color:#ffffff;">Unduh Word (.docx)</button>
                <button type="button" onclick="printActiveSheet()" class="btn-toolbar-sm" style="background:var(--pu-navy); color:#ffffff;">Cetak PDF</button>
                <button type="button" onclick="printAllSheets()" class="btn-toolbar-sm" style="background:#334155; color:#ffffff;">Cetak Semua Lembar</button>
            </div>
        </div>

        <div class="preview-scroll-wrapper">
            <!-- 1. VIEW LEMBAR ASLI & PARAF -->
            <div class="sheet-a4 printable-sheet" id="viewSheetAsli">
                <table class="kop-table">
                    <tr>
                        <td class="kop-logo-cell"><img src="{{ asset('images/logo_pu.png') }}" alt="Logo Kementerian PU" class="kop-logo-img"></td>
                        <td class="kop-text-cell">
                            <div class="kop-line-1" id="pvKop1">{{ $kop['kementerian'] }}</div>
                            <div class="kop-line-2" id="pvKop2">{{ $kop['ditjen'] }}</div>
                            <div id="pvKop3Wrap"><div class="kop-line-3">{{ $kop['balai'] }}</div></div>
                            <div class="kop-line-address" id="pvKopAddr">Jl. A. Sood Nomor 06 Pontianak 78121, Telepon (0561) 734866, Surel bwsk1pontianak@pu.go.id</div>
                        </td>
                    </tr>
                </table>

                <table class="letter-meta-table">
                    <tr>
                        <td style="width: 80px;">Nomor</td>
                        <td style="width: 15px;">:</td>
                        <td id="pvNomor"></td>
                        <td style="text-align: right;" id="pvTanggal"></td>
                    </tr>
                    <tr><td>Sifat</td><td>:</td><td id="pvSifat"></td><td></td></tr>
                    <tr><td>Lampiran</td><td>:</td><td id="pvLampiran"></td><td></td></tr>
                    <tr><td>Hal</td><td>:</td><td id="pvHal" style="font-weight: bold;"></td><td></td></tr>
                </table>

                <div class="undangan-tujuan-box" id="pvYthWrap">
                    <div>Yth.</div>
                    <ol class="undangan-list" id="pvListPenerima"></ol>
                    <div id="pvTerlampirText" style="display:none; margin-top:2px;">(Daftar Terlampir)</div>
                    <div style="margin-top: 4px;">di -</div>
                    <div style="padding-left: 20px; font-weight: bold;" id="pvDiTempat">Tempat</div>
                </div>

                <!-- ISI DINAMIS RENDERED HERE -->
                <div id="pvDynamicBody"></div>

                <div id="pvScheduleBox" style="margin: 12px 0; display:none;">
                    <table class="schedule-table-preview" id="pvScheduleTable">
                        <thead>
                            <tr><th style="width:140px;">Waktu</th><th>Agenda / Acara</th><th style="width:180px;">Narasumber / PIC</th></tr>
                        </thead>
                        <tbody id="pvScheduleTbody"></tbody>
                    </table>
                </div>

                <table class="ttd-paraf-grid">
                    <tr>
                        <td style="width: 48%; vertical-align: bottom;">
                            <div id="pvParafHierarkiBox" style="display:none; margin-bottom: 10px;">
                                <div class="paraf-mini-box">
                                    <div id="pvParafJab1">Kasubbag Umum &amp; TU</div>
                                    <div id="pvParafVal1" style="font-weight:700; margin-top:26px;">Iswandi, S.E., S.T.</div>
                                </div>
                            </div>
                        </td>
                        <td style="width: 52%; text-align: center; vertical-align: top;">
                            <div id="pvTtdJabatan"></div>
                            <div style="height: 76px;"></div>
                            <div id="pvTtdNama" style="font-weight: bold;"></div>
                            <div id="pvTtdNip" style="display:none;"></div>
                        </td>
                    </tr>
                </table>

                <div id="pvTembusanBox" style="display:none; margin-top: 22px; font-size: 13.5px;">
                    <div>Tembusan:</div>
                    <ol id="pvTembusanList" style="padding-left: 20px; margin-top: 4px; line-height: 1.45;"></ol>
                </div>
            </div>

            <!-- 2. VIEW LEMBAR LAMPIRAN I -->
            <div class="sheet-a4 printable-sheet" id="viewSheetLampiran" style="display:none;">
                <div style="margin-left: 50%; font-size: 13.5px; line-height: 1.4; margin-bottom: 24px;">
                    <div>Lampiran I Surat <span id="pvLpJabHeader"></span></div>
                    <div>Nomor : <span id="pvLpNomor"></span></div>
                    <div>Tanggal : <span id="pvLpTanggal"></span></div>
                </div>
                <div style="text-align: center; font-weight: bold; font-size: 15px; margin: 24px 0 18px 0;">DAFTAR UNDANGAN</div>
                <ol id="pvLpListContainer" style="line-height: 1.75; font-size: 14px; padding-left: 28px;"></ol>
                <div style="width: 320px; margin-left: auto; text-align: center; margin-top: 50px; font-size: 14px;">
                    <div id="pvLpTtdJabatan"></div>
                    <div style="height: 72px;"></div>
                    <div id="pvLpTtdNama" style="font-weight: bold;"></div>
                    <div id="pvLpTtdNip" style="display:none;"></div>
                </div>
            </div>

            <!-- 3. VIEW LEMBAR KONSEP NASKAH DINAS -->
            <div class="sheet-a4 printable-sheet" id="viewSheetKonsep" style="display:none;">
                <table style="width:100%; border-collapse:collapse; margin-bottom:10px;">
                    <tr>
                        <td style="width:78%; vertical-align:bottom;">
                            <table class="kop-table" style="margin-bottom:0;">
                                <tr>
                                    <td class="kop-logo-cell" style="width:70px;"><img src="{{ asset('images/logo_pu.png') }}" alt="Logo PU" style="width:64px; height:64px; object-fit:contain;"></td>
                                    <td class="kop-text-cell">
                                        <div style="font-size:14px;">{{ $kop['kementerian'] }}</div>
                                        <div style="font-size:10.5px;">{{ $kop['ditjen'] }}</div>
                                        <div style="font-size:8.5px; font-weight:700;">BALAI WILAYAH SUNGAI KALIMANTAN I PONTIANAK</div>
                                    </td>
                                </tr>
                            </table>
                        </td>
                        <td style="width:22%; vertical-align:bottom; padding-left:10px; font-size:11px;">
                            <div>DEADLINE:</div>
                            <div id="pvKpDeadline" style="font-weight:700;">-</div>
                        </td>
                    </tr>
                </table>

                <table class="konsep-table">
                    <tr>
                        <td rowspan="2" style="width:27%;">Konsep ini setelah suratnya dikirim harap dikembalikan kepada:<br><br><strong id="pvKpKembali"></strong></td>
                        <td rowspan="2" style="width:23%; text-align:center;">Konfirmasi / paraf / tanggal<br><br><span id="pvKpKonfJab"></span><br><br><strong id="pvKpKonfNama"></strong></td>
                        <td rowspan="2" style="width:23%; text-align:center;">Pemeriksa Akhir<br>Paraf &amp; Tanggal<br><br><span id="pvKpPemJab"></span><br><br><strong id="pvKpPemNama"></strong></td>
                        <td style="width:27%; height:65px;">Supaya diajukan lagi tanggal<br><br><div style="text-align:center; font-weight:700;" id="pvKpAjukan">…………………………</div></td>
                    </tr>
                    <tr>
                        <td style="height:60px; text-align:center;">Ditetapkan:<br><br><strong id="pvKpTetapNama"></strong></td>
                    </tr>
                    <tr>
                        <td>Jumlah Lembar : <strong id="pvKpJmlLembar"></strong></td>
                        <td colspan="3" rowspan="4" style="font-size:11px; line-height:1.45;">
                            <div style="display:flex; justify-content:space-between;"><span>Nomor : <strong id="pvKpNomor"></strong></span><span id="pvKpTglMini"></span></div>
                            <div>Hal : <strong id="pvKpHal"></strong></div>
                            <div style="margin-top:6px;">Yth. <span id="pvKpYthMini"></span></div>
                            <div id="pvKpMiniBody" style="margin-top:6px;"></div>
                            <div style="margin-top:14px; text-align:right; padding-right:20px;">
                                <div id="pvKpTtdJab"></div><div style="height:32px;"></div><strong id="pvKpTtdNama"></strong>
                            </div>
                        </td>
                    </tr>
                    <tr><td>No. Kode Klasifikasi :<br><div style="text-align:center; font-weight:700; margin-top:4px;" id="pvKpKlasifikasi"></div></td></tr>
                    <tr><td style="height:85px;">Konsep/Penulis Redaksi dari:<br><div style="text-align:center; margin-top:8px;" id="pvKpPenJab"></div><div style="text-align:center; font-weight:700; margin-top:16px;" id="pvKpPenNama"></div></td></tr>
                    <tr><td style="height:85px;">Diketik Oleh :<br><br><div style="text-align:center; font-weight:700;" id="pvKpKetikNama"></div></td></tr>
                </table>
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
    const KOP_PRESETS = @json($kopPresets);
    const KOP_SIGNERS = {
        balai:       { jab: 'Plt. Kepala Balai Wilayah Sungai Kalimantan I Pontianak', nama: 'Donni Saputra, S.T, M.Si., M.T.', nip: '197611012005021003' },
        satker_op:   { jab: 'Kepala Satuan Kerja Operasi dan Pemeliharaan SDA Kalimantan I', nama: 'Kepala Satker O&P SDA Kalimantan I', nip: '' },
        satker_pjpa: { jab: 'Kepala Satuan Kerja SNVT Pelaksanaan Jaringan Pemanfaatan Air Kalimantan I', nama: 'Kepala Satker SNVT PJPA Kalimantan I', nip: '' },
        satker_pjsa: { jab: 'Kepala Satuan Kerja SNVT Pelaksanaan Jaringan Sumber Air Kalimantan I', nama: 'Kepala Satker SNVT PJSA Kalimantan I', nip: '' },
        satker_bws:  { jab: 'Kepala Satuan Kerja Balai Wilayah Sungai Kalimantan I Pontianak', nama: 'Donni Saputra, S.T, M.Si., M.T.', nip: '197611012005021003' }
    };

    let bodySections = @json($bodySections);

    const el = id => document.getElementById(id);
    function escHtml(s) {
        return String(s == null ? '' : s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
    }

    /* ---------- DYNAMIC BODY BUILDER ---------- */
    function renderBodyBuilder() {
        const container = el('bodySectionsContainer');
        const n = bodySections.length;
        container.innerHTML = bodySections.map((sec, i) => {
            let inner = '';
            if (sec.type === 'points') {
                const itemsHtml = (sec.items || []).map((it, k) => `
                    <div class="rin-row" data-r="${k}">
                        <input type="text" class="form-control" data-rf="label" value="${escHtml(it.label)}" placeholder="Label">
                        <textarea class="form-control" data-rf="value" rows="${Math.max(1, String(it.value||'').split('\n').length)}" placeholder="Isi">${escHtml(it.value)}</textarea>
                        <button type="button" class="sec-head-btn del" data-rdel title="Hapus">&times;</button>
                    </div>
                `).join('');
                inner = itemsHtml + `<button type="button" class="btn-mini" data-radd>+ Baris Poin</button>`;
            } else {
                inner = `
                    <textarea class="form-control" data-sf="text" rows="3" placeholder="Tulis isi paragraf...">${escHtml(sec.text)}</textarea>
                    <label style="font-size:12px; display:flex; align-items:center; gap:6px; margin-top:6px; cursor:pointer;">
                        <input type="checkbox" data-sf="indent" ${sec.indent !== false ? 'checked' : ''}> Baris pertama menjorok (indent)
                    </label>
                `;
            }
            return `
                <div class="sec-box" data-s="${i}">
                    <div class="sec-head">
                        <span style="font-weight:700; font-size:12px; color:#334155;">Bagian ${i + 1}</span>
                        <select data-sf="type">
                            <option value="paragraph" ${sec.type !== 'points' ? 'selected' : ''}>Paragraf</option>
                            <option value="points" ${sec.type === 'points' ? 'selected' : ''}>Poin (Label : Isi)</option>
                        </select>
                        <span style="flex:1;"></span>
                        <button type="button" class="sec-head-btn" data-sop="up" ${i === 0 ? 'disabled' : ''}>&#9650;</button>
                        <button type="button" class="sec-head-btn" data-sop="down" ${i === n - 1 ? 'disabled' : ''}>&#9660;</button>
                        <button type="button" class="sec-head-btn del" data-sop="del">&times;</button>
                    </div>
                    <div class="sec-body">${inner}</div>
                </div>
            `;
        }).join('');
        syncDynamicBodyPreview();
    }

    function syncDynamicBodyPreview() {
        el('inBodyJson').value = JSON.stringify(bodySections);

        // Sinkronkan kolom tabel DB dari bagian poin pertama agar Dashboard & Arsip tetap terisi
        const ptSec = bodySections.find(s => s.type === 'points');
        if (ptSec && Array.isArray(ptSec.items)) {
            ptSec.items.forEach(it => {
                const lbl = String(it.label || '').toLowerCase();
                if (lbl.includes('hari') || lbl.includes('tanggal')) el('inHariTanggalHidden').value = it.value || '';
                else if (lbl.includes('jam') || lbl.includes('waktu')) el('inWaktuHidden').value = it.value || '';
                else if (lbl.includes('tempat')) el('inTempatHidden').value = it.value || '';
                else if (lbl.includes('agenda') || lbl.includes('acara')) el('inAgendaHidden').value = it.value || '';
            });
        }

        let html = '';
        let miniHtml = '';
        bodySections.forEach(sec => {
            if (sec.type === 'points') {
                let rows = '';
                (sec.items || []).forEach(it => {
                    if ((it.label || '').trim() || (it.value || '').trim()) {
                        const valHtml = escHtml(it.value).replace(/\n/g, '<br>');
                        rows += `<tr><td style="width:140px;">${escHtml(it.label)}</td><td style="width:14px;">:</td><td>${valHtml}</td></tr>`;
                        miniHtml += `<div>${escHtml(it.label)} : ${escHtml(it.value)}</div>`;
                    }
                });
                if (rows) html += `<table class="pv-points-table">${rows}</table>`;
            } else {
                const paras = String(sec.text || '').split(/\n\s*\n/).map(p => p.trim()).filter(Boolean);
                paras.forEach(p => {
                    const ind = sec.indent !== false ? 'text-indent: 36px;' : '';
                    html += `<div style="text-align: justify; ${ind} margin-top: 12px;">${escHtml(p)}</div>`;
                    miniHtml += `<div style="text-align:justify; margin-top:4px;">${escHtml(p)}</div>`;
                });
            }
        });
        el('pvDynamicBody').innerHTML = html;
        el('pvKpMiniBody').innerHTML = miniHtml;
    }

    const bodyContainer = el('bodySectionsContainer');
    bodyContainer.addEventListener('input', e => {
        const box = e.target.closest('.sec-box'); if (!box) return;
        const sec = bodySections[+box.dataset.s];
        const sf = e.target.dataset.sf, rf = e.target.dataset.rf;
        if (sf === 'text') sec.text = e.target.value;
        else if (sf === 'indent') sec.indent = e.target.checked;
        else if (rf) sec.items[+e.target.closest('.rin-row').dataset.r][rf] = e.target.value;
        syncDynamicBodyPreview();
    });
    bodyContainer.addEventListener('change', e => {
        if (e.target.dataset.sf !== 'type') return;
        const sec = bodySections[+e.target.closest('.sec-box').dataset.s];
        sec.type = e.target.value;
        if (sec.type === 'points' && (!sec.items || !sec.items.length)) sec.items = [{label:'', value:''}];
        renderBodyBuilder();
    });
    bodyContainer.addEventListener('click', e => {
        const box = e.target.closest('.sec-box'); if (!box) return;
        const i = +box.dataset.s, btn = e.target.closest('button'); if (!btn) return;
        const op = btn.dataset.sop;
        if (op === 'up' && i > 0) bodySections.splice(i - 1, 0, bodySections.splice(i, 1)[0]);
        else if (op === 'down' && i < bodySections.length - 1) bodySections.splice(i + 1, 0, bodySections.splice(i, 1)[0]);
        else if (op === 'del') bodySections.splice(i, 1);
        else if (btn.hasAttribute('data-radd')) bodySections[i].items.push({label:'', value:''});
        else if (btn.hasAttribute('data-rdel')) bodySections[i].items.splice(+btn.closest('.rin-row').dataset.r, 1);
        else return;
        renderBodyBuilder();
    });

    el('btnAddPara').addEventListener('click', () => {
        bodySections.push({ type: 'paragraph', indent: true, text: '', items: [] });
        renderBodyBuilder();
    });
    el('btnAddPoints').addEventListener('click', () => {
        bodySections.push({ type: 'points', indent: true, text: '', items: [{label:'Hari / Tanggal', value:''}] });
        renderBodyBuilder();
    });

    /* ---------- PENERIMA LIST & QUICK PICKER ---------- */
    function addPenerimaRow(val = '') {
        const div = document.createElement('div');
        div.className = 'form-group';
        div.style.cssText = 'display:flex; gap:5px; margin-bottom:6px;';
        div.innerHTML = `
            <input type="text" name="penerimas[]" class="form-control in-penerima" value="${escHtml(val)}" placeholder="Nama instansi / jabatan penerima...">
            <button type="button" class="btn-mini" onclick="movePenerima(this, -1)" title="Naik">&#9650;</button>
            <button type="button" class="btn-mini" onclick="movePenerima(this, 1)" title="Turun">&#9660;</button>
            <button type="button" style="border:1px solid #fca5a5; background:#fff1f2; color:#b91c1c; border-radius:6px; padding:0 9px; cursor:pointer;" onclick="this.parentElement.remove(); syncPenerimaList();">&times;</button>
        `;
        el('penerimaContainer').appendChild(div);
        div.querySelector('input').addEventListener('input', syncPenerimaList);
        syncPenerimaList();
    }

    window.movePenerima = function(btn, dir) {
        const row = btn.parentElement;
        const parent = row.parentElement;
        if (dir < 0 && row.previousElementSibling) parent.insertBefore(row, row.previousElementSibling);
        else if (dir > 0 && row.nextElementSibling) parent.insertBefore(row.nextElementSibling, row);
        syncPenerimaList();
    };

    el('btnAddPenerima').addEventListener('click', () => addPenerimaRow(''));
    el('btnSortPenerima').addEventListener('click', () => {
        const vals = Array.from(document.querySelectorAll('.in-penerima')).map(i => i.value.trim()).filter(Boolean);
        vals.sort((a, b) => a.localeCompare(b, 'id', {sensitivity:'base', numeric:true}));
        el('penerimaContainer').innerHTML = '';
        vals.forEach(v => addPenerimaRow(v));
    });
    el('btnRevPenerima').addEventListener('click', () => {
        const vals = Array.from(document.querySelectorAll('.in-penerima')).map(i => i.value.trim()).filter(Boolean).reverse();
        el('penerimaContainer').innerHTML = '';
        vals.forEach(v => addPenerimaRow(v));
    });
    el('pickerAddPenerima').addEventListener('change', function() {
        const opt = Array.from(document.querySelectorAll('#dl-pegawai-roles option')).find(o => o.value === this.value);
        if (opt) {
            addPenerimaRow(opt.getAttribute('data-jab') || this.value);
            this.value = '';
        }
    });

    function syncPenerimaList() {
        const inputs = document.querySelectorAll('.in-penerima');
        const pvList = el('pvListPenerima');
        const pvLpList = el('pvLpListContainer');
        const terlampirText = el('pvTerlampirText');
        const isTerlampir = el('inYthMode').value === 'terlampir';

        pvList.innerHTML = '';
        pvLpList.innerHTML = '';
        const names = [];
        inputs.forEach(inp => {
            if (inp.value.trim()) {
                names.push(inp.value.trim());
                const li = document.createElement('li'); li.innerText = inp.value.trim(); pvList.appendChild(li);
                const li2 = document.createElement('li'); li2.innerText = inp.value.trim(); pvLpList.appendChild(li2);
            }
        });
        pvList.style.display = isTerlampir ? 'none' : 'block';
        terlampirText.style.display = isTerlampir ? 'block' : 'none';
        el('pvKpYthMini').innerText = isTerlampir ? '(Daftar Terlampir)' : (names[0] || '');
    }

    /* ---------- KOP, META, PARAF, KONSEP ---------- */
    function updateKopPreview() {
        const preset = KOP_PRESETS[el('inKopType').value] || KOP_PRESETS.balai;
        el('pvKop1').innerText = preset.kementerian;
        el('pvKop2').innerText = preset.ditjen;
        const wrap = el('pvKop3Wrap'); wrap.innerHTML = '';
        (preset.lines3 || []).forEach(line => {
            const d = document.createElement('div'); d.className = 'kop-line-3'; d.innerText = line; wrap.appendChild(d);
        });
        el('pvKopAddr').innerText = preset.alamat;
    }

    function syncAllFields() {
        el('pvNomor').innerText = el('inNomor').value;
        el('pvLpNomor').innerText = el('inNomor').value;
        el('pvKpNomor').innerText = el('inNomor').value;
        el('pvKpKlasifikasi').innerText = el('inNomor').value;
        el('pvSifat').innerText = el('inSifat').value;
        el('pvLampiran').innerText = el('inLampiran').value;
        el('pvHal').innerText = el('inHal').value;
        el('pvKpHal').innerText = el('inHal').value;
        el('pvDiTempat').innerText = el('inDiTempat').value || 'Tempat';

        const val = el('inTanggalSurat').value;
        const p = val ? val.split('-') : [];
        const d = p.length === 3 ? new Date(+p[0], +p[1] - 1, +p[2]) : new Date();
        const months = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
        const formatted = d.getDate() + ' ' + months[d.getMonth()] + ' ' + d.getFullYear();
        const fullDate = (el('inKota').value ? el('inKota').value + ', ' : '') + formatted;
        el('pvTanggal').innerText = fullDate;
        el('pvLpTanggal').innerText = formatted;
        el('pvKpTglMini').innerText = fullDate;

        const gapPx = el('inGapYth').value === '120' ? '8px' : (el('inGapYth').value === '480' ? '26px' : '14px');
        el('pvYthWrap').style.marginTop = gapPx;

        const showNip = el('chkShowNip').checked;
        el('pvTtdJabatan').innerText = el('inTtdJabatan').value;
        el('pvLpJabHeader').innerText = el('inTtdJabatan').value;
        el('pvLpTtdJabatan').innerText = el('inTtdJabatan').value;
        el('pvKpTtdJab').innerText = el('inTtdJabatan').value;
        el('pvTtdNama').innerText = el('inTtdNama').value;
        el('pvLpTtdNama').innerText = el('inTtdNama').value;
        el('pvKpTtdNama').innerText = el('inTtdNama').value;
        const nipVal = el('inTtdNip').value ? 'NIP. ' + el('inTtdNip').value : '';
        el('pvTtdNip').innerText = nipVal;
        el('pvLpTtdNip').innerText = nipVal;
        el('pvTtdNip').style.display = (showNip && nipVal) ? 'block' : 'none';
        el('pvLpTtdNip').style.display = (showNip && nipVal) ? 'block' : 'none';

        el('pvParafJab1').innerText = el('inKonfJabParaf').value.trim() || el('inKonfJab').value || 'Kasubbag Umum & TU';
        el('pvParafVal1').innerText = el('inKonfNama').value || '';
        el('pvKpKonfJab').innerText = el('inKonfJab').value || '';
        el('pvKpKonfNama').innerText = el('inKonfNama').value || '';
        el('pvKpPemJab').innerText = el('inPemJab').value || '';
        el('pvKpPemNama').innerText = el('inPemNama').value || '';
        el('pvKpPenJab').innerText = el('inPenJab').value || '';
        el('pvKpPenNama').innerText = el('inPenNama').value || '';
        el('pvKpKetikNama').innerText = el('inKetikNama').value || '';
        el('pvKpKembali').innerText = el('inKembali').value || '';
        el('pvKpDeadline').innerText = el('inDeadline').value ? DocGen.fmtTgl(el('inDeadline').value) : '-';
        el('pvKpAjukan').innerText = el('inAjukan').value ? DocGen.fmtTgl(el('inAjukan').value) : '…………………………';
        const jml = parseInt(el('inJmlLembar').value || '1', 10);
        el('pvKpJmlLembar').innerText = jml + ' (' + DocGen.terbilang(jml) + ')';
        el('pvKpTetapNama').innerText = el('inTetapNama').value.trim() || el('inTtdNama').value;

        const tbLines = (el('inTembusan').value || '').split('\n').map(s => s.trim()).filter(Boolean);
        el('pvTembusanBox').style.display = tbLines.length ? 'block' : 'none';
        el('pvTembusanList').innerHTML = tbLines.map(l => `<li>${escHtml(l)}</li>`).join('');

        updateKopPreview();
        syncPenerimaList();
    }

    el('inKopType').addEventListener('change', function() {
        const curJab = el('inTtdJabatan').value.trim();
        const isPreset = Object.values(KOP_SIGNERS).some(s => s.jab === curJab);
        if (isPreset || !curJab) {
            const s = KOP_SIGNERS[this.value] || KOP_SIGNERS.balai;
            el('inTtdJabatan').value = s.jab;
            el('inTtdNama').value = s.nama;
            el('inTtdNip').value = s.nip;
        }
        syncAllFields();
    });

    function syncSchedulePreview() {
        const rows = document.querySelectorAll('.schedule-row');
        const tbody = el('pvScheduleTbody');
        tbody.innerHTML = '';
        const jsonArr = [];
        let hasData = false;
        rows.forEach(r => {
            const w = r.querySelector('.sc-waktu').value.trim();
            const a = r.querySelector('.sc-acara').value.trim();
            const p = r.querySelector('.sc-pic').value.trim();
            if (w || a || p) {
                hasData = true;
                jsonArr.push({ waktu: w, acara: a, pic: p });
                const tr = document.createElement('tr');
                tr.innerHTML = `<td style="text-align:center;">${escHtml(w)}</td><td>${escHtml(a)}</td><td>${escHtml(p)}</td>`;
                tbody.appendChild(tr);
            }
        });
        el('inScheduleJson').value = JSON.stringify(jsonArr);
        el('pvScheduleBox').style.display = (el('chkShowSchedule').checked && hasData) ? 'block' : 'none';
    }

    window.removeScheduleRow = function(btn) { btn.closest('.schedule-row').remove(); syncSchedulePreview(); };
    el('btnAddScheduleRow').addEventListener('click', function() {
        const div = document.createElement('div');
        div.className = 'grid-3 schedule-row'; div.style.marginBottom = '8px';
        div.innerHTML = `<input type="text" class="form-control sc-waktu" placeholder="Waktu"><input type="text" class="form-control sc-acara" placeholder="Kegiatan / Acara"><div style="display:flex; gap:4px;"><input type="text" class="form-control sc-pic" placeholder="Narasumber / PIC"><button type="button" style="border:1px solid #fca5a5; background:#fff1f2; color:#b91c1c; border-radius:4px; padding:0 8px; cursor:pointer;" onclick="removeScheduleRow(this)">&times;</button></div>`;
        el('scheduleRowsContainer').appendChild(div);
        div.querySelectorAll('input').forEach(inp => inp.addEventListener('input', syncSchedulePreview));
    });

    el('selPreviewPage').addEventListener('change', function() {
        const val = this.value;
        el('viewSheetAsli').style.display = (val === 'asli' || val === 'paraf') ? 'block' : 'none';
        el('viewSheetLampiran').style.display = (val === 'lampiran') ? 'block' : 'none';
        el('viewSheetKonsep').style.display = (val === 'konsep') ? 'block' : 'none';
        el('pvParafHierarkiBox').style.display = (val === 'paraf') ? 'block' : 'none';
    });

    el('selectPegawaiDb').addEventListener('change', function() {
        const opt = this.options[this.selectedIndex];
        if (opt.value) {
            el('inTtdNama').value = opt.getAttribute('data-nama') || '';
            el('inTtdNip').value = opt.getAttribute('data-nip') || '';
            el('inTtdJabatan').value = opt.getAttribute('data-jabatan') || '';
            syncAllFields();
        }
    });

    ['inYthMode', 'inGapYth', 'inUrutan', 'chkShowNip'].forEach(id => el(id).addEventListener('change', syncAllFields));
    ['inNomor','inSifat','inLampiran','inHal','inKota','inTanggalSurat','inDiTempat','inTembusan',
     'inTtdNama','inTtdNip','inTtdJabatan','inKonfJab','inKonfNama','inKonfJabParaf','inPemJab','inPemNama',
     'inPenJab','inPenNama','inKetikNama','inTetapNama','inAjukan','inDeadline','inJmlLembar','inKembali']
        .forEach(id => el(id).addEventListener('input', syncAllFields));
    document.querySelectorAll('.in-penerima').forEach(inp => inp.addEventListener('input', syncPenerimaList));
    document.querySelectorAll('.schedule-row input').forEach(inp => inp.addEventListener('input', syncSchedulePreview));
    el('chkShowSchedule').addEventListener('change', syncSchedulePreview);
    el('formSurat').addEventListener('submit', () => { syncDynamicBodyPreview(); syncSchedulePreview(); });

    function printActiveSheet() { document.body.classList.remove('print-all-sheets'); window.print(); }
    function printAllSheets() {
        document.body.classList.add('print-all-sheets');
        window.print();
        setTimeout(() => document.body.classList.remove('print-all-sheets'), 500);
    }

    renderBodyBuilder();
    syncAllFields();
    syncSchedulePreview();

    el('btnExportDocx').addEventListener('click', function() {
        const btn = this, orig = btn.innerHTML;
        btn.innerHTML = 'Membuat .docx...'; btn.disabled = true;

        const penerimas = Array.from(document.querySelectorAll('.in-penerima')).map(i => i.value.trim()).filter(Boolean);
        const preset = KOP_PRESETS[el('inKopType').value] || KOP_PRESETS.balai;

        const docData = {
            mode: 'undangan',
            nomor: el('inNomor').value,
            sifat: el('inSifat').value,
            lampiran: parseInt(el('inLampiran').value, 10) || 0,
            hal: el('inHal').value,
            kota: el('inKota').value,
            tanggal: el('inTanggalSurat').value,
            ythMode: el('inYthMode').value,
            ythText: penerimas.join('\n'),
            daftarUndangan: penerimas.join('\n'),
            diTempat: el('inDiTempat').value || 'Tempat',
            tembusan: el('inTembusan').value,
            gapYth: el('inGapYth').value,
            urutan: el('inUrutan').value,
            konsepMarginTemplate: el('chkKonsepMargin').checked,
            ttd_jab: el('inTtdJabatan').value,
            ttd_nama: el('inTtdNama').value,
            ttd_nip: el('inTtdNip').value,
            showNip: el('chkShowNip').checked,
            konf_jab: el('inKonfJab').value,
            konf_nama: el('inKonfNama').value,
            konf_jabParaf: el('inKonfJabParaf').value,
            pem_jab: el('inPemJab').value,
            pem_nama: el('inPemNama').value,
            pen_jab: el('inPenJab').value,
            pen_nama: el('inPenNama').value,
            ketik_nama: el('inKetikNama').value,
            kembali: el('inKembali').value,
            ajukan: el('inAjukan').value,
            deadline: el('inDeadline').value,
            jumlahLembar: el('inJmlLembar').value,
            tetap_nama: el('inTetapNama').value.trim() || el('inTtdNama').value,
            body: bodySections,
            pages: {
                konsep: el('pgKonsep').checked,
                paraf: el('pgParaf').checked,
                asli: el('pgAsli').checked,
                lampiran: el('pgLampiran').checked && el('inYthMode').value === 'terlampir'
            },
            margin: {
                top: +el('inMcTop').value || 2.5,
                bottom: +el('inMcBottom').value || 2.5,
                left: +el('inMcLeft').value || 2.5,
                right: +el('inMcRight').value || 2.5
            },
            marginLampiran: {
                top: +el('inMlcTop').value || 1,
                bottom: +el('inMlcBottom').value || 1
            }
        };

        const kopSpec = { line1: preset.kementerian, line2: preset.ditjen, line3: preset.lines3, line4: preset.alamat };
        DocGen.build(docData, {
            kop: kopSpec, logo: { b64: typeof LOGO !== 'undefined' ? LOGO : '' },
            arialW: typeof ARIALW !== 'undefined' ? ARIALW : null, JSZip: JSZip, outType: 'blob'
        }).then(blob => {
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url; a.download = ('Surat_Undangan_' + (el('inHal').value || 'Dinas')).replace(/[^a-zA-Z0-9_-]/g, '_') + '.docx';
            document.body.appendChild(a); a.click(); document.body.removeChild(a); URL.revokeObjectURL(url);
            btn.innerHTML = orig; btn.disabled = false;
        }).catch(err => {
            alert('Ekspor Word gagal: ' + (err.message || err));
            btn.innerHTML = orig; btn.disabled = false;
        });
    });
</script>
@endpush
