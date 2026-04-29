<?php

namespace App\Http\Controllers;

use App\Http\Requests\SubmitRevisionRequest;
use App\Models\ExamSubmission;
use App\Support\SubmissionSessionOptions;
use App\Services\Submissions\SubmissionRevisionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SubmissionRevisionController extends Controller
{
    public function edit(ExamSubmission $submission): View
    {
        $this->authorize('revise', $submission);

        $submission->load([
            'course',
            'moderations.moderator',
            'submissionFiles' => fn ($q) => $q->orderByDesc('version')->orderBy('type'),
        ]);

        $filesByVersion = $submission->submissionFiles->groupBy('version')->sortKeysDesc();

        return view('lecturer.submissions.edit', [
            'examSubmission' => $submission,
            'filesByVersion' => $filesByVersion,
            'moderationReviews' => $submission->moderations,
            'academicYearOptions' => SubmissionSessionOptions::academicYears(),
            'semesterOptions' => SubmissionSessionOptions::semesters(),
        ]);
    }

    public function update(
        SubmitRevisionRequest $request,
        ExamSubmission $submission,
        SubmissionRevisionService $submissionRevisionService,
    ): JsonResponse|RedirectResponse {
        $examSubmission = $submissionRevisionService->submitRevision(
            $request->user(),
            $submission,
            $request->validated('academic_year'),
            $request->validated('semester'),
            (int) $request->validated('students_count'),
            $request->validated('revision_notes'),
            [
                'received_moderated_questions' => $request->boolean('received_moderated_questions'),
                'received_moderated_marking_scheme' => $request->boolean('received_moderated_marking_scheme'),
                'received_course_outline' => $request->boolean('received_course_outline'),
                'received_moderator_comment_sheet' => $request->boolean('received_moderator_comment_sheet'),
                'received_from_moderator_on' => $request->validated('received_from_moderator_on'),
                'moderator_general_comment' => $request->validated('moderator_general_comment'),
                'response_action_taken' => $request->validated('response_action_taken'),
            ],
            $request,
        );

        if ($request->expectsJson()) {
            return response()->json([
                'message' => __('Revision submitted. Your HOD will review the final version.'),
                'redirect' => route('dashboard.submissions.show', $examSubmission),
            ]);
        }

        return redirect()
            ->route('dashboard.submissions.show', $examSubmission)
            ->with('status', __('Revision submitted. Your HOD will review the final version.'));
    }
}
