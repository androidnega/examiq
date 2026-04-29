<?php

namespace App\Services\Moderation;

use App\Enums\ModerationStatus;
use App\Enums\SubmissionStatus;
use App\Models\ExamSubmission;
use App\Models\Moderation;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ModerationReviewService
{
    public function submitReview(
        User $moderator,
        ExamSubmission $examSubmission,
        ModerationStatus $status,
        ?string $feedback,
        array $moderationFormData,
    ): Moderation {
        return DB::transaction(function () use ($moderator, $examSubmission, $status, $feedback, $moderationFormData): Moderation {
            $moderation = Moderation::query()->updateOrCreate(
                [
                    'submission_id' => $examSubmission->getKey(),
                    'moderator_id' => $moderator->getKey(),
                ],
                [
                    'status' => $status,
                    'feedback' => $feedback,
                    ...$moderationFormData,
                ],
            );

            $examSubmission->update([
                'status' => match ($status) {
                    ModerationStatus::Accepted => SubmissionStatus::AwaitingHodApproval,
                    ModerationStatus::MinorChanges, ModerationStatus::MajorChanges => SubmissionStatus::UnderRevision,
                    ModerationStatus::Rejected => SubmissionStatus::Rejected,
                },
            ]);

            Log::info('examiq.moderation.review', [
                'moderator_id' => $moderator->getKey(),
                'exam_submission_id' => $examSubmission->getKey(),
                'moderation_status' => $status->value,
            ]);

            return $moderation;
        });
    }
}
