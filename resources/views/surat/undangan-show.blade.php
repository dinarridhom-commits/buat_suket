@extends('layouts.app')

@section('title', 'Detail Surat Undangan')
@section('header_title', 'Detail & Cetak Surat Undangan')
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

    .toolbar-left {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
    }

    .toolbar-right {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
    }

    .btn-toolbar {
        padding: 7px 14px;
        border-radius: var(--radius-sm);
        font-size: 12.5px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        cursor: pointer;
        border: none;
        text-decoration: none;
        transition: all 0.15s ease;
    }

    .btn-back     { background: #f1f5f9; color: #334155; border: 1px solid #cbd5e1; }
    .btn-edit     { background: #fffbeb; color: #b45309; border: 1px solid #fde68a; }
    .btn-docx     { background: #0284c7; color: #fff; }
    .btn-print    { background: var(--pu-navy); color: #fff; }
    .btn-final    { background: #047857; color: #fff; }
    .btn-draft    { background: #b45309; color: #fff; }
    .btn-delete   { background: #fff5f5; color: #dc2626; border: 1px solid #fecaca; }

    .meta-info-bar {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
        align-items: center;
        font-size: 12.5px;
    }

    .meta-chip {
        background: #f1f5f9;
        color: #475569;
        padding: 4px 10px;
        border-radius: 20px;
        font-weight: 500;
    }

    .preview-scroll-wrapper {
        background: #cbd5e1;
        border-radius: var(--radius-lg);
        padding: 32px 24px;
        max-height: calc(100vh - 200px);
        overflow-y: auto;
        box-shadow: inset 0 2px 4px rgba(0,0,0,0.1);
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
        color: #000000;
        font-size: 14.5px;
        line-height: 1.45;
        position: relative;
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
    .kop-border-bottom { display: none; }

    .letter-meta-table { width: 100%; margin-bottom: 14px; font-size: 14.5px; }
    .letter-meta-table td { vertical-align: top; padding: 1.5px 0; }

    .undangan-tujuan-box { margin: 14px 0; font-size: 14.5px; line-height: 1.5; }
    .undangan-list { padding-left: 22px; margin-top: 4px; }

    .schedule-table-preview { width: 100%; border-collapse: collapse; margin: 14px 0; font-size: 13.5px; }
    .schedule-table-preview th, .schedule-table-preview td {
        border: 1px solid #333; padding: 6px 10px;
    }
    .schedule-table-preview th { background: #f1f5f9; font-weight: 700; text-align: center; }

    .tanda-tangan-wrap {
        float: right; width: 320px; text-align: center;
        margin-top: 28px; font-size: 14.5px;
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
        .sidebar, .topbar, .subpage-header, .action-toolbar, .alert-toast { display: none !important; }
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
        <a href="{{ route('surat.undangan.index') }}" class="btn-toolbar btn-back">← Kembali ke Arsip</a>
        <div class="meta-info-bar">
            <span class="meta-chip">📋 {{ $surat->nomor_surat }}</span>
            <span class="meta-chip">📅 {{ $surat->tanggal_surat->format('d/m/Y') }}</span>
            <span class="meta-chip" style="background:{{ $surat->status == 'final' ? '#dcfce7' : '#fef3c7' }}; color:{{ $surat->status == 'final' ? '#166534' : '#92400e' }};">
                {{ ucfirst($surat->status) }}
            </span>
        </div>
    </div>
    <div class="toolbar-right">
        @if($surat->status != 'final')
            <form method="POST" action="{{ route('surat.undangan.status', $surat->id) }}" style="display:inline;">
                @csrf
                @method('PATCH')
                <input type="hidden" name="status" value="final">
                <button type="submit" class="btn-toolbar btn-final">✅ Jadikan Final</button>
            </form>
        @else
            <form method="POST" action="{{ route('surat.undangan.status', $surat->id) }}" style="display:inline;">
                @csrf
                @method('PATCH')
                <input type="hidden" name="status" value="draft">
                <button type="submit" class="btn-toolbar btn-draft">↩️ Kembalikan ke Draft</button>
            </form>
        @endif
        <a href="{{ route('surat.undangan.edit', $surat->id) }}" class="btn-toolbar btn-edit">✏️ Edit</a>
        <button type="button" id="btnExportDocx" class="btn-toolbar btn-docx">📄 Unduh Word (.docx)</button>
        <button type="button" onclick="window.print()" class="btn-toolbar btn-print">🖨️ Cetak / PDF</button>
        <form method="POST" action="{{ route('surat.undangan.destroy', $surat->id) }}"
              onsubmit="return confirm('Hapus arsip surat ini secara permanen?')">
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

        {{-- META SURAT --}}
        <table class="letter-meta-table">
            <tr>
                <td style="width:80px;">Nomor</td>
                <td style="width:15px;">:</td>
                <td>{{ $surat->nomor_surat }}</td>
                <td style="text-align:right;">
                    Pontianak, {{ $surat->tanggal_surat->locale('id')->translatedFormat('d F Y') }}
                </td>
            </tr>
            <tr>
                <td>Sifat</td>
                <td>:</td>
                <td>{{ $surat->sifat ?? 'Biasa' }}</td>
                <td></td>
            </tr>
            <tr>
                <td>Lampiran</td>
                <td>:</td>
                <td>{{ $surat->lampiran ?? '-' }}</td>
                <td></td>
            </tr>
            <tr>
                <td>Hal</td>
                <td>:</td>
                <td style="font-weight:bold;">{{ $surat->hal }}</td>
                <td></td>
            </tr>
        </table>

        {{-- TUJUAN --}}
        <div class="undangan-tujuan-box">
            <div>Yth.</div>
            <ol class="undangan-list">
                @foreach($surat->penerimas as $p)
                    <li>{{ $p->nama_penerima }}</li>
                @endforeach
            </ol>
            <div style="margin-top:4px;">di -</div>
            <div style="padding-left:20px; font-weight:bold;">Tempat</div>
        </div>

        {{-- ISI SURAT --}}
        <div style="text-align:justify; text-indent:36px; margin-top:14px;">
            Sehubungan dengan pelaksanaan program kegiatan pada Balai Wilayah Sungai Kalimantan I Pontianak, dengan ini kami mengharapkan kehadiran Bapak/Ibu pada rapat yang akan dilaksanakan pada:
        </div>

        <table style="width:100%; margin:12px 0 12px 36px; font-size:14.5px;">
            <tr>
                <td style="width:130px; padding:2px 0;">Hari / Tanggal</td>
                <td style="width:15px;">:</td>
                <td style="font-weight:500;">{{ $surat->hari_tanggal_acara ?? '-' }}</td>
            </tr>
            <tr>
                <td style="padding:2px 0;">Waktu</td>
                <td>:</td>
                <td>{{ $surat->waktu_acara ?? '-' }}</td>
            </tr>
            <tr>
                <td style="padding:2px 0;">Tempat</td>
                <td>:</td>
                <td>{{ $surat->tempat }}</td>
            </tr>
            <tr>
                <td style="padding:2px 0;">Agenda</td>
                <td>:</td>
                <td style="font-weight:bold;">{{ $surat->agenda ?? '-' }}</td>
            </tr>
        </table>

        @if(!empty($surat->extra_data['schedule_rows']))
            <table class="schedule-table-preview">
                <thead>
                    <tr>
                        <th style="width:140px;">Waktu</th>
                        <th>Agenda / Acara</th>
                        <th style="width:180px;">Narasumber / PIC</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($surat->extra_data['schedule_rows'] as $row)
                        <tr>
                            <td style="text-align:center;">{{ $row['waktu'] ?? '' }}</td>
                            <td>{{ $row['acara'] ?? '' }}</td>
                            <td>{{ $row['pic'] ?? '' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

        <div style="text-align:justify; text-indent:36px; margin-top:14px;">
            Mengingat pentingnya acara tersebut di atas, diharapkan hadir tepat pada waktunya. Demikian kami sampaikan, atas perhatian dan kehadirannya diucapkan terima kasih.
        </div>

        {{-- TANDA TANGAN & QR VERIFIKASI --}}
        <div style="display:flex; justify-content:space-between; align-items:flex-end; margin-top:28px; gap:20px;">
            <div style="display:inline-flex; align-items:center; gap:10px; border:1px dashed #94a3b8; border-radius:6px; padding:6px 10px; background:#f8fafc;">
                <div id="qrVerifyCanvas"></div>
                <div style="font-size:9.5px; color:#334155; line-height:1.35; max-width:170px;">
                    <div style="font-weight:700; color:#13243d;">VERIFIKASI DOKUMEN</div>
                    <div>Pindai QR untuk cek keaslian arsip digital BWS Kalimantan I.</div>
                </div>
            </div>

            <div class="tanda-tangan-wrap" style="float:none; margin-top:0;">
                <div>{{ $surat->penandatangan_jabatan }}</div>
                <div style="height:80px;"></div>
                <div style="font-weight:bold; text-decoration:underline;">{{ $surat->penandatangan_nama }}</div>
                @if($surat->penandatangan_nip)
                    <div>NIP. {{ $surat->penandatangan_nip }}</div>
                @endif
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
    document.addEventListener('DOMContentLoaded', () => {
        const el = document.getElementById('qrVerifyCanvas');
        if (el && typeof QRCode !== 'undefined') {
            new QRCode(el, {
                text: @json($verifyUrl),
                width: 64,
                height: 64,
                colorDark: '#13243d',
                colorLight: '#ffffff',
                correctLevel: QRCode.CorrectLevel.M
            });
        }

        document.getElementById('btnExportDocx').addEventListener('click', function() {
            const btn = this;
            const orig = btn.innerHTML;
            btn.innerHTML = '<span>⏳</span> Membuat .docx...';
            btn.disabled = true;

            const docData = {
                mode: 'undangan',
                nomor: @json($surat->nomor_surat),
                sifat: @json($surat->sifat ?? 'Biasa'),
                lampiran: @json($surat->lampiran ?? '-'),
                hal: @json($surat->hal),
                kota: 'Pontianak',
                tanggal: @json($surat->tanggal_surat->format('Y-m-d')),
                ythMode: 'langsung',
                ythText: @json($surat->penerimas->pluck('nama_penerima')->implode("\n")),
                diTempat: 'Tempat',
                ttd_jab: @json($surat->penandatangan_jabatan),
                ttd_nama: @json($surat->penandatangan_nama),
                ttd_nip: @json($surat->penandatangan_nip ?? ''),
                showNip: true,
                body: [
                    {
                        type: 'paragraph',
                        indent: true,
                        text: 'Sehubungan dengan pelaksanaan program kegiatan pada Balai Wilayah Sungai Kalimantan I Pontianak, dengan ini kami mengharapkan kehadiran Bapak/Ibu pada rapat yang akan dilaksanakan pada:'
                    },
                    {
                        type: 'points',
                        items: [
                            { label: 'Hari / Tanggal', value: @json($surat->hari_tanggal_acara ?? '-') },
                            { label: 'Waktu', value: @json($surat->waktu_acara ?? '-') },
                            { label: 'Tempat', value: @json($surat->tempat) },
                            { label: 'Agenda', value: @json($surat->agenda ?? '-') }
                        ]
                    },
                    {
                        type: 'paragraph',
                        indent: false,
                        text: 'Mengingat pentingnya acara tersebut di atas, diharapkan hadir tepat pada waktunya. Demikian kami sampaikan, atas perhatian dan kehadirannya diucapkan terima kasih.'
                    }
                ],
                pages: { asli: true, paraf: false, konsep: false, lampiran: false },
                margin: { top: 2.5, bottom: 2.5, left: 2.5, right: 2.5 }
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
                a.download = @json('Surat_Undangan_' . preg_replace('/[^a-zA-Z0-9_-]/', '_', $surat->hal) . '.docx');
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
