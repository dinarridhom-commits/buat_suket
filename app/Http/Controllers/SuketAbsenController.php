<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\SuketAbsen;
use App\Models\Pegawai;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SuketAbsenController extends Controller
{
    public function index()
    {
        $sukets = SuketAbsen::latest()->paginate(10);
        return view('surat.suket-index', compact('sukets'));
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

        // Auto-nomor: hitung total suket tahun ini
        $tahun = date('Y');
        $urutan = SuketAbsen::whereYear('created_at', $tahun)->count() + 1;
        $autoNomor = 'KP.07.03/Bws1/' . str_pad($urutan, 3, '0', STR_PAD_LEFT) . '/' . $tahun;

        return view('surat.suket-create', compact('pegawais', 'kop', 'autoNomor'));
    }

    public function store(Request $request)
    {
        $validated = Validator::make($request->all(), [
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
        ActivityLog::record('create', 'Suket Absen', "Membuat Suket {$suket->nomor_surat} untuk {$suket->nama_pegawai}");

        return redirect()->route('surat.suket.index')->with('success', 'Surat Keterangan Lupa Absen berhasil disimpan ke database.');
    }

    public function show($id)
    {
        $suket = SuketAbsen::findOrFail($id);
        $kop = [
            'kementerian' => Setting::get('instansi_kementerian', 'KEMENTERIAN PEKERJAAN UMUM'),
            'ditjen'      => Setting::get('instansi_ditjen', 'DIREKTORAT JENDERAL SUMBER DAYA AIR'),
            'balai'       => Setting::get('instansi_balai', 'BALAI WILAYAH SUNGAI KALIMANTAN I PONTIANAK'),
            'alamat'      => Setting::get('instansi_alamat', 'Jalan Kantor No. 1 Pontianak, Kalimantan Barat, 78111'),
            'kontak'      => Setting::get('instansi_kontak', 'Telp. (0561) 732292 | Email: bws.kalimantan1@pu.go.id'),
        ];
        $verifyUrl = VerificationController::makeUrl('suket', $suket->id);
        return view('surat.suket-show', compact('suket', 'kop', 'verifyUrl'));
    }

    public function destroy($id)
    {
        $suket = SuketAbsen::findOrFail($id);
        $info = "{$suket->nomor_surat} ({$suket->nama_pegawai})";
        $suket->delete();
        ActivityLog::record('delete', 'Suket Absen', "Menghapus arsip Suket {$info}");

        return redirect()->route('surat.suket.index')->with('success', 'Arsip suket lupa absen berhasil dihapus.');
    }
}
