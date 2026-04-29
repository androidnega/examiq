<?php

namespace App\Models;

use App\Enums\SubmissionStatus;
use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'lecturer_id',
    'course_id',
    'academic_year',
    'semester',
    'students_count',
    'status',
    'current_version',
])]
class ExamSubmission extends Model
{
    use HasUuid;

    protected function casts(): array
    {
        return [
            'status' => SubmissionStatus::class,
        ];
    }

    public function lecturer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'lecturer_id');
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function submissionFiles(): HasMany
    {
        return $this->hasMany(SubmissionFile::class, 'submission_id');
    }

    public function moderations(): HasMany
    {
        return $this->hasMany(Moderation::class, 'submission_id');
    }

    public function moderationAssignments(): HasMany
    {
        return $this->hasMany(ModerationAssignment::class, 'exam_submission_id');
    }

    public function revisions(): HasMany
    {
        return $this->hasMany(Revision::class, 'submission_id');
    }
}
