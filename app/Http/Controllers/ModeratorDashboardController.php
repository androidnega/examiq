<?php

namespace App\Http\Controllers;

use App\Enums\SubmissionStatus;
use App\Models\ModerationAssignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ModeratorDashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $moderatorId = $request->user()->id;

        $openAssignments = ModerationAssignment::query()
            ->where('moderator_id', $moderatorId)
            ->whereNotExists(function ($q): void {
                $q->select(DB::raw('1'))
                    ->from('moderations')
                    ->whereColumn('moderations.submission_id', 'moderation_assignments.exam_submission_id')
                    ->whereColumn('moderations.moderator_id', 'moderation_assignments.moderator_id');
            })
            ->count();

        $completedReviews = ModerationAssignment::query()
            ->where('moderator_id', $moderatorId)
            ->whereExists(function ($q): void {
                $q->select(DB::raw('1'))
                    ->from('moderations')
                    ->whereColumn('moderations.submission_id', 'moderation_assignments.exam_submission_id')
                    ->whereColumn('moderations.moderator_id', 'moderation_assignments.moderator_id');
            })
            ->count();

        $moderationAssignments = ModerationAssignment::query()
            ->where('moderator_id', $moderatorId)
            ->whereHas('examSubmission', fn ($q) => $q->whereNotIn('status', [
                SubmissionStatus::Approved,
                SubmissionStatus::AwaitingHodApproval,
            ]))
            ->with(['examSubmission.course'])
            ->orderByDesc('assigned_at')
            ->paginate(12);

        return view('moderator.index', [
            'openAssignments' => $openAssignments,
            'completedReviews' => $completedReviews,
            'moderationAssignments' => $moderationAssignments,
        ]);
    }
}
