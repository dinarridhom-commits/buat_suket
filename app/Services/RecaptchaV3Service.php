<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RecaptchaV3Service
{
    public static function siteKey(): string
    {
        return trim((string) config('services.recaptcha.site_key', ''));
    }

    public static function secretKey(): string
    {
        return trim((string) config('services.recaptcha.secret_key', ''));
    }

    public static function minScore(): float
    {
        return max(0.0, min(1.0, (float) config('services.recaptcha.min_score', 0.5)));
    }

    public static function isEnabledFlag(): bool
    {
        return (bool) config('services.recaptcha.enabled', true);
    }

    public static function isActive(): bool
    {
        return self::isEnabledFlag() && self::siteKey() !== '' && self::secretKey() !== '';
    }

    /**
     * Verifikasi token Google reCAPTCHA v3.
     * Jika key belum dikonfigurasi, validasi dilewati agar portal tidak terkunci.
     *
     * @return array{ok: bool, message: ?string, score: ?float}
     */
    public static function verify(?string $token, string $expectedAction, string $remoteIp = ''): array
    {
        if (!self::isActive()) {
            return ['ok' => true, 'message' => null, 'score' => null];
        }

        $token = trim((string) $token);
        if ($token === '') {
            return [
                'ok'      => false,
                'message' => 'Verifikasi keamanan reCAPTCHA v3 belum selesai. Silakan muat ulang halaman dan coba lagi.',
                'score'   => null,
            ];
        }

        try {
            $payload = [
                'secret'   => self::secretKey(),
                'response' => $token,
            ];
            if ($remoteIp !== '') {
                $payload['remoteip'] = $remoteIp;
            }

            $response = Http::asForm()
                ->timeout(8)
                ->post('https://www.google.com/recaptcha/api/siteverify', $payload);

            if (!$response->successful()) {
                Log::warning('reCAPTCHA v3 HTTP error', ['status' => $response->status()]);
                return [
                    'ok'      => false,
                    'message' => 'Gagal menghubungi layanan verifikasi reCAPTCHA v3. Silakan coba lagi.',
                    'score'   => null,
                ];
            }

            $data = $response->json();
            if (!is_array($data) || empty($data['success'])) {
                Log::info('reCAPTCHA v3 rejected token', ['errors' => $data['error-codes'] ?? []]);
                return [
                    'ok'      => false,
                    'message' => 'Token reCAPTCHA v3 tidak valid atau sudah kedaluwarsa. Silakan ulangi.',
                    'score'   => null,
                ];
            }

            if (isset($data['action']) && $data['action'] !== $expectedAction) {
                Log::warning('reCAPTCHA v3 action mismatch', [
                    'expected' => $expectedAction,
                    'actual'   => $data['action'],
                ]);
                return [
                    'ok'      => false,
                    'message' => 'Aksi verifikasi reCAPTCHA v3 tidak sesuai. Silakan muat ulang halaman.',
                    'score'   => isset($data['score']) ? (float) $data['score'] : null,
                ];
            }

            $score = isset($data['score']) ? (float) $data['score'] : 0.0;
            if ($score < self::minScore()) {
                Log::info('reCAPTCHA v3 score below threshold', ['score' => $score, 'action' => $expectedAction]);
                return [
                    'ok'      => false,
                    'message' => 'Permintaan ditolak oleh sistem keamanan reCAPTCHA v3 (skor tidak mencukupi).',
                    'score'   => $score,
                ];
            }

            return ['ok' => true, 'message' => null, 'score' => $score];
        } catch (\Throwable $e) {
            Log::warning('reCAPTCHA v3 exception: ' . $e->getMessage());
            return [
                'ok'      => false,
                'message' => 'Terjadi gangguan koneksi saat memverifikasi reCAPTCHA v3. Silakan coba lagi.',
                'score'   => null,
            ];
        }
    }
}
