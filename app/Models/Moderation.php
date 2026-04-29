<?php

namespace App\Models;

use App\Enums\ModerationStatus;
use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'submission_id',
    'moderator_id',
    'status',
    'feedback',
    'rubric_1_grade',
    'rubric_2_grade',
    'rubric_3_grade',
    'rubric_4_grade',
    'rubric_5_grade',
    'rubric_6_grade',
    'rubric_7_grade',
    'rubric_8_grade',
    'rubric_9_grade',
    'rubric_10_grade',
    'rubric_11_grade',
    'recommend_accept_questions',
    'recommend_reject_questions',
    'recommend_reset_questions',
    'question_paper_comments',
    'marking_scheme_comments',
    'question_paper_assessment',
    'marking_scheme_assessment',
    'overall_rating',
    'improvement_comments',
    'moderated_on',
])]
class Moderation extends Model
{
    use HasUuid;

    protected function casts(): array
    {
        return [
            'status' => ModerationStatus::class,
            'moderated_on' => 'date',
        ];
    }

    public function submission(): BelongsTo
    {
        return $this->belongsTo(ExamSubmission::class, 'submission_id');
    }

    public function moderator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'moderator_id');
    }
}
