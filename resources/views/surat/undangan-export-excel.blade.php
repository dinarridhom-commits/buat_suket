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
    </style>
</head>
<body>
    <table>
        <tr><td colspan="7" class="title">BUKU AGENDA SURAT UNDANGAN KELUAR</td></tr>
        <tr><td colspan="7" class="subtitle">BALAI WILAYAH SUNGAI KALIMANTAN I PONTIANAK — KEMENTERIAN PEKERJAAN UMUM</td></tr>
        <tr><td colspan="7" class="meta">Diekspor pada: {{ $exportedAt }} &bull; Total: {{ $surats->count() }} Surat</td></tr>
        <tr><td colspan="7"></td></tr>
    </table>

    <table class="data">
        <thead>
            <tr>
                <th style="width: 45px;">No</th>
                <th style="width: 210px;">Nomor Surat</th>
                <th style="width: 110px;">Tanggal Surat</th>
                <th style="width: 280px;">Hal / Perihal</th>
                <th style="width: 240px;">Tempat</th>
                <th style="width: 220px;">Penandatangan</th>
                <th style="width: 100px;">Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($surats as $idx => $s)
                <tr class="{{ $idx % 2 === 1 ? 'zebra' : '' }}">
                    <td class="text-center">{{ $idx + 1 }}</td>
                    <td><strong>{{ $s->nomor_surat }}</strong></td>
                    <td class="text-center">{{ $s->tanggal_surat->format('d/m/Y') }}</td>
                    <td>{{ $s->hal }}</td>
                    <td>{{ $s->tempat }}</td>
                    <td>{{ $s->penandatangan_nama }}</td>
                    <td class="text-center">{{ ucfirst($s->status) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
