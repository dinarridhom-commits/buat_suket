<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\SuratUndangan;
use App\Models\Pegawai;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SuratUndanganController extends Controller
{
    public static function kopPresets(): array
    {
        $kementerian = Setting::get('instansi_kementerian', 'KEMENTERIAN PEKERJAAN UMUM');
        $ditjen      = Setting::get('instansi_ditjen', 'DIREKTORAT JENDERAL SUMBER DAYA AIR');
        $alamat      = 'Jl. A. Sood Nomor 06 Pontianak 78121, Telepon (0561) 734866, Surel bwsk1pontianak@pu.go.id';

        return [
            'balai' => [
                'label'       => 'Balai Wilayah Sungai Kalimantan I Pontianak',
                'kementerian' => $kementerian,
                'ditjen'      => $ditjen,
                'lines3'      => ['BALAI WILAYAH SUNGAI KALIMANTAN I PONTIANAK'],
                'alamat'      => $alamat,
            ],
            'satker_op' => [
                'label'       => 'Satker Operasi dan Pemeliharaan SDA Kalimantan I',
                'kementerian' => $kementerian,
                'ditjen'      => $ditjen,
                'lines3'      => [
                    'BALAI WILAYAH SUNGAI KALIMANTAN I PONTIANAK',
                    'SATUAN KERJA OPERASI DAN PEMELIHARAAN SUMBER DAYA AIR KALIMANTAN I',
                ],
                'alamat'      => $alamat,
            ],
            'satker_pjpa' => [
                'label'       => 'Satker SNVT Pelaksanaan Jaringan Pemanfaatan Air Kalimantan I',
                'kementerian' => $kementerian,
                'ditjen'      => $ditjen,
                'lines3'      => [
                    'BALAI WILAYAH SUNGAI KALIMANTAN I PONTIANAK',
                    'SATUAN KERJA SNVT PELAKSANAAN JARINGAN PEMANFAATAN AIR KALIMANTAN I',
                ],
                'alamat'      => $alamat,
            ],
            'satker_pjsa' => [
                'label'       => 'Satker SNVT Pelaksanaan Jaringan Sumber Air Kalimantan I',
                'kementerian' => $kementerian,
                'ditjen'      => $ditjen,
                'lines3'      => [
                    'BALAI WILAYAH SUNGAI KALIMANTAN I PONTIANAK',
                    'SATUAN KERJA SNVT PELAKSANAAN JARINGAN SUMBER AIR KALIMANTAN I',
                ],
                'alamat'      => $alamat,
            ],
            'satker_bws' => [
                'label'       => 'Satker Balai Wilayah Sungai Kalimantan I Pontianak',
                'kementerian' => $kementerian,
                'ditjen'      => $ditjen,
                'lines3'      => [
                    'BALAI WILAYAH SUNGAI KALIMANTAN I PONTIANAK',
                    'SATUAN KERJA BALAI WILAYAH SUNGAI KALIMANTAN I PONTIANAK',
                ],
                'alamat'      => $alamat,
            ],
        ];
    }

    private function buildFilteredQuery(Request $request)
    {
        $query = SuratUndangan::with('penerimas')->latest();

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($w) use ($q) {
                $w->where('nomor_surat', 'like', "%{$q}%")
                  ->orWhere('hal', 'like', "%{$q}%")
                  ->orWhere('tempat', 'like', "%{$q}%")
                  ->orWhere('penandatangan_nama', 'like', "%{$q}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('bulan')) {
            $parts = explode('-', $request->bulan);
            if (count($parts) === 2) {
                $query->whereYear('tanggal_surat', $parts[0])
                      ->whereMonth('tanggal_surat', $parts[1]);
            }
        }

        return $query;
    }

    private function generateAutoNomor(): string
    {
        $tahun  = date('Y');
        $prefix = Setting::get('prefix_undangan', 'UM.01.02/Bws1');
        $urutan = SuratUndangan::whereYear('created_at', $tahun)->count() + 1;
        return $prefix . '/' . str_pad($urutan, 3, '0', STR_PAD_LEFT) . '/' . $tahun;
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

    private function extractExtraData(Request $request): array
    {
        $extra = $request->input('extra_data', []);
        if (!is_array($extra)) {
            $extra = [];
        }
        if (isset($extra['schedule_json']) && is_string($extra['schedule_json'])) {
            $decoded = json_decode($extra['schedule_json'], true);
            $extra['schedule_rows'] = is_array($decoded) ? $decoded : [];
            unset($extra['schedule_json']);
        }
        if (isset($extra['body_json']) && is_string($extra['body_json'])) {
            $decodedBody = json_decode($extra['body_json'], true);
            $extra['body'] = is_array($decodedBody) ? $decodedBody : [];
            unset($extra['body_json']);
        }
        return $extra;
    }

    public function index(Request $request)
    {
        $surats = $this->buildFilteredQuery($request)->paginate(10)->withQueryString();
        return view('surat.undangan-index', compact('surats'));
    }

    public function exportExcel(Request $request)
    {
        $surats     = $this->buildFilteredQuery($request)->get();
        $filename   = 'Buku_Agenda_Surat_Undangan_' . date('Ymd_His') . '.xls';
        $exportedAt = now()->locale('id')->translatedFormat('d F Y, H:i') . ' WIB';

        ActivityLog::record('export', 'Surat Undangan', 'Mengekspor ' . $surats->count() . ' arsip Surat Undangan ke Excel.');

        $html = view('surat.undangan-export-excel', compact('surats', 'exportedAt'))->render();

        return response($html, 200, [
            'Content-Type'        => 'application/vnd.ms-excel; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma'              => 'no-cache',
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Expires'             => '0',
        ]);
    }

    public function create()
    {
        $pegawais   = Pegawai::all();
        $kop        = $this->defaultKop();
        $kopPresets = self::kopPresets();
        $autoNomor  = $this->generateAutoNomor();

        return view('surat.undangan-create', compact('pegawais', 'kop', 'kopPresets', 'autoNomor'));
    }

    public function duplicate($id)
    {
        $duplicateFrom = SuratUndangan::with('penerimas')->findOrFail($id);
        $pegawais      = Pegawai::all();
        $kop           = $this->defaultKop();
        $kopPresets    = self::kopPresets();
        $autoNomor     = $this->generateAutoNomor();

        return view('surat.undangan-create', compact('pegawais', 'kop', 'kopPresets', 'autoNomor', 'duplicateFrom'));
    }

    public function store(Request $request)
    {
        Validator::make($request->all(), [
            'nomor_surat'           => 'required',
            'hal'                   => 'required',
            'tanggal_surat'         => 'required|date',
            'tempat'                => 'required',
            'penandatangan_nama'    => 'required',
            'penandatangan_jabatan' => 'required',
        ])->validate();

        $payload = $request->except(['penerimas', '_token', 'extra_data']);
        $payload['extra_data'] = $this->extractExtraData($request);

        $surat = SuratUndangan::create($payload);

        if ($request->has('penerimas') && is_array($request->penerimas)) {
            foreach ($request->penerimas as $index => $nama) {
                if (trim($nama)) {
                    $surat->penerimas()->create([
                        'nama_penerima' => trim($nama),
                        'urutan'        => $index + 1,
                    ]);
                }
            }
        }

        ActivityLog::record('create', 'Surat Undangan', "Membuat Surat Undangan {$surat->nomor_surat} — {$surat->hal}");

        return redirect()->route('surat.undangan.show', $surat->id)->with('success', 'Surat Undangan berhasil disimpan ke database.');
    }

    public function show($id)
    {
        $surat      = SuratUndangan::with('penerimas')->findOrFail($id);
        $kop        = $this->defaultKop();
        $kopPresets = self::kopPresets();
        $verifyUrl  = VerificationController::makeUrl('undangan', $surat->id);

        return view('surat.undangan-show', compact('surat', 'kop', 'kopPresets', 'verifyUrl'));
    }

    public function edit($id)
    {
        $surat      = SuratUndangan::with('penerimas')->findOrFail($id);
        $pegawais   = Pegawai::all();
        $kop        = $this->defaultKop();
        $kopPresets = self::kopPresets();
        $autoNomor  = $surat->nomor_surat;

        return view('surat.undangan-create', compact('pegawais', 'kop', 'kopPresets', 'autoNomor', 'surat'));
    }

    public function update(Request $request, $id)
    {
        $surat = SuratUndangan::findOrFail($id);

        Validator::make($request->all(), [
            'nomor_surat'           => 'required',
            'hal'                   => 'required',
            'tanggal_surat'         => 'required|date',
            'tempat'                => 'required',
            'penandatangan_nama'    => 'required',
            'penandatangan_jabatan' => 'required',
        ])->validate();

        $payload = $request->except(['penerimas', '_token', '_method', 'extra_data']);
        $payload['extra_data'] = $this->extractExtraData($request);

        $surat->update($payload);

        if ($request->has('penerimas') && is_array($request->penerimas)) {
            $surat->penerimas()->delete();
            foreach ($request->penerimas as $index => $nama) {
                if (trim($nama)) {
                    $surat->penerimas()->create([
                        'nama_penerima' => trim($nama),
                        'urutan'        => $index + 1,
                    ]);
                }
            }
        }

        ActivityLog::record('update', 'Surat Undangan', "Memperbarui Surat Undangan {$surat->nomor_surat} — {$surat->hal}");

        return redirect()->route('surat.undangan.show', $surat->id)->with('success', 'Perubahan Surat Undangan berhasil disimpan.');
    }

    public function updateStatus(Request $request, $id)
    {
        $surat = SuratUndangan::findOrFail($id);
        $surat->update(['status' => $request->status]);
        ActivityLog::record('update', 'Surat Undangan', "Mengubah status surat {$surat->nomor_surat} menjadi " . ucfirst($request->status));
        return redirect()->back()->with('success', 'Status surat berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $surat = SuratUndangan::findOrFail($id);
        $nomor = $surat->nomor_surat;
        $surat->delete();
        ActivityLog::record('delete', 'Surat Undangan', "Menghapus arsip Surat Undangan {$nomor}");

        return redirect()->route('surat.undangan.index')->with('success', 'Arsip surat undangan berhasil dihapus.');
    }
}
