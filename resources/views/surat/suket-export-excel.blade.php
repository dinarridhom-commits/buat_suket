<!DOCTYPE html>
<html xmlns:o="urn:schemas-microsoft-com:office:office"
      xmlns:x="urn:schemas-microsoft-com:office:excel"
      xmlns="http://www.w3.org/TR/REC-html40">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Calibri, Arial, sans-serif; font-size: 11pt; }
        .title { font-size: 14pt; font-weight: bold; color: #13243d; text-align: center; }
        .subtitle { font-size: 11pt; font-weight: bold; color: #334155; text-align: center; }
        .meta { font-size: 9.5pt; color: #64748b; text-align: center; }
        table.data { border-collapse: collapse; width: 100%; }
        table.data th {
            background-color: #13243d; color: #ffffff; font-weight: bold;
            text-align: center; vertical-align: middle; border: 1px solid #0f172a;
            padding: 8px 10px; font-size: 10.5pt;
        }
        table.data td {
            border: 1px solid #cbd5e1; padding: 6px 10px; vertical-align: middle; font-size: 10.5pt;
        }
        .zebra { background-color: #f8fafc; }
        .text-center { text-align: center; }
        .text-nip { mso-number-format: "\@"; text-align: center; font-family: Consolas, monospace; }
    </style>
</head>
<body>
    <table>
        <tr><td colspan="8" class="title">REKAP SURAT KETERANGAN / IZIN PRESENSI PEGAWAI</td></tr>
        <tr><td colspan="8" class="subtitle">BALAI WILAYAH SUNGAI KALIMANTAN I PONTIANAK — KEMENTERIAN PEKERJAAN UMUM</td></tr>
        <tr><td colspan="8" class="meta">Diekspor pada: {{ $exportedAt }} &bull; Total: {{ $sukets->count() }} Dokumen</td></tr>
        <tr><td colspan="8"></td></tr>
    </table>

    @php
        $jenisLabel = [
            'masuk'      => 'Lupa Absen Masuk',
            'pulang'     => 'Lupa Absen Pulang',
            'terlambat'  => 'Terlambat Masuk',
            'pulangawal' => 'Pulang Sebelum Waktunya',
        ];
    @endphp

    <table class="data">
        <thead>
            <tr>
                <th style="width: 45px;">No</th>
                <th style="width: 190px;">Nomor Surat</th>
                <th style="width: 170px;">Jenis Keterangan</th>
                <th style="width: 220px;">Nama Pegawai</th>
                <th style="width: 165px;">NIP</th>
                <th style="width: 110px;">Tanggal</th>
                <th style="width: 250px;">Alasan</th>
                <th style="width: 200px;">Atasan Penyetuju</th>
            </tr>
        </thead>
        <tbody>
            @foreach($sukets as $idx => $sk)
                <tr class="{{ $idx % 2 === 1 ? 'zebra' : '' }}">
                    <td class="text-center">{{ $idx + 1 }}</td>
                    <td><strong>{{ $sk->nomor_surat }}</strong></td>
                    <td>{{ $jenisLabel[$sk->jenis_absen] ?? ucfirst($sk->jenis_absen) }}</td>
                    <td>{{ $sk->nama_pegawai }}</td>
                    <td class="text-nip">{{ $sk->nip_pegawai ?: '-' }}</td>
                    <td class="text-center">{{ $sk->tanggal_kejadian->format('d/m/Y') }}</td>
                    <td>{{ $sk->alasan }}</td>
                    <td>{{ $sk->atasan_nama }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
