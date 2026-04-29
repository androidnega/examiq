<?php

namespace App\Services\Moderation;

use App\Enums\SubmissionStatus;
use App\Models\ExamSubmission;
use App\Models\ModerationAssignment;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;

class ModerationAssignmentService
{
    /**
     * @param  array<int, string>  $examSubmissionIds
     * @param  array<int, string>  $moderatorIds
     * @return int Number of new assignment rows created
     */
    public function assignModerators(User $hod, array $examSubmissionIds, array $moderatorIds): int
    {
        $created = 0;

        DB::transaction(function () use ($hod, $examSubmissionIds, $moderatorIds, &$created): void {
            foreach ($examSubmissionIds as $examSubmissionId) {
                $examSubmission = ExamSubmission::query()->findOrFail($examSubmissionId);
                Gate::forUser($hod)->authorize('assignModerator', $examSubmission);

                foreach ($moderatorIds as $moderatorId) {
                    $assignment = ModerationAssignment::query()->firstOrCreate(
                        [
                            'exam_submission_id' => $examSubmission->getKey(),
                            'moderator_id' => $moderatorId,
                        ],
                        [
                            'assigned_by' => $hod->getKey(),
                            'assigned_at' => now(),
                        ],
                    );

                    if ($assignment->wasRecentlyCreated) {
                        $created++;
                        if (! in_array($examSubmission->status, [SubmissionStatus::Approved, SubmissionStatus::Rejected], true)) {
                            $examSubmission->update(['status' => SubmissionStatus::UnderReview]);
                        }
                    }
                }
            }
        });

        Log::info('examiq.moderation.assignments', [
            'hod_id' => $hod->getKey(),
            'submission_ids' => $examSubmissionIds,
            'moderator_ids' => $moderatorIds,
            'new_rows' => $created,
        ]);

        return $created;
    }
}
