<?php

namespace App\Services\Otp;

use App\Models\Otp;
use App\Models\User;
use App\Repositories\OtpRepository;
use App\Repositories\UserRepository;
use App\Services\Audit\ActivityLogger;
use App\Services\Sms\Contracts\SmsSender;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class OtpService
{
    public function __construct(
        protected UserRepository $users,
        protected OtpRepository $otps,
        protected SmsSender $sms,
    ) {}

    public function issueForPhone(string $phone): void
    {
        $normalized = $this->normalizePhone($phone);

        $user = $this->users->findByPhone($normalized);
        if (! $user) {
            throw ValidationException::withMessages([
                'phone' => [__('No account is registered for this phone number.')],
            ]);
        }

        if ($user->is_blocked) {
            ActivityLogger::log($user, 'security.login_blocked', ['stage' => 'otp_send']);

            throw ValidationException::withMessages([
                'phone' => [__('Account blocked')],
            ]);
        }

        $plain = (string) random_int(100_000, 999_999);

        $this->otps->deleteExpiredForPhone($normalized);
        Otp::query()->where('phone', $normalized)->delete();

        $this->otps->create([
            'phone' => $normalized,
            'code' => Hash::make($plain),
            'expires_at' => now()->addMinutes((int) config('examiq.otp_ttl_minutes', 10)),
        ]);

        $message = __('Your :app verification code is :code.', [
            'app' => config('app.name'),
            'code' => $plain,
        ]);

        $this->sms->send($normalized, $message);

        ActivityLogger::log($user, 'auth.otp_sent', ['phone' => $normalized]);
    }

    public function verifyAndLogin(string $phone, string $code): User
    {
        $normalized = $this->normalizePhone($phone);

        $user = $this->users->findByPhone($normalized);
        if (! $user) {
            throw ValidationException::withMessages([
                'code' => [__('Invalid or expired code.')],
            ]);
        }

        if ($user->is_blocked) {
            ActivityLogger::log($user, 'security.login_blocked', ['stage' => 'otp_verify']);

            throw ValidationException::withMessages([
                'phone' => [__('Account blocked')],
            ]);
        }

        $otp = $this->otps->latestForPhone($normalized);

        if (! $otp || $otp->expires_at->isPast() || ! Hash::check(trim($code), $otp->code)) {
            ActivityLogger::log($user, 'auth.otp_failed', ['phone' => $normalized]);

            throw ValidationException::withMessages([
                'code' => [__('Invalid or expired code.')],
            ]);
        }

        $otp->delete();

        auth()->login($user);
        request()->session()->regenerate();

        ActivityLogger::log($user, 'auth.login', ['method' => 'otp']);

        return $user;
    }

    public function normalizePhone(string $phone): string
    {
        $digits = preg_replace('/\D+/', '', $phone) ?? '';

        if ($digits === '') {
            return trim($phone);
        }

        // Ghana international (+233 XX XXX XXXX) → local 0XXXXXXXXX stored in DB
        if (str_starts_with($digits, '233') && strlen($digits) === 12) {
            $digits = '0'.substr($digits, 3);
        }

        return $digits;
    }
}
