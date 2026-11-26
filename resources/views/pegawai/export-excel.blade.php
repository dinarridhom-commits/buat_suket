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
            background-color: #13243d;
            color: #ffffff;
            font-weight: bold;
            text-align: center;
            vertical-align: middle;
            border: 1px solid #0f172a;
            padding: 8px 10px;
            font-size: 10.5pt;
        }
        table.data td {
            border: 1px solid #cbd5e1;
            padding: 6px 10px;
            vertical-align: middle;
            font-size: 10.5pt;
        }
        .zebra { background-color: #f8fafc; }
        .text-center { text-align: center; }
        .text-nip { mso-number-format: "\@"; text-align: center; font-family: Consolas, monospace; }
    </style>
</head>
<body>
    <table>
        <tr>
            <td colspan="13" class="title">DAFTAR MASTER PEGAWAI &amp; PEJABAT</td>
        </tr>
        <tr>
            <td colspan="13" class="subtitle">BALAI WILAYAH SUNGAI KALIMANTAN I PONTIANAK — KEMENTERIAN PEKERJAAN UMUM</td>
        </tr>
        <tr>
            <td colspan="13" class="meta">Diekspor pada: {{ $exportedAt }} &bull; Total: {{ $pegawais->count() }} Pegawai</td>
        </tr>
        <tr><td colspan="13"></td></tr>
    </table>

    <table class="data">
        <thead>
            <tr>
                <th style="width: 45px;">No</th>
                <th style="width: 240px;">Unit</th>
                <th style="width: 220px;">Satker</th>
                <th style="width: 240px;">Nama Lengkap</th>
                <th style="width: 170px;">NIP</th>
                <th style="width: 170px;">Pangkat / Gol.</th>
                <th style="width: 65px;">Kelas</th>
                <th style="width: 260px;">Jabatan Utama</th>
                <th style="width: 210px;">Jabatan Rangkap</th>
                <th style="width: 200px;">Jabatan di Surat</th>
                <th style="width: 200px;">Rangkap di Surat</th>
                <th style="width: 180px;">Nama di Surat</th>
                <th style="width: 130px;">Ket.</th>
            </tr>
        </thead>
        <tbody>
            @foreach($pegawais as $idx => $p)
                <tr class="{{ $idx % 2 === 1 ? 'zebra' : '' }}">
                    <td class="text-center">{{ $idx + 1 }}</td>
                    <td>{{ $p->unit ?: 'Balai Wilayah Sungai Kalimantan I Pontianak' }}</td>
                    <td>{{ $p->satker ?: '-' }}</td>
                    <td><strong>{{ $p->nama }}</strong></td>
                    <td class="text-nip">{{ $p->nip ?: '-' }}</td>
                    <td>{{ $p->pangkat ?: '-' }}</td>
                    <td class="text-center">{{ $p->kelas ?: '-' }}</td>
                    <td>{{ $p->jabatan ?: '-' }}</td>
                    <td>{{ $p->rangkap ?: '-' }}</td>
                    <td>{{ $p->jabatan_surat ?: '-' }}</td>
                    <td>{{ $p->rangkap_surat ?: '-' }}</td>
                    <td>{{ $p->nama_surat ?: '-' }}</td>
                    <td>{{ $p->ket ?: '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
