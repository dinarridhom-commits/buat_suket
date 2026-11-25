<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Keaslian Dokumen — {{ $kop['balai'] }}</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo_pu.png') }}">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@500;600&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Plus Jakarta Sans', system-ui, sans-serif;
            background: #f0f2f5;
            color: #111827;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px 16px;
        }
        .verify-card {
            width: 100%;
            max-width: 560px;
            background: #ffffff;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(19, 36, 61, 0.12);
            overflow: hidden;
            border: 1px solid #e5e7eb;
        }
        .verify-head {
            background: linear-gradient(135deg, #13243d 0%, #1f3659 100%);
            color: #ffffff;
            padding: 24px 28px;
            display: flex;
            align-items: center;
            gap: 14px;
        }
        .verify-logo {
            width: 48px;
            height: 48px;
            background: #ffffff;
            border-radius: 10px;
            padding: 4px;
            border: 2px solid #f59e0b;
            flex-shrink: 0;
        }
        .verify-logo img { width: 100%; height: 100%; object-fit: contain; }
        .verify-head h1 { font-size: 14px; font-weight: 800; line-height: 1.3; }
        .verify-head p { font-size: 11.5px; color: #fcd34d; margin-top: 2px; font-weight: 600; }

        .verify-status-banner {
            background: #ecfdf5;
            border-bottom: 1px solid #a7f3d0;
            padding: 16px 28px;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .status-badge-icon {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: #10b981;
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            font-weight: 800;
            flex-shrink: 0;
        }
        .verify-body { padding: 24px 28px; }
        .doc-row {
            display: flex;
            justify-content: space-between;
            gap: 16px;
            padding: 11px 0;
            border-bottom: 1px solid #f3f4f6;
            font-size: 13.5px;
        }
        .doc-row:last-child { border-bottom: none; }
        .doc-label { color: #6b7280; font-weight: 500; flex-shrink: 0; }
        .doc-val { color: #111827; font-weight: 700; text-align: right; word-break: break-word; }
        .mono { font-family: 'JetBrains Mono', monospace; font-size: 12.5px; }
        .verify-footer {
            background: #f9fafb;
            border-top: 1px solid #e5e7eb;
            padding: 14px 28px;
            font-size: 11.5px;
            color: #6b7280;
            text-align: center;
            line-height: 1.5;
        }
    </style>
</head>
<body>
    <div class="verify-card">
        <div class="verify-head">
            <div class="verify-logo">
                <img src="{{ asset('images/logo_pu.png') }}" alt="Logo PU">
            </div>
            <div>
                <h1>{{ $kop['balai'] }}</h1>
                <p>{{ $kop['kementerian'] }} — {{ $kop['ditjen'] }}</p>
            </div>
        </div>

        <div class="verify-status-banner">
            <div class="status-badge-icon">✓</div>
            <div>
                <div style="font-size:14.5px; font-weight:800; color:#065f46;">DOKUMEN RESMI TERVERIFIKASI</div>
                <div style="font-size:12px; color:#047857;">Tercatat sah dalam sistem arsip digital BWS Kalimantan I</div>
            </div>
        </div>

        <div class="verify-body">
            @if($type === 'undangan')
                <div class="doc-row">
                    <span class="doc-label">Jenis Dokumen</span>
                    <span class="doc-val">Surat Undangan Kedinasan</span>
                </div>
                <div class="doc-row">
                    <span class="doc-label">Nomor Surat</span>
                    <span class="doc-val mono">{{ $dokumen->nomor_surat }}</span>
                </div>
                <div class="doc-row">
                    <span class="doc-label">Perihal</span>
                    <span class="doc-val">{{ $dokumen->hal }}</span>
                </div>
                <div class="doc-row">
                    <span class="doc-label">Tanggal Surat</span>
                    <span class="doc-val">{{ $dokumen->tanggal_surat->locale('id')->translatedFormat('d F Y') }}</span>
                </div>
                <div class="doc-row">
                    <span class="doc-label">Tempat Acara</span>
                    <span class="doc-val">{{ $dokumen->tempat }}</span>
                </div>
                <div class="doc-row">
                    <span class="doc-label">Penandatangan</span>
                    <span class="doc-val">{{ $dokumen->penandatangan_nama }}<br><small style="font-weight:500; color:#6b7280;">{{ $dokumen->penandatangan_jabatan }}</small></span>
                </div>
                <div class="doc-row">
                    <span class="doc-label">Status Dokumen</span>
                    <span class="doc-val" style="color:{{ $dokumen->status == 'final' ? '#047857' : '#b45309' }};">
                        {{ strtoupper($dokumen->status) }}
                    </span>
                </div>
            @else
                @php
                    $jenisLabel = [
                        'masuk'      => 'Tidak Mengisi Daftar Hadir Kedatangan',
                        'pulang'     => 'Tidak Mengisi Daftar Hadir Kepulangan',
                        'terlambat'  => 'Terlambat Masuk Kerja',
                        'pulangawal' => 'Pulang Sebelum Waktunya',
                    ];
                @endphp
                <div class="doc-row">
                    <span class="doc-label">Jenis Dokumen</span>
                    <span class="doc-val">Surat Permohonan Izin / Lupa Absen</span>
                </div>
                <div class="doc-row">
                    <span class="doc-label">Jenis Keterangan</span>
                    <span class="doc-val">{{ $jenisLabel[$dokumen->jenis_absen] ?? ucfirst($dokumen->jenis_absen) }}</span>
                </div>
                <div class="doc-row">
                    <span class="doc-label">Nomor Surat</span>
                    <span class="doc-val mono">{{ $dokumen->nomor_surat }}</span>
                </div>
                <div class="doc-row">
                    <span class="doc-label">Nama Pegawai</span>
                    <span class="doc-val">{{ $dokumen->nama_pegawai }}</span>
                </div>
                <div class="doc-row">
                    <span class="doc-label">NIP</span>
                    <span class="doc-val mono">{{ $dokumen->nip_pegawai ?? '-' }}</span>
                </div>
                <div class="doc-row">
                    <span class="doc-label">Tanggal & Waktu</span>
                    <span class="doc-val">{{ $dokumen->tanggal_kejadian->locale('id')->translatedFormat('d F Y') }} ({{ $dokumen->waktu_kejadian }} WIB)</span>
                </div>
                <div class="doc-row">
                    <span class="doc-label">Atasan Penyetuju</span>
                    <span class="doc-val">{{ $dokumen->atasan_nama }}<br><small style="font-weight:500; color:#6b7280;">{{ $dokumen->atasan_jabatan }}</small></span>
                </div>
            @endif

            <div class="doc-row">
                <span class="doc-label">Kode Verifikasi</span>
                <span class="doc-val mono">{{ strtoupper($token) }}</span>
            </div>
        </div>

        <div class="verify-footer">
            Halaman verifikasi otomatis Portal Tata Usaha BWS Kalimantan I Pontianak.<br>
            Pastikan URL di bilah alamat sesuai dengan domain resmi instansi.
        </div>
    </div>
</body>
</html>
