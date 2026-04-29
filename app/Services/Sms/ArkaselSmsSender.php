<?php

namespace App\Services\Sms;

use App\Services\Sms\Contracts\SmsSender;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class ArkaselSmsSender implements SmsSender
{
    public function send(string $phone, string $message): void
    {
        $apiKey = (string) Cache::get('examiq.arkasel_api_key', (string) config('examiq.arkasel_api_key', ''));
        $senderId = (string) Cache::get('examiq.arkasel_sender_id', (string) config('examiq.arkasel_sender_id', 'EXAMIQ'));
        $baseUrl = rtrim((string) Cache::get('examiq.arkasel_base_url', (string) config('examiq.arkasel_base_url', 'https://sms.arkesel.com/api/v2')), '/');
        $normalizedRecipient = $this->normalizeRecipient($phone);

        if ($apiKey === '') {
            throw new RuntimeException('Arkassel API key is not configured.');
        }

        $response = Http::withHeaders([
            'api-key' => $apiKey,
            'Accept' => 'application/json',
        ])->post($baseUrl.'/sms/send', [
            'sender' => $senderId,
            'message' => $message,
            // Arkassel accepts recipients as array or comma-separated string.
            // We send internationalized Ghana number for delivery reliability.
            'recipients' => [$normalizedRecipient],
        ]);

        if (! $response->successful()) {
            throw new RuntimeException('Arkassel SMS request failed with status '.$response->status().'.');
        }

        $payload = $response->json();
        if (is_array($payload)) {
            $status = strtolower((string) ($payload['status'] ?? $payload['message'] ?? ''));
            $ok = in_array($status, ['success', 'ok', 'sent', 'queued'], true)
                || (($payload['code'] ?? null) === 'ok')
                || (($payload['success'] ?? null) === true);

            if (! $ok) {
                throw new RuntimeException('Arkassel SMS response indicates failure: '.$response->body());
            }
        }
    }

    private function normalizeRecipient(string $phone): string
    {
        $digits = preg_replace('/\D+/', '', $phone) ?? '';
        if ($digits === '') {
            return trim($phone);
        }

        if (str_starts_with($digits, '233') && strlen($digits) === 12) {
            return $digits;
        }

        if (strlen($digits) === 10 && str_starts_with($digits, '0')) {
            return '233'.substr($digits, 1);
        }

        return $digits;
    }
}
