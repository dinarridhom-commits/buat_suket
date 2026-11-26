<?php

namespace App\Http\Controllers;

use App\Models\Pegawai;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PegawaiController extends Controller
{
    private function buildFilteredQuery(Request $request)
    {
        $query = Pegawai::orderBy('id', 'asc');

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($w) use ($q) {
                $w->where('nama', 'like', "%{$q}%")
                  ->orWhere('nip', 'like', "%{$q}%")
                  ->orWhere('pangkat', 'like', "%{$q}%")
                  ->orWhere('kelas', 'like', "%{$q}%")
                  ->orWhere('jabatan', 'like', "%{$q}%")
                  ->orWhere('rangkap', 'like', "%{$q}%")
                  ->orWhere('unit', 'like', "%{$q}%")
                  ->orWhere('satker', 'like', "%{$q}%")
                  ->orWhere('ket', 'like', "%{$q}%")
                  ->orWhere('jabatan_surat', 'like', "%{$q}%")
                  ->orWhere('rangkap_surat', 'like', "%{$q}%")
                  ->orWhere('nama_surat', 'like', "%{$q}%");
            });
        }

        if ($request->filled('unit')) {
            $query->where('unit', $request->unit);
        }

        return $query;
    }

    private function validatePegawai(Request $request): array
    {
        return Validator::make($request->all(), [
            'nama'          => 'required|string|max:255',
            'nip'           => 'nullable|string|max:50',
            'pangkat'       => 'nullable|string|max:100',
            'kelas'         => 'nullable|string|max:50',
            'jabatan'       => 'required|string|max:255',
            'rangkap'       => 'nullable|string|max:255',
            'unit'          => 'nullable|string|max:255',
            'satker'        => 'nullable|string|max:255',
            'ket'           => 'nullable|string|max:255',
            'jabatan_surat' => 'nullable|string|max:255',
            'rangkap_surat' => 'nullable|string|max:255',
            'nama_surat'    => 'nullable|string|max:255',
        ])->validate();
    }

    public function index(Request $request)
    {
        $perPage      = $this->resolvePerPage($request, [15, 25, 50, 100], 15);
        $pegawais     = $this->buildFilteredQuery($request)->paginate($perPage)->withQueryString();
        $totalPegawai = Pegawai::count();
        $units        = Pegawai::orderBy('id', 'asc')->pluck('unit')->filter()->unique()->values();

        return view('pegawai.index', compact('pegawais', 'totalPegawai', 'units', 'perPage'));
    }

    public function search(Request $request)
    {
        $q = $request->get('q', '');
        $pegawais = Pegawai::where('nama', 'like', "%{$q}%")
            ->orWhere('nip', 'like', "%{$q}%")
            ->orWhere('jabatan', 'like', "%{$q}%")
            ->orWhere('rangkap', 'like', "%{$q}%")
            ->take(20)
            ->get();

        return response()->json($pegawais);
    }

    public function store(Request $request)
    {
        $data = $this->validatePegawai($request);
        Pegawai::create($data);

        return redirect()->route('pegawai.index')->with('success', 'Data pegawai baru berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $pegawai = Pegawai::findOrFail($id);
        $data    = $this->validatePegawai($request);
        $pegawai->update($data);

        return redirect()->route('pegawai.index')->with('success', 'Data pegawai berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $pegawai = Pegawai::findOrFail($id);
        $pegawai->delete();

        return redirect()->route('pegawai.index')->with('success', 'Data pegawai berhasil dihapus.');
    }

    public function destroyBulk(Request $request)
    {
        $validated = $request->validate([
            'ids'   => 'required|array|min:1',
            'ids.*' => 'integer',
        ], [
            'ids.required' => 'Pilih minimal satu data pegawai yang akan dihapus.',
            'ids.min'      => 'Pilih minimal satu data pegawai yang akan dihapus.',
        ]);

        $ids = array_values(array_unique(array_map('intval', $validated['ids'])));
        $deleted = Pegawai::whereIn('id', $ids)->delete();

        return redirect()->route('pegawai.index')
            ->with('success', $deleted . ' data pegawai berhasil dihapus.');
    }

    public function exportCsv(Request $request)
    {
        $pegawais   = $this->buildFilteredQuery($request)->get();
        $filename   = 'Master_Pegawai_BWS_Kalimantan_I_' . date('Ymd_His') . '.xls';
        $exportedAt = now()->locale('id')->translatedFormat('d F Y, H:i') . ' WIB';

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

        $path  = $request->file('file')->getRealPath();
        $raw   = file_get_contents($path);
        $raw   = preg_replace('/^\xEF\xBB\xBF/', '', $raw);
        $lines = preg_split("/\r\n|\n|\r/", $raw);

        if (count($lines) < 2) {
            return redirect()->route('pegawai.index')->with('error', 'File CSV kosong atau tidak memiliki baris data.');
        }

        $headerLine = $lines[0];
        $delimiter  = ',';
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
                'unit'          => $row['unit'] ?? $row['unit kerja'] ?? null,
                'satker'        => $row['satker'] ?? $row['satuan kerja'] ?? null,
                'nama'          => $nama,
                'nip'           => $nip,
                'pangkat'       => $row['pangkat'] ?? $row['pangkat / gol.'] ?? $row['pangkat / golongan'] ?? null,
                'kelas'         => $row['kelas'] ?? null,
                'jabatan'       => $row['jabatan'] ?? $row['jabatan utama'] ?? $row['jabatan definitif / struktural'] ?? null,
                'rangkap'       => $row['rangkap'] ?? $row['jabatan rangkap'] ?? null,
                'jabatan_surat' => $row['jabatan_surat'] ?? $row['jabatan di surat'] ?? $row['jabatansurat'] ?? null,
                'rangkap_surat' => $row['rangkap_surat'] ?? $row['rangkap di surat'] ?? $row['rangkapsurat'] ?? null,
                'nama_surat'    => $row['nama_surat'] ?? $row['nama di surat'] ?? $row['namasurat'] ?? null,
                'ket'           => $row['ket'] ?? $row['ket.'] ?? $row['keterangan'] ?? null,
            ];

            if ($nip && ($existing = Pegawai::where('nip', $nip)->first())) {
                $existing->update(array_filter($payload, fn($v) => $v !== null && $v !== ''));
                $updated++;
            } else {
                Pegawai::create($payload);
                $created++;
            }
        }

        return redirect()->route('pegawai.index')->with('success', "Impor selesai: {$created} pegawai ditambahkan, {$updated} pegawai diperbarui.");
    }
}
