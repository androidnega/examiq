<?php

namespace App\Http\Controllers;

use App\Enums\ModerationStatus;
use App\Enums\SubmissionStatus;
use App\Http\Requests\SubmitModerationReviewRequest;
use App\Models\ExamSubmission;
use App\Models\Moderation;
use App\Models\SubmissionFile;
use App\Services\Moderation\ModerationReviewService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ModerationReviewController extends Controller
{
    public function index(Request $request): View
    {
        $moderatorId = $request->user()->id;

        $examSubmissions = ExamSubmission::query()
            ->whereNotIn('status', [SubmissionStatus::Approved, SubmissionStatus::AwaitingHodApproval])
            ->whereHas('moderationAssignments', fn ($q) => $q->where('moderator_id', $moderatorId))
            ->with([
                'course',
                'moderations' => fn ($q) => $q->where('moderator_id', $moderatorId),
            ])
            ->orderByDesc('updated_at')
            ->paginate(12);

        return view('moderator.reviews.index', [
            'examSubmissions' => $examSubmissions,
        ]);
    }

    public function show(Request $request, ExamSubmission $examSubmission): View
    {
        $this->authorize('view', $examSubmission);

        $examSubmission->load([
            'course',
            'lecturer',
            'submissionFiles' => fn ($q) => $q->orderByDesc('version')->orderBy('type'),
        ]);

        $moderationReview = Moderation::query()
            ->where('submission_id', $examSubmission->getKey())
            ->where('moderator_id', $request->user()->id)
            ->first();

        return view('moderator.reviews.show', [
            'examSubmission' => $examSubmission,
            'submissionFiles' => $examSubmission->submissionFiles,
            'moderationReview' => $moderationReview,
        ]);
    }

    public function store(
        SubmitModerationReviewRequest $request,
        ExamSubmission $examSubmission,
        ModerationReviewService $moderationReviewService,
    ): RedirectResponse {
        $moderationReviewService->submitReview(
            $request->user(),
            $examSubmission,
            ModerationStatus::from($request->validated('status')),
            $request->validated('feedback'),
            $request->safe()->only([
                'rubric_1_grade',
                'rubric_2_grade',
                'rubric_3_grade',
                'rubric_4_grade',
                'rubric_5_grade',
                'rubric_6_grade',
                'rubric_7_grade',
                'rubric_8_grade',
                'rubric_9_grade',
                'rubric_10_grade',
                'rubric_11_grade',
                'question_count_section_a',
                'question_count_section_b',
                'question_count_section_c',
                'paper_duration',
                'recommend_accept_questions',
                'recommend_reject_questions',
                'recommend_reset_questions',
                'question_paper_comments',
                'marking_scheme_comments',
                'question_paper_assessment',
                'question_paper_assessments',
                'marking_scheme_assessment',
                'marking_scheme_assessments',
                'overall_rating',
                'improvement_comments',
                'moderated_on',
                'moderator_signature_name',
            ]),
        );

        return redirect()
            ->route('dashboard.reviews.show', $examSubmission)
            ->with('status', __('Review saved.'));
    }

    public function streamSubmissionFile(ExamSubmission $examSubmission, SubmissionFile $submissionFile): StreamedResponse
    {
        $this->authorize('view', $examSubmission);

        abort_unless($submissionFile->submission_id === $examSubmission->getKey(), 404);

        $disk = SubmissionFile::STORAGE_DISK;
        abort_unless(Storage::disk($disk)->exists($submissionFile->file_path), 404);

        return Storage::disk($disk)->response(
            $submissionFile->file_path,
            $submissionFile->original_name ?? 'document.pdf',
            [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="'.str_replace('"', '', $submissionFile->original_name ?? 'document.pdf').'"',
            ]
        );
    }
}
