<?php

namespace App\Models;

use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['phone', 'code', 'expires_at'])]
class Otp extends Model
{
    use HasUuid;

    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
        ];
    }
}
