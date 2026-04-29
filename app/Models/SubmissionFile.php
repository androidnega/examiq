<?php

namespace App\Models;

use App\Enums\SubmissionFileType;
use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['submission_id', 'version', 'type', 'file_path', 'original_name'])]
class SubmissionFile extends Model
{
    use HasUuid;

    /** Disk for stored paths (not publicly linked). */
    public const STORAGE_DISK = 'private';

    protected function casts(): array
    {
        return [
            'type' => SubmissionFileType::class,
        ];
    }

    public function submission(): BelongsTo
    {
        return $this->belongsTo(ExamSubmission::class, 'submission_id');
    }
}
