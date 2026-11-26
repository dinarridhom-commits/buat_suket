<?php

namespace App\Http\Controllers;

use App\Models\SuketAbsen;
use App\Models\Pegawai;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SuketAbsenController extends Controller
{
    private function buildFilteredQuery(Request $request)
    {
        $query = SuketAbsen::latest();

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($w) use ($q) {
                $w->where('nomor_surat', 'like', "%{$q}%")
                  ->orWhere('nama_pegawai', 'like', "%{$q}%")
                  ->orWhere('nip_pegawai', 'like', "%{$q}%")
                  ->orWhere('atasan_nama', 'like', "%{$q}%");
            });
        }

        if ($request->filled('jenis_absen')) {
            $query->where('jenis_absen', $request->jenis_absen);
        }

        if ($request->filled('bulan')) {
            $parts = explode('-', $request->bulan);
            if (count($parts) === 2) {
                $query->whereYear('tanggal_kejadian', $parts[0])
                      ->whereMonth('tanggal_kejadian', $parts[1]);
            }
        }

        return $query;
    }

    public function index(Request $request)
    {
        $perPage = $this->resolvePerPage($request, [5, 10, 15, 25, 50, 100], 10);
        $sukets  = $this->buildFilteredQuery($request)->paginate($perPage)->withQueryString();
        return view('surat.suket-index', compact('sukets', 'perPage'));
    }

    public function exportExcel(Request $request)
    {
        $sukets     = $this->buildFilteredQuery($request)->get();
        $filename   = 'Rekap_Suket_Absen_' . date('Ymd_His') . '.xls';
        $exportedAt = now()->locale('id')->translatedFormat('d F Y, H:i') . ' WIB';

        $html = view('surat.suket-export-excel', compact('sukets', 'exportedAt'))->render();

        return response($html, 200, [
            'Content-Type'        => 'application/vnd.ms-excel; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma'              => 'no-cache',
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Expires'             => '0',
        ]);
    }

    private function defaultKop(): array
    {
        return [
            'kementerian' => Setting::get('instansi_kementerian', 'KEMENTERIAN PEKERJAAN UMUM'),
            'ditjen'      => Setting::get('instansi_ditjen', 'DIREKTORAT JENDERAL SUMBER DAYA AIR'),
            'balai'       => Setting::get('instansi_balai', 'BALAI WILAYAH SUNGAI KALIMANTAN I PONTIANAK'),
            'alamat'      => Setting::get('instansi_alamat', 'Jl. A. Sood Nomor 06 Pontianak 78121, Telepon (0561) 734866'),
            'kontak'      => Setting::get('instansi_kontak', 'Surel bwsk1pontianak@pu.go.id'),
        ];
    }

    public function create()
    {
        $pegawais = Pegawai::all();
        $kop      = $this->defaultKop();

        $tahun     = date('Y');
        $prefix    = Setting::get('prefix_suket', 'KP.07.03/Bws1');
        $urutan    = SuketAbsen::whereYear('created_at', $tahun)->count() + 1;
        $autoNomor = $prefix . '/' . str_pad($urutan, 3, '0', STR_PAD_LEFT) . '/' . $tahun;

        return view('surat.suket-create', compact('pegawais', 'kop', 'autoNomor'));
    }

    public function store(Request $request)
    {
        Validator::make($request->all(), [
            'nomor_surat'      => 'required',
            'jenis_absen'      => 'required|in:masuk,pulang',
            'nama_pegawai'     => 'required',
            'jabatan_pegawai'  => 'required',
            'tanggal_kejadian' => 'required|date',
            'waktu_kejadian'   => 'required',
            'alasan'           => 'required',
            'atasan_nama'      => 'required',
            'atasan_jabatan'   => 'required',
        ])->validate();

        $suket = SuketAbsen::create($request->all());

        return redirect()->route('surat.suket.show', $suket->id)->with('success', 'Surat Keterangan Lupa Absen berhasil disimpan dan siap dicetak.');
    }

    public function show($id)
    {
        $suket = SuketAbsen::findOrFail($id);
        $kop   = $this->defaultKop();
        return view('surat.suket-show', compact('suket', 'kop'));
    }

    public function edit($id)
    {
        $suket     = SuketAbsen::findOrFail($id);
        $pegawais  = Pegawai::all();
        $kop       = $this->defaultKop();
        $autoNomor = $suket->nomor_surat;

        return view('surat.suket-create', compact('pegawais', 'kop', 'autoNomor', 'suket'));
    }

    public function update(Request $request, $id)
    {
        $suket = SuketAbsen::findOrFail($id);

        Validator::make($request->all(), [
            'nomor_surat'      => 'required',
            'jenis_absen'      => 'required|in:masuk,pulang',
            'nama_pegawai'     => 'required',
            'jabatan_pegawai'  => 'required',
            'tanggal_kejadian' => 'required|date',
            'waktu_kejadian'   => 'required',
            'alasan'           => 'required',
            'atasan_nama'      => 'required',
            'atasan_jabatan'   => 'required',
        ])->validate();

        $suket->update($request->except(['_token', '_method']));

        return redirect()->route('surat.suket.show', $suket->id)->with('success', 'Perubahan Surat Keterangan berhasil disimpan.');
    }

    public function destroy($id)
    {
        $suket = SuketAbsen::findOrFail($id);
        $suket->delete();

        return redirect()->route('surat.suket.index')->with('success', 'Arsip suket lupa absen berhasil dihapus.');
    }

    public function destroyBulk(Request $request)
    {
        $validated = $request->validate([
            'ids'   => 'required|array|min:1',
            'ids.*' => 'integer',
        ], [
            'ids.required' => 'Pilih minimal satu arsip suket lupa absen yang akan dihapus.',
            'ids.min'      => 'Pilih minimal satu arsip suket lupa absen yang akan dihapus.',
        ]);

        $ids = array_values(array_unique(array_map('intval', $validated['ids'])));
        $deleted = SuketAbsen::whereIn('id', $ids)->delete();

        return redirect()->route('surat.suket.index')
            ->with('success', $deleted . ' arsip suket lupa absen berhasil dihapus.');
    }
}
