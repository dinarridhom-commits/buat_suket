<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        $settings = [
            'kementerian'      => Setting::get('instansi_kementerian', 'KEMENTERIAN PEKERJAAN UMUM'),
            'ditjen'           => Setting::get('instansi_ditjen', 'DIREKTORAT JENDERAL SUMBER DAYA AIR'),
            'balai'            => Setting::get('instansi_balai', 'BALAI WILAYAH SUNGAI KALIMANTAN I PONTIANAK'),
            'alamat'           => Setting::get('instansi_alamat', 'Jalan Kantor No. 1 Pontianak, Kalimantan Barat, 78111'),
            'kontak'           => Setting::get('instansi_kontak', 'Telp. (0561) 732292 | Email: bws.kalimantan1@pu.go.id'),
            'prefix_undangan'  => Setting::get('prefix_undangan', 'UM.01.02/Bws1'),
            'prefix_suket'     => Setting::get('prefix_suket', 'KP.07.03/Bws1'),
        ];

        return view('settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'instansi_kementerian' => 'required|string',
            'instansi_ditjen'      => 'required|string',
            'instansi_balai'       => 'required|string',
            'instansi_alamat'      => 'required|string',
            'instansi_kontak'      => 'required|string',
        ]);

        Setting::set('instansi_kementerian', $request->instansi_kementerian);
        Setting::set('instansi_ditjen',      $request->instansi_ditjen);
        Setting::set('instansi_balai',       $request->instansi_balai);
        Setting::set('instansi_alamat',      $request->instansi_alamat);
        Setting::set('instansi_kontak',      $request->instansi_kontak);

        if ($request->filled('prefix_undangan')) {
            Setting::set('prefix_undangan', $request->prefix_undangan);
        }
        if ($request->filled('prefix_suket')) {
            Setting::set('prefix_suket', $request->prefix_suket);
        }

        return redirect()->route('settings.index')->with('success', 'Pengaturan Kop Balai & Profil Instansi berhasil diperbarui.');
    }
}
