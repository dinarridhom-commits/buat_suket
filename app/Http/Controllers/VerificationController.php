<?php

namespace App\Http\Controllers;

use App\Models\SuratUndangan;
use App\Models\SuketAbsen;
use App\Models\Setting;
use Illuminate\Http\Request;

class VerificationController extends Controller
{
    /**
     * Buat token HMAC pendek anti-pemalsuan untuk QR verifikasi surat
     */
    public static function makeToken(string $type, int $id): string
    {
        $key = (string) config('app.key');
        return substr(hash_hmac('sha256', $type . ':' . $id, $key), 0, 12);
    }

    /**
     * Hasilkan URL verifikasi publik lengkap untuk surat
     */
    public static function makeUrl(string $type, int $id): string
    {
        return route('verifikasi.surat', [
            'type'  => $type,
            'id'    => $id,
            'token' => self::makeToken($type, $id),
        ]);
    }

    /**
     * Halaman publik read-only untuk verifikasi keaslian surat via scan QR
     */
    public function show(string $type, int $id, string $token)
    {
        $expected = self::makeToken($type, $id);
        if (!hash_equals($expected, $token)) {
            abort(404);
        }

        $kop = [
            'kementerian' => Setting::get('instansi_kementerian', 'KEMENTERIAN PEKERJAAN UMUM'),
            'ditjen'      => Setting::get('instansi_ditjen', 'DIREKTORAT JENDERAL SUMBER DAYA AIR'),
            'balai'       => Setting::get('instansi_balai', 'BALAI WILAYAH SUNGAI KALIMANTAN I PONTIANAK'),
        ];

        if ($type === 'undangan') {
            $dokumen = SuratUndangan::with('penerimas')->findOrFail($id);
            return view('verifikasi.show', [
                'type'    => 'undangan',
                'dokumen' => $dokumen,
                'kop'     => $kop,
                'token'   => $token,
            ]);
        }

        if ($type === 'suket') {
            $dokumen = SuketAbsen::findOrFail($id);
            return view('verifikasi.show', [
                'type'    => 'suket',
                'dokumen' => $dokumen,
                'kop'     => $kop,
                'token'   => $token,
            ]);
        }

        abort(404);
    }
}
