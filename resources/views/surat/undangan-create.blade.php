@extends('layouts.app')

@php
    $isEdit = isset($surat);
    $src = $surat ?? ($duplicateFrom ?? null);
    $extra = $src ? ($src->extra_data ?? []) : [];

    $kopTypeVal = old('extra_data.kop_type', $extra['kop_type'] ?? 'balai');
    $activePreset = $kopPresets[$kopTypeVal] ?? $kopPresets['balai'];
    $defaultKopLines3 = implode("\n", (!empty($extra['kop_lines3']) && is_array($extra['kop_lines3'])) ? $extra['kop_lines3'] : ($activePreset['lines3'] ?? []));
    $defaultKopAlamat = !empty($extra['kop_alamat']) ? $extra['kop_alamat'] : ($activePreset['alamat'] ?? '');
    $kopLines3Val = old('extra_data.kop_lines3_text', $defaultKopLines3);
    $kopAlamatVal = old('extra_data.kop_alamat', $defaultKopAlamat);

    $urutanVal = old('extra_data.urutan', $extra['urutan'] ?? 'paraf-dulu');
    $pagesCfg = $extra['pages'] ?? ['konsep' => '0', 'paraf' => '0', 'asli' => '1', 'lampiran' => '1'];
    $pgKonsepVal = old('extra_data.pages.konsep', $pagesCfg['konsep'] ?? '0');
    $pgParafVal = old('extra_data.pages.paraf', $pagesCfg['paraf'] ?? '0');
    $pgAsliVal = old('extra_data.pages.asli', $pagesCfg['asli'] ?? '1');
    $pgLampiranVal = old('extra_data.pages.lampiran', $pagesCfg['lampiran'] ?? '1');

    $margin = $extra['margin'] ?? ['top' => 1.5, 'bottom' => 1.5, 'left' => 1.8, 'right' => 1.8, 'lamp_top' => 1, 'lamp_bottom' => 1];
    $konsepMarginVal = old('extra_data.konsep_margin', array_key_exists('konsep_margin', $extra) ? $extra['konsep_margin'] : '1');
    $gapYthVal = old('extra_data.gap_yth', $extra['gap_yth'] ?? '240');
    $showNipVal = old('extra_data.show_nip', $extra['show_nip'] ?? '0');

    $konsep = $extra['konsep'] ?? [];
    $tglSuratVal = old('tanggal_surat', $isEdit ? $surat->tanggal_surat->format('Y-m-d') : date('Y-m-d'));

    $rawLampiran = old('lampiran', $src ? $src->lampiran : '1');
    $lampiranNum = is_numeric($rawLampiran) ? (int) $rawLampiran : (preg_match('/^(\d+)/', (string) $rawLampiran, $mLamp) ? (int) $mLamp[1] : 0);

    $sifatVal = old('sifat', $src ? $src->sifat : 'Biasa');
    $ythModeVal = old('extra_data.yth_mode', $extra['yth_mode'] ?? 'terlampir');
    $diTempatVal = old('extra_data.di_tempat', $extra['di_tempat'] ?? 'Tempat');
    $tembusanVal = old('extra_data.tembusan', $extra['tembusan'] ?? '');

    $penerimaList = old('penerimas', ($src && $src->penerimas->count() > 0) ? $src->penerimas->pluck('nama_penerima')->toArray() : []);

    $defaultBodySections = $src
        ? [
            [
                'type' => 'paragraph',
                'indent' => true,
                'text' => 'Sehubungan dengan pelaksanaan kegiatan pada Balai Wilayah Sungai Kalimantan I Pontianak, dengan ini kami mengharapkan kehadiran Bapak/Ibu pada rapat yang akan dilaksanakan pada:',
                'items' => [],
            ],
            [
                'type' => 'points',
                'indent' => true,
                'text' => '',
                'items' => [
                    ['label' => 'Hari / Tanggal', 'value' => $src->hari_tanggal_acara ?? ''],
                    ['label' => 'Jam', 'value' => $src->waktu_acara ?? ''],
                    ['label' => 'Tempat', 'value' => $src->tempat ?? ''],
                    ['label' => 'Agenda', 'value' => $src->agenda ?? ''],
                ],
            ],
            [
                'type' => 'paragraph',
                'indent' => false,
                'text' => 'Demikian disampaikan, atas perhatian dan kehadirannya diucapkan terima kasih.',
                'items' => [],
            ],
        ]
        : [
            [
                'type' => 'paragraph',
                'indent' => true,
                'text' => '',
                'items' => [],
            ],
            [
                'type' => 'points',
                'indent' => true,
                'text' => '',
                'items' => [
                    ['label' => 'Hari / Tanggal', 'value' => ''],
                    ['label' => 'Jam', 'value' => ''],
                    ['label' => 'Tempat', 'value' => ''],
                    ['label' => 'Agenda', 'value' => ''],
                ],
            ],
            [
                'type' => 'paragraph',
                'indent' => false,
                'text' => 'Demikian disampaikan, atas perhatian dan kehadirannya diucapkan terima kasih.',
                'items' => [],
            ],
        ];

    $oldBodyJson = old('extra_data.body_json');
    $decodedOldBody = is_string($oldBodyJson) ? json_decode($oldBodyJson, true) : null;
    $bodySections = (is_array($decodedOldBody) && count($decodedOldBody) > 0)
        ? $decodedOldBody
        : ((!empty($extra['body']) && is_array($extra['body'])) ? $extra['body'] : $defaultBodySections);

    $pickerItems = [];
    foreach ($pegawais as $p) {
        $jab = $p->jabatan_definitif ?? $p->jabatan ?? '';
        $pickerItems[] = [
            'nama' => $p->nama_gelar,
            'nip' => $p->nip ?? '',
            'jab' => $jab,
            'unit' => $p->unit ?? '',
            'rangkap' => false,
        ];
        if (!empty($p->rangkap)) {
            $pickerItems[] = [
                'nama' => $p->nama_gelar,
                'nip' => $p->nip ?? '',
                'jab' => $p->rangkap_surat ?: $p->rangkap,
                'unit' => $p->unit ?? '',
                'rangkap' => true,
            ];
        }
    }
@endphp

@section('title', $isEdit ? 'Edit Surat Undangan' : 'Buat Surat Undangan')
@section('header_title', $isEdit ? 'Edit Surat Undangan Kedinasan' : 'Generator Surat Undangan Kedinasan')
@section('header_subtitle', 'Format Resmi Tata Naskah Dinas BWS Kalimantan I Pontianak')

@push('styles')
<style>
    .editor-layout {
        display: grid;
        grid-template-columns: minmax(430px, 520px) 1fr;
        gap: 22px;
        align-items: start;
    }

    /* Card style sesuai V5 HTML */
    .v5-card {
        background: var(--bg-surface);
        border: 1px solid #d9e0e6;
        border-radius: 14px;
        margin-bottom: 16px;
        box-shadow: 0 1px 4px rgba(15, 23, 42, 0.04);
    }
    .v5-card-head {
        padding: 13px 18px;
        border-bottom: 1px solid #edf0f3;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .v5-card-title {
        font-size: 13px;
        font-weight: 800;
        letter-spacing: 0.6px;
        color: #12487e;
        text-transform: uppercase;
    }
    .v5-card-badge {
        font-size: 12px;
        color: #64748b;
        font-weight: 500;
    }
    .v5-card-body {
        padding: 16px 18px 18px;
    }

    .form-group { margin-bottom: 14px; }
    .form-group:last-child { margin-bottom: 0; }
    .form-label {
        display: flex;
        align-items: center;
        gap: 6px;
        flex-wrap: wrap;
        font-size: 13px;
        font-weight: 600;
        color: #1e293b;
        margin-bottom: 6px;
    }
    .lbl-num {
        font-weight: 800;
        color: #12487e;
    }
    .lbl-pill {
        font-size: 11px;
        font-weight: 500;
        color: #475569;
        background: #f1f5f9;
        padding: 2px 8px;
        border-radius: 99px;
    }
    .form-hint {
        font-size: 12px;
        color: #64748b;
        margin-top: 5px;
        line-height: 1.45;
    }

    .form-control {
        width: 100%;
        padding: 8px 11px;
        border: 1px solid #cfd8e3;
        border-radius: 8px;
        font-size: 13.5px;
        font-family: inherit;
        background: #ffffff;
        color: #0f172a;
        outline: none;
        transition: border-color 0.15s, box-shadow 0.15s;
    }
    .form-control:focus {
        border-color: #12487e;
        box-shadow: 0 0 0 3px rgba(18, 72, 126, 0.12);
    }
    textarea.form-control { resize: vertical; }

    .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
    .grid-4 { display: grid; grid-template-columns: repeat(4, 1fr); gap: 8px; }

    /* Accordion "Ubah teks kop" */
    .v5-details {
        border: 1px solid #d9e0e6;
        border-radius: 10px;
        background: #f8fafc;
        margin: 10px 0 14px;
    }
    .v5-details summary {
        list-style: none;
        cursor: pointer;
        padding: 9px 13px;
        font-size: 13px;
        font-weight: 600;
        color: #1e293b;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .v5-details summary::-webkit-details-marker { display: none; }
    .v5-details summary::before {
        content: '▶';
        font-size: 10px;
        color: #334155;
        transition: transform 0.15s;
    }
    .v5-details[open] summary::before { transform: rotate(90deg); }
    .v5-details-body {
        padding: 10px 13px 13px;
        border-top: 1px solid #e2e8f0;
        background: #ffffff;
        border-radius: 0 0 10px 10px;
    }

    .check-row {
        display: flex;
        align-items: flex-start;
        gap: 8px;
        font-size: 13px;
        color: #1e293b;
        cursor: pointer;
        line-height: 1.45;
    }
    .check-row input[type="checkbox"] {
        margin-top: 3px;
        width: 15px;
        height: 15px;
        accent-color: #1d4ed8;
        flex-shrink: 0;
        cursor: pointer;
    }
    .pages-check-list {
        display: flex;
        flex-direction: column;
        gap: 6px;
        margin-top: 2px;
    }

    /* Grouped dashed box + Searchable Pegawai Picker */
    .v5-group-box {
        border: 1px dashed #cbd5e1;
        border-radius: 11px;
        padding: 10px;
        background: #fbfcfe;
        display: flex;
        flex-direction: column;
        gap: 8px;
    }
    .picker-wrap {
        position: relative;
    }
    .picker-input-row {
        position: relative;
        display: flex;
        align-items: center;
    }
    .picker-icon {
        position: absolute;
        left: 11px;
        color: #64748b;
        pointer-events: none;
        display: flex;
        align-items: center;
    }
    .picker-input {
        width: 100%;
        padding: 8px 28px 8px 32px;
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        font-size: 13px;
        font-family: inherit;
        background: #ffffff;
        color: #0f172a;
        outline: none;
    }
    .picker-input:focus {
        border-color: #12487e;
        box-shadow: 0 0 0 3px rgba(18, 72, 126, 0.1);
    }
    .picker-caret {
        position: absolute;
        right: 10px;
        font-size: 10px;
        color: #475569;
        pointer-events: none;
    }
    .picker-menu {
        display: none;
        position: absolute;
        top: calc(100% + 4px);
        left: 0;
        right: 0;
        max-height: 240px;
        overflow-y: auto;
        background: #ffffff;
        border: 1px solid #cbd5e1;
        border-radius: 10px;
        box-shadow: 0 12px 26px rgba(15, 23, 42, 0.16);
        z-index: 90;
        padding: 4px;
    }
    .picker-menu.open { display: block; }
    .picker-opt {
        padding: 7px 10px;
        border-radius: 7px;
        cursor: pointer;
        font-size: 12.5px;
        line-height: 1.35;
        border-bottom: 1px solid #f1f5f9;
    }
    .picker-opt:last-child { border-bottom: none; }
    .picker-opt:hover { background: #eff6ff; }
    .picker-opt-name { font-weight: 700; color: #0f172a; }
    .picker-opt-jab { font-size: 11.5px; color: #475569; margin-top: 1px; }
    .picker-opt-badge {
        display: inline-block;
        font-size: 10px;
        font-weight: 700;
        background: #fef3c7;
        color: #92400e;
        padding: 1px 5px;
        border-radius: 4px;
        margin-right: 4px;
    }
    .picker-empty {
        padding: 10px;
        font-size: 12px;
        color: #64748b;
        text-align: center;
    }

    /* Info banner Paraf Konsep */
    .paraf-info-banner {
        background: #eef4fa;
        border: 1px solid #d5e3f2;
        border-radius: 8px;
        padding: 9px 12px;
        font-size: 12.5px;
        color: #1e3a5f;
        margin-bottom: 12px;
        line-height: 1.45;
    }

    /* Recipient list (Yth.) */
    .yth-radio-row {
        display: flex;
        align-items: center;
        gap: 20px;
        margin-bottom: 10px;
        flex-wrap: wrap;
    }
    .yth-radio-label {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 13px;
        font-weight: 600;
        color: #0f172a;
        cursor: pointer;
    }
    .yth-radio-label input {
        width: 15px;
        height: 15px;
        accent-color: #1d4ed8;
        cursor: pointer;
    }
    .penerima-list-box {
        border: 1px solid #d9e0e6;
        border-radius: 9px;
        padding: 8px;
        background: #ffffff;
        margin-bottom: 8px;
    }
    .penerima-empty-msg {
        padding: 8px 6px;
        font-size: 12.5px;
        color: #64748b;
        line-height: 1.45;
    }
    .penerima-item {
        display: flex;
        align-items: center;
        gap: 5px;
        margin-bottom: 6px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 7px;
        padding: 4px 6px;
    }
    .penerima-item:last-child { margin-bottom: 0; }
    .penerima-item.dragging { opacity: 0.45; }
    .drag-handle {
        cursor: grab;
        color: #94a3b8;
        font-weight: 700;
        padding: 0 4px;
        user-select: none;
        font-size: 13px;
    }
    .penerima-item input {
        flex: 1;
        border: none;
        background: transparent;
        font-size: 13px;
        font-family: inherit;
        color: #0f172a;
        outline: none;
        padding: 3px 4px;
    }
    .btn-v5 {
        padding: 6px 12px;
        font-size: 12.5px;
        font-weight: 600;
        border: 1px solid #cbd5e1;
        background: #ffffff;
        color: #1e293b;
        border-radius: 8px;
        cursor: pointer;
        font-family: inherit;
        transition: background 0.15s;
    }
    .btn-v5:hover { background: #f1f5f9; }
    .btn-sq {
        border: 1px solid #cbd5e1;
        background: #ffffff;
        border-radius: 6px;
        width: 26px;
        height: 26px;
        cursor: pointer;
        font-size: 11px;
        color: #334155;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0;
        flex-shrink: 0;
    }
    .btn-sq:hover:not(:disabled) { background: #e2e8f0; }
    .btn-sq:disabled { opacity: 0.38; cursor: default; }
    .btn-sq.del { color: #dc2626; border-color: #fecaca; background: #fff5f5; }
    .btn-sq.del:hover { background: #fee2e2; }

    /* Dynamic Body Builder */
    .sec-box {
        border: 1px solid #d9e0e6;
        border-radius: 10px;
        background: #ffffff;
        margin-bottom: 10px;
        overflow: hidden;
    }
    .sec-head {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 7px 10px;
        border-bottom: 1px solid #edf0f3;
        background: #f8fafc;
    }
    .sec-head select {
        padding: 4px 8px;
        font-size: 12.5px;
        font-weight: 500;
        border-radius: 6px;
        border: 1px solid #cbd5e1;
        background: #ffffff;
        color: #0f172a;
    }
    .sec-body { padding: 10px; }
    .rin-row {
        display: grid;
        grid-template-columns: 125px 1fr 26px;
        gap: 6px;
        margin-bottom: 6px;
        align-items: start;
    }

    /* RIGHT PANE: A4 PREVIEW */
    .preview-pane { position: sticky; top: 86px; }
    .preview-header-bar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 12px;
        background: #ffffff;
        padding: 10px 14px;
        border: 1px solid var(--border);
        border-radius: var(--radius-md);
        box-shadow: var(--shadow-sm);
        flex-wrap: wrap;
        gap: 8px;
    }
    .btn-toolbar-sm {
        font-weight: 600;
        padding: 6px 12px;
        font-size: 12.5px;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        border-radius: 6px;
        border: none;
        cursor: pointer;
        text-decoration: none;
    }
    .preview-scroll-wrapper {
        background: #cbd5e1;
        border-radius: var(--radius-lg);
        padding: 24px;
        max-height: calc(100vh - 170px);
        overflow-y: auto;
        box-shadow: inset 0 2px 4px rgba(0,0,0,0.1);
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 20px;
    }

    .sheet-a4 {
        width: 794px;
        min-height: 1123px;
        background: #ffffff;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.15);
        padding: 48px 56px;
        font-family: Arial, Helvetica, sans-serif;
        color: #000000;
        font-size: 14.5px;
        line-height: 1.45;
        position: relative;
        zoom: 0.76;
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

    .ttd-paraf-grid { width: 100%; margin-top: 28px; border-collapse: collapse; font-size: 14.5px; }
    .ttd-paraf-grid td { vertical-align: top; }
    .paraf-mini-box {
        border: 1.5px solid #000;
        width: 165px;
        min-height: 78px;
        padding: 4px 6px;
        text-align: center;
        font-size: 10.5px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
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

@if($errors->any())
    <div style="background:#fef2f2; border:1px solid #fecaca; color:#b91c1c; padding:12px 16px; border-radius:10px; margin-bottom:16px; font-size:13px;">
        <strong>Periksa kembali isian formulir:</strong>
        <ul style="margin:4px 0 0 18px;">
            @foreach($errors->all() as $err)
                <li>{{ $err }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="editor-layout">
    <!-- LEFT PANE: FORM -->
    <div class="editor-form-col">
        <form method="POST" action="{{ $isEdit ? route('surat.undangan.update', $surat->id) : route('surat.undangan.store') }}" id="formSurat">
            @csrf
            @if($isEdit) @method('PUT') @endif
            <input type="hidden" name="extra_data[body_json]" id="inBodyJson" value="">
            <input type="hidden" name="hari_tanggal_acara" id="inHariTanggalHidden" value="{{ old('hari_tanggal_acara', $src ? $src->hari_tanggal_acara : '') }}">
            <input type="hidden" name="waktu_acara" id="inWaktuHidden" value="{{ old('waktu_acara', $src ? $src->waktu_acara : '') }}">
            <input type="hidden" name="tempat" id="inTempatHidden" value="{{ old('tempat', $src ? $src->tempat : '') }}">
            <input type="hidden" name="agenda" id="inAgendaHidden" value="{{ old('agenda', $src ? $src->agenda : '') }}">

            <!-- CARD 1: PENGATURAN DOKUMEN -->
            <div class="v5-card">
                <div class="v5-card-head">
                    <span class="v5-card-title">PENGATURAN DOKUMEN</span>
                </div>
                <div class="v5-card-body">
                    <div class="form-group">
                        <label class="form-label" for="inKopType">Kop surat</label>
                        <select name="extra_data[kop_type]" id="inKopType" class="form-control">
                            @foreach($kopPresets as $key => $preset)
                                <option value="{{ $key }}" {{ $kopTypeVal === $key ? 'selected' : '' }}>{{ $preset['label'] }}</option>
                            @endforeach
                        </select>
                    </div>

                    <details class="v5-details" id="detailsKopText">
                        <summary>Ubah teks kop</summary>
                        <div class="v5-details-body">
                            <div class="form-group">
                                <label class="form-label" for="inKopLines3">Baris nama instansi (di bawah Ditjen SDA)</label>
                                <textarea name="extra_data[kop_lines3_text]" id="inKopLines3" rows="2" class="form-control">{{ $kopLines3Val }}</textarea>
                                <div class="form-hint">Satu baris = satu baris pada kop surat.</div>
                            </div>
                            <div class="form-group">
                                <label class="form-label" for="inKopAlamat">Alamat &amp; kontak</label>
                                <input type="text" name="extra_data[kop_alamat]" id="inKopAlamat" value="{{ $kopAlamatVal }}" class="form-control">
                            </div>
                            <button type="button" class="btn-v5" id="btnResetKop">Reset teks kop ke bawaan</button>
                        </div>
                    </details>

                    <div class="grid-2 form-group">
                        <div>
                            <label class="form-label" for="inUrutan">Urutan halaman</label>
                            <select name="extra_data[urutan]" id="inUrutan" class="form-control">
                                <option value="paraf-dulu" {{ $urutanVal === 'paraf-dulu' ? 'selected' : '' }}>Konsep &rarr; Paraf &rarr; Asli</option>
                                <option value="asli-dulu" {{ $urutanVal === 'asli-dulu' ? 'selected' : '' }}>Konsep &rarr; Asli &rarr; Paraf</option>
                            </select>
                        </div>
                        <div>
                            <div class="form-label">Halaman yang dicetak</div>
                            <div class="pages-check-list">
                                <label class="check-row">
                                    <input type="hidden" name="extra_data[pages][konsep]" value="0">
                                    <input type="checkbox" name="extra_data[pages][konsep]" value="1" id="pgKonsep" {{ !empty($pgKonsepVal) ? 'checked' : '' }}>
                                    <span>Konsep</span>
                                </label>
                                <label class="check-row">
                                    <input type="hidden" name="extra_data[pages][paraf]" value="0">
                                    <input type="checkbox" name="extra_data[pages][paraf]" value="1" id="pgParaf" {{ !empty($pgParafVal) ? 'checked' : '' }}>
                                    <span>Paraf konsep</span>
                                </label>
                                <label class="check-row">
                                    <input type="hidden" name="extra_data[pages][asli]" value="0">
                                    <input type="checkbox" name="extra_data[pages][asli]" value="1" id="pgAsli" {{ !empty($pgAsliVal) ? 'checked' : '' }}>
                                    <span>Surat asli</span>
                                </label>
                                <label class="check-row">
                                    <input type="hidden" name="extra_data[pages][lampiran]" value="0">
                                    <input type="checkbox" name="extra_data[pages][lampiran]" value="1" id="pgLampiran" {{ !empty($pgLampiranVal) ? 'checked' : '' }}>
                                    <span>Lampiran daftar undangan</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="form-label">Margin surat asli &amp; paraf (cm) &mdash; kiri/kanan juga dipakai lampiran</div>
                        <div class="grid-4">
                            <div>
                                <div style="font-size:12px; color:#64748b; margin-bottom:4px;">Atas</div>
                                <input type="number" step="0.1" min="0.5" name="extra_data[margin][top]" id="inMcTop" value="{{ old('extra_data.margin.top', $margin['top'] ?? 2.5) }}" class="form-control">
                            </div>
                            <div>
                                <div style="font-size:12px; color:#64748b; margin-bottom:4px;">Bawah</div>
                                <input type="number" step="0.1" min="0.5" name="extra_data[margin][bottom]" id="inMcBottom" value="{{ old('extra_data.margin.bottom', $margin['bottom'] ?? 2.5) }}" class="form-control">
                            </div>
                            <div>
                                <div style="font-size:12px; color:#64748b; margin-bottom:4px;">Kiri</div>
                                <input type="number" step="0.1" min="0.5" name="extra_data[margin][left]" id="inMcLeft" value="{{ old('extra_data.margin.left', $margin['left'] ?? 2.5) }}" class="form-control">
                            </div>
                            <div>
                                <div style="font-size:12px; color:#64748b; margin-bottom:4px;">Kanan</div>
                                <input type="number" step="0.1" min="0.5" name="extra_data[margin][right]" id="inMcRight" value="{{ old('extra_data.margin.right', $margin['right'] ?? 2.5) }}" class="form-control">
                            </div>
                        </div>
                    </div>

                    <div class="form-group" style="display:flex; align-items:center; gap:10px; flex-wrap:wrap;">
                        <span style="font-size:12.5px; font-weight:600; color:#1e293b;">Margin lampiran (cm) &mdash;</span>
                        <label style="font-size:12.5px; color:#475569; display:inline-flex; align-items:center; gap:6px;">
                            Atas
                            <input type="number" step="0.1" min="0.5" name="extra_data[margin][lamp_top]" id="inMlcTop" value="{{ old('extra_data.margin.lamp_top', $margin['lamp_top'] ?? 1) }}" class="form-control" style="width:76px;">
                        </label>
                        <label style="font-size:12.5px; color:#475569; display:inline-flex; align-items:center; gap:6px;">
                            Bawah
                            <input type="number" step="0.1" min="0.5" name="extra_data[margin][lamp_bottom]" id="inMlcBottom" value="{{ old('extra_data.margin.lamp_bottom', $margin['lamp_bottom'] ?? 1) }}" class="form-control" style="width:76px;">
                        </label>
                    </div>

                    <div class="form-group">
                        <label class="check-row">
                            <input type="hidden" name="extra_data[konsep_margin]" value="0">
                            <input type="checkbox" name="extra_data[konsep_margin]" value="1" id="chkKonsepMargin" {{ !empty($konsepMarginVal) ? 'checked' : '' }}>
                            <span>Halaman konsep pakai margin sempit seperti contoh (1,5 cm atas, 1 cm sisi lain &mdash; disarankan; dengan margin 2,5 cm tabel konsep jadi sempit &amp; berisiko 2 halaman)</span>
                        </label>
                    </div>

                    <div class="form-group" style="display:flex; align-items:center; gap:10px;">
                        <label for="inGapYth" style="font-size:13px; font-weight:600; color:#1e293b; white-space:nowrap;">Jarak Hal &rarr; Yth.</label>
                        <select name="extra_data[gap_yth]" id="inGapYth" class="form-control">
                            <option value="120" {{ $gapYthVal == '120' ? 'selected' : '' }}>Rapat (1/2 baris)</option>
                            <option value="240" {{ $gapYthVal == '240' ? 'selected' : '' }}>Normal (1 baris)</option>
                            <option value="480" {{ $gapYthVal == '480' ? 'selected' : '' }}>Lebar (2 baris)</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="check-row">
                            <input type="hidden" name="extra_data[show_nip]" value="0">
                            <input type="checkbox" name="extra_data[show_nip]" value="1" id="chkShowNip" {{ !empty($showNipVal) ? 'checked' : '' }}>
                            <span>Tampilkan NIP di bawah nama penanda tangan</span>
                        </label>
                    </div>
                </div>
            </div>

            <!-- CARD 2: KONSEP SURAT -->
            <div class="v5-card">
                <div class="v5-card-head">
                    <span class="v5-card-title">KONSEP SURAT</span>
                    <span class="v5-card-badge">halaman 1</span>
                </div>
                <div class="v5-card-body">
                    <div class="form-group">
                        <label class="form-label"><span class="lbl-num">1</span> Setelah dikirim, konsep dikembalikan kepada (jabatan)</label>
                        <div class="v5-group-box">
                            <div class="picker-wrap" data-target="kembali"></div>
                            <textarea name="extra_data[konsep][kembali]" id="inKembali" rows="2" class="form-control" placeholder="Jabatan">{{ old('extra_data.konsep.kembali', $konsep['kembali'] ?? '') }}</textarea>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label"><span class="lbl-num">2</span> Konfirmasi (jabatan &amp; nama) <span class="lbl-pill">juga dipakai di kotak paraf hal. 2</span></label>
                        <div class="v5-group-box">
                            <div class="picker-wrap" data-target="konf"></div>
                            <textarea name="extra_data[konsep][konf_jab]" id="inKonfJab" rows="2" class="form-control" placeholder="Jabatan">{{ old('extra_data.konsep.konf_jab', $konsep['konf_jab'] ?? '') }}</textarea>
                            <input type="text" name="extra_data[konsep][konf_nama]" id="inKonfNama" value="{{ old('extra_data.konsep.konf_nama', $konsep['konf_nama'] ?? '') }}" class="form-control" placeholder="Nama">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label"><span class="lbl-num">3</span> Pemeriksa Akhir (jabatan &amp; nama)</label>
                        <div class="v5-group-box">
                            <div class="picker-wrap" data-target="pem"></div>
                            <textarea name="extra_data[konsep][pem_jab]" id="inPemJab" rows="2" class="form-control" placeholder="Jabatan">{{ old('extra_data.konsep.pem_jab', $konsep['pem_jab'] ?? 'Plt. Kepala Balai Wilayah Sungai Kalimantan I Pontianak') }}</textarea>
                            <input type="text" name="extra_data[konsep][pem_nama]" id="inPemNama" value="{{ old('extra_data.konsep.pem_nama', $konsep['pem_nama'] ?? 'Donni Saputra') }}" class="form-control" placeholder="Nama">
                        </div>
                    </div>

                    <div class="grid-2 form-group">
                        <div>
                            <label class="form-label" for="inAjukan"><span class="lbl-num">4</span> Supaya diajukan lagi tanggal</label>
                            <input type="date" name="extra_data[konsep][ajukan]" id="inAjukan" value="{{ old('extra_data.konsep.ajukan', $konsep['ajukan'] ?? '') }}" class="form-control">
                            <div class="form-hint">Kosongkan &rarr; tercetak titik-titik</div>
                        </div>
                        <div>
                            <label class="form-label" for="inDeadline"><span class="lbl-num">5</span> DEADLINE</label>
                            <input type="date" name="extra_data[konsep][deadline]" id="inDeadline" value="{{ old('extra_data.konsep.deadline', $konsep['deadline'] ?? '') }}" class="form-control">
                        </div>
                    </div>

                    <div class="grid-2 form-group">
                        <div>
                            <label class="form-label"><span class="lbl-num">6</span> Ditetapkan (nama)</label>
                            <div class="v5-group-box">
                                <div class="picker-wrap" data-target="tetap"></div>
                                <input type="text" name="extra_data[konsep][tetap_nama]" id="inTetapNama" value="{{ old('extra_data.konsep.tetap_nama', $konsep['tetap_nama'] ?? 'Donni Saputra') }}" class="form-control" placeholder="Nama">
                            </div>
                        </div>
                        <div>
                            <label class="form-label" for="inJmlLembar"><span class="lbl-num">7</span> Jumlah lembar</label>
                            <input type="number" min="0" name="extra_data[konsep][jumlah_lembar]" id="inJmlLembar" value="{{ old('extra_data.konsep.jumlah_lembar', $konsep['jumlah_lembar'] ?? 1) }}" class="form-control">
                            <div class="form-hint" id="hintJmlLembar">&rarr; "1 (Satu)"</div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label"><span class="lbl-num">8</span> Konsep / Penulis Redaksi (jabatan &amp; nama)</label>
                        <div class="v5-group-box">
                            <div class="picker-wrap" data-target="pen"></div>
                            <textarea name="extra_data[konsep][pen_jab]" id="inPenJab" rows="2" class="form-control" placeholder="Jabatan">{{ old('extra_data.konsep.pen_jab', $konsep['pen_jab'] ?? '') }}</textarea>
                            <input type="text" name="extra_data[konsep][pen_nama]" id="inPenNama" value="{{ old('extra_data.konsep.pen_nama', $konsep['pen_nama'] ?? '') }}" class="form-control" placeholder="Nama">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label"><span class="lbl-num">9</span> Diketik oleh (nama)</label>
                        <div class="v5-group-box">
                            <div class="picker-wrap" data-target="ketik"></div>
                            <input type="text" name="extra_data[konsep][ketik_nama]" id="inKetikNama" value="{{ old('extra_data.konsep.ketik_nama', $konsep['ketik_nama'] ?? '') }}" class="form-control" placeholder="Nama">
                        </div>
                    </div>
                </div>
            </div>

            <!-- CARD 3: PARAF KONSEP -->
            <div class="v5-card">
                <div class="v5-card-head">
                    <span class="v5-card-title">PARAF KONSEP</span>
                    <span class="v5-card-badge">halaman 2</span>
                </div>
                <div class="v5-card-body">
                    <div class="paraf-info-banner">
                        Kotak paraf otomatis diisi dari <strong>Konfirmasi</strong> (poin 2): <span id="parafBannerStatus">(belum diisi)</span>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="inKonfJabParaf">Jabatan singkat di kotak paraf <span class="lbl-pill">opsional</span></label>
                        <input type="text" name="extra_data[konsep][konf_jab_paraf]" id="inKonfJabParaf" value="{{ old('extra_data.konsep.konf_jab_paraf', $konsep['konf_jab_paraf'] ?? '') }}" class="form-control" placeholder="mis. Kepala Seksi KPISDA — kosongkan untuk memakai jabatan Konfirmasi">
                        <div class="form-hint">Kotak paraf kecil (&plusmn;3,5 cm), jadi jabatan panjang akan terlipat beberapa baris.</div>
                    </div>
                </div>
            </div>

            <!-- CARD 4: SURAT ASLI -->
            <div class="v5-card">
                <div class="v5-card-head">
                    <span class="v5-card-title">SURAT ASLI</span>
                </div>
                <div class="v5-card-body">
                    <div class="grid-2 form-group">
                        <div>
                            <label class="form-label" for="inNomor"><span class="lbl-num">1</span> Nomor</label>
                            <input type="text" name="nomor_surat" id="inNomor" value="{{ old('nomor_surat', $autoNomor ?? 'UM.01.02/Bws1/001/2026') }}" class="form-control" required>
                            <div class="form-hint">Ketik nomor urut di belakang garis miring terakhir</div>
                        </div>
                        <div>
                            <label class="form-label" for="inTanggalSurat"><span class="lbl-num">2</span> Tanggal surat</label>
                            <div style="display:flex; gap:6px;">
                                <input type="text" name="extra_data[kota]" id="inKota" value="{{ old('extra_data.kota', $extra['kota'] ?? 'Pontianak') }}" class="form-control" style="width:98px; flex-shrink:0;" placeholder="Kota">
                                <input type="date" name="tanggal_surat" id="inTanggalSurat" value="{{ $tglSuratVal }}" class="form-control" required>
                            </div>
                        </div>
                    </div>

                    <div class="grid-2 form-group">
                        <div>
                            <label class="form-label" for="inSifat"><span class="lbl-num">3</span> Sifat</label>
                            <select name="sifat" id="inSifat" class="form-control">
                                @php $sifatOps = ['Biasa', 'Penting', 'Segera', 'Rahasia']; @endphp
                                @if(!in_array($sifatVal, $sifatOps))
                                    <option value="{{ $sifatVal }}" selected>{{ $sifatVal }}</option>
                                @endif
                                @foreach($sifatOps as $so)
                                    <option value="{{ $so }}" {{ $sifatVal === $so ? 'selected' : '' }}>{{ $so }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="form-label" for="inLampiran"><span class="lbl-num">4</span> Lampiran (lembar)</label>
                            <input type="number" min="0" name="lampiran" id="inLampiran" value="{{ $lampiranNum }}" class="form-control">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="inHal"><span class="lbl-num">5</span> Hal</label>
                        <textarea name="hal" id="inHal" rows="2" class="form-control" placeholder="Tulis perihal surat undangan..." required>{{ old('hal', $src ? $src->hal : '') }}</textarea>
                    </div>

                    <div class="form-group">
                        <div class="form-label"><span class="lbl-num">6</span> Yth.</div>
                        <div class="yth-radio-row">
                            <label class="yth-radio-label">
                                <input type="radio" name="extra_data[yth_mode]" value="terlampir" {{ $ythModeVal !== 'langsung' ? 'checked' : '' }}>
                                <span>(Daftar Terlampir)</span>
                            </label>
                            <label class="yth-radio-label">
                                <input type="radio" name="extra_data[yth_mode]" value="langsung" {{ $ythModeVal === 'langsung' ? 'checked' : '' }}>
                                <span>Isi langsung</span>
                            </label>
                        </div>

                        <div id="ythSubLabel" style="font-size:12.5px; font-weight:600; color:#1e293b; margin-bottom:8px;">
                            Daftar undangan (dicetak di halaman lampiran &mdash; satu baris satu tujuan)
                        </div>

                        <div class="v5-group-box" style="margin-bottom:8px;">
                            <div class="picker-wrap" data-target="yth"></div>
                        </div>

                        <div class="penerima-list-box" id="penerimaListBox">
                            <div id="penerimaEmptyMsg" class="penerima-empty-msg">Belum ada tujuan. Tambah dari database atau ketik manual di bawah.</div>
                            <div id="penerimaContainer">
                                @foreach($penerimaList as $pNama)
                                    @if(trim((string) $pNama) !== '')
                                        <div class="penerima-item" draggable="true">
                                            <span class="drag-handle" title="Seret untuk urutkan">&#8942;&#8942;</span>
                                            <input type="text" name="penerimas[]" class="in-penerima" value="{{ $pNama }}" placeholder="Nama / jabatan tujuan...">
                                            <button type="button" class="btn-sq" data-pmove="-1" title="Naik">&#9650;</button>
                                            <button type="button" class="btn-sq" data-pmove="1" title="Turun">&#9660;</button>
                                            <button type="button" class="btn-sq del" data-pdel title="Hapus">&times;</button>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>

                        <div style="display:flex; gap:8px; margin-bottom:8px;">
                            <input type="text" id="inNewPenerima" class="form-control" placeholder="Ketik tujuan baru lalu Enter...">
                            <button type="button" class="btn-v5" id="btnAddPenerima" style="white-space:nowrap;">+ Tambah</button>
                        </div>

                        <div style="display:flex; gap:6px; flex-wrap:wrap; margin-bottom:6px;">
                            <button type="button" class="btn-v5" id="btnSortPenerima">Urutkan A&ndash;Z</button>
                            <button type="button" class="btn-v5" id="btnRevPenerima">Balik urutan</button>
                            <button type="button" class="btn-v5" id="btnBulkTextPenerima">Edit sebagai teks</button>
                        </div>

                        <div id="bulkTextBox" style="display:none; background:#f8fafc; border:1px solid #cbd5e1; border-radius:8px; padding:10px; margin-bottom:8px;">
                            <div style="font-size:12px; color:#475569; margin-bottom:6px;">Satu baris = satu penerima tujuan:</div>
                            <textarea id="bulkPenerimaTextarea" rows="4" class="form-control" placeholder="Tempel atau ketik daftar tujuan per baris..."></textarea>
                            <div style="display:flex; gap:6px; margin-top:8px;">
                                <button type="button" class="btn-v5" id="btnApplyBulkPenerima" style="background:var(--pu-navy); color:#fff; border-color:var(--pu-navy);">Terapkan Daftar</button>
                                <button type="button" class="btn-v5" id="btnCancelBulkPenerima">Tutup</button>
                            </div>
                        </div>

                        <div class="form-hint" style="margin-bottom:10px;">Seret &#8942;&#8942; atau tombol &#9650;&#9660; untuk mengubah urutan &middot; <kbd style="background:#f1f5f9; border:1px solid #cbd5e1; padding:1px 5px; border-radius:4px; font-size:11px;">Alt</kbd> + <kbd style="background:#f1f5f9; border:1px solid #cbd5e1; padding:1px 5px; border-radius:4px; font-size:11px;">&uarr;</kbd>/<kbd style="background:#f1f5f9; border:1px solid #cbd5e1; padding:1px 5px; border-radius:4px; font-size:11px;">&darr;</kbd> saat mengetik</div>

                        <div style="display:flex; align-items:center; gap:12px;">
                            <label for="inDiTempat" style="font-size:13px; color:#475569; white-space:nowrap;">di-</label>
                            <input type="text" name="extra_data[di_tempat]" id="inDiTempat" value="{{ $diTempatVal }}" class="form-control">
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="form-label"><span class="lbl-num">7</span> Isi surat</div>
                        <div class="form-hint" style="margin-top:0; margin-bottom:10px;">
                            Susun isi surat dari beberapa bagian. <strong>Paragraf</strong> = teks rata kiri-kanan (pisahkan paragraf dengan satu baris kosong). <strong>Poin</strong> = daftar <em>Label : Isi</em> seperti Hari/Tanggal, Jam, Tempat, Agenda.
                        </div>
                        <div id="bodySectionsContainer"></div>
                        <div style="display:flex; gap:8px; margin-top:8px;">
                            <button type="button" class="btn-v5" id="btnAddPara">+ Bagian paragraf</button>
                            <button type="button" class="btn-v5" id="btnAddPoints">+ Bagian poin</button>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label"><span class="lbl-num">8</span> Penanda tangan (jabatan &amp; nama)</label>
                        <div class="v5-group-box">
                            <div class="picker-wrap" data-target="ttd"></div>
                            <textarea name="penandatangan_jabatan" id="inTtdJabatan" rows="2" class="form-control" placeholder="Jabatan" required>{{ old('penandatangan_jabatan', $src ? $src->penandatangan_jabatan : 'Plt. Kepala Balai Wilayah Sungai Kalimantan I Pontianak') }}</textarea>
                            <input type="text" name="penandatangan_nama" id="inTtdNama" value="{{ old('penandatangan_nama', $src ? $src->penandatangan_nama : 'Donni Saputra') }}" class="form-control" placeholder="Nama lengkap" required>
                            <input type="text" name="penandatangan_nip" id="inTtdNip" value="{{ old('penandatangan_nip', $src ? $src->penandatangan_nip : '197611012005021003') }}" class="form-control" placeholder="NIP">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="inTembusan"><span class="lbl-num">9</span> Tembusan</label>
                        <textarea name="extra_data[tembusan]" id="inTembusan" rows="2" class="form-control" placeholder="Satu baris = satu nomor">{{ $tembusanVal }}</textarea>
                        <div class="form-hint">Dicetak sebagai daftar bernomor. Kosongkan jika tanpa tembusan.</div>
                    </div>
                </div>
            </div>

            <!-- SUBMIT BUTTONS -->
            <div style="display:flex; gap:12px; margin-bottom: 24px;">
                @if($isEdit)
                    <a href="{{ route('surat.undangan.show', $surat->id) }}" class="btn-quick-create" style="background:#f1f5f9; color:#334155; justify-content:center; padding:12px 18px;">Batal</a>
                @endif
                <button type="submit" class="btn-quick-create" style="flex:1; justify-content:center; padding:12px; font-size:14px;">
                    {{ $isEdit ? 'Simpan Perubahan & Lanjut Cetak' : 'Simpan & Lanjut Cetak' }}
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
                    <option value="asli">Surat Asli</option>
                    <option value="paraf">Paraf Konsep</option>
                    <option value="lampiran">Lampiran Daftar Undangan</option>
                    <option value="konsep">Konsep Surat</option>
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
                            <div class="kop-line-address" id="pvKopAddr">{{ $kopAlamatVal }}</div>
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

                <table class="ttd-paraf-grid">
                    <tr>
                        <td style="width: 48%; vertical-align: bottom;">
                            <div id="pvParafHierarkiBox" style="display:none; margin-bottom: 10px;">
                                <div class="paraf-mini-box">
                                    <div id="pvParafJab1"></div>
                                    <div id="pvParafVal1" style="font-weight:700; margin-top:26px;"></div>
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
                                        <div style="font-size:14px;" id="pvKpKop1">{{ $kop['kementerian'] }}</div>
                                        <div style="font-size:10.5px;" id="pvKpKop2">{{ $kop['ditjen'] }}</div>
                                        <div id="pvKpKop3Wrap"></div>
                                        <div style="font-size:7.5px;" id="pvKpKopAddr"></div>
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
                        <td rowspan="2" style="width:23%; text-align:center;">Konfirmasi/<br>paraf/tanggal<br><br><span id="pvKpKonfJab"></span><br><br><strong id="pvKpKonfNama"></strong></td>
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
                            <div>Sifat : <span id="pvKpSifat"></span></div>
                            <div>Lampiran : <span id="pvKpLampiran"></span></div>
                            <div>Hal : <strong id="pvKpHal"></strong></div>
                            <div style="margin-top:6px;">Yth.<br><span id="pvKpYthMini"></span><br>di-<br><span style="padding-left:14px;" id="pvKpDiMini"></span></div>
                            <div id="pvKpMiniBody" style="margin-top:6px;"></div>
                            <div style="margin-top:14px; text-align:right; padding-right:20px;">
                                <div id="pvKpTtdJab"></div><div style="height:32px;"></div><strong id="pvKpTtdNama"></strong><div id="pvKpTtdNip" style="display:none;"></div>
                            </div>
                        </td>
                    </tr>
                    <tr><td>No.<br>Kode Klasifikasi :<br><div style="text-align:center; margin-top:4px;">NOMOR</div><div style="text-align:center; font-weight:700;" id="pvKpKlasifikasi"></div></td></tr>
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
    const PEGAWAI_ITEMS = @json($pickerItems);
    const KOP_SIGNERS = {
        balai:       { jab: 'Plt. Kepala Balai Wilayah Sungai Kalimantan I Pontianak', nama: 'Donni Saputra', nip: '197611012005021003' },
        satker_op:   { jab: 'Kepala Satuan Kerja Operasi dan Pemeliharaan SDA Kalimantan I', nama: 'Kepala Satker O&P SDA Kalimantan I', nip: '' },
        satker_pjpa: { jab: 'Kepala Satuan Kerja SNVT Pelaksanaan Jaringan Pemanfaatan Air Kalimantan I', nama: 'Kepala Satker SNVT PJPA Kalimantan I', nip: '' },
        satker_pjsa: { jab: 'Kepala Satuan Kerja SNVT Pelaksanaan Jaringan Sumber Air Kalimantan I', nama: 'Kepala Satker SNVT PJSA Kalimantan I', nip: '' },
        satker_bws:  { jab: 'Kepala Satuan Kerja Balai Wilayah Sungai Kalimantan I Pontianak', nama: 'Donni Saputra', nip: '197611012005021003' }
    };

    let bodySections = @json($bodySections);
    const el = id => document.getElementById(id);

    function escHtml(s) {
        return String(s == null ? '' : s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
    }

    function getYthMode() {
        const chk = document.querySelector('input[name="extra_data[yth_mode]"]:checked');
        return chk ? chk.value : 'terlampir';
    }

    /* ---------- SEARCHABLE PEGAWAI PICKER ---------- */
    const PICKER_CFG = {
        kembali: { placeholder: 'Cari dari database... (opsional)', hasCaret: false, onPick: it => { el('inKembali').value = it.jab; } },
        konf:    { placeholder: 'Cari nama / jabatan...', hasCaret: true, onPick: it => { el('inKonfJab').value = it.jab; el('inKonfNama').value = it.nama; } },
        pem:     { placeholder: 'Cari nama / jabatan...', hasCaret: false, onPick: it => { el('inPemJab').value = it.jab; el('inPemNama').value = it.nama; } },
        tetap:   { placeholder: 'Cari nama...', hasCaret: false, onPick: it => { el('inTetapNama').value = it.nama; } },
        pen:     { placeholder: 'Cari nama / jabatan...', hasCaret: false, onPick: it => { el('inPenJab').value = it.jab; el('inPenNama').value = it.nama; } },
        ketik:   { placeholder: 'Cari nama...', hasCaret: false, onPick: it => { el('inKetikNama').value = it.nama; } },
        yth:     { placeholder: 'Tambah jabatan dari database...', hasCaret: false, onPick: it => { addPenerimaRow(it.jab); } },
        ttd:     { placeholder: 'Cari nama / jabatan...', hasCaret: false, onPick: it => { el('inTtdJabatan').value = it.jab; el('inTtdNama').value = it.nama; el('inTtdNip').value = it.nip; } }
    };

    const SEARCH_SVG = '<svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>';

    function initPickers() {
        document.querySelectorAll('.picker-wrap').forEach(wrap => {
            const key = wrap.dataset.target;
            const cfg = PICKER_CFG[key];
            if (!cfg) return;

            wrap.innerHTML = `
                <div class="picker-input-row">
                    <span class="picker-icon">${SEARCH_SVG}</span>
                    <input type="text" class="picker-input" placeholder="${escHtml(cfg.placeholder)}" autocomplete="off">
                    ${cfg.hasCaret ? '<span class="picker-caret">&#9660;</span>' : ''}
                </div>
                <div class="picker-menu"></div>
            `;

            const input = wrap.querySelector('.picker-input');
            const menu = wrap.querySelector('.picker-menu');

            function renderMenu(q) {
                const needle = (q || '').trim().toLowerCase();
                const list = needle
                    ? PEGAWAI_ITEMS.filter(it =>
                        it.nama.toLowerCase().includes(needle) ||
                        it.jab.toLowerCase().includes(needle) ||
                        it.unit.toLowerCase().includes(needle) ||
                        it.nip.includes(needle)
                    )
                    : PEGAWAI_ITEMS;

                const slice = list.slice(0, 40);
                if (!slice.length) {
                    menu.innerHTML = '<div class="picker-empty">Tidak ditemukan pegawai/jabatan yang cocok.</div>';
                    return;
                }
                menu.innerHTML = slice.map((it, idx) => {
                    const origIdx = PEGAWAI_ITEMS.indexOf(it);
                    return `
                        <div class="picker-opt" data-idx="${origIdx}">
                            <div class="picker-opt-name">${escHtml(it.nama)}</div>
                            <div class="picker-opt-jab">${it.rangkap ? '<span class="picker-opt-badge">Rangkap</span>' : ''}${escHtml(it.jab)}</div>
                        </div>
                    `;
                }).join('');
            }

            input.addEventListener('focus', () => {
                document.querySelectorAll('.picker-menu.open').forEach(m => { if (m !== menu) m.classList.remove('open'); });
                renderMenu(input.value);
                menu.classList.add('open');
            });
            input.addEventListener('input', () => {
                renderMenu(input.value);
                menu.classList.add('open');
            });
            menu.addEventListener('mousedown', e => {
                const opt = e.target.closest('.picker-opt');
                if (!opt) return;
                e.preventDefault();
                const item = PEGAWAI_ITEMS[+opt.dataset.idx];
                if (item) {
                    cfg.onPick(item);
                    input.value = '';
                    menu.classList.remove('open');
                    syncAllFields();
                }
            });
        });

        document.addEventListener('click', e => {
            if (!e.target.closest('.picker-wrap')) {
                document.querySelectorAll('.picker-menu.open').forEach(m => m.classList.remove('open'));
            }
        });
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
                        <textarea class="form-control" data-rf="value" rows="${Math.max(1, String(it.value || '').split('\n').length)}" placeholder="Isi (Enter = baris lanjutan)">${escHtml(it.value)}</textarea>
                        <button type="button" class="btn-sq del" data-rdel title="Hapus baris">&times;</button>
                    </div>
                `).join('');
                inner = itemsHtml + `<button type="button" class="btn-v5" data-radd style="margin-top:2px;">+ Baris poin</button>`;
            } else {
                inner = `
                    <textarea class="form-control" data-sf="text" rows="3" placeholder="Tulis paragraf... (baris kosong = paragraf baru)">${escHtml(sec.text)}</textarea>
                    <label class="check-row" style="margin-top:8px;">
                        <input type="checkbox" data-sf="indent" ${sec.indent !== false ? 'checked' : ''}>
                        <span>Baris pertama menjorok (seperti paragraf pembuka)</span>
                    </label>
                `;
            }
            return `
                <div class="sec-box" data-s="${i}">
                    <div class="sec-head">
                        <span style="font-weight:700; font-size:12.5px; color:#334155;">Bagian ${i + 1}</span>
                        <select data-sf="type">
                            <option value="paragraph" ${sec.type !== 'points' ? 'selected' : ''}>Paragraf</option>
                            <option value="points" ${sec.type === 'points' ? 'selected' : ''}>Poin (Label : Isi)</option>
                        </select>
                        <span style="flex:1;"></span>
                        <button type="button" class="btn-sq" data-sop="up" ${i === 0 ? 'disabled' : ''} title="Naik">&#9650;</button>
                        <button type="button" class="btn-sq" data-sop="down" ${i === n - 1 ? 'disabled' : ''} title="Turun">&#9660;</button>
                        <button type="button" class="btn-sq del" data-sop="del" title="Hapus bagian">&times;</button>
                    </div>
                    <div class="sec-body">${inner}</div>
                </div>
            `;
        }).join('');
        syncDynamicBodyPreview();
    }

    function syncDynamicBodyPreview() {
        el('inBodyJson').value = JSON.stringify(bodySections);

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
                        miniHtml += `<div>${escHtml(it.label)} : ${valHtml}</div>`;
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

    /* ---------- PENERIMA LIST (DRAG, ALT+UP/DOWN, BULK TEXT) ---------- */
    let draggedItem = null;

    function createPenerimaElement(val = '') {
        const row = document.createElement('div');
        row.className = 'penerima-item';
        row.draggable = true;
        row.innerHTML = `
            <span class="drag-handle" title="Seret untuk urutkan">&#8942;&#8942;</span>
            <input type="text" name="penerimas[]" class="in-penerima" value="${escHtml(val)}" placeholder="Nama / jabatan tujuan...">
            <button type="button" class="btn-sq" data-pmove="-1" title="Naik">&#9650;</button>
            <button type="button" class="btn-sq" data-pmove="1" title="Turun">&#9660;</button>
            <button type="button" class="btn-sq del" data-pdel title="Hapus">&times;</button>
        `;
        return row;
    }

    function addPenerimaRow(val = '') {
        if (!String(val).trim()) return;
        el('penerimaContainer').appendChild(createPenerimaElement(val.trim()));
        syncPenerimaList();
    }

    function getPenerimaValues() {
        return Array.from(document.querySelectorAll('.in-penerima')).map(i => i.value.trim()).filter(Boolean);
    }

    function setPenerimaList(vals) {
        const c = el('penerimaContainer');
        c.innerHTML = '';
        vals.forEach(v => {
            if (v.trim()) c.appendChild(createPenerimaElement(v.trim()));
        });
        syncPenerimaList();
    }

    const penerimaContainer = el('penerimaContainer');
    penerimaContainer.addEventListener('input', syncPenerimaList);
    penerimaContainer.addEventListener('click', e => {
        const btn = e.target.closest('button'); if (!btn) return;
        const row = btn.closest('.penerima-item'); if (!row) return;
        if (btn.hasAttribute('data-pdel')) {
            row.remove();
            syncPenerimaList();
        } else if (btn.hasAttribute('data-pmove')) {
            const dir = +btn.dataset.pmove;
            if (dir < 0 && row.previousElementSibling) penerimaContainer.insertBefore(row, row.previousElementSibling);
            else if (dir > 0 && row.nextElementSibling) penerimaContainer.insertBefore(row.nextElementSibling, row);
            syncPenerimaList();
        }
    });
    penerimaContainer.addEventListener('keydown', e => {
        if (!e.altKey || (e.key !== 'ArrowUp' && e.key !== 'ArrowDown')) return;
        const inp = e.target.closest('.in-penerima'); if (!inp) return;
        e.preventDefault();
        const row = inp.closest('.penerima-item');
        if (e.key === 'ArrowUp' && row.previousElementSibling) penerimaContainer.insertBefore(row, row.previousElementSibling);
        else if (e.key === 'ArrowDown' && row.nextElementSibling) penerimaContainer.insertBefore(row.nextElementSibling, row);
        inp.focus();
        syncPenerimaList();
    });
    penerimaContainer.addEventListener('dragstart', e => {
        draggedItem = e.target.closest('.penerima-item');
        if (draggedItem) draggedItem.classList.add('dragging');
    });
    penerimaContainer.addEventListener('dragend', () => {
        if (draggedItem) draggedItem.classList.remove('dragging');
        draggedItem = null;
        syncPenerimaList();
    });
    penerimaContainer.addEventListener('dragover', e => {
        e.preventDefault();
        if (!draggedItem) return;
        const target = e.target.closest('.penerima-item');
        if (target && target !== draggedItem) {
            const rect = target.getBoundingClientRect();
            const after = (e.clientY - rect.top) > rect.height / 2;
            penerimaContainer.insertBefore(draggedItem, after ? target.nextElementSibling : target);
        }
    });

    el('btnAddPenerima').addEventListener('click', () => {
        const v = el('inNewPenerima').value.trim();
        if (v) { addPenerimaRow(v); el('inNewPenerima').value = ''; }
    });
    el('inNewPenerima').addEventListener('keydown', e => {
        if (e.key === 'Enter') {
            e.preventDefault();
            el('btnAddPenerima').click();
        }
    });
    el('btnSortPenerima').addEventListener('click', () => {
        const vals = getPenerimaValues().sort((a, b) => a.localeCompare(b, 'id', { sensitivity: 'base', numeric: true }));
        setPenerimaList(vals);
    });
    el('btnRevPenerima').addEventListener('click', () => {
        setPenerimaList(getPenerimaValues().reverse());
    });
    el('btnBulkTextPenerima').addEventListener('click', () => {
        el('bulkPenerimaTextarea').value = getPenerimaValues().join('\n');
        el('bulkTextBox').style.display = el('bulkTextBox').style.display === 'none' ? 'block' : 'none';
    });
    el('btnApplyBulkPenerima').addEventListener('click', () => {
        const lines = el('bulkPenerimaTextarea').value.split(/\r?\n/).map(s => s.trim()).filter(Boolean);
        setPenerimaList(lines);
        el('bulkTextBox').style.display = 'none';
    });
    el('btnCancelBulkPenerima').addEventListener('click', () => {
        el('bulkTextBox').style.display = 'none';
    });

    function syncPenerimaList() {
        const rows = penerimaContainer.querySelectorAll('.penerima-item');
        el('penerimaEmptyMsg').style.display = rows.length ? 'none' : 'block';

        const isTerlampir = getYthMode() !== 'langsung';
        el('ythSubLabel').innerText = isTerlampir
            ? 'Daftar undangan (dicetak di halaman lampiran — satu baris satu tujuan)'
            : 'Daftar tujuan (dicetak langsung di bawah Yth. — satu baris satu tujuan)';

        const pvList = el('pvListPenerima');
        const pvLpList = el('pvLpListContainer');
        const terlampirText = el('pvTerlampirText');
        pvList.innerHTML = '';
        pvLpList.innerHTML = '';

        const names = getPenerimaValues();
        names.forEach(n => {
            const li = document.createElement('li'); li.innerText = n; pvList.appendChild(li);
            const li2 = document.createElement('li'); li2.innerText = n; pvLpList.appendChild(li2);
        });

        pvList.style.display = isTerlampir ? 'none' : 'block';
        terlampirText.style.display = isTerlampir ? 'block' : 'none';
        el('pvKpYthMini').innerText = isTerlampir ? '(Daftar Terlampir)' : (names.join(', ') || '');
    }

    /* ---------- KOP, META, PARAF, KONSEP ---------- */
    function getEffectiveKop() {
        const preset = KOP_PRESETS[el('inKopType').value] || KOP_PRESETS.balai;
        const lines3 = (el('inKopLines3').value || '').split(/\r?\n/).map(s => s.trim()).filter(Boolean);
        const addr = el('inKopAlamat').value.trim() || preset.alamat;
        return {
            kementerian: preset.kementerian,
            ditjen: preset.ditjen,
            lines3: lines3.length ? lines3 : preset.lines3,
            alamat: addr
        };
    }

    function updateKopPreview() {
        const kop = getEffectiveKop();
        el('pvKop1').innerText = kop.kementerian;
        el('pvKop2').innerText = kop.ditjen;
        el('pvKpKop1').innerText = kop.kementerian;
        el('pvKpKop2').innerText = kop.ditjen;

        const wrap = el('pvKop3Wrap'); wrap.innerHTML = '';
        const kpWrap = el('pvKpKop3Wrap'); kpWrap.innerHTML = '';
        kop.lines3.forEach(line => {
            const d = document.createElement('div'); d.className = 'kop-line-3'; d.innerText = line; wrap.appendChild(d);
            const d2 = document.createElement('div'); d2.style.cssText = 'font-size:8px; font-weight:700;'; d2.innerText = line; kpWrap.appendChild(d2);
        });
        el('pvKopAddr').innerText = kop.alamat;
        el('pvKpKopAddr').innerText = kop.alamat;
    }

    function syncAllFields() {
        el('pvNomor').innerText = el('inNomor').value;
        el('pvLpNomor').innerText = el('inNomor').value;
        el('pvKpNomor').innerText = el('inNomor').value;
        el('pvKpKlasifikasi').innerText = el('inNomor').value;
        el('pvSifat').innerText = el('inSifat').value;
        el('pvKpSifat').innerText = el('inSifat').value;

        const lampNum = parseInt(el('inLampiran').value, 10) || 0;
        const lampText = lampNum > 0 ? (lampNum + ' Lembar') : '-';
        el('pvLampiran').innerText = lampText;
        el('pvKpLampiran').innerText = lampText;

        el('pvHal').innerText = el('inHal').value;
        el('pvKpHal').innerText = el('inHal').value;
        const diTempat = el('inDiTempat').value || 'Tempat';
        el('pvDiTempat').innerText = diTempat;
        el('pvKpDiMini').innerText = diTempat;

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
        el('pvLpJabHeader').innerText = DocGen.noComma(el('inTtdJabatan').value);
        el('pvKpTtdJab').innerText = el('inTtdJabatan').value;
        el('pvTtdNama').innerText = el('inTtdNama').value;
        el('pvKpTtdNama').innerText = el('inTtdNama').value;
        const nipVal = el('inTtdNip').value ? 'NIP. ' + el('inTtdNip').value : '';
        el('pvTtdNip').innerText = nipVal;
        el('pvKpTtdNip').innerText = nipVal;
        el('pvTtdNip').style.display = (showNip && nipVal) ? 'block' : 'none';
        el('pvKpTtdNip').style.display = (showNip && nipVal) ? 'block' : 'none';

        const konfJab = el('inKonfJab').value.trim();
        const konfNama = el('inKonfNama').value.trim();
        el('pvParafJab1').innerText = el('inKonfJabParaf').value.trim() || konfJab;
        el('pvParafVal1').innerText = konfNama;
        el('parafBannerStatus').innerText = (konfNama || konfJab)
            ? [konfNama, konfJab].filter(Boolean).join(' — ')
            : '(belum diisi)';

        el('pvKpKonfJab').innerText = konfJab;
        el('pvKpKonfNama').innerText = konfNama;
        el('pvKpPemJab').innerText = el('inPemJab').value || '';
        el('pvKpPemNama').innerText = el('inPemNama').value || '';
        el('pvKpPenJab').innerText = el('inPenJab').value || '';
        el('pvKpPenNama').innerText = el('inPenNama').value || '';
        el('pvKpKetikNama').innerText = el('inKetikNama').value || '';
        el('pvKpKembali').innerText = el('inKembali').value || '';
        el('pvKpDeadline').innerText = el('inDeadline').value ? DocGen.fmtTgl(el('inDeadline').value) : '-';
        el('pvKpAjukan').innerText = el('inAjukan').value ? DocGen.fmtTgl(el('inAjukan').value) : '…………………………';

        const jml = parseInt(el('inJmlLembar').value || '0', 10);
        const jmlStr = jml > 0 ? (jml + ' (' + DocGen.terbilang(jml) + ')') : '';
        el('pvKpJmlLembar').innerText = jmlStr;
        el('hintJmlLembar').innerText = jmlStr ? ('→ "' + jmlStr + '"') : '→ (kosong)';

        el('pvKpTetapNama').innerText = el('inTetapNama').value.trim() || '';

        const tbLines = (el('inTembusan').value || '').split('\n').map(s => s.trim()).filter(Boolean);
        el('pvTembusanBox').style.display = tbLines.length ? 'block' : 'none';
        el('pvTembusanList').innerHTML = tbLines.map(l => `<li>${escHtml(l)}</li>`).join('');

        updateKopPreview();
        syncPenerimaList();
    }

    el('inKopType').addEventListener('change', function() {
        const preset = KOP_PRESETS[this.value] || KOP_PRESETS.balai;
        el('inKopLines3').value = (preset.lines3 || []).join('\n');
        el('inKopAlamat').value = preset.alamat || '';

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

    el('btnResetKop').addEventListener('click', () => {
        if (!confirm('Kembalikan teks kop ke bawaan?')) return;
        const preset = KOP_PRESETS[el('inKopType').value] || KOP_PRESETS.balai;
        el('inKopLines3').value = (preset.lines3 || []).join('\n');
        el('inKopAlamat').value = preset.alamat || '';
        updateKopPreview();
    });

    el('selPreviewPage').addEventListener('change', function() {
        const val = this.value;
        el('viewSheetAsli').style.display = (val === 'asli' || val === 'paraf') ? 'block' : 'none';
        el('viewSheetLampiran').style.display = (val === 'lampiran') ? 'block' : 'none';
        el('viewSheetKonsep').style.display = (val === 'konsep') ? 'block' : 'none';
        el('pvParafHierarkiBox').style.display = (val === 'paraf') ? 'block' : 'none';
    });

    document.querySelectorAll('input[name="extra_data[yth_mode]"]').forEach(r => r.addEventListener('change', syncPenerimaList));
    ['inSifat', 'inGapYth', 'inUrutan', 'chkShowNip'].forEach(id => el(id).addEventListener('change', syncAllFields));
    ['inKopLines3','inKopAlamat','inNomor','inLampiran','inHal','inKota','inTanggalSurat','inDiTempat','inTembusan',
     'inTtdNama','inTtdNip','inTtdJabatan','inKonfJab','inKonfNama','inKonfJabParaf','inPemJab','inPemNama',
     'inPenJab','inPenNama','inKetikNama','inTetapNama','inAjukan','inDeadline','inJmlLembar','inKembali']
        .forEach(id => el(id).addEventListener('input', syncAllFields));

    el('formSurat').addEventListener('submit', () => {
        if (el('bulkTextBox').style.display !== 'none') {
            const lines = el('bulkPenerimaTextarea').value.split(/\r?\n/).map(s => s.trim()).filter(Boolean);
            setPenerimaList(lines);
        }
        syncDynamicBodyPreview();
    });

    function printActiveSheet() { document.body.classList.remove('print-all-sheets'); window.print(); }
    function printAllSheets() {
        document.body.classList.add('print-all-sheets');
        window.print();
        setTimeout(() => document.body.classList.remove('print-all-sheets'), 500);
    }

    initPickers();
    renderBodyBuilder();
    syncAllFields();

    el('btnExportDocx').addEventListener('click', function() {
        const btn = this, orig = btn.innerHTML;
        btn.innerHTML = 'Membuat .docx...'; btn.disabled = true;

        const penerimas = getPenerimaValues();
        const kopType = el('inKopType').value;
        const effKop = getEffectiveKop();

        const docData = {
            mode: 'undangan',
            nomor: el('inNomor').value,
            sifat: el('inSifat').value,
            lampiran: parseInt(el('inLampiran').value, 10) || 0,
            hal: el('inHal').value,
            kota: el('inKota').value,
            tanggal: el('inTanggalSurat').value,
            ythMode: getYthMode(),
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
            tetap_nama: el('inTetapNama').value.trim(),
            body: bodySections,
            pages: {
                konsep: el('pgKonsep').checked,
                paraf: el('pgParaf').checked,
                asli: el('pgAsli').checked,
                lampiran: el('pgLampiran').checked && getYthMode() !== 'langsung'
            },
            margin: {
                top: +el('inMcTop').value || 1.5,
                bottom: +el('inMcBottom').value || 1.5,
                left: +el('inMcLeft').value || 1.8,
                right: +el('inMcRight').value || 1.8
            },
            marginLampiran: {
                top: +el('inMlcTop').value || 1,
                bottom: +el('inMlcBottom').value || 1
            }
        };

        const kopSpec = {
            line1: effKop.kementerian,
            line2: effKop.ditjen,
            line3: effKop.lines3,
            line4: effKop.alamat,
            logoTw: Math.round((kopType === 'balai' ? 2.25 : 2.75) * 567)
        };
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
