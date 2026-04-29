<?php

namespace App\Http\Controllers;

use App\Enums\ModerationStatus;
use App\Enums\SubmissionStatus;
use App\Models\ExamSubmission;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LecturerDashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $lecturerId = $request->user()->id;

        $base = ExamSubmission::query()
            ->where('lecturer_id', $lecturerId);

        $totalSubmissions = (clone $base)->count();
        $pendingReviews = (clone $base)->where('status', SubmissionStatus::UnderReview)->count();

        $revisionsNeeded = (clone $base)->whereIn('id', function ($q) use ($lecturerId): void {
            $q->select('m1.submission_id')
                ->from('moderations as m1')
                ->join('exam_submissions as es', 'es.id', '=', 'm1.submission_id')
                ->where('es.lecturer_id', $lecturerId)
                ->whereRaw(
                    'm1.created_at = (select max(m2.created_at) from moderations m2 where m2.submission_id = m1.submission_id)'
                )
                ->whereIn('m1.status', [
                    ModerationStatus::MinorChanges->value,
                    ModerationStatus::MajorChanges->value,
                ]);
        })->count();

        $recentSubmissions = (clone $base)
            ->with('course')
            ->latest('updated_at')
            ->limit(15)
            ->get();

        return view('lecturer.index', [
            'totalSubmissions' => $totalSubmissions,
            'pendingReviews' => $pendingReviews,
            'revisionsNeeded' => $revisionsNeeded,
            'recentSubmissions' => $recentSubmissions,
        ]);
    }
}
