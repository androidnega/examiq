@extends('layouts.app', ['header' => __('Moderation review')])

@section('content')
    <div class="w-full space-y-8">
        <div class="rounded-xl border border-gray-200 bg-white p-6">
            <h2 class="text-sm font-semibold text-gray-900">{{ __('Submission information') }}</h2>
            <dl class="mt-4 grid gap-3 text-sm sm:grid-cols-2">
                <div>
                    <dt class="text-gray-500">{{ __('Course') }}</dt>
                    <dd class="font-medium text-gray-900">{{ $examSubmission->course?->code }} — {{ $examSubmission->course?->name }}</dd>
                </div>
                <div>
                    <dt class="text-gray-500">{{ __('Lecturer') }}</dt>
                    <dd class="font-medium text-gray-900">{{ $examSubmission->lecturer?->name }}</dd>
                </div>
                <div>
                    <dt class="text-gray-500">{{ __('Session') }}</dt>
                    <dd class="font-medium text-gray-900">{{ $examSubmission->academic_year }} · {{ $examSubmission->semester }}</dd>
                </div>
                <div>
                    <dt class="text-gray-500">{{ __('Students') }}</dt>
                    <dd class="font-medium text-gray-900">{{ $examSubmission->students_count }}</dd>
                </div>
                <div>
                    <dt class="text-gray-500">{{ __('Version') }}</dt>
                    <dd class="font-medium text-gray-900">{{ $examSubmission->current_version }}</dd>
                </div>
                <div>
                    <dt class="text-gray-500">{{ __('Status') }}</dt>
                    <dd class="mt-1"><x-badge :value="$examSubmission->status" /></dd>
                </div>
            </dl>
        </div>

        <div class="rounded-xl border border-gray-200 bg-white p-6">
            <h2 class="text-sm font-semibold text-gray-900">{{ __('Files (read only)') }}</h2>
            <p class="mt-1 text-xs text-gray-500">{{ __('Open each PDF in the browser. Downloads are not exposed as public links.') }}</p>
            <ul class="mt-4 space-y-2">
                @forelse ($submissionFiles as $submissionFile)
                    <li class="flex flex-col gap-1 rounded-lg border border-gray-100 bg-gray-50 px-3 py-2 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <span class="text-sm font-medium text-gray-900">{{ $submissionFile->type->label() }}</span>
                            <span class="mt-0.5 block text-xs text-gray-500">{{ __('Version') }} {{ $submissionFile->version }} · {{ $submissionFile->original_name ?? __('document.pdf') }}</span>
                        </div>
                        <a
                            href="{{ route('dashboard.reviews.files.show', [$examSubmission, $submissionFile]) }}"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="text-sm font-medium text-gray-700 underline decoration-gray-300 underline-offset-2 hover:text-gray-900"
                        >{{ __('View PDF') }}</a>
                    </li>
                @empty
                    <li class="text-sm text-gray-500">{{ __('No files uploaded for this submission.') }}</li>
                @endforelse
            </ul>
        </div>

        <div class="rounded-xl border border-gray-200 bg-white p-6">
            <h2 class="text-sm font-semibold text-gray-900">{{ __('Your moderation') }}</h2>
            @if ($moderationReview)
                <p class="mt-1 text-xs text-gray-500">{{ __('You have already submitted a review. Submitting again will update your record.') }}</p>
                <div class="mt-3 flex flex-wrap items-center gap-2">
                    <span class="text-xs font-medium text-gray-500">{{ __('Current outcome') }}</span>
                    <x-badge :value="$moderationReview->status" />
                    <x-button href="{{ route('dashboard.submissions.moderation-forms.print', [$examSubmission, $moderationReview]) }}" variant="ghost">{{ __('Print form') }}</x-button>
                </div>
            @else
                <p class="mt-1 text-xs text-gray-500">{{ __('Choose an outcome and provide feedback where required.') }}</p>
            @endif

            @can('review', $examSubmission)
                <form method="post" action="{{ route('dashboard.reviews.store', $examSubmission) }}" class="mt-5 space-y-6">
                    @csrf
                    <div class="rounded-xl border border-gray-100 bg-gray-50 p-4">
                        <h3 class="text-sm font-semibold text-gray-900">{{ __('Section A: Information about the question') }}</h3>
                        <div class="mt-3 grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                            <div>
                                <p class="text-xs text-gray-500">{{ __('Name of Department') }}</p>
                                <p class="text-sm font-medium text-gray-900">{{ $examSubmission->course?->department?->name ?? __('N/A') }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500">{{ __('Programme') }}</p>
                                <p class="text-sm font-medium text-gray-900">{{ $examSubmission->course?->program ?? __('N/A') }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500">{{ __('Course title') }}</p>
                                <p class="text-sm font-medium text-gray-900">{{ $examSubmission->course?->name ?? __('N/A') }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500">{{ __('Academic year') }}</p>
                                <p class="text-sm font-medium text-gray-900">{{ $examSubmission->academic_year }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500">{{ __('Semester') }}</p>
                                <p class="text-sm font-medium text-gray-900">{{ str((string) $examSubmission->semester)->title() }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500">{{ __('Name of Lecturer') }}</p>
                                <p class="text-sm font-medium text-gray-900">{{ $examSubmission->lecturer?->name ?? __('N/A') }}</p>
                            </div>
                            <div>
                                <label for="question_count_section_a" class="block text-sm font-medium text-gray-700">{{ __('Number of questions (Section A)') }}</label>
                                <input id="question_count_section_a" type="number" min="0" name="question_count_section_a" value="{{ old('question_count_section_a', $moderationReview?->question_count_section_a) }}" required class="mt-1 w-full rounded-xl border border-gray-200 px-3 py-2 text-sm" />
                            </div>
                            <div>
                                <label for="question_count_section_b" class="block text-sm font-medium text-gray-700">{{ __('Number of questions (Section B)') }}</label>
                                <input id="question_count_section_b" type="number" min="0" name="question_count_section_b" value="{{ old('question_count_section_b', $moderationReview?->question_count_section_b) }}" required class="mt-1 w-full rounded-xl border border-gray-200 px-3 py-2 text-sm" />
                            </div>
                            <div>
                                <label for="question_count_section_c" class="block text-sm font-medium text-gray-700">{{ __('Number of questions (Section C)') }}</label>
                                <input id="question_count_section_c" type="number" min="0" name="question_count_section_c" value="{{ old('question_count_section_c', $moderationReview?->question_count_section_c) }}" required class="mt-1 w-full rounded-xl border border-gray-200 px-3 py-2 text-sm" />
                            </div>
                        </div>
                        <div class="mt-3 max-w-sm">
                            <label for="paper_duration" class="block text-sm font-medium text-gray-700">{{ __('Duration of paper') }}</label>
                            <input id="paper_duration" type="text" name="paper_duration" value="{{ old('paper_duration', $moderationReview?->paper_duration) }}" required class="mt-1 w-full rounded-xl border border-gray-200 px-3 py-2 text-sm" placeholder="{{ __('e.g. 2.5 Hrs') }}" />
                        </div>
                    </div>

                    <div class="rounded-xl border border-gray-100 bg-gray-50 p-4">
                        <h3 class="text-sm font-semibold text-gray-900">{{ __('Section B: Assessment of the question paper') }}</h3>
                        <p class="mt-1 text-xs text-gray-500">{{ __('Grading scale: A=70-100, B=60-69, C=50-59, D=40-49, E=0-39') }}</p>
                        <div class="mt-4 grid gap-3 sm:grid-cols-2">
                            @php
                                $rubricLabels = [
                                    1 => __('Representative samples'),
                                    2 => __('Learning outcomes'),
                                    3 => __('Syllabus coverage'),
                                    4 => __('Clarity'),
                                    5 => __('Unambiguous language'),
                                    6 => __('Difficulty level'),
                                    7 => __('Format/length'),
                                    8 => __('QA guidelines'),
                                ];
                            @endphp
                            @foreach (range(1, 8) as $rubricIndex)
                                @php($field = 'rubric_'.$rubricIndex.'_grade')
                                <div>
                                    <label for="{{ $field }}" class="block text-xs font-medium text-gray-700">{{ $rubricLabels[$rubricIndex] }}</label>
                                    <select id="{{ $field }}" name="{{ $field }}" required class="mt-1 w-full rounded-xl border border-gray-200 bg-white px-3 py-2 text-sm">
                                        <option value="">{{ __('Select grade') }}</option>
                                        @foreach (['A', 'B', 'C', 'D', 'E'] as $grade)
                                            <option value="{{ $grade }}" @selected(old($field, $moderationReview?->{$field}) === $grade)>{{ $grade }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            @endforeach
                        </div>
                        <div class="mt-4 grid gap-3 sm:grid-cols-3">
                            <div>
                                <label for="recommend_accept_questions" class="block text-sm font-medium text-gray-700">{{ __('Accepted questions') }}</label>
                                <input id="recommend_accept_questions" type="text" name="recommend_accept_questions" value="{{ old('recommend_accept_questions', $moderationReview?->recommend_accept_questions) }}" class="mt-1 w-full rounded-xl border border-gray-200 px-3 py-2 text-sm" />
                            </div>
                            <div>
                                <label for="recommend_reject_questions" class="block text-sm font-medium text-gray-700">{{ __('Rejected questions') }}</label>
                                <input id="recommend_reject_questions" type="text" name="recommend_reject_questions" value="{{ old('recommend_reject_questions', $moderationReview?->recommend_reject_questions) }}" class="mt-1 w-full rounded-xl border border-gray-200 px-3 py-2 text-sm" />
                            </div>
                            <div>
                                <label for="recommend_reset_questions" class="block text-sm font-medium text-gray-700">{{ __('Re-set questions') }}</label>
                                <input id="recommend_reset_questions" type="text" name="recommend_reset_questions" value="{{ old('recommend_reset_questions', $moderationReview?->recommend_reset_questions) }}" class="mt-1 w-full rounded-xl border border-gray-200 px-3 py-2 text-sm" />
                            </div>
                        </div>
                        <div class="mt-3">
                            <label for="question_paper_comments" class="block text-sm font-medium text-gray-700">{{ __('Other comments (question paper)') }}</label>
                            <textarea id="question_paper_comments" name="question_paper_comments" rows="3" class="mt-1 w-full rounded-xl border border-gray-200 px-3 py-2 text-sm">{{ old('question_paper_comments', $moderationReview?->question_paper_comments) }}</textarea>
                        </div>
                    </div>

                    <div class="rounded-xl border border-gray-100 bg-gray-50 p-4">
                        <h3 class="text-sm font-semibold text-gray-900">{{ __('Section C: Marking scheme') }}</h3>
                        <p class="mt-1 text-xs text-gray-500">{{ __('Evaluate solution guide and mark allocations.') }}</p>
                        <div class="mt-4 grid gap-3 sm:grid-cols-3">
                            @foreach (range(9, 11) as $rubricIndex)
                                @php($field = 'rubric_'.$rubricIndex.'_grade')
                                <div>
                                    <label for="{{ $field }}" class="block text-xs font-medium text-gray-700">
                                        {{ match ($rubricIndex) {
                                            9 => __('Syllabus correspondence'),
                                            10 => __('Intention/knowledge'),
                                            11 => __('Mark distribution'),
                                        } }}
                                    </label>
                                    <select id="{{ $field }}" name="{{ $field }}" required class="mt-1 w-full rounded-xl border border-gray-200 bg-white px-3 py-2 text-sm">
                                        <option value="">{{ __('Select grade') }}</option>
                                        @foreach (['A', 'B', 'C', 'D', 'E'] as $grade)
                                            <option value="{{ $grade }}" @selected(old($field, $moderationReview?->{$field}) === $grade)>{{ $grade }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            @endforeach
                        </div>
                        <div class="mt-3">
                            <label for="marking_scheme_comments" class="block text-sm font-medium text-gray-700">{{ __('Other comments (marking scheme)') }}</label>
                            <textarea id="marking_scheme_comments" name="marking_scheme_comments" rows="3" class="mt-1 w-full rounded-xl border border-gray-200 px-3 py-2 text-sm">{{ old('marking_scheme_comments', $moderationReview?->marking_scheme_comments) }}</textarea>
                        </div>
                    </div>

                    <div class="rounded-xl border border-gray-100 bg-gray-50 p-4">
                        <h3 class="text-sm font-semibold text-gray-900">{{ __('Section D: Final assessment and sign-off') }}</h3>
                        <div class="mt-4">
                            <p class="text-sm font-medium text-gray-700">{{ __('General assessment (question paper)') }}</p>
                            @php($questionAssessments = old('question_paper_assessments', $moderationReview?->question_paper_assessments ?? []))
                            <div class="mt-2 space-y-2 text-sm text-gray-700">
                                @foreach ([
                                    'accepted_without_corrections' => __('Question paper accepted without any corrections/modifications.'),
                                    'accepted_minor_corrections' => __('Question paper accepted with minor corrections as indicated on the paper.'),
                                    'accepted_with_modifications' => __('Question paper accepted with some modifications as indicated.'),
                                    'rejected_new_questions' => __('Question paper rejected and new questions to be set.'),
                                ] as $value => $label)
                                    <label class="flex items-start gap-2">
                                        <input type="checkbox" name="question_paper_assessments[]" value="{{ $value }}" @checked(in_array($value, is_array($questionAssessments) ? $questionAssessments : [], true)) class="mt-1 rounded border-gray-300 text-gray-900 focus:ring-gray-300" />
                                        <span>{{ $label }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                        <div class="mt-4">
                            <p class="text-sm font-medium text-gray-700">{{ __('General assessment (marking scheme)') }}</p>
                            @php($schemeAssessments = old('marking_scheme_assessments', $moderationReview?->marking_scheme_assessments ?? []))
                            <div class="mt-2 space-y-2 text-sm text-gray-700">
                                @foreach ([
                                    'accepted_all' => __('Marking scheme accepted for all questions.'),
                                    'to_be_reprepared' => __('Marking scheme to be re-prepared according to comments.'),
                                ] as $value => $label)
                                    <label class="flex items-start gap-2">
                                        <input type="checkbox" name="marking_scheme_assessments[]" value="{{ $value }}" @checked(in_array($value, is_array($schemeAssessments) ? $schemeAssessments : [], true)) class="mt-1 rounded border-gray-300 text-gray-900 focus:ring-gray-300" />
                                        <span>{{ $label }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                        <div class="mt-4 grid gap-4 sm:grid-cols-2">
                            <div>
                                <label for="overall_rating" class="block text-sm font-medium text-gray-700">{{ __('Overall rating') }}</label>
                                <select id="overall_rating" name="overall_rating" required class="mt-1 w-full rounded-xl border border-gray-200 px-3 py-2 text-sm">
                                    <option value="">{{ __('Select...') }}</option>
                                    <option value="excellent" @selected(old('overall_rating', $moderationReview?->overall_rating) === 'excellent')>{{ __('Excellent') }}</option>
                                    <option value="very_good" @selected(old('overall_rating', $moderationReview?->overall_rating) === 'very_good')>{{ __('Very Good') }}</option>
                                    <option value="good" @selected(old('overall_rating', $moderationReview?->overall_rating) === 'good')>{{ __('Good') }}</option>
                                    <option value="satisfactory" @selected(old('overall_rating', $moderationReview?->overall_rating) === 'satisfactory')>{{ __('Satisfactory') }}</option>
                                    <option value="unsatisfactory" @selected(old('overall_rating', $moderationReview?->overall_rating) === 'unsatisfactory')>{{ __('Unsatisfactory') }}</option>
                                </select>
                            </div>
                            <div>
                                <label for="status" class="block text-sm font-medium text-gray-700">{{ __('Outcome') }}</label>
                                <select id="status" name="status" required class="mt-1 w-full rounded-xl border border-gray-200 px-3 py-2 text-sm">
                                    <option value="">{{ __('Select…') }}</option>
                                    <option value="accepted" @selected(old('status', $moderationReview?->status->value) === 'accepted')>{{ __('Accepted') }}</option>
                                    <option value="minor_changes" @selected(old('status', $moderationReview?->status->value) === 'minor_changes')>{{ __('Minor changes') }}</option>
                                    <option value="major_changes" @selected(old('status', $moderationReview?->status->value) === 'major_changes')>{{ __('Major changes') }}</option>
                                    <option value="rejected" @selected(old('status', $moderationReview?->status->value) === 'rejected')>{{ __('Rejected') }}</option>
                                </select>
                            </div>
                        </div>
                        <div class="mt-3">
                            <label for="feedback" class="block text-sm font-medium text-gray-700">{{ __('Feedback') }}</label>
                            <textarea id="feedback" name="feedback" rows="3" class="mt-1 w-full rounded-xl border border-gray-200 px-3 py-2 text-sm" placeholder="{{ __('Required unless the outcome is Accepted.') }}">{{ old('feedback', $moderationReview?->feedback) }}</textarea>
                        </div>
                        <div class="mt-3">
                            <label for="improvement_comments" class="block text-sm font-medium text-gray-700">{{ __('Further comments for improvement') }}</label>
                            <textarea id="improvement_comments" name="improvement_comments" rows="4" class="mt-1 w-full rounded-xl border border-gray-200 px-3 py-2 text-sm">{{ old('improvement_comments', $moderationReview?->improvement_comments) }}</textarea>
                        </div>
                        <div class="mt-3 grid gap-4 sm:grid-cols-2">
                            <div>
                                <label for="moderator_signature_name" class="block text-sm font-medium text-gray-700">{{ __('Signature of internal moderator (name)') }}</label>
                                <input id="moderator_signature_name" type="text" name="moderator_signature_name" value="{{ old('moderator_signature_name', $moderationReview?->moderator_signature_name ?? auth()->user()?->name) }}" required class="mt-1 w-full rounded-xl border border-gray-200 px-3 py-2 text-sm" />
                            </div>
                            <div>
                                <label for="moderated_on" class="block text-sm font-medium text-gray-700">{{ __('Date') }}</label>
                                <input id="moderated_on" type="date" name="moderated_on" value="{{ old('moderated_on', optional($moderationReview?->moderated_on)->format('Y-m-d')) }}" required class="mt-1 w-full rounded-xl border border-gray-200 px-3 py-2 text-sm" />
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-wrap gap-2">
                        <x-button type="submit" variant="primary">{{ __('Submit review') }}</x-button>
                        <x-button href="{{ route('dashboard.reviews.index') }}" variant="secondary">{{ __('Back') }}</x-button>
                    </div>
                </form>
            @endcan
            @cannot('review', $examSubmission)
                <p class="mt-5 text-sm text-gray-500">{{ __('This submission is not open for moderation updates from your account right now.') }}</p>
                <div class="mt-4">
                    <x-button href="{{ route('dashboard.reviews.index') }}" variant="secondary">{{ __('Back') }}</x-button>
                </div>
            @endcannot
        </div>
    </div>
@endsection
