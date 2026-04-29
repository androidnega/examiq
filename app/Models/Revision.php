<?php

namespace App\Models;

use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'submission_id',
    'lecturer_id',
    'notes',
    'received_moderated_questions',
    'received_moderated_marking_scheme',
    'received_course_outline',
    'received_moderator_comment_sheet',
    'received_from_moderator_on',
    'moderator_general_comment',
    'response_action_taken',
])]
class Revision extends Model
{
    use HasUuid;

    protected function casts(): array
    {
        return [
            'received_moderated_questions' => 'boolean',
            'received_moderated_marking_scheme' => 'boolean',
            'received_course_outline' => 'boolean',
            'received_moderator_comment_sheet' => 'boolean',
            'received_from_moderator_on' => 'date',
        ];
    }

    public function submission(): BelongsTo
    {
        return $this->belongsTo(ExamSubmission::class, 'submission_id');
    }

    public function lecturer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'lecturer_id');
    }
}
