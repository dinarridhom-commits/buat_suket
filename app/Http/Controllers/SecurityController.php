<?php

namespace App\Http\Controllers;

use App\Models\SecurityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SecurityController extends Controller
{
    /**
     * Daftar tabel inti yang disertakan pada ekspor backup SQL.
     */
    private const BACKUP_TABLES = [
        'settings',
        'pegawais',
        'surat_undangans',
        'surat_undangan_penerimas',
        'suket_absens',
        'security_logs',
    ];

    /**
     * Halaman monitoring keamanan, status konfigurasi produksi, dan backup database.
     */
    public function index(Request $request)
    {
        SecurityLog::ensureTableExists();

        $checks = [
            [
                'label'  => 'Protokol HTTPS & Trust Proxy',
                'ok'     => $request->isSecure() || str_starts_with((string) config('app.url'), 'https://'),
                'detail' => $request->isSecure()
                    ? 'Koneksi saat ini terenkripsi HTTPS dan TrustProxies aktif.'
                    : 'Gunakan alamat https:// pada browser; APP_URL tercatat ' . config('app.url'),
            ],
            [
                'label'  => 'Security HTTP Headers',
                'ok'     => true,
                'detail' => 'Middleware SecurityHeaders aktif (X-Frame-Options, nosniff, Referrer-Policy, CSP, Permissions-Policy).',
            ],
            [
                'label'  => 'Cookie Sesi HTTPS & HttpOnly',
                'ok'     => (bool) config('session.secure', false) && (bool) config('session.http_only', true),
                'detail' => (bool) config('session.secure', false)
                    ? 'SESSION_SECURE_COOKIE aktif & http_only aktif.'
                    : 'SESSION_SECURE_COOKIE belum bernilai true di .env (wajib aktifkan di hosting HTTPS).',
            ],
            [
                'label'  => 'Google reCAPTCHA v3 (Anti-Bot Login)',
                'ok'     => trim((string) config('services.recaptcha.site_key', '')) !== ''
                    && trim((string) config('services.recaptcha.secret_key', '')) !== '',
                'detail' => (trim((string) config('services.recaptcha.site_key', '')) !== ''
                    && trim((string) config('services.recaptcha.secret_key', '')) !== '')
                    ? 'Site Key & Secret Key terpasang — verifikasi aktif di Login & Reset Sandi.'
                    : 'RECAPTCHA_SITE_KEY / RECAPTCHA_SECRET_KEY di .env masih kosong (validasi dilewati sementara).',
            ],
            [
                'label'  => 'Rate Limiter & Kata Sandi Kuat',
                'ok'     => true,
                'detail' => 'Batas 5 percobaan/menit aktif di Login, Lupa Sandi, & Reset Sandi; sandi minimal 8 karakter (huruf + angka).',
            ],
            [
                'label'  => 'SMTP Email Lupa Kata Sandi',
                'ok'     => config('mail.default') === 'smtp'
                    && trim((string) config('mail.mailers.smtp.username', '')) !== ''
                    && trim((string) config('mail.mailers.smtp.password', '')) !== '',
                'detail' => config('mail.default') === 'smtp'
                    ? 'Mailer SMTP dikonfigurasi (' . (string) config('mail.mailers.smtp.host') . ').'
                    : 'MAIL_MAILER masih "' . (string) config('mail.default') . '" — perlu SMTP (mis. Gmail App Password) agar email reset terkirim.',
            ],
            [
                'label'  => 'Mode Debug Produksi (APP_DEBUG)',
                'ok'     => !config('app.debug'),
                'detail' => config('app.debug')
                    ? 'Peringatan: APP_DEBUG masih true! Pastikan APP_DEBUG=false di server publik.'
                    : 'APP_DEBUG=false (error teknis disembunyikan dari publik & ditulis ke laravel.log).',
            ],
        ];

        $query = SecurityLog::query()->orderByDesc('id');

        if ($request->filled('level') && in_array($request->level, ['info', 'warning', 'danger'], true)) {
            $query->where('level', $request->level);
        }

        if ($request->filled('q')) {
            $q = trim((string) $request->q);
            $query->where(function ($sub) use ($q) {
                $sub->where('event', 'like', "%{$q}%")
                    ->orWhere('actor_name', 'like', "%{$q}%")
                    ->orWhere('ip_address', 'like', "%{$q}%")
                    ->orWhere('description', 'like', "%{$q}%");
            });
        }

        $logs = $query->paginate(15)->withQueryString();

        $summary = [
            'total'   => SecurityLog::count(),
            'warning' => SecurityLog::where('level', 'warning')->count(),
            'danger'  => SecurityLog::where('level', 'danger')->count(),
        ];

        return view('security.index', compact('checks', 'logs', 'summary'));
    }

    /**
     * Unduh cadangan data inti database dalam format SQL (.sql).
     */
    public function downloadBackup(Request $request)
    {
        SecurityLog::ensureTableExists();
        SecurityLog::record('backup_downloaded', 'Mengunduh cadangan database SQL manual dari menu Keamanan.', 'info', $request);

        $filename = 'backup-bwsk1-' . now()->format('Ymd-His') . '.sql';

        return response()->streamDownload(function () {
            $pdo    = DB::getPdo();
            $driver = DB::getDriverName();

            echo "-- Backup Data Portal Tata Usaha BWS Kalimantan I\n";
            echo "-- Waktu Ekspor : " . now()->toDateTimeString() . " (" . config('app.timezone') . ")\n";
            echo "-- Driver DB    : {$driver}\n\n";

            if ($driver === 'mysql') {
                echo "SET NAMES utf8mb4;\n";
                echo "SET FOREIGN_KEY_CHECKS = 0;\n\n";
            }

            foreach (self::BACKUP_TABLES as $table) {
                if (!Schema::hasTable($table)) {
                    continue;
                }

                $columns = Schema::getColumnListing($table);
                if (empty($columns)) {
                    continue;
                }

                $rows = DB::table($table)->get();
                echo "-- --------------------------------------------------------\n";
                echo "-- Tabel `{$table}` ({$rows->count()} baris)\n";
                echo "-- --------------------------------------------------------\n";

                if ($rows->isEmpty()) {
                    echo "\n";
                    continue;
                }

                $colSql = implode(', ', array_map(fn ($c) => "`{$c}`", $columns));

                foreach ($rows as $row) {
                    $vals = [];
                    foreach ($columns as $col) {
                        $val = $row->$col ?? null;
                        if ($val === null) {
                            $vals[] = 'NULL';
                        } elseif (is_bool($val)) {
                            $vals[] = $val ? '1' : '0';
                        } elseif (is_int($val) || is_float($val)) {
                            $vals[] = (string) $val;
                        } else {
                            $vals[] = $pdo->quote((string) $val);
                        }
                    }
                    echo "INSERT INTO `{$table}` ({$colSql}) VALUES (" . implode(', ', $vals) . ");\n";
                }

                echo "\n";
            }

            if ($driver === 'mysql') {
                echo "SET FOREIGN_KEY_CHECKS = 1;\n";
            }
        }, $filename, [
            'Content-Type' => 'application/sql; charset=UTF-8',
        ]);
    }

    /**
     * Pangkas log aktivitas keamanan lama agar tabel tetap ringan.
     */
    public function prune(Request $request)
    {
        SecurityLog::ensureTableExists();

        $scope = (string) $request->input('scope', '30days');
        if ($scope === 'all') {
            $deleted = SecurityLog::query()->delete();
            SecurityLog::record('security_logs_pruned', "Membersihkan seluruh {$deleted} catatan log keamanan.", 'warning', $request);
        } else {
            $deleted = SecurityLog::query()->where('created_at', '<', now()->subDays(30))->delete();
            SecurityLog::record('security_logs_pruned', "Memangkas {$deleted} catatan log keamanan berusia lebih dari 30 hari.", 'info', $request);
        }

        return redirect()->route('security.index')
            ->with('success', "Berhasil membersihkan {$deleted} catatan log keamanan.");
    }
}
