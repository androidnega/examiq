<?php

namespace App\Http\Controllers;

use App\Enums\SubmissionStatus;
use App\Enums\UserRole;
use App\Models\Course;
use App\Models\ExamSubmission;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardSearchController extends Controller
{
    public function index(Request $request): View
    {
        $q = trim((string) $request->query('q', ''));
        $user = $request->user();
        $role = $user->role instanceof UserRole ? $user->role : UserRole::tryFrom((string) $user->role);

        $submissions = collect();
        $users = collect();
        $courses = collect();

        if ($q !== '' && $role !== null) {
            $term = '%'.str_replace(['%', '_'], ['\\%', '\\_'], $q).'%';

            $submissions = $this->searchSubmissions($user, $role, $term);
            if ($role === UserRole::Admin) {
                $users = User::query()
                    ->where(function ($query) use ($term): void {
                        $query->where('name', 'like', $term)
                            ->orWhere('phone', 'like', $term);
                    })
                    ->orderBy('name')
                    ->limit(15)
                    ->get();
                $courses = Course::query()
                    ->where(function ($query) use ($term): void {
                        $query->where('name', 'like', $term)
                            ->orWhere('code', 'like', $term);
                    })
                    ->orderBy('code')
                    ->limit(15)
                    ->get();
            }
        }

        return view('dashboard.search', [
            'q' => $q,
            'submissions' => $submissions,
            'users' => $users,
            'courses' => $courses,
            'role' => $role,
        ]);
    }

    /**
     * @return \Illuminate\Support\Collection<int, ExamSubmission>
     */
    protected function searchSubmissions(User $user, UserRole $role, string $term)
    {
        $base = ExamSubmission::query()->with('course');

        match ($role) {
            UserRole::Lecturer => $base->where('lecturer_id', $user->id),
            UserRole::Hod => $base->whereHas('course', fn ($q) => $q->where('department_id', $user->department_id)),
            UserRole::Moderator => $base->whereHas('moderationAssignments', fn ($q) => $q->where('moderator_id', $user->id)),
            UserRole::ExamOfficer => $base->where('status', SubmissionStatus::Approved),
            UserRole::Admin => $base,
            default => $base->whereRaw('1 = 0'),
        };

        $base->where(function ($query) use ($term): void {
            $query->whereHas('course', function ($q) use ($term): void {
                $q->where('name', 'like', $term)
                    ->orWhere('code', 'like', $term);
            })->orWhere('academic_year', 'like', $term);
        });

        return $base->orderByDesc('updated_at')->limit(20)->get();
    }
}
