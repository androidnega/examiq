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
                <form method="post" action="{{ route('dashboard.reviews.store', $examSubmission) }}" class="mt-5 space-y-4" data-form-wizard="true">
                    @csrf
                    <p data-wizard-indicator class="text-xs font-semibold uppercase tracking-wide text-gray-500"></p>
                    <div data-wizard-step class="space-y-4">
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700">{{ __('Outcome') }}</label>
                            <select id="status" name="status" required class="mt-1 w-full rounded-xl border border-gray-200 px-3 py-2 text-sm focus:border-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-200">
                                <option value="">{{ __('Select…') }}</option>
                                <option value="accepted" @selected(old('status', $moderationReview?->status->value) === 'accepted')>{{ __('Accepted') }}</option>
                                <option value="minor_changes" @selected(old('status', $moderationReview?->status->value) === 'minor_changes')>{{ __('Minor changes') }}</option>
                                <option value="major_changes" @selected(old('status', $moderationReview?->status->value) === 'major_changes')>{{ __('Major changes') }}</option>
                                <option value="rejected" @selected(old('status', $moderationReview?->status->value) === 'rejected')>{{ __('Rejected') }}</option>
                            </select>
                            @error('status')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="feedback" class="block text-sm font-medium text-gray-700">{{ __('Feedback') }}</label>
                            <textarea id="feedback" name="feedback" rows="4" class="mt-1 w-full rounded-xl border border-gray-200 px-3 py-2 text-sm focus:border-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-200" placeholder="{{ __('Required unless the outcome is Accepted.') }}">{{ old('feedback', $moderationReview?->feedback) }}</textarea>
                            @error('feedback')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="rounded-xl border border-gray-100 bg-gray-50 p-4">
                            <h3 class="text-sm font-semibold text-gray-900">{{ __('Moderation rubric') }}</h3>
                            <p class="mt-1 text-xs text-gray-500">{{ __('Select grades A-E for each rubric item.') }}</p>
                            <div class="mt-4 grid gap-3 sm:grid-cols-2">
                                @foreach (range(1, 11) as $rubricIndex)
                                    @php($field = 'rubric_'.$rubricIndex.'_grade')
                                    <div>
                                        <label for="{{ $field }}" class="block text-xs font-medium text-gray-700">{{ __('Rubric item :n', ['n' => $rubricIndex]) }}</label>
                                        <select id="{{ $field }}" name="{{ $field }}" required class="mt-1 w-full rounded-xl border border-gray-200 bg-white px-3 py-2 text-sm focus:border-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-200">
                                            <option value="">{{ __('Select grade') }}</option>
                                            @foreach (['A', 'B', 'C', 'D', 'E'] as $grade)
                                                <option value="{{ $grade }}" @selected(old($field, $moderationReview?->{$field}) === $grade)>{{ $grade }}</option>
                                            @endforeach
                                        </select>
                                        @error($field)
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        <div class="flex flex-wrap gap-2">
                            <x-button type="button" variant="primary" data-wizard-next>{{ __('Next') }}</x-button>
                            <x-button href="{{ route('dashboard.reviews.index') }}" variant="secondary">{{ __('Back') }}</x-button>
                        </div>
                    </div>

                    <div data-wizard-step class="hidden space-y-4">
                        <div class="grid gap-4 sm:grid-cols-3">
                            <div>
                                <label for="recommend_accept_questions" class="block text-sm font-medium text-gray-700">{{ __('Question numbers to accept') }}</label>
                                <input id="recommend_accept_questions" type="text" name="recommend_accept_questions" value="{{ old('recommend_accept_questions', $moderationReview?->recommend_accept_questions) }}" class="mt-1 w-full rounded-xl border border-gray-200 px-3 py-2 text-sm focus:border-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-200" placeholder="{{ __('e.g. all or 1,2,3') }}" />
                            </div>
                            <div>
                                <label for="recommend_reject_questions" class="block text-sm font-medium text-gray-700">{{ __('Question numbers to reject') }}</label>
                                <input id="recommend_reject_questions" type="text" name="recommend_reject_questions" value="{{ old('recommend_reject_questions', $moderationReview?->recommend_reject_questions) }}" class="mt-1 w-full rounded-xl border border-gray-200 px-3 py-2 text-sm focus:border-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-200" />
                            </div>
                            <div>
                                <label for="recommend_reset_questions" class="block text-sm font-medium text-gray-700">{{ __('Question numbers to re-set') }}</label>
                                <input id="recommend_reset_questions" type="text" name="recommend_reset_questions" value="{{ old('recommend_reset_questions', $moderationReview?->recommend_reset_questions) }}" class="mt-1 w-full rounded-xl border border-gray-200 px-3 py-2 text-sm focus:border-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-200" />
                            </div>
                        </div>
                        <div>
                            <label for="question_paper_comments" class="block text-sm font-medium text-gray-700">{{ __('Question paper comments') }}</label>
                            <textarea id="question_paper_comments" name="question_paper_comments" rows="3" class="mt-1 w-full rounded-xl border border-gray-200 px-3 py-2 text-sm focus:border-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-200">{{ old('question_paper_comments', $moderationReview?->question_paper_comments) }}</textarea>
                        </div>
                        <div>
                            <label for="marking_scheme_comments" class="block text-sm font-medium text-gray-700">{{ __('Marking scheme comments') }}</label>
                            <textarea id="marking_scheme_comments" name="marking_scheme_comments" rows="3" class="mt-1 w-full rounded-xl border border-gray-200 px-3 py-2 text-sm focus:border-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-200">{{ old('marking_scheme_comments', $moderationReview?->marking_scheme_comments) }}</textarea>
                        </div>
                        <div class="flex flex-wrap gap-2">
                            <x-button type="button" variant="secondary" data-wizard-prev>{{ __('Back') }}</x-button>
                            <x-button type="button" variant="primary" data-wizard-next>{{ __('Next') }}</x-button>
                        </div>
                    </div>

                    <div data-wizard-step class="hidden space-y-4">
                        <div class="grid gap-4 sm:grid-cols-3">
                            <div>
                                <label for="question_paper_assessment" class="block text-sm font-medium text-gray-700">{{ __('Question paper assessment') }}</label>
                                <select id="question_paper_assessment" name="question_paper_assessment" required class="mt-1 w-full rounded-xl border border-gray-200 px-3 py-2 text-sm focus:border-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-200">
                                    <option value="">{{ __('Select...') }}</option>
                                    <option value="accepted_without_corrections" @selected(old('question_paper_assessment', $moderationReview?->question_paper_assessment) === 'accepted_without_corrections')>{{ __('Accepted without corrections') }}</option>
                                    <option value="accepted_minor_corrections" @selected(old('question_paper_assessment', $moderationReview?->question_paper_assessment) === 'accepted_minor_corrections')>{{ __('Accepted with minor corrections') }}</option>
                                    <option value="accepted_with_modifications" @selected(old('question_paper_assessment', $moderationReview?->question_paper_assessment) === 'accepted_with_modifications')>{{ __('Accepted with some modifications') }}</option>
                                    <option value="rejected_new_questions" @selected(old('question_paper_assessment', $moderationReview?->question_paper_assessment) === 'rejected_new_questions')>{{ __('Rejected and new questions to be set') }}</option>
                                </select>
                            </div>
                            <div>
                                <label for="marking_scheme_assessment" class="block text-sm font-medium text-gray-700">{{ __('Marking scheme assessment') }}</label>
                                <select id="marking_scheme_assessment" name="marking_scheme_assessment" required class="mt-1 w-full rounded-xl border border-gray-200 px-3 py-2 text-sm focus:border-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-200">
                                    <option value="">{{ __('Select...') }}</option>
                                    <option value="accepted_all" @selected(old('marking_scheme_assessment', $moderationReview?->marking_scheme_assessment) === 'accepted_all')>{{ __('Accepted for all questions') }}</option>
                                    <option value="to_be_reprepared" @selected(old('marking_scheme_assessment', $moderationReview?->marking_scheme_assessment) === 'to_be_reprepared')>{{ __('To be re-prepared according to comments') }}</option>
                                </select>
                            </div>
                            <div>
                                <label for="overall_rating" class="block text-sm font-medium text-gray-700">{{ __('Overall rating') }}</label>
                                <select id="overall_rating" name="overall_rating" required class="mt-1 w-full rounded-xl border border-gray-200 px-3 py-2 text-sm focus:border-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-200">
                                    <option value="">{{ __('Select...') }}</option>
                                    <option value="excellent" @selected(old('overall_rating', $moderationReview?->overall_rating) === 'excellent')>{{ __('Excellent') }}</option>
                                    <option value="very_good" @selected(old('overall_rating', $moderationReview?->overall_rating) === 'very_good')>{{ __('Very Good') }}</option>
                                    <option value="good" @selected(old('overall_rating', $moderationReview?->overall_rating) === 'good')>{{ __('Good') }}</option>
                                    <option value="satisfactory" @selected(old('overall_rating', $moderationReview?->overall_rating) === 'satisfactory')>{{ __('Satisfactory') }}</option>
                                    <option value="unsatisfactory" @selected(old('overall_rating', $moderationReview?->overall_rating) === 'unsatisfactory')>{{ __('Unsatisfactory') }}</option>
                                </select>
                            </div>
                        </div>
                        <div>
                            <label for="improvement_comments" class="block text-sm font-medium text-gray-700">{{ __('Further comments for improvement') }}</label>
                            <textarea id="improvement_comments" name="improvement_comments" rows="3" class="mt-1 w-full rounded-xl border border-gray-200 px-3 py-2 text-sm focus:border-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-200">{{ old('improvement_comments', $moderationReview?->improvement_comments) }}</textarea>
                        </div>
                        <div>
                            <label for="moderated_on" class="block text-sm font-medium text-gray-700">{{ __('Moderation date') }}</label>
                            <input id="moderated_on" type="date" name="moderated_on" value="{{ old('moderated_on', optional($moderationReview?->moderated_on)->format('Y-m-d')) }}" required class="mt-1 w-full max-w-xs rounded-xl border border-gray-200 px-3 py-2 text-sm focus:border-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-200" />
                        </div>
                        <div class="flex flex-wrap gap-2">
                            <x-button type="button" variant="secondary" data-wizard-prev>{{ __('Back') }}</x-button>
                            <x-button type="submit" variant="primary">{{ __('Submit review') }}</x-button>
                            <x-button href="{{ route('dashboard.reviews.index') }}" variant="secondary">{{ __('Cancel') }}</x-button>
                        </div>
                    </div>
                </form>
            @else
                <p class="mt-5 text-sm text-gray-500">{{ __('This submission is not open for moderation updates from your account right now.') }}</p>
                <div class="mt-4">
                    <x-button href="{{ route('dashboard.reviews.index') }}" variant="secondary">{{ __('Back') }}</x-button>
                </div>
            @endcan
        </div>
    </div>
@endsection
