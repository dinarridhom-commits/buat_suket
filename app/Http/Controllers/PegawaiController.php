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

    public function exportCsv()
    {
        $pegawais = Pegawai::orderBy('id', 'asc')->get();
        $filename = 'Daftar_Pegawai_BWS_Kalimantan_I_' . date('Ymd_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function() use ($pegawais) {
            $handle = fopen('php://output', 'w');
            // Write UTF-8 BOM for Microsoft Excel
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

            // Header row
            fputcsv($handle, [
                'No',
                'NIP',
                'Nama Lengkap & Gelar',
                'Pangkat / Golongan',
                'Jabatan Definitif / Struktural',
                'Jabatan Rangkap',
                'Unit Kerja'
            ]);

            foreach ($pegawais as $idx => $p) {
                fputcsv($handle, [
                    $idx + 1,
                    $p->nip ? "'" . $p->nip : '-',
                    $p->nama,
                    $p->pangkat ?? '-',
                    $p->jabatan ?? '-',
                    $p->rangkap ?? '-',
                    $p->unit ?? 'Balai Wilayah Sungai Kalimantan I Pontianak'
                ]);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }
}
