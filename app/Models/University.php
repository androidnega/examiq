<?php

namespace App\Models;

use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name'])]
class University extends Model
{
    use HasUuid;

    public function faculties(): HasMany
    {
        return $this->hasMany(Faculty::class);
    }
}
