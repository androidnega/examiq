<?php

namespace App\Http\Controllers;

use App\Enums\SubmissionStatus;
use App\Models\ExamSubmission;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ExamOfficerRegistryController extends Controller
{
    public function __invoke(Request $request): View
    {
        $approvedBase = ExamSubmission::query()->where('status', SubmissionStatus::Approved);

        $registryTotalExams = (int) (clone $approvedBase)->count();
        $registryTotalStudents = (int) (clone $approvedBase)->sum('students_count');
        $registryAcademicYears = (int) (clone $approvedBase)->distinct()->count('academic_year');
        $registryTotalCourses = (int) (clone $approvedBase)->distinct()->count('course_id');
        $largeCohortCount = (int) (clone $approvedBase)->where('students_count', '>=', 50)->count();
        $maxStudentsInRegistry = (int) (clone $approvedBase)->max('students_count') ?: 1;

        $query = ExamSubmission::query()
            ->where('status', SubmissionStatus::Approved)
            ->with(['course', 'lecturer'])
            ->select('exam_submissions.*');

        $search = $request->query('q');
        if (is_string($search) && $search !== '') {
            $term = '%'.str_replace(['%', '_'], ['\\%', '\\_'], $search).'%';
            $query->whereHas('course', function ($q) use ($term): void {
                $q->where('name', 'like', $term)
                    ->orWhere('code', 'like', $term);
            });
        }

        $sort = $request->query('sort', 'course');
        $direction = strtolower((string) $request->query('dir', 'asc')) === 'desc' ? 'desc' : 'asc';

        $query->join('courses', 'courses.id', '=', 'exam_submissions.course_id');

        match ($sort) {
            'year' => $query->orderBy('exam_submissions.academic_year', $direction),
            'students' => $query->orderBy('exam_submissions.students_count', $direction),
            default => $query->orderBy('courses.name', $direction),
        };

        $perPage = (int) $request->query('per_page', 12);
        if (! in_array($perPage, [6, 12, 24, 48], true)) {
            $perPage = 12;
        }

        $viewMode = $request->query('view', 'grid');
        if (! in_array($viewMode, ['grid', 'list'], true)) {
            $viewMode = 'grid';
        }

        $submissions = $query->paginate($perPage)->withQueryString();

        return view('exam-officer.registry', [
            'submissions' => $submissions,
            'sort' => $sort,
            'dir' => $direction,
            'search' => is_string($search) ? $search : '',
            'registryTotalExams' => $registryTotalExams,
            'registryTotalStudents' => $registryTotalStudents,
            'registryAcademicYears' => $registryAcademicYears,
            'registryTotalCourses' => $registryTotalCourses,
            'largeCohortCount' => $largeCohortCount,
            'maxStudentsInRegistry' => $maxStudentsInRegistry,
            'viewMode' => $viewMode,
            'perPage' => $perPage,
        ]);
    }
}
