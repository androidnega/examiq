<?php

namespace Database\Seeders;

use App\Enums\SubmissionFileType;
use App\Enums\SubmissionStatus;
use App\Enums\UserRole;
use App\Models\Course;
use App\Models\ExamSubmission;
use App\Models\ModerationAssignment;
use App\Models\SubmissionFile;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ExamSubmissionSeeder extends Seeder
{
    public function run(): void
    {
        $lecturer = User::query()->where('role', UserRole::Lecturer)->firstOrFail();
        $hod = User::query()->where('phone', '0241000001')->firstOrFail();
        $moderator = User::query()->where('phone', '0243000001')->firstOrFail();

        $courses = Course::query()->orderBy('code')->get();

        if ($courses->isEmpty()) {
            return;
        }

        $statusCycle = [
            SubmissionStatus::Pending,
            SubmissionStatus::UnderReview,
            SubmissionStatus::UnderRevision,
            SubmissionStatus::Approved,
            SubmissionStatus::AwaitingHodApproval,
        ];

        $pdfStub = "%PDF-1.4\n1 0 obj<<>>endobj\ntrailer<<>>\n%%EOF\n";

        foreach (range(0, 4) as $i) {
            $course = $courses[$i % $courses->count()];
            $status = $statusCycle[$i];

            $version = $status === SubmissionStatus::UnderRevision ? 2 : 1;

            $submission = ExamSubmission::query()->create([
                'lecturer_id' => $lecturer->id,
                'course_id' => $course->id,
                'academic_year' => '2025/2026',
                'semester' => 'first',
                'students_count' => rand(40, 200),
                'status' => $status,
                'current_version' => $version,
            ]);

            $this->seedSubmissionFiles($submission, 1, $pdfStub);

            if ($version > 1) {
                $this->seedSubmissionFiles($submission, 2, $pdfStub);
            }

            if ($status === SubmissionStatus::UnderReview) {
                ModerationAssignment::query()->create([
                    'exam_submission_id' => $submission->id,
                    'moderator_id' => $moderator->id,
                    'assigned_by' => $hod->id,
                    'assigned_at' => now(),
                ]);
            }
        }
    }

    private function seedSubmissionFiles(ExamSubmission $submission, int $version, string $pdfStub): void
    {
        $seedOne = function (SubmissionFileType $type, string $originalName) use ($submission, $version, $pdfStub): void {
            $unique = Str::uuid()->toString().'.pdf';
            $directory = sprintf('submissions/%s/v%d', $submission->getKey(), $version);
            $path = $directory.'/'.$unique;
            Storage::disk(SubmissionFile::STORAGE_DISK)->put($path, $pdfStub);

            SubmissionFile::query()->create([
                'submission_id' => $submission->getKey(),
                'version' => $version,
                'type' => $type,
                'file_path' => $path,
                'original_name' => $originalName,
            ]);
        };

        $seedOne(SubmissionFileType::Questions, 'exam-questions.pdf');
        $seedOne(SubmissionFileType::MarkingScheme, 'marking-scheme.pdf');
        $seedOne(SubmissionFileType::Outline, 'course-outline.pdf');
    }
}
