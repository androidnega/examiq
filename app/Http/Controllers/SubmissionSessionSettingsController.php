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
            'academic_year_options' => ['required', 'string', 'max:4000'],
            'semester_options' => ['required', 'string', 'max:4000'],
        ]);

        SubmissionSessionOptions::setAcademicYears($this->linesToValues($data['academic_year_options']));
        SubmissionSessionOptions::setSemesters($this->linesToValues($data['semester_options']));

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
            'academic_year_options' => ['required', 'string', 'max:4000'],
            'semester_options' => ['required', 'string', 'max:4000'],
        ]);

        SubmissionSessionOptions::setAcademicYears($this->linesToValues($data['academic_year_options']));
        SubmissionSessionOptions::setSemesters($this->linesToValues($data['semester_options']));

        return back()->with('status', __('Submission session options updated.'));
    }

    /**
     * @return array<int, string>
     */
    private function linesToValues(string $value): array
    {
        $lines = preg_split('/\r\n|\r|\n/', $value) ?: [];

        return array_values(array_unique(array_filter(array_map(
            static fn (string $line): string => trim($line),
            $lines
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
