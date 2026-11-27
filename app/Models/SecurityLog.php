<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class SecurityLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'actor_name',
        'event',
        'level',
        'ip_address',
        'user_agent',
        'description',
        'created_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    /**
     * Pastikan tabel security_logs tersedia (berguna pada hosting tanpa akses CLI migrate).
     */
    public static function ensureTableExists(): void
    {
        if (Schema::hasTable('security_logs')) {
            return;
        }

        Schema::create('security_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->string('actor_name', 120)->nullable();
            $table->string('event', 60)->index();
            $table->string('level', 20)->default('info')->index();
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent', 255)->nullable();
            $table->string('description', 500);
            $table->timestamp('created_at')->nullable()->index();
        });
    }

    /**
     * Catat peristiwa keamanan ke database dengan fallback aman ke laravel.log.
     */
    public static function record(
        string $event,
        string $description,
        string $level = 'info',
        ?Request $request = null,
        ?User $actor = null
    ): void {
        $req  = $request ?? (app()->bound('request') ? request() : null);
        $user = $actor ?? Auth::user();

        $payload = [
            'user_id'     => $user?->id,
            'actor_name'  => Str::limit((string) ($user?->name ?? 'Tamu / Sistem'), 120, ''),
            'event'       => Str::limit($event, 60, ''),
            'level'       => in_array($level, ['info', 'warning', 'danger'], true) ? $level : 'info',
            'ip_address'  => $req ? Str::limit((string) $req->ip(), 45, '') : null,
            'user_agent'  => $req ? Str::limit((string) $req->userAgent(), 255, '') : null,
            'description' => Str::limit($description, 500, '...'),
            'created_at'  => now(),
        ];

        try {
            self::query()->create($payload);
        } catch (\Throwable $firstError) {
            try {
                self::ensureTableExists();
                self::query()->create($payload);
            } catch (\Throwable $fallbackError) {
                Log::warning('SecurityLog fallback: ' . $fallbackError->getMessage(), $payload);
            }
        }
    }
}
