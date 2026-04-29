<?php

namespace Database\Seeders;

use App\Enums\ModerationStatus;
use App\Enums\SubmissionStatus;
use App\Enums\UserRole;
use App\Models\ExamSubmission;
use App\Models\Moderation;
use App\Models\User;
use Illuminate\Database\Seeder;

class ModerationSeeder extends Seeder
{
    public function run(): void
    {
        $moderator = User::query()->where('role', UserRole::Moderator)->firstOrFail();
        $submission = ExamSubmission::query()
            ->where('status', SubmissionStatus::UnderReview)
            ->firstOrFail();

        Moderation::query()->create([
            'submission_id' => $submission->id,
            'moderator_id' => $moderator->id,
            'status' => ModerationStatus::MinorChanges,
            'feedback' => 'Improve clarity of question 3 and adjust marking scheme.',
        ]);
    }
}
