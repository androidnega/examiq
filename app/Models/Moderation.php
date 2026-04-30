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
    'question_count_section_a',
    'question_count_section_b',
    'question_count_section_c',
    'paper_duration',
    'recommend_accept_questions',
    'recommend_reject_questions',
    'recommend_reset_questions',
    'question_paper_comments',
    'marking_scheme_comments',
    'question_paper_assessment',
    'question_paper_assessments',
    'marking_scheme_assessment',
    'marking_scheme_assessments',
    'overall_rating',
    'improvement_comments',
    'moderated_on',
    'moderator_signature_name',
])]
class Moderation extends Model
{
    use HasUuid;

    protected function casts(): array
    {
        return [
            'status' => ModerationStatus::class,
            'moderated_on' => 'date',
            'question_paper_assessments' => 'array',
            'marking_scheme_assessments' => 'array',
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
