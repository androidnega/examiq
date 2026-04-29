<?php

namespace App\Models;

use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'exam_submission_id',
    'moderator_id',
    'assigned_by',
    'assigned_at',
])]
class ModerationAssignment extends Model
{
    use HasUuid;

    protected function casts(): array
    {
        return [
            'assigned_at' => 'datetime',
        ];
    }

    public function examSubmission(): BelongsTo
    {
        return $this->belongsTo(ExamSubmission::class, 'exam_submission_id');
    }

    public function moderator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'moderator_id');
    }

    public function assignedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }
}
