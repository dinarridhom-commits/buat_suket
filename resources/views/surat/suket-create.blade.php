@extends('layouts.app')

@section('title', 'Buat Suket Lupa Absen')
@section('header_title', 'Surat Keterangan Lupa Absen')
@section('header_subtitle', 'Format Resmi Balai Wilayah Sungai Kalimantan I Pontianak — Kop Balai 1 Halaman')

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

    .form-card-body {
        padding: 16px 18px;
    }

    .form-group {
        margin-bottom: 14px;
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
    }

    .preview-scroll-wrapper {
        background: #cbd5e1;
        border-radius: var(--radius-lg);
        padding: 24px;
        max-height: calc(100vh - 170px);
        overflow-y: auto;
        display: flex;
        justify-content: center;
    }

    .sheet-a4 {
        width: 794px;
        min-height: 1123px;
        background: #ffffff;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.15);
        padding: 50px 60px;
        font-family: Arial, Helvetica, sans-serif;
        color: #000000;
        font-size: 14.5px;
        line-height: 1.5;
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

    .kop-table td { vertical-align: middle; }
    .kop-logo-cell { width: 90px; text-align: center; }
    .kop-logo-img { width: 80px; height: auto; }
    .kop-text-cell { text-align: center; padding-right: 20px; }
    .kop-line-1 { font-size: 14.5px; font-weight: bold; }
    .kop-line-2 { font-size: 15.5px; font-weight: bold; }
    .kop-line-3 { font-size: 16px; font-weight: bold; }
    .kop-line-address { font-size: 10.5px; margin-top: 3px; line-height: 1.3; }
    .kop-border-bottom {
        border-bottom: 2.5px solid #000000;
        margin-top: 6px;
        margin-bottom: 20px;
    }

    .suket-title {
        text-align: center;
        margin-bottom: 22px;
    }

    .suket-title h3 {
        margin: 0 0 4px 0;
        font-size: 17px;
        font-weight: 700;
        text-decoration: underline;
        letter-spacing: 0.5px;
    }

    .ttd-dual-table {
        width: 100%;
        margin-top: 40px;
        font-size: 14px;
    }

    .ttd-dual-table td {
        width: 50%;
        vertical-align: top;
        text-align: center;
    }

    .qr-verify-seal {
        margin: 6px auto;
        padding: 5px;
        border: 1px dashed #94a3b8;
        border-radius: 6px;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: #f8fafc;
        max-width: 250px;
        text-align: left;
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
        <form method="POST" action="{{ route('surat.suket.store') }}" id="formSuket">
            @csrf

            <!-- CARD 1: DATA PEGAWAI -->
            <div class="form-card">
                <div class="form-card-head">
                    <span>👤</span> 1. Pegawai yang Bersangkutan
                </div>
                <div class="form-card-body">
                    <div class="form-group">
                        <label class="form-label">Pilih dari Master Pegawai (148 Pegawai BWS Kalimantan I)</label>
                        <select id="selectPegawai" class="form-control" style="background:#f1f5f9; font-weight:600;">
                            <option value="">-- Pilih Nama Pegawai --</option>
                            @foreach($pegawais as $p)
                                <option value="{{ $p->id }}" 
                                    data-nama="{{ $p->nama_gelar }}" 
                                    data-nip="{{ $p->nip }}" 
                                    data-jabatan="{{ $p->jabatan_definitif ?? $p->jabatan_struktural ?? 'Staf Pelaksana' }}">
                                    {{ $p->nama_gelar }} — {{ $p->nip }} ({{ $p->unit ?? 'BWS Kalimantan I' }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid-2">
                        <div class="form-group">
                            <label class="form-label">Nama Pegawai</label>
                            <input type="text" name="nama_pegawai" id="inNamaPegawai" value="Juananda Dwi Satria, S.T." class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">NIP</label>
                            <input type="text" name="nip_pegawai" id="inNipPegawai" value="199407072025061009" class="form-control">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Jabatan</label>
                        <input type="text" name="jabatan_pegawai" id="inJabatanPegawai" value="Penata Kelola Penyehatan Lingkungan Ahli Pertama" class="form-control" required>
                    </div>
                </div>
            </div>

            <!-- CARD 2: RINCIAN KENDALA ABSEN -->
            <div class="form-card">
                <div class="form-card-head">
                    <span>⏰</span> 2. Rincian Lupa Absen
                </div>
                <div class="form-card-body">
                    <div class="form-group">
                        <label class="form-label">Nomor Surat Keterangan</label>
                        <input type="text" name="nomor_surat" id="inNomor" value="{{ $autoNomor ?? 'KP.07.03/Bws1/001/2026' }}" class="form-control" required>
                    </div>

                    <div class="grid-2">
                        <div class="form-group">
                            <label class="form-label">Jenis Absensi</label>
                            <div style="display:flex; gap:16px; margin-top:8px;">
                                <label style="display:flex; align-items:center; gap:6px; font-size:13px; cursor:pointer;">
                                    <input type="radio" name="jenis_absen" value="masuk" checked> Masuk Kerja
                                </label>
                                <label style="display:flex; align-items:center; gap:6px; font-size:13px; cursor:pointer;">
                                    <input type="radio" name="jenis_absen" value="pulang"> Pulang Kerja
                                </label>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Waktu Kejadian Presensi</label>
                            <input type="text" name="waktu_kejadian" id="inWaktuKejadian" value="07:30" class="form-control" placeholder="07:30">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Tanggal Kejadian Lupa Absen</label>
                        <input type="date" name="tanggal_kejadian" id="inTanggalAbsen" value="{{ date('Y-m-d') }}" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Alasan Kendala</label>
                        <textarea name="alasan" id="inAlasan" rows="2" class="form-control" required>Jaringan internet seluler bermasalah dan aplikasi absensi online sedang maintenance saat jam presensi.</textarea>
                    </div>
                </div>
            </div>

            <!-- CARD 3: ATASAN LANGSUNG -->
            <div class="form-card">
                <div class="form-card-head">
                    <span>👔</span> 3. Pejabat yang Menyetujui
                </div>
                <div class="form-card-body">
                    <div class="form-group">
                        <label class="form-label">Jabatan Atasan Langsung</label>
                        <input type="text" name="atasan_jabatan" id="inAtasanJabatan" value="Kepala Sub Bagian Umum dan Tata Usaha" class="form-control" required>
                    </div>

                    <div class="grid-2">
                        <div class="form-group">
                            <label class="form-label">Nama Atasan Langsung</label>
                            <input type="text" name="atasan_nama" id="inAtasanNama" value="Iswandi, S.E., S.T." class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">NIP Atasan</label>
                            <input type="text" name="atasan_nip" id="inAtasanNip" value="197603222009111001" class="form-control">
                        </div>
                    </div>

                    <div style="margin-top:10px; padding:10px; background:#f0fdf4; border:1px solid #bbf7d0; border-radius:6px;">
                        <label style="font-size:12.5px; font-weight:600; color:#166534; display:flex; align-items:center; gap:8px; cursor:pointer;">
                            <input type="checkbox" id="chkQrStampSuket" checked>
                            <span>Sertakan Stempel Digital & QR Code e-Sign BWS Kalimantan I</span>
                        </label>
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

    <!-- RIGHT PANE: LIVE A4 PREVIEW -->
    <div class="preview-pane">
        <div class="preview-header-bar">
            <span style="font-size:13px; font-weight:700; color:var(--text-main); display:flex; align-items:center; gap:6px;">
                <span>👁️</span> Pratinjau Kertas A4 (Siap Cetak Standar BWS)
            </span>
            <div style="display:flex; gap:8px;">
                <button type="button" onclick="window.print()" class="btn-action" style="background:var(--pu-navy); color:#ffffff; font-weight:600; padding:6px 14px; font-size:12.5px; display:flex; align-items:center; gap:5px; border-radius:6px; border:none; cursor:pointer;">
                    <span>🖨️</span> Cetak / PDF
                </button>
            </div>
        </div>

        <div class="preview-scroll-wrapper">
            <div class="sheet-a4" id="sheetPrintArea">
                <!-- KOP RESMI -->
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
                            <div class="kop-line-address">{{ $kop['kontak'] }}</div>
                        </td>
                    </tr>
                </table>
                <div class="kop-border-bottom"></div>

                <!-- TITLE -->
                <div class="suket-title">
                    <h3>SURAT KETERANGAN LUPA ABSEN</h3>
                    <div style="font-size:13.5px;" id="pvNomor">Nomor : UM.01.02/Bws1/      /2026</div>
                </div>

                <div style="margin-bottom:14px;">Yang bertanda tangan di bawah ini menerangkan bahwa:</div>

                <table style="width:100%; margin-left:24px; margin-bottom:18px; font-size:14.5px;">
                    <tr>
                        <td style="width:130px; padding:2px 0;">Nama</td>
                        <td style="width:15px;">:</td>
                        <td id="pvNama" style="font-weight:bold;">Juananda Dwi Satria, S.T.</td>
                    </tr>
                    <tr>
                        <td style="padding:2px 0;">NIP</td>
                        <td>:</td>
                        <td id="pvNip">199407072025061009</td>
                    </tr>
                    <tr>
                        <td style="padding:2px 0;">Jabatan</td>
                        <td>:</td>
                        <td id="pvJabatan">Penata Kelola Penyehatan Lingkungan Ahli Pertama</td>
                    </tr>
                </table>

                <div style="text-align:justify; line-height:1.6; margin-bottom:16px;">
                    Benar bahwa pegawai yang bersangkutan telah hadir bekerja dan mengalami kendala lupa melakukan presensi <b id="pvJenisAbsen">MASUK</b> pada aplikasi absensi online pada tanggal <span id="pvTanggal">{{ date('d F Y') }}</span> pukul <span id="pvWaktu">07:30</span> WIB dengan alasan: <i id="pvAlasan">Jaringan internet seluler bermasalah dan aplikasi absensi online sedang maintenance saat jam presensi.</i>
                </div>

                <div style="text-align:justify; line-height:1.6;">
                    Demikian surat keterangan ini dibuat dengan sebenarnya untuk dipergunakan sebagaimana mestinya.
                </div>

                <!-- TTD DUAL -->
                <table class="ttd-dual-table">
                    <tr>
                        <td>
                            <div>Pegawai yang bersangkutan,</div>
                            <div style="height:65px;"></div>
                            <div id="pvTtdPegawai" style="font-weight:bold; text-decoration:underline;">Juananda Dwi Satria, S.T.</div>
                            <div id="pvTtdNipPegawai">NIP. 199407072025061009</div>
                        </td>
                        <td>
                            <div>Mengetahui / Menyetujui,</div>
                            <div id="pvTtdAtasanJabatan">Kepala Sub Bagian Umum dan Tata Usaha</div>
                            
                            <!-- QR VERIFICATION STAMP -->
                            <div id="pvQrSealSuket" style="padding: 6px 0;">
                                <div class="qr-verify-seal">
                                    <div id="qrCanvasContainerSuket"></div>
                                    <div style="font-size:9px; color:#475569; line-height:1.2;">
                                        <b>KEMENTERIAN PU</b><br>
                                        BWS Kalimantan I<br>
                                        <span style="color:#0284c7; font-weight:600;">Disetujui Elektronik</span>
                                    </div>
                                </div>
                            </div>

                            <div id="pvTtdAtasanNama" style="font-weight:bold; text-decoration:underline;">Iswandi, S.E., S.T.</div>
                            <div id="pvTtdAtasanNip">NIP. 197603222009111001</div>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/qrcode.min.js') }}"></script>
<script>
    const inNomor = document.getElementById('inNomor');
    const inNama = document.getElementById('inNamaPegawai');
    const inNip = document.getElementById('inNipPegawai');
    const inJabatan = document.getElementById('inJabatanPegawai');
    const inAlasan = document.getElementById('inAlasan');
    const inWaktu = document.getElementById('inWaktuKejadian');
    const inTanggalAbsen = document.getElementById('inTanggalAbsen');
    const inAtasanNama = document.getElementById('inAtasanNama');
    const inAtasanNip = document.getElementById('inAtasanNip');
    const inAtasanJabatan = document.getElementById('inAtasanJabatan');
    const selectPegawai = document.getElementById('selectPegawai');
    const chkQrStampSuket = document.getElementById('chkQrStampSuket');

    function renderSuketQr() {
        const wrap = document.getElementById('pvQrSealSuket');
        if (!chkQrStampSuket.checked) {
            wrap.style.display = 'none';
            return;
        }
        wrap.style.display = 'block';
        const box = document.getElementById('qrCanvasContainerSuket');
        box.innerHTML = '';
        const txt = 'SUKET LUPA ABSEN BWS KALIMANTAN I\n' +
                    'No: ' + inNomor.value + '\n' +
                    'Pegawai: ' + inNama.value + ' (NIP: ' + inNip.value + ')\n' +
                    'Tanggal: ' + inTanggalAbsen.value + '\n' +
                    'Disetujui: ' + inAtasanNama.value;
        if (typeof QRCode !== 'undefined') {
            new QRCode(box, {
                text: txt,
                width: 55,
                height: 55,
                colorDark: '#0f172a',
                colorLight: '#ffffff',
                correctLevel: QRCode.CorrectLevel.M
            });
        }
    }

    inNomor.addEventListener('input', () => {
        document.getElementById('pvNomor').innerText = 'Nomor : ' + inNomor.value;
        renderSuketQr();
    });
    inNama.addEventListener('input', () => {
        document.getElementById('pvNama').innerText = inNama.value;
        document.getElementById('pvTtdPegawai').innerText = inNama.value;
        renderSuketQr();
    });
    inNip.addEventListener('input', () => {
        document.getElementById('pvNip').innerText = inNip.value;
        document.getElementById('pvTtdNipPegawai').innerText = 'NIP. ' + inNip.value;
        renderSuketQr();
    });
    inJabatan.addEventListener('input', () => document.getElementById('pvJabatan').innerText = inJabatan.value);
    inAlasan.addEventListener('input', () => document.getElementById('pvAlasan').innerText = inAlasan.value);
    inWaktu.addEventListener('input', () => document.getElementById('pvWaktu').innerText = inWaktu.value);

    inTanggalAbsen.addEventListener('input', () => {
        const d = inTanggalAbsen.value ? new Date(inTanggalAbsen.value) : new Date();
        const months = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
        document.getElementById('pvTanggal').innerText = d.getDate() + ' ' + months[d.getMonth()] + ' ' + d.getFullYear();
        renderSuketQr();
    });

    inAtasanNama.addEventListener('input', () => {
        document.getElementById('pvTtdAtasanNama').innerText = inAtasanNama.value;
        renderSuketQr();
    });
    inAtasanNip.addEventListener('input', () => document.getElementById('pvTtdAtasanNip').innerText = 'NIP. ' + inAtasanNip.value);
    inAtasanJabatan.addEventListener('input', () => document.getElementById('pvTtdAtasanJabatan').innerText = inAtasanJabatan.value);

    document.querySelectorAll('input[name="jenis_absen"]').forEach(r => {
        r.addEventListener('change', function() {
            document.getElementById('pvJenisAbsen').innerText = this.value.toUpperCase();
        });
    });

    selectPegawai.addEventListener('change', function() {
        const opt = this.options[this.selectedIndex];
        if (opt.value) {
            inNama.value = opt.getAttribute('data-nama');
            inNip.value = opt.getAttribute('data-nip') || '';
            inJabatan.value = opt.getAttribute('data-jabatan');

            document.getElementById('pvNama').innerText = inNama.value;
            document.getElementById('pvTtdPegawai').innerText = inNama.value;
            document.getElementById('pvNip').innerText = inNip.value;
            document.getElementById('pvTtdNipPegawai').innerText = 'NIP. ' + inNip.value;
            document.getElementById('pvJabatan').innerText = inJabatan.value;
            renderSuketQr();
        }
    });

    chkQrStampSuket.addEventListener('change', renderSuketQr);
    renderSuketQr();
</script>
@endpush
