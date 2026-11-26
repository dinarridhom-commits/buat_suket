@extends('layouts.app')

@section('title', 'Detail Surat Undangan')
@section('header_title', 'Detail & Cetak Surat Undangan')
@section('header_subtitle', 'Arsip Resmi BWS Kalimantan I Pontianak — Lembar A4 Siap Cetak')

@php
    $extra = $surat->extra_data ?? [];
    $kopType = $extra['kop_type'] ?? 'balai';
    $preset = $kopPresets[$kopType] ?? $kopPresets['balai'];
    $ythMode = $extra['yth_mode'] ?? 'langsung';
    $diTempat = $extra['di_tempat'] ?? 'Tempat';
    $showNip = !empty($extra['show_nip']);
    $gapYth = $extra['gap_yth'] ?? '240';
    $gapPx = $gapYth == '120' ? '8px' : ($gapYth == '480' ? '26px' : '14px');
    $tembusanLines = array_values(array_filter(array_map('trim', explode("\n", $extra['tembusan'] ?? ''))));
    $kota = $extra['kota'] ?? 'Pontianak';
    $showSchedule = $extra['show_schedule'] ?? '0';
    $konsep = $extra['konsep'] ?? [];
    $margin = $extra['margin'] ?? ['top' => 2.5, 'bottom' => 2.5, 'left' => 2.5, 'right' => 2.5, 'lamp_top' => 1, 'lamp_bottom' => 1];

    $bodySections = !empty($extra['body']) && is_array($extra['body']) ? $extra['body'] : [
        [
            'type' => 'paragraph',
            'indent' => true,
            'text' => 'Sehubungan dengan pelaksanaan program kegiatan pada Balai Wilayah Sungai Kalimantan I Pontianak, dengan ini kami mengharapkan kehadiran Bapak/Ibu pada rapat yang akan dilaksanakan pada:',
        ],
        [
            'type' => 'points',
            'items' => [
                ['label' => 'Hari / Tanggal', 'value' => $surat->hari_tanggal_acara ?? '-'],
                ['label' => 'Jam', 'value' => $surat->waktu_acara ?? '-'],
                ['label' => 'Tempat', 'value' => $surat->tempat],
                ['label' => 'Agenda', 'value' => $surat->agenda ?? '-'],
            ],
        ],
        [
            'type' => 'paragraph',
            'indent' => false,
            'text' => 'Mengingat pentingnya acara tersebut di atas, diharapkan hadir tepat pada waktunya. Demikian kami sampaikan, atas perhatian dan kehadirannya diucapkan terima kasih.',
        ],
    ];
@endphp

@push('styles')
<style>
    .action-toolbar {
        display: flex; align-items: center; justify-content: space-between;
        background: var(--bg-surface); border: 1px solid var(--border); border-radius: var(--radius-lg);
        padding: 12px 20px; margin-bottom: 20px; box-shadow: var(--shadow-sm); flex-wrap: wrap; gap: 10px;
    }
    .toolbar-left  { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; }
    .toolbar-right { display: flex; gap: 8px; flex-wrap: wrap; }
    .btn-toolbar {
        padding: 7px 13px; border-radius: var(--radius-sm); font-size: 12.5px; font-weight: 600;
        display: inline-flex; align-items: center; gap: 6px; cursor: pointer; border: none;
        text-decoration: none; transition: all 0.15s ease;
    }
    .btn-back   { background: #f1f5f9; color: #334155; border: 1px solid #cbd5e1; }
    .btn-edit   { background: #fffbeb; color: #b45309; border: 1px solid #fde68a; }
    .btn-dup    { background: #f8fafc; color: #334155; border: 1px solid #cbd5e1; }
    .btn-docx   { background: #0284c7; color: #fff; }
    .btn-print  { background: var(--pu-navy); color: #fff; }
    .btn-final  { background: #047857; color: #fff; }
    .btn-draft  { background: #b45309; color: #fff; }
    .btn-delete { background: #fff5f5; color: #dc2626; border: 1px solid #fecaca; }
    .meta-chip  { background: #f1f5f9; color: #475569; padding: 4px 10px; border-radius: 20px; font-size: 12.5px; font-weight: 500; }

    .preview-scroll-wrapper {
        background: #cbd5e1; border-radius: var(--radius-lg); padding: 32px 24px;
        max-height: calc(100vh - 200px); overflow-y: auto; box-shadow: inset 0 2px 4px rgba(0,0,0,0.1);
        display: flex; flex-direction: column; align-items: center; gap: 20px;
    }

    /* ===== A4 SHEET RESMI ===== */
    .sheet-a4 {
        width: 794px; min-height: 1123px; background: #ffffff;
        box-shadow: 0 4px 15px rgba(0,0,0,0.15); padding: 48px 56px;
        font-family: Arial, Helvetica, sans-serif; color: #000000;
        font-size: 14.5px; line-height: 1.45; position: relative;
    }

    .kop-table { width: 100%; border-collapse: collapse; margin-bottom: 16px; }
    .kop-table td { vertical-align: bottom; }
    .kop-logo-cell { width: 92px; text-align: left; padding-right: 10px; }
    .kop-logo-img  { width: 82px; height: 82px; object-fit: contain; display: block; }
    .kop-text-cell { text-align: center; border-bottom: 2px solid #000000; padding-bottom: 5px; }
    .kop-line-1 { font-size: 17px; font-weight: 400; letter-spacing: 0.2px; line-height: 1.2; }
    .kop-line-2 { font-size: 12px; font-weight: 400; line-height: 1.25; }
    .kop-line-3 { font-size: 9.5px; font-weight: 700; line-height: 1.25; }
    .kop-line-address { font-size: 8.5px; margin-top: 2px; line-height: 1.25; }

    .letter-meta-table { width: 100%; margin-bottom: 14px; font-size: 14.5px; border-collapse: collapse; }
    .letter-meta-table td { vertical-align: top; padding: 1.5px 0; }

    .undangan-tujuan-box { margin-bottom: 14px; font-size: 14.5px; line-height: 1.5; }
    .undangan-list { padding-left: 22px; margin-top: 4px; }

    .pv-points-table { width: 100%; margin: 10px 0 10px 36px; font-size: 14.5px; border-collapse: collapse; }
    .pv-points-table td { vertical-align: top; padding: 2px 0; }

    .schedule-table-preview { width: 100%; border-collapse: collapse; margin: 14px 0; font-size: 13.5px; }
    .schedule-table-preview th, .schedule-table-preview td { border: 1px solid #333; padding: 6px 10px; }
    .schedule-table-preview th { background: #f1f5f9; font-weight: 700; text-align: center; }

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
            box-shadow: none !important; padding: 15mm 20mm !important;
            width: 100% !important; min-height: 297mm !important; margin: 0 !important;
            page-break-after: always;
        }
        .sheet-a4:last-child { page-break-after: auto; }
    }
</style>
@endpush

@section('content')

<div class="action-toolbar">
    <div class="toolbar-left">
        <a href="{{ route('surat.undangan.index') }}" class="btn-toolbar btn-back">&larr; Kembali</a>
        <div style="display:flex; gap:8px; flex-wrap:wrap; align-items:center;">
            <span class="meta-chip">{{ $surat->nomor_surat }}</span>
            <span class="meta-chip">{{ $surat->tanggal_surat->format('d/m/Y') }}</span>
            <span class="meta-chip" style="background:{{ $surat->status == 'final' ? '#dcfce7' : '#fef3c7' }}; color:{{ $surat->status == 'final' ? '#166534' : '#92400e' }};">
                {{ ucfirst($surat->status) }}
            </span>
        </div>
    </div>
    <div class="toolbar-right">
        <a href="{{ route('surat.undangan.edit', $surat->id) }}" class="btn-toolbar btn-edit">Edit</a>
        <a href="{{ route('surat.undangan.duplicate', $surat->id) }}" class="btn-toolbar btn-dup">Duplikat</a>
        @if($surat->status != 'final')
            <form method="POST" action="{{ route('surat.undangan.status', $surat->id) }}" style="display:inline;">
                @csrf @method('PATCH')
                <input type="hidden" name="status" value="final">
                <button type="submit" class="btn-toolbar btn-final">Jadikan Final</button>
            </form>
        @else
            <form method="POST" action="{{ route('surat.undangan.status', $surat->id) }}" style="display:inline;">
                @csrf @method('PATCH')
                <input type="hidden" name="status" value="draft">
                <button type="submit" class="btn-toolbar btn-draft">Kembalikan Draft</button>
            </form>
        @endif
        <button type="button" id="btnExportDocx" class="btn-toolbar btn-docx">Unduh Word (.docx)</button>
        <button type="button" onclick="window.print()" class="btn-toolbar btn-print">Cetak / PDF</button>
        <form method="POST" action="{{ route('surat.undangan.destroy', $surat->id) }}"
              onsubmit="return confirm('Hapus arsip surat ini secara permanen?')">
            @csrf @method('DELETE')
            <button type="submit" class="btn-toolbar btn-delete">Hapus</button>
        </form>
    </div>
</div>

<div class="preview-scroll-wrapper">
    <!-- HALAMAN 1: SURAT UTAMA -->
    <div class="sheet-a4">
        <table class="kop-table">
            <tr>
                <td class="kop-logo-cell">
                    <img src="{{ asset('images/logo_pu.png') }}" alt="Logo Kementerian PU" class="kop-logo-img">
                </td>
                <td class="kop-text-cell">
                    <div class="kop-line-1">{{ $preset['kementerian'] }}</div>
                    <div class="kop-line-2">{{ $preset['ditjen'] }}</div>
                    @foreach($preset['lines3'] as $l3)
                        <div class="kop-line-3">{{ $l3 }}</div>
                    @endforeach
                    <div class="kop-line-address">{{ $preset['alamat'] }}</div>
                </td>
            </tr>
        </table>

        <table class="letter-meta-table">
            <tr>
                <td style="width:80px;">Nomor</td>
                <td style="width:15px;">:</td>
                <td>{{ $surat->nomor_surat }}</td>
                <td style="text-align:right;">
                    {{ $kota }}, {{ $surat->tanggal_surat->locale('id')->translatedFormat('d F Y') }}
                </td>
            </tr>
            <tr><td>Sifat</td><td>:</td><td>{{ $surat->sifat ?? 'Biasa' }}</td><td></td></tr>
            <tr><td>Lampiran</td><td>:</td><td>{{ $surat->lampiran ?? '-' }}</td><td></td></tr>
            <tr><td>Hal</td><td>:</td><td style="font-weight:bold;">{{ $surat->hal }}</td><td></td></tr>
        </table>

        <div class="undangan-tujuan-box" style="margin-top:{{ $gapPx }};">
            <div>Yth.</div>
            @if($ythMode === 'terlampir')
                <div style="margin-top:2px;">(Daftar Terlampir)</div>
            @else
                <ol class="undangan-list">
                    @foreach($surat->penerimas as $p)
                        <li>{{ $p->nama_penerima }}</li>
                    @endforeach
                </ol>
            @endif
            <div style="margin-top:4px;">di -</div>
            <div style="padding-left:20px; font-weight:bold;">{{ $diTempat }}</div>
        </div>

        @foreach($bodySections as $sec)
            @if(($sec['type'] ?? 'paragraph') === 'points')
                <table class="pv-points-table">
                    @foreach($sec['items'] ?? [] as $it)
                        @if(trim($it['label'] ?? '') !== '' || trim($it['value'] ?? '') !== '')
                            <tr>
                                <td style="width:140px;">{{ $it['label'] ?? '' }}</td>
                                <td style="width:14px;">:</td>
                                <td>{!! nl2br(e($it['value'] ?? '')) !!}</td>
                            </tr>
                        @endif
                    @endforeach
                </table>
            @else
                @php
                    $paras = array_values(array_filter(array_map('trim', preg_split('/\n\s*\n/', $sec['text'] ?? ''))));
                    $indentStyle = ($sec['indent'] ?? true) ? 'text-indent:36px;' : '';
                @endphp
                @foreach($paras as $para)
                    <div style="text-align:justify; {{ $indentStyle }} margin-top:12px;">{{ $para }}</div>
                @endforeach
            @endif
        @endforeach

        @if($showSchedule && !empty($extra['schedule_rows']))
            <table class="schedule-table-preview">
                <thead>
                    <tr>
                        <th style="width:140px;">Waktu</th>
                        <th>Agenda / Acara</th>
                        <th style="width:180px;">Narasumber / PIC</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($extra['schedule_rows'] as $row)
                        <tr>
                            <td style="text-align:center;">{{ $row['waktu'] ?? '' }}</td>
                            <td>{{ $row['acara'] ?? '' }}</td>
                            <td>{{ $row['pic'] ?? '' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

        <table style="width:100%; margin-top:28px; border-collapse:collapse;">
            <tr>
                <td style="width:48%; vertical-align:bottom;">
                    <div style="display:inline-flex; align-items:center; gap:10px; border:1px dashed #94a3b8; border-radius:6px; padding:6px 10px; background:#f8fafc;">
                        <div id="qrVerifyCanvas"></div>
                        <div style="font-size:9.5px; color:#334155; line-height:1.35; max-width:165px;">
                            <div style="font-weight:700; color:#13243d;">VERIFIKASI DOKUMEN</div>
                            <div>Pindai QR untuk cek keaslian arsip digital BWS Kalimantan I.</div>
                        </div>
                    </div>
                </td>
                <td style="width:52%; text-align:center; vertical-align:top; font-size:14.5px;">
                    <div>{{ $surat->penandatangan_jabatan }}</div>
                    <div style="height:78px;"></div>
                    <div style="font-weight:bold;">{{ $surat->penandatangan_nama }}</div>
                    @if($showNip && $surat->penandatangan_nip)
                        <div>NIP. {{ $surat->penandatangan_nip }}</div>
                    @endif
                </td>
            </tr>
        </table>

        @if(count($tembusanLines) > 0)
            <div style="margin-top:24px; font-size:13.5px;">
                <div>Tembusan:</div>
                <ol style="padding-left:20px; margin-top:4px; line-height:1.45;">
                    @foreach($tembusanLines as $tb)
                        <li>{{ $tb }}</li>
                    @endforeach
                </ol>
            </div>
        @endif
    </div>

    <!-- HALAMAN 2 (JIKA MODE DAFTAR TERLAMPIR) -->
    @if($ythMode === 'terlampir' && $surat->penerimas->count() > 0)
        <div class="sheet-a4">
            <div style="margin-left: 50%; font-size: 13.5px; line-height: 1.4; margin-bottom: 24px;">
                <div>Lampiran I Surat {{ $surat->penandatangan_jabatan }}</div>
                <div>Nomor : {{ $surat->nomor_surat }}</div>
                <div>Tanggal : {{ $surat->tanggal_surat->locale('id')->translatedFormat('d F Y') }}</div>
            </div>

            <div style="text-align: center; font-weight: bold; font-size: 15px; margin: 24px 0 18px 0;">
                DAFTAR UNDANGAN
            </div>

            <ol style="line-height: 1.75; font-size: 14px; padding-left: 28px;">
                @foreach($surat->penerimas as $p)
                    <li>{{ $p->nama_penerima }}</li>
                @endforeach
            </ol>

            <div style="width: 320px; margin-left: auto; text-align: center; margin-top: 50px; font-size: 14px;">
                <div>{{ $surat->penandatangan_jabatan }}</div>
                <div style="height: 72px;"></div>
                <div style="font-weight: bold;">{{ $surat->penandatangan_nama }}</div>
                @if($showNip && $surat->penandatangan_nip)
                    <div>NIP. {{ $surat->penandatangan_nip }}</div>
                @endif
            </div>
        </div>
    @endif
</div>

@endsection

@push('scripts')
<script src="{{ asset('js/jszip.min.js') }}"></script>
<script src="{{ asset('js/surat_constants.js') }}"></script>
<script src="{{ asset('js/docgen.js') }}"></script>
<script src="{{ asset('js/qrcode.min.js') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const el = document.getElementById('qrVerifyCanvas');
        if (el && typeof QRCode !== 'undefined') {
            new QRCode(el, {
                text: @json($verifyUrl),
                width: 62, height: 62,
                colorDark: '#13243d', colorLight: '#ffffff',
                correctLevel: QRCode.CorrectLevel.M
            });
        }

        document.getElementById('btnExportDocx').addEventListener('click', function() {
            const btn = this;
            const orig = btn.innerHTML;
            btn.innerHTML = 'Membuat .docx...';
            btn.disabled = true;

            const penerimasText = @json($surat->penerimas->pluck('nama_penerima')->implode("\n"));
            const ythMode = @json($ythMode);
            const konsep = @json($konsep);
            const margin = @json($margin);

            const docData = {
                mode: 'undangan',
                nomor: @json($surat->nomor_surat),
                sifat: @json($surat->sifat ?? 'Biasa'),
                lampiran: parseInt(@json($surat->lampiran ?? '1'), 10) || 0,
                hal: @json($surat->hal),
                kota: @json($kota),
                tanggal: @json($surat->tanggal_surat->format('Y-m-d')),
                ythMode: ythMode,
                ythText: penerimasText,
                daftarUndangan: penerimasText,
                diTempat: @json($diTempat),
                tembusan: @json($extra['tembusan'] ?? ''),
                gapYth: @json($gapYth),
                urutan: @json($extra['urutan'] ?? 'paraf-dulu'),
                konsepMarginTemplate: true,
                ttd_jab: @json($surat->penandatangan_jabatan),
                ttd_nama: @json($surat->penandatangan_nama),
                ttd_nip: @json($surat->penandatangan_nip ?? ''),
                showNip: @json($showNip),
                konf_jab: konsep.konf_jab || 'Kasubbag Umum & TU',
                konf_nama: konsep.konf_nama || '',
                konf_jabParaf: konsep.konf_jab_paraf || '',
                pem_jab: konsep.pem_jab || 'Kepala Subbagian TU',
                pem_nama: konsep.pem_nama || '',
                pen_jab: konsep.pen_jab || '',
                pen_nama: konsep.pen_nama || '',
                ketik_nama: konsep.ketik_nama || '',
                kembali: konsep.kembali || '',
                ajukan: konsep.ajukan || '',
                deadline: konsep.deadline || '',
                jumlahLembar: konsep.jumlah_lembar || 1,
                tetap_nama: konsep.tetap_nama || @json($surat->penandatangan_nama),
                body: @json($bodySections),
                pages: { asli: true, paraf: true, konsep: true, lampiran: ythMode === 'terlampir' },
                margin: {
                    top: +(margin.top || 2.5),
                    bottom: +(margin.bottom || 2.5),
                    left: +(margin.left || 2.5),
                    right: +(margin.right || 2.5)
                },
                marginLampiran: {
                    top: +(margin.lamp_top || 1),
                    bottom: +(margin.lamp_bottom || 1)
                }
            };

            const kopSpec = {
                line1: @json($preset['kementerian']),
                line2: @json($preset['ditjen']),
                line3: @json($preset['lines3']),
                line4: @json($preset['alamat'])
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
                a.download = @json('Surat_Undangan_' . preg_replace('/[^a-zA-Z0-9_-]/', '_', $surat->hal) . '.docx');
                document.body.appendChild(a);
                a.click();
                document.body.removeChild(a);
                URL.revokeObjectURL(url);
                btn.innerHTML = orig;
                btn.disabled = false;
            }).catch(function(err) {
                alert('Ekspor Word gagal: ' + (err.message || err));
                btn.innerHTML = orig;
                btn.disabled = false;
            });
        });
    });
</script>
@endpush
