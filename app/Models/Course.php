<?php

namespace App\Models;

use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['department_id', 'name', 'code', 'level', 'program', 'semester'])]
class Course extends Model
{
    use HasUuid;

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function examSubmissions(): HasMany
    {
        return $this->hasMany(ExamSubmission::class);
    }
}
