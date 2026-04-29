<?php

namespace App\Http\Controllers;

use App\Enums\SubmissionStatus;
use App\Models\ActivityLog;
use App\Models\Department;
use App\Models\ExamSubmission;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    public function __invoke(): View
    {
        $now = now();
        $usersCount = User::query()->count();
        $blockedUsersCount = User::query()->where('is_blocked', true)->count();
        $departmentsCount = Department::query()->count();
        $submissionsTotal = ExamSubmission::query()->count();

        $statusCounts = ExamSubmission::query()
            ->selectRaw('status, COUNT(*) as aggregate')
            ->groupBy('status')
            ->pluck('aggregate', 'status')
            ->map(fn (int|string $count): int => (int) $count)
            ->all();

        $pendingCount = $statusCounts[SubmissionStatus::Pending->value] ?? 0;
        $underReviewCount = $statusCounts[SubmissionStatus::UnderReview->value] ?? 0;
        $underRevisionCount = $statusCounts[SubmissionStatus::UnderRevision->value] ?? 0;
        $awaitingHodCount = $statusCounts[SubmissionStatus::AwaitingHodApproval->value] ?? 0;
        $moderatedCount = $statusCounts[SubmissionStatus::Moderated->value] ?? 0;
        $approvedCount = $statusCounts[SubmissionStatus::Approved->value] ?? 0;
        $rejectedCount = $statusCounts[SubmissionStatus::Rejected->value] ?? 0;

        $pipelineOpen = $pendingCount + $underReviewCount + $underRevisionCount;
        $pipelineHold = $awaitingHodCount + $moderatedCount;
        $pipelineCompleted = $approvedCount + $rejectedCount;
        $pipelineTotal = max(1, $submissionsTotal);

        $workflowProgressPct = (int) round($pipelineCompleted / $pipelineTotal * 100);
        $approvalSharePct = $submissionsTotal > 0
            ? (int) round($approvedCount / $submissionsTotal * 100)
            : 0;

        $statusRows = [
            [
                'label' => __('Pending'),
                'count' => $pendingCount,
                'text' => 'text-amber-600',
                'bar' => 'bg-amber-300',
            ],
            [
                'label' => __('Under review'),
                'count' => $underReviewCount,
                'text' => 'text-sky-600',
                'bar' => 'bg-sky-300',
            ],
            [
                'label' => __('Under revision'),
                'count' => $underRevisionCount,
                'text' => 'text-violet-600',
                'bar' => 'bg-violet-300',
            ],
            [
                'label' => __('Awaiting HOD approval'),
                'count' => $awaitingHodCount,
                'text' => 'text-indigo-600',
                'bar' => 'bg-indigo-300',
            ],
            [
                'label' => __('Moderated'),
                'count' => $moderatedCount,
                'text' => 'text-cyan-600',
                'bar' => 'bg-cyan-300',
            ],
            [
                'label' => __('Approved'),
                'count' => $approvedCount,
                'text' => 'text-emerald-600',
                'bar' => 'bg-emerald-300',
            ],
            [
                'label' => __('Rejected'),
                'count' => $rejectedCount,
                'text' => 'text-rose-600',
                'bar' => 'bg-rose-300',
            ],
        ];

        $maxStatusCount = max(array_column($statusRows, 'count')) ?: 1;

        $submissionsTrend = [];
        $weekLabels = [];
        for ($i = 11; $i >= 0; $i--) {
            $start = $now->copy()->subWeeks($i)->startOfWeek();
            $end = (clone $start)->endOfWeek();
            $weekLabels[] = $start->format('M j');
            $submissionsTrend[] = ExamSubmission::query()
                ->whereBetween('created_at', [$start, $end])
                ->count();
        }

        $activityTrend = [];
        for ($i = 13; $i >= 0; $i--) {
            $day = $now->copy()->subDays($i)->startOfDay();
            $activityTrend[] = ActivityLog::query()
                ->whereDate('created_at', $day->toDateString())
                ->count();
        }

        $usersTrend = [];
        for ($i = 11; $i >= 0; $i--) {
            $start = $now->copy()->subWeeks($i)->startOfWeek();
            $end = (clone $start)->endOfWeek();
            $usersTrend[] = User::query()
                ->whereBetween('created_at', [$start, $end])
                ->count();
        }

        return view('admin.index', [
            'usersCount' => $usersCount,
            'blockedUsersCount' => $blockedUsersCount,
            'departmentsCount' => $departmentsCount,
            'submissionsTotal' => $submissionsTotal,
            'approvedCount' => $approvedCount,
            'pipelineOpen' => $pipelineOpen,
            'pipelineHold' => $pipelineHold,
            'pipelineCompleted' => $pipelineCompleted,
            'workflowProgressPct' => $workflowProgressPct,
            'approvalSharePct' => $approvalSharePct,
            'statusRows' => $statusRows,
            'maxStatusCount' => $maxStatusCount,
            'submissionsTrend' => $submissionsTrend,
            'activityTrend' => $activityTrend,
            'usersTrend' => $usersTrend,
            'weekLabels' => $weekLabels,
            'lastRefreshedAt' => $now,
            'autoRefreshEnabled' => Cache::get('examiq.admin_dashboard_auto_refresh_enabled', true),
            'autoRefreshSeconds' => max(10, (int) Cache::get('examiq.admin_dashboard_auto_refresh_seconds', 30)),
        ]);
    }
}
