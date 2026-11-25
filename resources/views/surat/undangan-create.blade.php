@extends('layouts.app')

@php
    $isEdit = isset($surat);
    $src = $surat ?? ($duplicateFrom ?? null);
    $extra = $src ? ($src->extra_data ?? []) : [];
    $kopTypeVal = old('extra_data.kop_type', $extra['kop_type'] ?? 'balai');
    $ythModeVal = old('extra_data.yth_mode', $extra['yth_mode'] ?? 'langsung');
    $tembusanVal = old('extra_data.tembusan', $extra['tembusan'] ?? '');
    $showScheduleVal = old('extra_data.show_schedule', $extra['show_schedule'] ?? '1');
    $konsep = $extra['konsep'] ?? [];
    $tglSuratVal = old('tanggal_surat', $isEdit ? $surat->tanggal_surat->format('Y-m-d') : date('Y-m-d'));

    $penerimaList = ($src && $src->penerimas->count() > 0)
        ? $src->penerimas->pluck('nama_penerima')->toArray()
        : [
            'Para Pejabat Administrator BWS Kalimantan I',
            'Para Kepala Satuan Kerja di Lingkungan BWS Kalimantan I',
            'Para Pejabat Pembuat Komitmen (PPK) di Lingkungan BWS Kalimantan I',
        ];

    $scheduleRows = !empty($extra['schedule_rows']) && is_array($extra['schedule_rows'])
        ? $extra['schedule_rows']
        : [
            ['waktu' => '09.00 - 09.30', 'acara' => 'Pembukaan & Arahan Kepala Balai', 'pic' => 'Plt. Kepala Balai'],
            ['waktu' => '09.30 - 11.30', 'acara' => 'Pemaparan Progres Fisik & Keuangan', 'pic' => 'Para PPK & Satker'],
        ];
@endphp

@section('title', $isEdit ? 'Edit Surat Undangan' : 'Buat Surat Undangan')
@section('header_title', $isEdit ? 'Edit Surat Undangan Kedinasan' : 'Generator Surat Undangan Kedinasan')
@section('header_subtitle', 'Format Resmi Tata Naskah Dinas BWS Kalimantan I Pontianak')

@push('styles')
<style>
    .editor-layout {
        display: grid;
        grid-template-columns: minmax(400px, 490px) 1fr;
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

    .schedule-table-preview { width: 100%; border-collapse: collapse; margin: 14px 0; font-size: 13.5px; }
    .schedule-table-preview th, .schedule-table-preview td { border: 1px solid #333; padding: 6px 10px; }
    .schedule-table-preview th { background: #f1f5f9; font-weight: 700; text-align: center; }

    .ttd-paraf-grid {
        width: 100%; margin-top: 28px; border-collapse: collapse; font-size: 14.5px;
    }
    .ttd-paraf-grid td { vertical-align: top; }
    .paraf-mini-box {
        border: 1.5px solid #000; width: 155px; min-height: 78px; padding: 4px 6px;
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

<div class="editor-layout">
    <!-- LEFT PANE: FORM -->
    <div class="editor-form-col">
        <form method="POST" action="{{ $isEdit ? route('surat.undangan.update', $surat->id) : route('surat.undangan.store') }}" id="formSurat">
            @csrf
            @if($isEdit) @method('PUT') @endif
            <input type="hidden" name="extra_data[schedule_json]" id="inScheduleJson" value="">

            <!-- CARD 1: KOP & PENOMORAN SURAT -->
            <div class="form-card">
                <div class="form-card-head">
                    <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                    1. Kop Instansi &amp; Penomoran Surat
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

                    <div class="form-group">
                        <label class="form-label">Nomor Surat Dinas</label>
                        <input type="text" name="nomor_surat" id="inNomor" value="{{ old('nomor_surat', $autoNomor ?? 'UM.01.02/Bws1/001/2026') }}" class="form-control" required>
                    </div>

                    <div class="grid-2">
                        <div class="form-group">
                            <label class="form-label">Sifat</label>
                            <input type="text" name="sifat" id="inSifat" value="{{ old('sifat', $src ? $src->sifat : 'Biasa / Segera') }}" class="form-control">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Lampiran</label>
                            <input type="text" name="lampiran" id="inLampiran" value="{{ old('lampiran', $src ? $src->lampiran : '1 (satu) Berkas') }}" class="form-control">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Hal / Perihal</label>
                        <input type="text" name="hal" id="inHal" value="{{ old('hal', $src ? $src->hal : 'Undangan Rapat Koordinasi Teknis') }}" class="form-control" required>
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
                </div>
            </div>

            <!-- CARD 2: JADWAL & TEMPAT ACARA -->
            <div class="form-card">
                <div class="form-card-head">
                    <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                    2. Jadwal &amp; Tempat Acara
                </div>
                <div class="form-card-body">
                    <div class="form-group">
                        <label class="form-label">Hari / Tanggal Acara</label>
                        <input type="text" name="hari_tanggal_acara" id="inHariTanggalAcara" value="{{ old('hari_tanggal_acara', $src ? $src->hari_tanggal_acara : 'Senin / 30 November 2026') }}" class="form-control">
                    </div>

                    <div class="grid-2">
                        <div class="form-group">
                            <label class="form-label">Waktu Pelaksanaan</label>
                            <input type="text" name="waktu_acara" id="inWaktuAcara" value="{{ old('waktu_acara', $src ? $src->waktu_acara : '09.00 WIB s.d Selesai') }}" class="form-control">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Tempat Pertemuan</label>
                            <input type="text" name="tempat" id="inTempat" value="{{ old('tempat', $src ? $src->tempat : 'Ruang Rapat BWS Kalimantan I Pontianak') }}" class="form-control" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Agenda / Pembahasan Pokok</label>
                        <textarea name="agenda" id="inAgenda" rows="2" class="form-control">{{ old('agenda', $src ? $src->agenda : 'Rapat Pembahasan Evaluasi Kinerja Triwulan Program Pengelolaan SDA') }}</textarea>
                    </div>
                </div>
            </div>

            <!-- CARD 3: SUSUNAN ACARA -->
            <div class="form-card">
                <div class="form-card-head" style="justify-content: space-between;">
                    <div style="display:flex; align-items:center; gap:8px;">
                        <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/><line x1="3" y1="6" x2="3.01" y2="6"/><line x1="3" y1="12" x2="3.01" y2="12"/><line x1="3" y1="18" x2="3.01" y2="18"/></svg>
                        3. Susunan Acara / Sesi (Opsional)
                    </div>
                    <button type="button" id="btnAddScheduleRow" style="background:#e2e8f0; border:none; padding:4px 10px; border-radius:4px; font-size:11px; font-weight:700; cursor:pointer;">+ Tambah Sesi</button>
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
                    <label style="font-size:12px; color:#475569; display:flex; align-items:center; gap:6px; margin-top:8px; cursor:pointer;">
                        <input type="hidden" name="extra_data[show_schedule]" value="0">
                        <input type="checkbox" name="extra_data[show_schedule]" value="1" id="chkShowSchedule" {{ $showScheduleVal ? 'checked' : '' }}>
                        Tampilkan tabel susunan acara pada lembar surat
                    </label>
                </div>
            </div>

            <!-- CARD 4: DAFTAR PENERIMA & TEMBUSAN -->
            <div class="form-card">
                <div class="form-card-head" style="justify-content: space-between;">
                    <div style="display:flex; align-items:center; gap:8px;">
                        <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                        4. Penerima Undangan &amp; Tembusan
                    </div>
                    <button type="button" id="btnAddPenerima" style="background:#e2e8f0; border:none; padding:4px 10px; border-radius:4px; font-size:11px; font-weight:700; cursor:pointer;">+ Tambah Penerima</button>
                </div>
                <div class="form-card-body">
                    <div class="form-group">
                        <label class="form-label">Mode Penampilan Penerima (Yth.)</label>
                        <select name="extra_data[yth_mode]" id="inYthMode" class="form-control">
                            <option value="langsung" {{ $ythModeVal === 'langsung' ? 'selected' : '' }}>Tampilkan Langsung di Badan Surat</option>
                            <option value="terlampir" {{ $ythModeVal === 'terlampir' ? 'selected' : '' }}>Daftar Terlampir (Otomatis Pindah ke Lampiran I)</option>
                        </select>
                    </div>

                    <div id="penerimaContainer">
                        @foreach($penerimaList as $pNama)
                            <div class="form-group" style="display:flex; gap:6px;">
                                <input type="text" name="penerimas[]" class="form-control in-penerima" value="{{ $pNama }}">
                                <button type="button" style="border:1px solid #fca5a5; background:#fff1f2; color:#b91c1c; border-radius:6px; padding:0 10px; cursor:pointer;" onclick="this.parentElement.remove(); syncPenerimaList();">&times;</button>
                            </div>
                        @endforeach
                    </div>

                    <div class="form-group" style="margin-top: 12px;">
                        <label class="form-label">Tembusan (Opsional — 1 baris per instansi/pejabat)</label>
                        <textarea name="extra_data[tembusan]" id="inTembusan" rows="2" class="form-control" placeholder="Contoh:&#10;Direktur Jenderal Sumber Daya Air&#10;Arsip">{{ $tembusanVal }}</textarea>
                    </div>
                </div>
            </div>

            <!-- CARD 5: PENANDA TANGAN & LEMBAR KONSEP -->
            <div class="form-card">
                <div class="form-card-head">
                    <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 19l7-7 3 3-7 7-3-3z"/><path d="M18 13l-1.5-7.5L2 2l3.5 14.5L13 18l5-5z"/><path d="M2 2l7.586 7.586"/></svg>
                    5. Penanda Tangan, Paraf &amp; Konsep
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
                                    {{ (!$src && str_contains($p->nama, 'Donni')) ? 'selected' : '' }}>
                                    {{ $p->nama_gelar }} — {{ $p->jabatan_definitif ?? $p->jabatan_struktural }}
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

                    <div style="margin:10px 0 14px; padding:10px; background:#f0fdf4; border:1px solid #bbf7d0; border-radius:6px;">
                        <label style="font-size:12.5px; font-weight:600; color:#166534; display:flex; align-items:center; gap:8px; cursor:pointer;">
                            <input type="checkbox" id="chkQrStamp" checked>
                            <span>Sertakan Stempel Digital &amp; QR Code Verifikasi</span>
                        </label>
                    </div>

                    <div style="border-top:1px dashed var(--border); padding-top:12px;">
                        <div style="font-size:12px; font-weight:700; color:var(--pu-navy); margin-bottom:10px; text-transform:uppercase;">Metadata Lembar Paraf &amp; Konsep</div>
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
                                <label class="form-label">Konseptor / Penulis Redaksi</label>
                                <input type="text" name="extra_data[konsep][pen_nama]" id="inPenNama" value="{{ old('extra_data.konsep.pen_nama', $konsep['pen_nama'] ?? 'Staf Pelaksana TU') }}" class="form-control">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Diketik Oleh</label>
                                <input type="text" name="extra_data[konsep][ketik_nama]" id="inKetikNama" value="{{ old('extra_data.konsep.ketik_nama', $konsep['ketik_nama'] ?? 'Operator TU') }}" class="form-control">
                            </div>
                        </div>
                        <div class="grid-3">
                            <div class="form-group">
                                <label class="form-label">Deadline</label>
                                <input type="date" name="extra_data[konsep][deadline]" id="inDeadline" value="{{ old('extra_data.konsep.deadline', $konsep['deadline'] ?? '') }}" class="form-control">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Jumlah Lembar</label>
                                <input type="number" min="1" name="extra_data[konsep][jumlah_lembar]" id="inJmlLembar" value="{{ old('extra_data.konsep.jumlah_lembar', $konsep['jumlah_lembar'] ?? 1) }}" class="form-control">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Dikembalikan Kepada</label>
                                <input type="text" name="extra_data[konsep][kembali]" id="inKembali" value="{{ old('extra_data.konsep.kembali', $konsep['kembali'] ?? 'Subbag Umum & TU') }}" class="form-control">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- SUBMIT BUTTONS -->
            <div style="display:flex; gap:12px; margin-bottom: 24px;">
                @if($isEdit)
                    <a href="{{ route('surat.undangan.show', $surat->id) }}" class="btn-quick-create" style="background:#f1f5f9; color:#334155; justify-content:center; padding:12px 18px;">
                        Batal
                    </a>
                @endif
                <button type="submit" class="btn-quick-create" style="flex:1; justify-content:center; padding:12px; font-size:14px;">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
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
                <button type="button" id="btnExportDocx" class="btn-toolbar-sm" style="background:#0284c7; color:#ffffff;">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                    Unduh Word (.docx)
                </button>
                <button type="button" onclick="printActiveSheet()" class="btn-toolbar-sm" style="background:var(--pu-navy); color:#ffffff;">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
                    Cetak PDF
                </button>
                <button type="button" onclick="printAllSheets()" class="btn-toolbar-sm" style="background:#334155; color:#ffffff;" title="Cetak semua lembar sekaligus dalam satu PDF">
                    Cetak Semua Lembar
                </button>
            </div>
        </div>

        <div class="preview-scroll-wrapper">
            <!-- 1. VIEW LEMBAR ASLI & PARAF -->
            <div class="sheet-a4 printable-sheet" id="viewSheetAsli">
                <table class="kop-table">
                    <tr>
                        <td class="kop-logo-cell">
                            <img src="{{ asset('images/logo_pu.png') }}" alt="Logo Kementerian PU" class="kop-logo-img">
                        </td>
                        <td class="kop-text-cell">
                            <div class="kop-line-1" id="pvKop1">{{ $kop['kementerian'] }}</div>
                            <div class="kop-line-2" id="pvKop2">{{ $kop['ditjen'] }}</div>
                            <div id="pvKop3Wrap">
                                <div class="kop-line-3">{{ $kop['balai'] }}</div>
                            </div>
                            <div class="kop-line-address" id="pvKopAddr">Jl. A. Sood Nomor 06 Pontianak 78121, Telepon (0561) 734866, Surel bwsk1pontianak@pu.go.id</div>
                        </td>
                    </tr>
                </table>

                <table class="letter-meta-table">
                    <tr>
                        <td style="width: 80px;">Nomor</td>
                        <td style="width: 15px;">:</td>
                        <td id="pvNomor">{{ $autoNomor ?? 'UM.01.02/Bws1/001/2026' }}</td>
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

                <div class="undangan-tujuan-box">
                    <div>Yth.</div>
                    <ol class="undangan-list" id="pvListPenerima"></ol>
                    <div id="pvTerlampirText" style="display:none; margin-top:2px;">(Daftar Terlampir)</div>
                    <div style="margin-top: 4px;">di -</div>
                    <div style="padding-left: 20px; font-weight: bold;">Tempat</div>
                </div>

                <div style="text-align: justify; text-indent: 36px; margin-top: 14px;">
                    Sehubungan dengan pelaksanaan program kegiatan pada Balai Wilayah Sungai Kalimantan I Pontianak, dengan ini kami mengharapkan kehadiran Bapak/Ibu pada rapat yang akan dilaksanakan pada:
                </div>

                <table style="width: 100%; margin: 12px 0 12px 36px; font-size: 14.5px;">
                    <tr>
                        <td style="width: 130px; padding: 2px 0;">Hari / Tanggal</td>
                        <td style="width: 15px;">:</td>
                        <td id="pvHariTanggal" style="font-weight: 500;"></td>
                    </tr>
                    <tr>
                        <td style="padding: 2px 0;">Waktu</td>
                        <td>:</td>
                        <td id="pvWaktu"></td>
                    </tr>
                    <tr>
                        <td style="padding: 2px 0;">Tempat</td>
                        <td>:</td>
                        <td id="pvTempat"></td>
                    </tr>
                    <tr>
                        <td style="padding: 2px 0;">Agenda</td>
                        <td>:</td>
                        <td id="pvAgenda" style="font-weight: bold;"></td>
                    </tr>
                </table>

                <div id="pvScheduleBox" style="margin: 12px 0;">
                    <table class="schedule-table-preview" id="pvScheduleTable">
                        <thead>
                            <tr>
                                <th style="width:140px;">Waktu</th>
                                <th>Agenda / Acara</th>
                                <th style="width:180px;">Narasumber / PIC</th>
                            </tr>
                        </thead>
                        <tbody id="pvScheduleTbody"></tbody>
                    </table>
                </div>

                <div style="text-align: justify; text-indent: 36px; margin-top: 14px;">
                    Mengingat pentingnya acara tersebut di atas, diharapkan hadir tepat pada waktunya. Demikian kami sampaikan, atas perhatian dan kehadirannya diucapkan terima kasih.
                </div>

                <!-- TANDA TANGAN + KOTAK PARAF (SEJAJAR DI KIRI JABATAN SESUAI DOCGEN.JS) -->
                <table class="ttd-paraf-grid">
                    <tr>
                        <td style="width: 48%; vertical-align: bottom;">
                            <div id="pvParafHierarkiBox" style="display:none; margin-bottom: 10px;">
                                <div class="paraf-mini-box">
                                    <div id="pvParafJab1">Kasubbag Umum &amp; TU</div>
                                    <div id="pvParafVal1" style="font-weight:700; margin-top:26px;">Iswandi, S.E., S.T.</div>
                                </div>
                            </div>
                            <div id="pvQrSealWrapper" style="padding: 4px 0;">
                                <div class="qr-verify-seal" style="display:inline-flex; align-items:center; gap:8px; border:1px dashed #94a3b8; border-radius:6px; padding:5px 9px; background:#f8fafc;">
                                    <div id="qrCanvasContainer"></div>
                                    <div style="font-size:9px; color:#334155; line-height:1.3; max-width:155px;">
                                        <div style="font-weight:700; color:#13243d;">VERIFIKASI DOKUMEN</div>
                                        <div>Pindai QR untuk cek keaslian arsip digital BWS Kalimantan I.</div>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td style="width: 52%; text-align: center; vertical-align: top;">
                            <div id="pvTtdJabatan"></div>
                            <div style="height: 76px;"></div>
                            <div id="pvTtdNama" style="font-weight: bold;"></div>
                            <div id="pvTtdNip"></div>
                        </td>
                    </tr>
                </table>

                <!-- TEMBUSAN -->
                <div id="pvTembusanBox" style="display:none; margin-top: 22px; font-size: 13.5px;">
                    <div>Tembusan:</div>
                    <ol id="pvTembusanList" style="padding-left: 20px; margin-top: 4px; line-height: 1.45;"></ol>
                </div>
            </div>

            <!-- 2. VIEW LEMBAR LAMPIRAN I -->
            <div class="sheet-a4 printable-sheet" id="viewSheetLampiran" style="display:none;">
                <div style="margin-left: 50%; font-size: 13.5px; line-height: 1.4; margin-bottom: 24px;">
                    <div>Lampiran I Surat <span id="pvLpJabHeader">Kepala BWS Kalimantan I</span></div>
                    <div>Nomor : <span id="pvLpNomor"></span></div>
                    <div>Tanggal : <span id="pvLpTanggal"></span></div>
                </div>

                <div style="text-align: center; font-weight: bold; font-size: 15px; margin: 24px 0 18px 0; letter-spacing: 0.4px;">
                    DAFTAR UNDANGAN
                </div>

                <ol id="pvLpListContainer" style="line-height: 1.75; font-size: 14px; padding-left: 28px;"></ol>

                <div style="width: 320px; margin-left: auto; text-align: center; margin-top: 50px; font-size: 14px;">
                    <div id="pvLpTtdJabatan"></div>
                    <div style="height: 72px;"></div>
                    <div id="pvLpTtdNama" style="font-weight: bold;"></div>
                    <div id="pvLpTtdNip"></div>
                </div>
            </div>

            <!-- 3. VIEW LEMBAR KONSEP NASKAH DINAS -->
            <div class="sheet-a4 printable-sheet" id="viewSheetKonsep" style="display:none;">
                <table style="width:100%; border-collapse:collapse; margin-bottom:10px;">
                    <tr>
                        <td style="width:78%; vertical-align:bottom;">
                            <table class="kop-table" style="margin-bottom:0;">
                                <tr>
                                    <td class="kop-logo-cell" style="width:70px;">
                                        <img src="{{ asset('images/logo_pu.png') }}" alt="Logo Kementerian PU" style="width:64px; height:64px; object-fit:contain;">
                                    </td>
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
                        <td rowspan="2" style="width:27%;">
                            Konsep ini setelah suratnya dikirim harap dikembalikan kepada:<br><br>
                            <strong id="pvKpKembali">Subbag Umum &amp; TU</strong>
                        </td>
                        <td rowspan="2" style="width:23%; text-align:center;">
                            Konfirmasi / paraf / tanggal<br><br>
                            <span id="pvKpKonfJab">Kasubbag Umum &amp; TU</span><br><br>
                            <strong id="pvKpKonfNama">Iswandi, S.E., S.T.</strong>
                        </td>
                        <td rowspan="2" style="width:23%; text-align:center;">
                            Pemeriksa Akhir<br>Paraf &amp; Tanggal<br><br>
                            <span id="pvKpPemJab">Kepala Subbagian TU</span><br><br>
                            <strong id="pvKpPemNama">Iswandi, S.E., S.T.</strong>
                        </td>
                        <td style="width:27%; height:65px;">
                            Supaya diajukan lagi tanggal<br><br>
                            <div style="text-align:center;">…………………………</div>
                        </td>
                    </tr>
                    <tr>
                        <td style="height:60px; text-align:center;">
                            Ditetapkan:<br><br>
                            <strong id="pvKpTetapNama"></strong>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            Jumlah Lembar : <strong id="pvKpJmlLembar">1 (Satu)</strong>
                        </td>
                        <td colspan="3" rowspan="4" style="font-size:11px; line-height:1.45;">
                            <div style="display:flex; justify-content:space-between;">
                                <span>Nomor : <strong id="pvKpNomor"></strong></span>
                                <span id="pvKpTglMini"></span>
                            </div>
                            <div>Hal : <strong id="pvKpHal"></strong></div>
                            <div style="margin-top:6px;">Yth. <span id="pvKpYthMini"></span></div>
                            <div style="margin-top:6px; text-align:justify;">
                                Sehubungan dengan pelaksanaan program kegiatan pada Balai Wilayah Sungai Kalimantan I Pontianak, dengan ini kami mengharapkan kehadiran Bapak/Ibu pada rapat yang akan dilaksanakan pada:
                            </div>
                            <div style="margin:6px 0 6px 14px;">
                                Hari/Tanggal : <span id="pvKpHari"></span><br>
                                Waktu : <span id="pvKpWaktu"></span><br>
                                Tempat : <span id="pvKpTempat"></span><br>
                                Agenda : <strong id="pvKpAgenda"></strong>
                            </div>
                            <div style="margin-top:16px; text-align:right; padding-right:20px;">
                                <div id="pvKpTtdJab"></div>
                                <div style="height:36px;"></div>
                                <strong id="pvKpTtdNama"></strong>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            No. Kode Klasifikasi :<br>
                            <div style="text-align:center; font-weight:700; margin-top:4px;" id="pvKpKlasifikasi"></div>
                        </td>
                    </tr>
                    <tr>
                        <td style="height:85px;">
                            Konsep/Penulis Redaksi dari:<br><br>
                            <div style="text-align:center; font-weight:700;" id="pvKpPenNama">Staf Pelaksana TU</div>
                        </td>
                    </tr>
                    <tr>
                        <td style="height:85px;">
                            Diketik Oleh :<br><br>
                            <div style="text-align:center; font-weight:700;" id="pvKpKetikNama">Operator TU</div>
                        </td>
                    </tr>
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
<script src="{{ asset('js/qrcode.min.js') }}"></script>

<script>
    const KOP_PRESETS = @json($kopPresets);

    const inKopType = document.getElementById('inKopType');
    const inYthMode = document.getElementById('inYthMode');
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
    const inTembusan = document.getElementById('inTembusan');
    const inTtdNama = document.getElementById('inTtdNama');
    const inTtdNip = document.getElementById('inTtdNip');
    const inTtdJabatan = document.getElementById('inTtdJabatan');
    const inKonfJab = document.getElementById('inKonfJab');
    const inKonfNama = document.getElementById('inKonfNama');
    const inPemJab = document.getElementById('inPemJab');
    const inPemNama = document.getElementById('inPemNama');
    const inPenNama = document.getElementById('inPenNama');
    const inKetikNama = document.getElementById('inKetikNama');
    const inDeadline = document.getElementById('inDeadline');
    const inJmlLembar = document.getElementById('inJmlLembar');
    const inKembali = document.getElementById('inKembali');
    const selectPegawai = document.getElementById('selectPegawaiDb');
    const chkShowSchedule = document.getElementById('chkShowSchedule');
    const chkQrStamp = document.getElementById('chkQrStamp');
    const selPreviewPage = document.getElementById('selPreviewPage');

    function updateKopPreview() {
        const preset = KOP_PRESETS[inKopType.value] || KOP_PRESETS.balai;
        document.getElementById('pvKop1').innerText = preset.kementerian;
        document.getElementById('pvKop2').innerText = preset.ditjen;
        const wrap = document.getElementById('pvKop3Wrap');
        wrap.innerHTML = '';
        (preset.lines3 || []).forEach(line => {
            const d = document.createElement('div');
            d.className = 'kop-line-3';
            d.innerText = line;
            wrap.appendChild(d);
        });
        document.getElementById('pvKopAddr').innerText = preset.alamat;
    }

    function updateTanggalPreview() {
        const val = inTanggalSurat.value;
        const p = val ? val.split('-') : [];
        const d = p.length === 3 ? new Date(+p[0], +p[1] - 1, +p[2]) : new Date();
        const months = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
        const formatted = d.getDate() + ' ' + months[d.getMonth()] + ' ' + d.getFullYear();
        const fullDate = (inKota.value ? inKota.value + ', ' : '') + formatted;
        document.getElementById('pvTanggal').innerText = fullDate;
        document.getElementById('pvLpTanggal').innerText = formatted;
        document.getElementById('pvKpTglMini').innerText = fullDate;
        renderQrSeal();
    }

    function syncPenerimaList() {
        const inputs = document.querySelectorAll('.in-penerima');
        const pvList = document.getElementById('pvListPenerima');
        const pvLpList = document.getElementById('pvLpListContainer');
        const terlampirText = document.getElementById('pvTerlampirText');
        const isTerlampir = inYthMode.value === 'terlampir';

        pvList.innerHTML = '';
        pvLpList.innerHTML = '';
        const names = [];
        inputs.forEach(inp => {
            if (inp.value.trim()) {
                names.push(inp.value.trim());
                const li = document.createElement('li');
                li.innerText = inp.value.trim();
                pvList.appendChild(li);

                const li2 = document.createElement('li');
                li2.innerText = inp.value.trim();
                pvLpList.appendChild(li2);
            }
        });

        pvList.style.display = isTerlampir ? 'none' : 'block';
        terlampirText.style.display = isTerlampir ? 'block' : 'none';
        document.getElementById('pvKpYthMini').innerText = isTerlampir ? '(Daftar Terlampir)' : (names[0] || '');
    }

    function syncTembusan() {
        const lines = (inTembusan.value || '').split('\n').map(s => s.trim()).filter(Boolean);
        const box = document.getElementById('pvTembusanBox');
        const list = document.getElementById('pvTembusanList');
        list.innerHTML = '';
        if (lines.length === 0) {
            box.style.display = 'none';
            return;
        }
        box.style.display = 'block';
        lines.forEach(l => {
            const li = document.createElement('li');
            li.innerText = l;
            list.appendChild(li);
        });
    }

    function syncSchedulePreview() {
        const rows = document.querySelectorAll('.schedule-row');
        const tbody = document.getElementById('pvScheduleTbody');
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
                tr.innerHTML = `<td style="text-align:center;">${w}</td><td>${a}</td><td>${p}</td>`;
                tbody.appendChild(tr);
            }
        });
        document.getElementById('inScheduleJson').value = JSON.stringify(jsonArr);
        document.getElementById('pvScheduleBox').style.display = (chkShowSchedule.checked && hasData) ? 'block' : 'none';
    }

    function syncKonsep() {
        document.getElementById('pvParafJab1').innerText = inKonfJab.value || 'Kasubbag Umum & TU';
        document.getElementById('pvParafVal1').innerText = inKonfNama.value || '';
        document.getElementById('pvKpKonfJab').innerText = inKonfJab.value || '';
        document.getElementById('pvKpKonfNama').innerText = inKonfNama.value || '';
        document.getElementById('pvKpPemJab').innerText = inPemJab.value || '';
        document.getElementById('pvKpPemNama').innerText = inPemNama.value || '';
        document.getElementById('pvKpPenNama').innerText = inPenNama.value || '';
        document.getElementById('pvKpKetikNama').innerText = inKetikNama.value || '';
        document.getElementById('pvKpKembali').innerText = inKembali.value || '';
        document.getElementById('pvKpDeadline').innerText = inDeadline.value ? DocGen.fmtTgl(inDeadline.value) : '-';
        const jml = parseInt(inJmlLembar.value || '1', 10);
        document.getElementById('pvKpJmlLembar').innerText = jml + ' (' + DocGen.terbilang(jml) + ')';
        document.getElementById('pvKpTetapNama').innerText = inTtdNama.value;
    }

    function syncAllFields() {
        document.getElementById('pvNomor').innerText = inNomor.value;
        document.getElementById('pvLpNomor').innerText = inNomor.value;
        document.getElementById('pvKpNomor').innerText = inNomor.value;
        document.getElementById('pvKpKlasifikasi').innerText = inNomor.value;
        document.getElementById('pvSifat').innerText = inSifat.value;
        document.getElementById('pvLampiran').innerText = inLampiran.value;
        document.getElementById('pvHal').innerText = inHal.value;
        document.getElementById('pvKpHal').innerText = inHal.value;
        document.getElementById('pvHariTanggal').innerText = inHariTanggal.value;
        document.getElementById('pvKpHari').innerText = inHariTanggal.value;
        document.getElementById('pvWaktu').innerText = inWaktu.value;
        document.getElementById('pvKpWaktu').innerText = inWaktu.value;
        document.getElementById('pvTempat').innerText = inTempat.value;
        document.getElementById('pvKpTempat').innerText = inTempat.value;
        document.getElementById('pvAgenda').innerText = inAgenda.value;
        document.getElementById('pvKpAgenda').innerText = inAgenda.value;
        document.getElementById('pvTtdJabatan').innerText = inTtdJabatan.value;
        document.getElementById('pvLpJabHeader').innerText = inTtdJabatan.value;
        document.getElementById('pvLpTtdJabatan').innerText = inTtdJabatan.value;
        document.getElementById('pvKpTtdJab').innerText = inTtdJabatan.value;
        document.getElementById('pvTtdNama').innerText = inTtdNama.value;
        document.getElementById('pvLpTtdNama').innerText = inTtdNama.value;
        document.getElementById('pvKpTtdNama').innerText = inTtdNama.value;
        const nipVal = inTtdNip.value ? 'NIP. ' + inTtdNip.value : '';
        document.getElementById('pvTtdNip').innerText = nipVal;
        document.getElementById('pvLpTtdNip').innerText = nipVal;
        updateKopPreview();
        updateTanggalPreview();
        syncPenerimaList();
        syncTembusan();
        syncSchedulePreview();
        syncKonsep();
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
            <input type="text" class="form-control sc-waktu" placeholder="Waktu">
            <input type="text" class="form-control sc-acara" placeholder="Kegiatan / Acara">
            <div style="display:flex; gap:4px;">
                <input type="text" class="form-control sc-pic" placeholder="Narasumber / PIC">
                <button type="button" style="border:1px solid #fca5a5; background:#fff1f2; color:#b91c1c; border-radius:4px; padding:0 8px; cursor:pointer;" onclick="removeScheduleRow(this)">&times;</button>
            </div>
        `;
        container.appendChild(div);
        div.querySelectorAll('input').forEach(inp => inp.addEventListener('input', syncSchedulePreview));
    });

    document.getElementById('btnAddPenerima').addEventListener('click', function() {
        const container = document.getElementById('penerimaContainer');
        const div = document.createElement('div');
        div.className = 'form-group';
        div.style.display = 'flex';
        div.style.gap = '6px';
        div.innerHTML = `
            <input type="text" name="penerimas[]" class="form-control in-penerima" placeholder="Nama instansi / jabatan penerima...">
            <button type="button" style="border:1px solid #fca5a5; background:#fff1f2; color:#b91c1c; border-radius:6px; padding:0 10px; cursor:pointer;" onclick="this.parentElement.remove(); syncPenerimaList();">&times;</button>
        `;
        container.appendChild(div);
        div.querySelector('input').addEventListener('input', syncPenerimaList);
    });

    function renderQrSeal() {
        const wrapper = document.getElementById('pvQrSealWrapper');
        if (!chkQrStamp.checked) { wrapper.style.display = 'none'; return; }
        wrapper.style.display = 'block';
        const container = document.getElementById('qrCanvasContainer');
        container.innerHTML = '';
        const qrText = 'VERIFIKASI SURAT DINAS BWS KALIMANTAN I\nNo: ' + inNomor.value + '\nHal: ' + inHal.value + '\nTgl: ' + inTanggalSurat.value + '\nTTD: ' + inTtdNama.value;
        if (typeof QRCode !== 'undefined') {
            new QRCode(container, { text: qrText, width: 58, height: 58, colorDark: '#13243d', colorLight: '#ffffff', correctLevel: QRCode.CorrectLevel.M });
        }
    }

    selPreviewPage.addEventListener('change', function() {
        const val = this.value;
        const sheetAsli = document.getElementById('viewSheetAsli');
        const sheetLampiran = document.getElementById('viewSheetLampiran');
        const sheetKonsep = document.getElementById('viewSheetKonsep');
        const parafBox = document.getElementById('pvParafHierarkiBox');
        sheetAsli.style.display = (val === 'asli' || val === 'paraf') ? 'block' : 'none';
        sheetLampiran.style.display = (val === 'lampiran') ? 'block' : 'none';
        sheetKonsep.style.display = (val === 'konsep') ? 'block' : 'none';
        parafBox.style.display = (val === 'paraf') ? 'block' : 'none';
    });

    selectPegawai.addEventListener('change', function() {
        const opt = this.options[this.selectedIndex];
        if (opt.value) {
            inTtdNama.value = opt.getAttribute('data-nama') || '';
            inTtdNip.value = opt.getAttribute('data-nip') || '';
            inTtdJabatan.value = opt.getAttribute('data-jabatan') || '';
            syncAllFields();
        }
    });

    [inKopType, inYthMode].forEach(el => el.addEventListener('change', syncAllFields));
    [inNomor, inSifat, inLampiran, inHal, inKota, inTanggalSurat, inHariTanggal, inWaktu, inTempat, inAgenda, inTembusan,
     inTtdNama, inTtdNip, inTtdJabatan, inKonfJab, inKonfNama, inPemJab, inPemNama, inPenNama, inKetikNama, inDeadline, inJmlLembar, inKembali]
        .forEach(el => el.addEventListener('input', syncAllFields));
    document.querySelectorAll('.in-penerima').forEach(inp => inp.addEventListener('input', syncPenerimaList));
    document.querySelectorAll('.schedule-row input').forEach(inp => inp.addEventListener('input', syncSchedulePreview));
    chkShowSchedule.addEventListener('change', syncSchedulePreview);
    chkQrStamp.addEventListener('change', renderQrSeal);
    document.getElementById('formSurat').addEventListener('submit', syncSchedulePreview);

    function printActiveSheet() {
        document.body.classList.remove('print-all-sheets');
        window.print();
    }
    function printAllSheets() {
        document.body.classList.add('print-all-sheets');
        window.print();
        setTimeout(() => document.body.classList.remove('print-all-sheets'), 500);
    }

    syncAllFields();

    // Export DOCX (.docx) sesuai standar DocGen
    document.getElementById('btnExportDocx').addEventListener('click', function() {
        const btn = this;
        const originalText = btn.innerHTML;
        btn.innerHTML = 'Membuat .docx...';
        btn.disabled = true;

        try {
            const penerimas = [];
            document.querySelectorAll('.in-penerima').forEach(inp => {
                if (inp.value.trim()) penerimas.push(inp.value.trim());
            });
            const preset = KOP_PRESETS[inKopType.value] || KOP_PRESETS.balai;

            const docData = {
                mode: 'undangan',
                nomor: inNomor.value,
                sifat: inSifat.value,
                lampiran: inLampiran.value,
                hal: inHal.value,
                kota: inKota.value,
                tanggal: inTanggalSurat.value,
                ythMode: inYthMode.value,
                ythText: penerimas.join('\n'),
                daftarUndangan: penerimas.join('\n'),
                diTempat: 'Tempat',
                tembusan: inTembusan.value,
                ttd_jab: inTtdJabatan.value,
                ttd_nama: inTtdNama.value,
                ttd_nip: inTtdNip.value,
                showNip: true,
                konf_jab: inKonfJab.value,
                konf_nama: inKonfNama.value,
                pem_jab: inPemJab.value,
                pem_nama: inPemNama.value,
                pen_nama: inPenNama.value,
                ketik_nama: inKetikNama.value,
                kembali: inKembali.value,
                deadline: inDeadline.value,
                jumlahLembar: inJmlLembar.value,
                tetap_nama: inTtdNama.value,
                body: [
                    {
                        type: 'paragraph',
                        indent: true,
                        text: 'Sehubungan dengan pelaksanaan program kegiatan pada Balai Wilayah Sungai Kalimantan I Pontianak, dengan ini kami mengharapkan kehadiran Bapak/Ibu pada rapat yang akan dilaksanakan pada:'
                    },
                    {
                        type: 'points',
                        items: [
                            { label: 'Hari / Tanggal', value: inHariTanggal.value },
                            { label: 'Waktu', value: inWaktu.value },
                            { label: 'Tempat', value: inTempat.value },
                            { label: 'Agenda', value: inAgenda.value }
                        ]
                    },
                    {
                        type: 'paragraph',
                        indent: false,
                        text: 'Mengingat pentingnya acara tersebut di atas, diharapkan hadir tepat pada waktunya. Demikian kami sampaikan, atas perhatian dan kehadirannya diucapkan terima kasih.'
                    }
                ],
                pages: { asli: true, paraf: true, konsep: true, lampiran: inYthMode.value === 'terlampir' },
                margin: { top: 2.5, bottom: 2.5, left: 2.5, right: 2.5 }
            };

            const kopSpec = {
                line1: preset.kementerian,
                line2: preset.ditjen,
                line3: preset.lines3,
                line4: preset.alamat
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
                a.download = ('Surat_Undangan_' + (inHal.value || 'Dinas')).replace(/[^a-zA-Z0-9_-]/g, '_') + '.docx';
                document.body.appendChild(a);
                a.click();
                document.body.removeChild(a);
                URL.revokeObjectURL(url);
                btn.innerHTML = originalText;
                btn.disabled = false;
            }).catch(function(err) {
                alert('Ekspor Word gagal: ' + (err.message || err));
                btn.innerHTML = originalText;
                btn.disabled = false;
            });
        } catch (e) {
            btn.innerHTML = originalText;
            btn.disabled = false;
        }
    });
</script>
@endpush
