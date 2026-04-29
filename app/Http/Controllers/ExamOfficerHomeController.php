<?php

namespace App\Http\Controllers;

use App\Enums\SubmissionStatus;
use App\Models\ExamSubmission;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ExamOfficerHomeController extends Controller
{
    public function __invoke(Request $request): View
    {
        $approvedQuery = ExamSubmission::query()->where('status', SubmissionStatus::Approved);

        $registryTotalExams = (int) (clone $approvedQuery)->count();
        $registryTotalStudents = (int) (clone $approvedQuery)->sum('students_count');
        $registryAcademicYears = (int) (clone $approvedQuery)->distinct()->count('academic_year');

        $statusCounts = ExamSubmission::query()
            ->selectRaw('status, count(*) as c')
            ->groupBy('status')
            ->pluck('c', 'status');

        $byStatus = [];
        foreach (SubmissionStatus::cases() as $case) {
            $byStatus[$case->value] = (int) ($statusCounts[$case->value] ?? 0);
        }

        $totalPipeline = array_sum($byStatus);

        $overallProgress = $totalPipeline > 0
            ? (int) round(($byStatus[SubmissionStatus::Approved->value] / $totalPipeline) * 100)
            : 0;

        return view('exam-officer.home', [
            'registryTotalExams' => $registryTotalExams,
            'registryTotalStudents' => $registryTotalStudents,
            'registryAcademicYears' => $registryAcademicYears,
            'totalPipeline' => $totalPipeline,
            'byStatus' => $byStatus,
            'overallProgress' => $overallProgress,
        ]);
    }
}
