<?php

namespace App\Repositories;

use App\Models\Otp;
use Illuminate\Support\Carbon;

class OtpRepository
{
    public function create(array $attributes): Otp
    {
        return Otp::query()->create($attributes);
    }

    public function latestForPhone(string $phone): ?Otp
    {
        return Otp::query()
            ->where('phone', $phone)
            ->orderByDesc('created_at')
            ->first();
    }

    public function deleteExpiredForPhone(string $phone): void
    {
        Otp::query()
            ->where('phone', $phone)
            ->where('expires_at', '<', Carbon::now())
            ->delete();
    }
}
