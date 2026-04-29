<?php

namespace App\Http\Controllers;

use App\Enums\SubmissionFileType;
use App\Enums\SubmissionStatus;
use App\Http\Requests\StoreSubmissionRequest;
use App\Http\Requests\UpdateSubmissionRequest;
use App\Models\Course;
use App\Models\ExamSubmission;
use App\Support\SubmissionSessionOptions;
use App\Services\Submissions\SubmissionDocumentStorage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class SubmissionController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', ExamSubmission::class);

        $submissions = ExamSubmission::query()
            ->where('lecturer_id', $request->user()->id)
            ->with('course')
            ->latest('updated_at')
            ->paginate(12);

        return view('lecturer.submissions.index', [
            'submissions' => $submissions,
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', ExamSubmission::class);

        $departmentId = auth()->user()->department_id;
        abort_if($departmentId === null, 403);

        $courses = Course::query()
            ->where('department_id', $departmentId)
            ->orderBy('name')
            ->get();

        return view('lecturer.submissions.create', [
            'courses' => $courses,
            'submission' => null,
            'academicYearOptions' => SubmissionSessionOptions::academicYears(),
            'semesterOptions' => SubmissionSessionOptions::semesters(),
        ]);
    }

    public function store(StoreSubmissionRequest $request, SubmissionDocumentStorage $storage): JsonResponse|RedirectResponse
    {
        $submission = DB::transaction(function () use ($request, $storage): ExamSubmission {
            $submission = ExamSubmission::query()->create([
                'lecturer_id' => $request->user()->id,
                'course_id' => $request->validated('course_id'),
                'academic_year' => $request->validated('academic_year'),
                'semester' => $request->validated('semester'),
                'students_count' => (int) $request->validated('students_count'),
                'status' => SubmissionStatus::Pending,
                'current_version' => 1,
            ]);

            $this->persistVersionFiles($request, $storage, $submission, 1);

            return $submission;
        });

        return $this->respondAfterSave($request, $submission, __('Submission created.'));
    }

    public function show(Request $request, ExamSubmission $submission): View
    {
        $this->authorize('view', $submission);

        $submission->load([
            'course',
            'submissionFiles' => fn ($q) => $q->orderByDesc('version')->orderBy('type'),
        ]);

        $filesByVersion = $submission->submissionFiles->groupBy('version')->sortKeysDesc();

        return view('lecturer.submissions.show', [
            'submission' => $submission,
            'filesByVersion' => $filesByVersion,
            'canUpdate' => $request->user()->can('update', $submission),
            'canRevise' => $request->user()->can('revise', $submission),
        ]);
    }

    public function edit(ExamSubmission $submission): View
    {
        $this->authorize('update', $submission);

        $submission->load('course');

        return view('lecturer.submissions.update', [
            'submission' => $submission,
            'academicYearOptions' => SubmissionSessionOptions::academicYears(),
            'semesterOptions' => SubmissionSessionOptions::semesters(),
        ]);
    }

    public function update(
        UpdateSubmissionRequest $request,
        ExamSubmission $submission,
        SubmissionDocumentStorage $storage,
    ): JsonResponse|RedirectResponse {
        $newVersion = $submission->current_version + 1;

        DB::transaction(function () use ($request, $storage, $submission, $newVersion): void {
            $submission->update([
                'academic_year' => $request->validated('academic_year'),
                'semester' => $request->validated('semester'),
                'students_count' => (int) $request->validated('students_count'),
                'current_version' => $newVersion,
                'status' => SubmissionStatus::Pending,
            ]);

            $this->persistVersionFiles($request, $storage, $submission, $newVersion);
        });

        $submission->refresh();

        return $this->respondAfterSave($request, $submission, __('Submission updated. New version saved.'));
    }

    private function persistVersionFiles(
        Request $request,
        SubmissionDocumentStorage $storage,
        ExamSubmission $submission,
        int $version,
    ): void {
        $storage->store($submission, $version, SubmissionFileType::Questions, $request->file('file_questions'));
        $storage->store($submission, $version, SubmissionFileType::MarkingScheme, $request->file('file_marking_scheme'));
        $storage->store($submission, $version, SubmissionFileType::Outline, $request->file('file_outline'));

        if ($request->hasFile('file_supporting')) {
            $storage->store($submission, $version, SubmissionFileType::Supporting, $request->file('file_supporting'));
        }
    }

    private function respondAfterSave(Request $request, ExamSubmission $submission, string $message): JsonResponse|RedirectResponse
    {
        if ($request->expectsJson()) {
            return response()->json([
                'message' => $message,
                'redirect' => route('dashboard.submissions.show', $submission),
            ]);
        }

        return redirect()
            ->route('dashboard.submissions.show', $submission)
            ->with('status', $message);
    }
}
