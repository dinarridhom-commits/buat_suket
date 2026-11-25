<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class BackupController extends Controller
{
    public function download()
    {
        $connection = config('database.default');
        $timestamp  = now()->format('Ymd-His');

        ActivityLog::record('backup', 'Sistem & Backup', 'Mengunduh backup database sistem.');

        // Jika menggunakan SQLite, langsung unduh file database.sqlite
        if ($connection === 'sqlite') {
            $dbPath = config('database.connections.sqlite.database');
            if ($dbPath && file_exists($dbPath)) {
                return response()->download(
                    $dbPath,
                    "backup-bws-kalimantan1-{$timestamp}.sqlite",
                    ['Content-Type' => 'application/x-sqlite3']
                );
            }
        }

        // Fallback universal (MySQL / lainnya di shared hosting): ekspor semua tabel ke JSON terstruktur
        $tables = [
            'users',
            'pegawais',
            'surat_undangans',
            'surat_undangan_penerimas',
            'suket_absens',
            'qr_code_items',
            'settings',
            'activity_logs',
        ];

        $payload = [
            'app'        => config('app.name'),
            'exported_at'=> now()->toIso8601String(),
            'connection' => $connection,
            'tables'     => [],
        ];

        foreach ($tables as $table) {
            if (Schema::hasTable($table)) {
                $payload['tables'][$table] = DB::table($table)->get();
            }
        }

        $json = json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

        return response($json, 200, [
            'Content-Type'        => 'application/json',
            'Content-Disposition' => "attachment; filename=\"backup-bws-kalimantan1-{$timestamp}.json\"",
        ]);
    }
}
