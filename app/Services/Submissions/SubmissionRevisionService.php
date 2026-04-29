<?php

namespace App\Services\Submissions;

use App\Enums\SubmissionFileType;
use App\Enums\SubmissionStatus;
use App\Models\ExamSubmission;
use App\Models\Revision;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SubmissionRevisionService
{
    public function __construct(
        private readonly SubmissionDocumentStorage $storage,
    ) {}

    public function submitRevision(
        User $lecturer,
        ExamSubmission $examSubmission,
        string $academicYear,
        string $semester,
        int $studentsCount,
        string $revisionNotes,
        array $complianceData,
        Request $request,
    ): ExamSubmission {
        return DB::transaction(function () use ($lecturer, $examSubmission, $academicYear, $semester, $studentsCount, $revisionNotes, $complianceData, $request): ExamSubmission {
            $newVersion = $examSubmission->current_version + 1;

            $examSubmission->update([
                'academic_year' => $academicYear,
                'semester' => $semester,
                'students_count' => $studentsCount,
                'current_version' => $newVersion,
                'status' => SubmissionStatus::AwaitingHodApproval,
            ]);

            $this->persistVersionFiles($request, $examSubmission, $newVersion);

            Revision::query()->create([
                'submission_id' => $examSubmission->getKey(),
                'lecturer_id' => $lecturer->getKey(),
                'notes' => $revisionNotes,
                ...$complianceData,
            ]);

            Log::info('examiq.submission.revision', [
                'exam_submission_id' => $examSubmission->getKey(),
                'lecturer_id' => $lecturer->getKey(),
                'version' => $newVersion,
            ]);

            return $examSubmission->fresh();
        });
    }

    private function persistVersionFiles(Request $request, ExamSubmission $examSubmission, int $version): void
    {
        $this->storage->store($examSubmission, $version, SubmissionFileType::Questions, $request->file('file_questions'));
        $this->storage->store($examSubmission, $version, SubmissionFileType::MarkingScheme, $request->file('file_marking_scheme'));
        $this->storage->store($examSubmission, $version, SubmissionFileType::Outline, $request->file('file_outline'));

        if ($request->hasFile('file_supporting')) {
            $this->storage->store($examSubmission, $version, SubmissionFileType::Supporting, $request->file('file_supporting'));
        }
    }
}
