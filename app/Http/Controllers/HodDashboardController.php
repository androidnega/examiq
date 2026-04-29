<?php

namespace App\Http\Controllers;

use App\Enums\SubmissionStatus;
use App\Models\ExamSubmission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class HodDashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $departmentId = $request->user()->department_id;
        abort_if($departmentId === null, 403);

        $statsBase = ExamSubmission::query()
            ->whereHas('course', fn ($q) => $q->where('department_id', $departmentId));

        $totalSubmissions = (clone $statsBase)->count();
        $pendingReview = (clone $statsBase)->where('status', SubmissionStatus::Pending)->count();
        $moderationInProgress = (clone $statsBase)->where('status', SubmissionStatus::UnderReview)->count();
        $underRevision = (clone $statsBase)->where('status', SubmissionStatus::UnderRevision)->count();
        $awaitingApproval = (clone $statsBase)->where('status', SubmissionStatus::AwaitingHodApproval)->count();

        return view('hod.index', [
            'totalSubmissions' => $totalSubmissions,
            'pendingReview' => $pendingReview,
            'moderationInProgress' => $moderationInProgress,
            'underRevision' => $underRevision,
            'awaitingApproval' => $awaitingApproval,
            'canManageSessionOptions' => (bool) Cache::get(
                'examiq.allow_hod_submission_session_management',
                (bool) config('examiq.allow_hod_submission_session_management', false)
            ),
        ]);
    }
}
