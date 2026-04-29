<?php

namespace App\Models\Concerns;

use Illuminate\Support\Str;

trait HasUuid
{
    public $incrementing = false;

    protected $keyType = 'string';

    protected static function bootHasUuid(): void
    {
        static::creating(function ($model): void {
            $key = $model->getKeyName();
            if (empty($model->{$key})) {
                $model->{$key} = (string) Str::uuid();
            }
        });
    }
}
