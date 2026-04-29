<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Http\Requests\AssignModeratorsRequest;
use App\Models\Course;
use App\Models\ExamSubmission;
use App\Models\User;
use App\Services\Moderation\ModerationAssignmentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ModerationAssignmentController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', ExamSubmission::class);

        $departmentId = $request->user()->department_id;
        abort_if($departmentId === null, 403);

        $query = ExamSubmission::query()
            ->with(['course', 'lecturer'])
            ->whereHas('course', fn ($q) => $q->where('department_id', $departmentId));

        $statusFilter = $request->query('status');
        if (is_string($statusFilter) && $statusFilter !== '' && $statusFilter !== 'all') {
            $query->where('status', $statusFilter);
        }

        $semesterFilter = $request->query('semester');
        if (is_string($semesterFilter) && $semesterFilter !== '' && $semesterFilter !== 'all') {
            $query->where('semester', $semesterFilter);
        }

        $yearFilter = $request->query('academic_year');
        if (is_string($yearFilter) && $yearFilter !== '' && $yearFilter !== 'all') {
            $query->where('academic_year', $yearFilter);
        }

        $examSubmissions = $query->orderByDesc('updated_at')->paginate(12)->withQueryString();

        $departmentModerators = User::query()
            ->where('role', UserRole::Moderator)
            ->where('department_id', $departmentId)
            ->orderBy('name')
            ->get();

        $semesters = Course::query()
            ->where('department_id', $departmentId)
            ->whereNotNull('semester')
            ->distinct()
            ->orderBy('semester')
            ->pluck('semester');

        $academicYears = ExamSubmission::query()
            ->whereHas('course', fn ($q) => $q->where('department_id', $departmentId))
            ->distinct()
            ->orderBy('academic_year')
            ->pluck('academic_year');

        $request->user()->loadMissing('department');

        return view('hod.submissions.index', [
            'examSubmissions' => $examSubmissions,
            'departmentModerators' => $departmentModerators,
            'department' => $request->user()->department,
            'semesters' => $semesters,
            'academicYears' => $academicYears,
            'filters' => [
                'status' => $statusFilter ?: 'all',
                'semester' => $semesterFilter ?: 'all',
                'academic_year' => $yearFilter ?: 'all',
            ],
        ]);
    }

    public function assignModerators(
        AssignModeratorsRequest $request,
        ModerationAssignmentService $moderationAssignmentService,
    ): RedirectResponse {
        $created = $moderationAssignmentService->assignModerators(
            $request->user(),
            $request->validated('submission_ids'),
            $request->validated('moderator_ids'),
        );

        $message = $created > 0
            ? trans_choice('Added :count new moderator assignment.|Added :count new moderator assignments.', $created, ['count' => $created])
            : __('No new assignments were added (those moderator pairs already exist).');

        return redirect()
            ->route('dashboard.department.index')
            ->with('status', $message);
    }
}
