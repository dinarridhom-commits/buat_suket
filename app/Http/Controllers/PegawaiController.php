<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Pegawai;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PegawaiController extends Controller
{
    public function index(Request $request)
    {
        $query = Pegawai::query();

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function($w) use ($q) {
                $w->where('nama', 'like', "%{$q}%")
                  ->orWhere('nip', 'like', "%{$q}%")
                  ->orWhere('jabatan', 'like', "%{$q}%");
            });
        }

        if ($request->filled('unit')) {
            $query->where('unit', $request->unit);
        }

        $units = Pegawai::distinct()->pluck('unit')->filter()->values();
        $pegawais = $query->paginate(15)->withQueryString();

        return view('pegawai.index', compact('pegawais', 'units'));
    }

    public function search(Request $request)
    {
        $q = $request->get('q', '');
        $pegawais = Pegawai::where('nama', 'like', "%{$q}%")
            ->orWhere('nip', 'like', "%{$q}%")
            ->orWhere('jabatan', 'like', "%{$q}%")
            ->take(20)
            ->get();

        return response()->json($pegawais);
    }

    public function store(Request $request)
    {
        $validated = Validator::make($request->all(), [
            'nama' => 'required|string|max:255',
            'nip' => 'nullable|string|max:50',
            'pangkat' => 'nullable|string|max:100',
            'jabatan' => 'required|string|max:255',
            'unit' => 'nullable|string|max:255',
            'rangkap' => 'nullable|string|max:255',
        ])->validate();

        $pegawai = Pegawai::create($request->all());
        ActivityLog::record('create', 'Database Pegawai', "Menambahkan pegawai baru: {$pegawai->nama}");

        return redirect()->route('pegawai.index')->with('success', 'Data pegawai baru berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $pegawai = Pegawai::findOrFail($id);

        $validated = Validator::make($request->all(), [
            'nama' => 'required|string|max:255',
            'nip' => 'nullable|string|max:50',
            'pangkat' => 'nullable|string|max:100',
            'jabatan' => 'required|string|max:255',
            'unit' => 'nullable|string|max:255',
            'rangkap' => 'nullable|string|max:255',
        ])->validate();

        $pegawai->update($request->all());
        ActivityLog::record('update', 'Database Pegawai', "Memperbarui data pegawai: {$pegawai->nama}");

        return redirect()->route('pegawai.index')->with('success', 'Data pegawai berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $pegawai = Pegawai::findOrFail($id);
        $nama = $pegawai->nama;
        $pegawai->delete();
        ActivityLog::record('delete', 'Database Pegawai', "Menghapus data pegawai: {$nama}");

        return redirect()->route('pegawai.index')->with('success', 'Data pegawai berhasil dihapus.');
    }

    public function exportCsv(Request $request)
    {
        $query = Pegawai::orderBy('id', 'asc');

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($w) use ($q) {
                $w->where('nama', 'like', "%{$q}%")
                  ->orWhere('nip', 'like', "%{$q}%")
                  ->orWhere('jabatan', 'like', "%{$q}%");
            });
        }

        if ($request->filled('unit')) {
            $query->where('unit', $request->unit);
        }

        $pegawais = $query->get();
        $filename = 'Master_Pegawai_BWS_Kalimantan_I_' . date('Ymd_His') . '.xls';
        $exportedAt = now()->locale('id')->translatedFormat('d F Y, H:i') . ' WIB';

        ActivityLog::record('export', 'Database Pegawai', 'Mengekspor ' . $pegawais->count() . ' data pegawai ke Excel.');

        $html = view('pegawai.export-excel', compact('pegawais', 'exportedAt'))->render();

        return response($html, 200, [
            'Content-Type'        => 'application/vnd.ms-excel; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma'              => 'no-cache',
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Expires'             => '0',
        ]);
    }

    public function importCsv(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt|max:5120',
        ]);

        $path = $request->file('file')->getRealPath();
        $raw  = file_get_contents($path);
        // Bersihkan UTF-8 BOM jika ada
        $raw  = preg_replace('/^\xEF\xBB\xBF/', '', $raw);
        $lines = preg_split("/\r\n|\n|\r/", $raw);

        if (count($lines) < 2) {
            return redirect()->route('pegawai.index')->with('error', 'File CSV kosong atau tidak memiliki baris data.');
        }

        // Deteksi delimiter otomatis (, ; atau tab)
        $headerLine = $lines[0];
        $delimiter = ',';
        if (substr_count($headerLine, ';') > substr_count($headerLine, ',')) {
            $delimiter = ';';
        } elseif (substr_count($headerLine, "\t") > substr_count($headerLine, ',')) {
            $delimiter = "\t";
        }

        $headers = array_map(fn($h) => strtolower(trim($h)), str_getcsv($headerLine, $delimiter));
        $created = 0;
        $updated = 0;

        for ($i = 1; $i < count($lines); $i++) {
            $line = trim($lines[$i]);
            if ($line === '') {
                continue;
            }
            $cols = str_getcsv($line, $delimiter);
            $row  = [];
            foreach ($headers as $idx => $key) {
                $row[$key] = isset($cols[$idx]) ? trim($cols[$idx]) : null;
            }

            $nama = $row['nama'] ?? $row['nama lengkap'] ?? $row['nama lengkap & gelar'] ?? null;
            if (!$nama) {
                continue;
            }

            $nip = $row['nip'] ?? null;
            if ($nip) {
                $nip = ltrim($nip, "'");
                if ($nip === '-') {
                    $nip = null;
                }
            }

            $payload = [
                'nama'    => $nama,
                'nip'     => $nip,
                'pangkat' => $row['pangkat'] ?? $row['pangkat / golongan'] ?? null,
                'jabatan' => $row['jabatan'] ?? $row['jabatan definitif / struktural'] ?? null,
                'rangkap' => $row['rangkap'] ?? $row['jabatan rangkap'] ?? null,
                'unit'    => $row['unit'] ?? $row['unit kerja'] ?? null,
            ];

            if ($nip && ($existing = Pegawai::where('nip', $nip)->first())) {
                $existing->update(array_filter($payload, fn($v) => $v !== null && $v !== ''));
                $updated++;
            } else {
                Pegawai::create($payload);
                $created++;
            }
        }

        ActivityLog::record('import', 'Database Pegawai', "Impor CSV pegawai: {$created} baru, {$updated} diperbarui.");

        return redirect()->route('pegawai.index')->with('success', "Impor selesai: {$created} pegawai ditambahkan, {$updated} pegawai diperbarui.");
    }
}
