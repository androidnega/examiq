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

        if ($apiKey === '') {
            throw new RuntimeException('Arkassel API key is not configured.');
        }

        $response = Http::withHeaders([
            'api-key' => $apiKey,
            'Accept' => 'application/json',
        ])->post($baseUrl.'/sms/send', [
            'sender' => $senderId,
            'message' => $message,
            'recipients' => [$phone],
        ]);

        if (! $response->successful()) {
            throw new RuntimeException('Arkassel SMS request failed with status '.$response->status().'.');
        }
    }
}
