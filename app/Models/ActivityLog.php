<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class ActivityLog extends Model
{
    protected $guarded = [];

    /**
     * Catat aktivitas pengguna ke log audit
     */
    public static function record(string $action, string $module, string $description): void
    {
        try {
            $user = Auth::user();
            static::create([
                'user_id'     => $user?->id,
                'user_name'   => $user?->name ?? 'Sistem',
                'action'      => $action,
                'module'      => $module,
                'description' => $description,
                'ip_address'  => request()?->ip(),
            ]);
        } catch (\Throwable $e) {
            // Jangan gagalkan operasi utama jika logging bermasalah
        }
    }
}
