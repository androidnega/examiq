<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Moderation of End-of-Semester Questions') }}</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 24px; color: #111827; }
        h1, h2, h3 { margin: 0 0 8px 0; }
        .muted { color: #6b7280; font-size: 12px; }
        .section { margin-top: 20px; }
        .grid { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
        .grid-3 { display: grid; grid-template-columns: repeat(3, 1fr); gap: 12px; }
        .box { border: 1px solid #d1d5db; border-radius: 8px; padding: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        th, td { border: 1px solid #d1d5db; padding: 6px 8px; text-align: left; font-size: 13px; }
        .actions { margin-bottom: 12px; }
        @media print {
            .actions { display: none; }
            body { margin: 0; }
        }
    </style>
</head>
<body>
    <div class="actions">
        <button type="button" onclick="window.print()">{{ __('Print / Save as PDF') }}</button>
    </div>

    <h1>{{ __('Moderation of End-of-Semester Questions') }}</h1>
    <p class="muted">{{ __('Generated from Examiq moderation record') }}</p>

    <div class="section grid">
        <div class="box"><strong>{{ __('Name of Department') }}:</strong> {{ $submission->course?->department?->name ?? __('N/A') }}</div>
        <div class="box"><strong>{{ __('Programme') }}:</strong> {{ $submission->course?->program ?? __('N/A') }}</div>
        <div class="box"><strong>{{ __('Course Title') }}:</strong> {{ $submission->course?->name ?? __('N/A') }}</div>
        <div class="box"><strong>{{ __('Academic Year') }}:</strong> {{ $submission->academic_year }}</div>
        <div class="box"><strong>{{ __('Semester') }}:</strong> {{ str((string) $submission->semester)->title() }}</div>
        <div class="box"><strong>{{ __('Name of Lecturer') }}:</strong> {{ $submission->lecturer?->name ?? __('N/A') }}</div>
        <div class="box"><strong>{{ __('No. of Questions (Section A)') }}:</strong> {{ $moderation->question_count_section_a ?? '—' }}</div>
        <div class="box"><strong>{{ __('No. of Questions (Section B)') }}:</strong> {{ $moderation->question_count_section_b ?? '—' }}</div>
        <div class="box"><strong>{{ __('No. of Questions (Section C)') }}:</strong> {{ $moderation->question_count_section_c ?? '—' }}</div>
        <div class="box"><strong>{{ __('Duration of Paper') }}:</strong> {{ $moderation->paper_duration ?: '—' }}</div>
        <div class="box"><strong>{{ __('Internal Moderator') }}:</strong> {{ $moderation->moderator?->name ?? __('N/A') }}</div>
        <div class="box"><strong>{{ __('Date') }}:</strong> {{ $moderation->moderated_on?->format('M j, Y') ?? __('N/A') }}</div>
    </div>

    <div class="section">
        <h2>{{ __('Section B: Assessment of the Question Paper') }}</h2>
        <table>
            <thead>
                <tr>
                    <th>{{ __('Item') }}</th>
                    <th>{{ __('Grade') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach ([
                    1 => __('Representative samples'),
                    2 => __('Learning outcomes'),
                    3 => __('Syllabus coverage'),
                    4 => __('Clarity'),
                    5 => __('Unambiguous language'),
                    6 => __('Difficulty level'),
                    7 => __('Format/length'),
                    8 => __('QA guidelines'),
                ] as $rubricIndex => $rubricLabel)
                    @php($field = 'rubric_'.$rubricIndex.'_grade')
                    <tr>
                        <td>{{ $rubricLabel }}</td>
                        <td>{{ $moderation->{$field} ?? '—' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="section grid">
        <div class="box"><strong>{{ __('Accepted Questions') }}:</strong> {{ $moderation->recommend_accept_questions ?: '—' }}</div>
        <div class="box"><strong>{{ __('Rejected Questions') }}:</strong> {{ $moderation->recommend_reject_questions ?: '—' }}</div>
        <div class="box"><strong>{{ __('Re-set Questions') }}:</strong> {{ $moderation->recommend_reset_questions ?: '—' }}</div>
        <div class="box"><strong>{{ __('Other Comments (Question)') }}:</strong><br>{{ $moderation->question_paper_comments ?: '—' }}</div>
    </div>

    <div class="section">
        <h2>{{ __('Section C: Marking Scheme') }}</h2>
        <table>
            <thead>
            <tr>
                <th>{{ __('Item') }}</th>
                <th>{{ __('Grade') }}</th>
            </tr>
            </thead>
            <tbody>
            @foreach ([
                9 => __('Syllabus correspondence'),
                10 => __('Intention/knowledge'),
                11 => __('Mark distribution'),
            ] as $rubricIndex => $rubricLabel)
                @php($field = 'rubric_'.$rubricIndex.'_grade')
                <tr>
                    <td>{{ $rubricLabel }}</td>
                    <td>{{ $moderation->{$field} ?? '—' }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
        <div class="box" style="margin-top: 10px;"><strong>{{ __('Other Comments (Marking Scheme)') }}:</strong><br>{{ $moderation->marking_scheme_comments ?: '—' }}</div>
    </div>

    @php
        $questionAssessments = is_array($moderation->question_paper_assessments) ? $moderation->question_paper_assessments : [];
        $schemeAssessments = is_array($moderation->marking_scheme_assessments) ? $moderation->marking_scheme_assessments : [];
        $questionAssessmentLabels = [
            'accepted_without_corrections' => __('Question paper accepted without any corrections/modifications.'),
            'accepted_minor_corrections' => __('Question paper accepted with minor corrections as indicated on the paper.'),
            'accepted_with_modifications' => __('Question paper accepted with some modifications as indicated.'),
            'rejected_new_questions' => __('Question paper rejected and new questions to be set.'),
        ];
        $schemeAssessmentLabels = [
            'accepted_all' => __('Marking scheme accepted for all questions.'),
            'to_be_reprepared' => __('Marking scheme to be re-prepared according to comments.'),
        ];
    @endphp

    <div class="section">
        <h2>{{ __('Section D: Final Assessment & Sign-off') }}</h2>
        <div class="box">
            <strong>{{ __('General Assessment') }}</strong>
            <ul>
                @foreach ($questionAssessmentLabels as $value => $label)
                    <li>{{ in_array($value, $questionAssessments, true) ? '[x]' : '[ ]' }} {{ $label }}</li>
                @endforeach
                @foreach ($schemeAssessmentLabels as $value => $label)
                    <li>{{ in_array($value, $schemeAssessments, true) ? '[x]' : '[ ]' }} {{ $label }}</li>
                @endforeach
            </ul>
        </div>
        <div class="grid-3" style="margin-top: 10px;">
            <div class="box"><strong>{{ __('Overall rating') }}:</strong> {{ str((string) $moderation->overall_rating)->replace('_', ' ')->title() ?: '—' }}</div>
            <div class="box"><strong>{{ __('Moderation outcome') }}:</strong> {{ str($moderation->status->value)->replace('_', ' ')->title() }}</div>
            <div class="box"><strong>{{ __('Date') }}:</strong> {{ $moderation->moderated_on?->format('M j, Y') ?? '—' }}</div>
        </div>
        <div class="box" style="margin-top: 10px;"><strong>{{ __('Further comments for improvement') }}:</strong><br>{{ $moderation->improvement_comments ?: '—' }}</div>
        <div class="grid" style="margin-top: 10px;">
            <div class="box"><strong>{{ __('Signature of internal moderator') }}:</strong> {{ $moderation->moderator_signature_name ?: ($moderation->moderator?->name ?? '—') }}</div>
            <div class="box"><strong>{{ __('Feedback') }}:</strong> {{ $moderation->feedback ?: '—' }}</div>
        </div>
    </div>
</body>
</html>
