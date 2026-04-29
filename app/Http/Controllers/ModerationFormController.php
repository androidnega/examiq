<?php

namespace App\Http\Controllers;

use App\Models\ExamSubmission;
use App\Models\Moderation;
use Illuminate\View\View;

class ModerationFormController extends Controller
{
    public function index(ExamSubmission $submission): View
    {
        $this->authorize('view', $submission);

        $submission->load([
            'course',
            'lecturer',
            'moderations.moderator',
        ]);

        return view('moderation.forms.index', [
            'submission' => $submission,
            'moderations' => $submission->moderations,
        ]);
    }

    public function print(ExamSubmission $submission, Moderation $moderation): View
    {
        $this->authorize('view', $submission);
        abort_unless($moderation->submission_id === $submission->getKey(), 404);

        $submission->loadMissing(['course', 'lecturer']);
        $moderation->loadMissing('moderator');

        return view('moderation.forms.print', [
            'submission' => $submission,
            'moderation' => $moderation,
        ]);
    }
}
