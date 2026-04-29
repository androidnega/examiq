<?php

namespace App\Repositories;

use App\Models\User;

class UserRepository
{
    public function findByPhone(string $phone): ?User
    {
        return User::query()->where('phone', $phone)->first();
    }
}
