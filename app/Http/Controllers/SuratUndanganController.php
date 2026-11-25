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
    public function index(Request $request)
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

        $surats = $query->paginate(10)->withQueryString();
        return view('surat.undangan-index', compact('surats'));
    }

    public function create()
    {
        $pegawais = Pegawai::all();
        $kop = [
            'kementerian' => Setting::get('instansi_kementerian', 'KEMENTERIAN PEKERJAAN UMUM'),
            'ditjen'      => Setting::get('instansi_ditjen', 'DIREKTORAT JENDERAL SUMBER DAYA AIR'),
            'balai'       => Setting::get('instansi_balai', 'BALAI WILAYAH SUNGAI KALIMANTAN I PONTIANAK'),
            'alamat'      => Setting::get('instansi_alamat', 'Jalan Kantor No. 1 Pontianak, Kalimantan Barat, 78111'),
            'kontak'      => Setting::get('instansi_kontak', 'Telp. (0561) 732292 | Email: bws.kalimantan1@pu.go.id'),
        ];

        // Auto-nomor: hitung total undangan tahun ini
        $tahun = date('Y');
        $prefix = Setting::get('prefix_undangan', 'UM.01.02/Bws1');
        $urutan = SuratUndangan::whereYear('created_at', $tahun)->count() + 1;
        $autoNomor = $prefix . '/' . str_pad($urutan, 3, '0', STR_PAD_LEFT) . '/' . $tahun;

        return view('surat.undangan-create', compact('pegawais', 'kop', 'autoNomor'));
    }

    public function store(Request $request)
    {
        $validated = Validator::make($request->all(), [
            'nomor_surat'           => 'required',
            'hal'                   => 'required',
            'tanggal_surat'         => 'required|date',
            'tempat'                => 'required',
            'penandatangan_nama'    => 'required',
            'penandatangan_jabatan' => 'required',
        ])->validate();

        $surat = SuratUndangan::create($request->except('penerimas'));

        if ($request->has('penerimas') && is_array($request->penerimas)) {
            foreach ($request->penerimas as $index => $nama) {
                if (trim($nama)) {
                    $surat->penerimas()->create([
                        'nama_penerima' => trim($nama),
                        'urutan'        => $index + 1
                    ]);
                }
            }
        }

        ActivityLog::record('create', 'Surat Undangan', "Membuat Surat Undangan {$surat->nomor_surat} — {$surat->hal}");

        return redirect()->route('surat.undangan.index')->with('success', 'Surat Undangan berhasil disimpan ke database.');
    }

    public function show($id)
    {
        $surat = SuratUndangan::with('penerimas')->findOrFail($id);
        $kop = [
            'kementerian' => Setting::get('instansi_kementerian', 'KEMENTERIAN PEKERJAAN UMUM'),
            'ditjen'      => Setting::get('instansi_ditjen', 'DIREKTORAT JENDERAL SUMBER DAYA AIR'),
            'balai'       => Setting::get('instansi_balai', 'BALAI WILAYAH SUNGAI KALIMANTAN I PONTIANAK'),
            'alamat'      => Setting::get('instansi_alamat', 'Jalan Kantor No. 1 Pontianak, Kalimantan Barat, 78111'),
            'kontak'      => Setting::get('instansi_kontak', 'Telp. (0561) 732292 | Email: bws.kalimantan1@pu.go.id'),
        ];
        $verifyUrl = VerificationController::makeUrl('undangan', $surat->id);
        return view('surat.undangan-show', compact('surat', 'kop', 'verifyUrl'));
    }

    public function edit($id)
    {
        $surat = SuratUndangan::with('penerimas')->findOrFail($id);
        $pegawais = Pegawai::all();
        $kop = [
            'kementerian' => Setting::get('instansi_kementerian', 'KEMENTERIAN PEKERJAAN UMUM'),
            'ditjen'      => Setting::get('instansi_ditjen', 'DIREKTORAT JENDERAL SUMBER DAYA AIR'),
            'balai'       => Setting::get('instansi_balai', 'BALAI WILAYAH SUNGAI KALIMANTAN I PONTIANAK'),
            'alamat'      => Setting::get('instansi_alamat', 'Jalan Kantor No. 1 Pontianak, Kalimantan Barat, 78111'),
            'kontak'      => Setting::get('instansi_kontak', 'Telp. (0561) 732292 | Email: bws.kalimantan1@pu.go.id'),
        ];
        $autoNomor = $surat->nomor_surat;

        return view('surat.undangan-create', compact('pegawais', 'kop', 'autoNomor', 'surat'));
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

        $surat->update($request->except(['penerimas', '_token', '_method']));

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
