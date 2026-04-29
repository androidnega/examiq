<?php

namespace App\Services\Sms;

use App\Services\Sms\Contracts\SmsSender;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Throwable;

class RuntimeSmsSender implements SmsSender
{
    public function __construct(
        private readonly LogSmsSender $logSmsSender,
        private readonly ArkaselSmsSender $arkaselSmsSender,
    ) {}

    public function send(string $phone, string $message): void
    {
        $provider = (string) Cache::get('examiq.sms_provider', (string) config('examiq.sms_provider', 'log'));

        if ($provider !== 'arkasel') {
            $this->logSmsSender->send($phone, $message);

            return;
        }

        try {
            $this->arkaselSmsSender->send($phone, $message);
        } catch (Throwable $e) {
            $fallbackEnabled = (bool) Cache::get(
                'examiq.otp_log_fallback_enabled',
                (bool) config('examiq.otp_log_fallback_enabled', true)
            );

            if (! $fallbackEnabled) {
                throw $e;
            }

            Log::warning('OTP SMS fallback to log sender after Arkassel failure.', [
                'phone' => $phone,
                'error' => $e->getMessage(),
            ]);
            $this->logSmsSender->send($phone, $message);
        }
    }
}
