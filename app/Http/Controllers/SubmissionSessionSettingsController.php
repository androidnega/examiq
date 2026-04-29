<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Support\SubmissionSessionOptions;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class SubmissionSessionSettingsController extends Controller
{
    public function editForAdmin(Request $request): View
    {
        abort_unless($request->user()?->role === UserRole::Admin, 403);

        return view('settings.submission-session-options', [
            'academicYears' => SubmissionSessionOptions::academicYears(),
            'semesters' => SubmissionSessionOptions::semesters(),
            'isHodPage' => false,
        ]);
    }

    public function updateForAdmin(Request $request): RedirectResponse
    {
        abort_unless($request->user()?->role === UserRole::Admin, 403);

        $data = $request->validate([
            'academic_year_options' => ['required', 'array', 'min:1'],
            'academic_year_options.*' => ['nullable', 'string', 'max:32'],
            'semester_options' => ['required', 'array', 'min:1'],
            'semester_options.*' => ['nullable', 'string', 'max:32'],
        ]);

        SubmissionSessionOptions::setAcademicYears($this->arrayToValues($data['academic_year_options']));
        SubmissionSessionOptions::setSemesters($this->arrayToValues($data['semester_options']));

        return back()->with('status', __('Submission session options updated.'));
    }

    public function editForHod(Request $request): View
    {
        abort_unless($request->user()?->role === UserRole::Hod, 403);
        abort_unless($this->allowHodSessionManagement(), 403);

        return view('settings.submission-session-options', [
            'academicYears' => SubmissionSessionOptions::academicYears(),
            'semesters' => SubmissionSessionOptions::semesters(),
            'isHodPage' => true,
        ]);
    }

    public function updateForHod(Request $request): RedirectResponse
    {
        abort_unless($request->user()?->role === UserRole::Hod, 403);
        abort_unless($this->allowHodSessionManagement(), 403);

        $data = $request->validate([
            'academic_year_options' => ['required', 'array', 'min:1'],
            'academic_year_options.*' => ['nullable', 'string', 'max:32'],
            'semester_options' => ['required', 'array', 'min:1'],
            'semester_options.*' => ['nullable', 'string', 'max:32'],
        ]);

        SubmissionSessionOptions::setAcademicYears($this->arrayToValues($data['academic_year_options']));
        SubmissionSessionOptions::setSemesters($this->arrayToValues($data['semester_options']));

        return back()->with('status', __('Submission session options updated.'));
    }

    /**
     * @return array<int, string>
     */
    private function arrayToValues(array $values): array
    {
        return array_values(array_unique(array_filter(array_map(
            static fn (mixed $value): string => trim((string) $value),
            $values
        ))));
    }

    private function allowHodSessionManagement(): bool
    {
        return (bool) Cache::get(
            'examiq.allow_hod_submission_session_management',
            (bool) config('examiq.allow_hod_submission_session_management', false)
        );
    }
}
