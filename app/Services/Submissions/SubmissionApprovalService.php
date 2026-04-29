<?php

namespace App\Services\Submissions;

use App\Enums\SubmissionStatus;
use App\Models\ExamSubmission;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SubmissionApprovalService
{
    public function approveSubmission(User $hod, ExamSubmission $examSubmission): void
    {
        DB::transaction(function () use ($hod, $examSubmission): void {
            $examSubmission->update([
                'status' => SubmissionStatus::Approved,
            ]);

            Log::info('examiq.submission.approved', [
                'hod_id' => $hod->getKey(),
                'exam_submission_id' => $examSubmission->getKey(),
            ]);
        });
    }

    public function rejectSubmission(User $hod, ExamSubmission $examSubmission, ?string $notes = null): void
    {
        DB::transaction(function () use ($hod, $examSubmission, $notes): void {
            $examSubmission->update([
                'status' => SubmissionStatus::Rejected,
            ]);

            Log::info('examiq.submission.rejected', [
                'hod_id' => $hod->getKey(),
                'exam_submission_id' => $examSubmission->getKey(),
                'notes' => $notes,
            ]);
        });
    }
}
