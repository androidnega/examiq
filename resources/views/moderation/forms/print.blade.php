<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Moderation Form Print') }}</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 24px; color: #111827; }
        h1, h2 { margin: 0 0 8px 0; }
        .muted { color: #6b7280; font-size: 12px; }
        .section { margin-top: 20px; }
        .grid { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
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

    <h1>{{ __('Internal Examiner Moderation Form') }}</h1>
    <p class="muted">{{ __('Generated from Examiq moderation record') }}</p>

    <div class="section grid">
        <div class="box"><strong>{{ __('Department/Course') }}:</strong> {{ $submission->course?->code }} — {{ $submission->course?->name }}</div>
        <div class="box"><strong>{{ __('Lecturer') }}:</strong> {{ $submission->lecturer?->name }}</div>
        <div class="box"><strong>{{ __('Moderator') }}:</strong> {{ $moderation->moderator?->name }}</div>
        <div class="box"><strong>{{ __('Date') }}:</strong> {{ $moderation->moderated_on?->format('M j, Y') ?? __('N/A') }}</div>
    </div>

    <div class="section">
        <h2>{{ __('Rubric Grades (A-E)') }}</h2>
        <table>
            <thead>
                <tr>
                    <th>{{ __('Item') }}</th>
                    <th>{{ __('Grade') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach (range(1, 11) as $rubricIndex)
                    @php($field = 'rubric_'.$rubricIndex.'_grade')
                    <tr>
                        <td>{{ __('Rubric item :n', ['n' => $rubricIndex]) }}</td>
                        <td>{{ $moderation->{$field} ?? '—' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="section grid">
        <div class="box"><strong>{{ __('Questions accepted') }}:</strong> {{ $moderation->recommend_accept_questions ?: '—' }}</div>
        <div class="box"><strong>{{ __('Questions rejected') }}:</strong> {{ $moderation->recommend_reject_questions ?: '—' }}</div>
        <div class="box"><strong>{{ __('Questions to re-set') }}:</strong> {{ $moderation->recommend_reset_questions ?: '—' }}</div>
        <div class="box"><strong>{{ __('Outcome') }}:</strong> {{ str($moderation->status->value)->replace('_', ' ')->title() }}</div>
    </div>

    <div class="section">
        <h2>{{ __('Comments') }}</h2>
        <div class="box"><strong>{{ __('Question paper comments') }}:</strong><br>{{ $moderation->question_paper_comments ?: '—' }}</div>
        <div class="box" style="margin-top: 10px;"><strong>{{ __('Marking scheme comments') }}:</strong><br>{{ $moderation->marking_scheme_comments ?: '—' }}</div>
        <div class="box" style="margin-top: 10px;"><strong>{{ __('Further improvement comments') }}:</strong><br>{{ $moderation->improvement_comments ?: '—' }}</div>
    </div>

    <div class="section grid">
        <div class="box"><strong>{{ __('Question paper assessment') }}:</strong> {{ str((string) $moderation->question_paper_assessment)->replace('_', ' ')->title() ?: '—' }}</div>
        <div class="box"><strong>{{ __('Marking scheme assessment') }}:</strong> {{ str((string) $moderation->marking_scheme_assessment)->replace('_', ' ')->title() ?: '—' }}</div>
        <div class="box"><strong>{{ __('Overall rating') }}:</strong> {{ str((string) $moderation->overall_rating)->replace('_', ' ')->title() ?: '—' }}</div>
    </div>
</body>
</html>
