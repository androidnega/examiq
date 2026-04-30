<?php

namespace App\Http\Controllers;

use App\Enums\SubmissionStatus;
use App\Http\Requests\RejectSubmissionRequest;
use App\Models\ExamSubmission;
use App\Models\SubmissionFile;
use App\Services\Submissions\SubmissionApprovalService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SubmissionApprovalController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', ExamSubmission::class);

        $departmentId = $request->user()->department_id;
        abort_if($departmentId === null, 403);

        $examSubmissions = ExamSubmission::query()
            ->with(['course', 'lecturer'])
            ->where('status', SubmissionStatus::AwaitingHodApproval)
            ->whereHas('course', fn ($q) => $q->where('department_id', $departmentId))
            ->orderByDesc('updated_at')
            ->paginate(12);

        return view('hod.approvals.index', [
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
            'moderationAssignments.moderator',
            'moderationAssignments.assignedBy',
            'moderations.moderator',
            'revisions.lecturer',
        ]);

        $filesByVersion = $examSubmission->submissionFiles->groupBy('version')->sortKeysDesc();

        return view('hod.submissions.show', [
            'examSubmission' => $examSubmission,
            'moderationAssignments' => $examSubmission->moderationAssignments,
            'moderationReviews' => $examSubmission->moderations,
            'submissionRevisions' => $examSubmission->revisions,
            'filesByVersion' => $filesByVersion,
            'canApprove' => $request->user()->can('approve', $examSubmission),
            'canReject' => $request->user()->can('reject', $examSubmission),
        ]);
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

    public function approve(Request $request, ExamSubmission $examSubmission, SubmissionApprovalService $submissionApprovalService): RedirectResponse
    {
        $this->authorize('approve', $examSubmission);

        $submissionApprovalService->approveSubmission($request->user(), $examSubmission);

        return redirect()
            ->route('dashboard.department.show', $examSubmission)
            ->with('status', __('Submission approved.'));
    }

    public function reject(
        RejectSubmissionRequest $request,
        ExamSubmission $examSubmission,
        SubmissionApprovalService $submissionApprovalService,
    ): RedirectResponse {
        $submissionApprovalService->rejectSubmission(
            $request->user(),
            $examSubmission,
            $request->validated('rejection_notes'),
        );

        return redirect()
            ->route('dashboard.department.show', $examSubmission)
            ->with('status', __('Submission rejected.'));
    }
}
