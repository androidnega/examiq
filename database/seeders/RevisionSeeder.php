<?php

namespace Database\Seeders;

use App\Enums\SubmissionStatus;
use App\Enums\UserRole;
use App\Models\ExamSubmission;
use App\Models\Revision;
use App\Models\User;
use Illuminate\Database\Seeder;

class RevisionSeeder extends Seeder
{
    public function run(): void
    {
        $submission = ExamSubmission::query()
            ->where('status', SubmissionStatus::UnderRevision)
            ->first();

        if ($submission === null) {
            return;
        }

        $lecturer = User::query()->where('role', UserRole::Lecturer)->firstOrFail();

        Revision::query()->create([
            'submission_id' => $submission->id,
            'lecturer_id' => $lecturer->id,
            'notes' => 'Rewrote question 3 and updated the marking scheme weights per moderator feedback.',
        ]);
    }
}
