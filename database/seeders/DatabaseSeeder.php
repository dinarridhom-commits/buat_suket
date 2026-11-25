<?php

namespace Database\Seeders;

use App\Models\Pegawai;
use App\Models\Setting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Seed Default Settings Kop Balai
        Setting::set('instansi_kementerian', 'KEMENTERIAN PEKERJAAN UMUM');
        Setting::set('instansi_ditjen', 'DIREKTORAT JENDERAL SUMBER DAYA AIR');
        Setting::set('instansi_balai', 'BALAI WILAYAH SUNGAI KALIMANTAN I PONTIANAK');
        Setting::set('instansi_alamat', 'Jalan Kantor No. 1 Pontianak, Kalimantan Barat, 78111');
        Setting::set('instansi_kontak', 'Telp. (0561) 732292 | Email: bws.kalimantan1@pu.go.id');

        // 2. Seed Default User / Akun Login Pengelola
        $this->call(UserSeeder::class);

        // 2. Seed Pegawai dari data BWSK I
        $dataPath = database_path('data/pegawai.json');
        if (File::exists($dataPath)) {
            $json = File::get($dataPath);
            $pegawais = json_decode($json, true);

            if (is_array($pegawais)) {
                foreach ($pegawais as $p) {
                    Pegawai::create([
                        'nip' => !empty($p['nip']) ? $p['nip'] : null,
                        'nama' => $p['nama'] ?? '-',
                        'pangkat' => $p['pangkat'] ?? null,
                        'kelas' => $p['kelas'] ?? null,
                        'jabatan' => $p['jabatan'] ?? null,
                        'rangkap' => $p['rangkap'] ?? null,
                        'unit' => $p['unit'] ?? null,
                        'satker' => $p['satker'] ?? null,
                        'ket' => $p['ket'] ?? null,
                        'jabatan_surat' => $p['jabatanSurat'] ?? null,
                        'rangkap_surat' => $p['rangkapSurat'] ?? null,
                        'nama_surat' => $p['namaSurat'] ?? null,
                        'is_active' => true,
                    ]);
                }
            }
        }
    }
}
