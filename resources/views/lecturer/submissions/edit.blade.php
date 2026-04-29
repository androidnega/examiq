@extends('layouts.app', ['header' => __('Submit revision')])

@section('content')
    <div class="w-full space-y-8">
        <p class="text-sm text-gray-500">{{ __('Address moderator feedback, upload corrected PDFs, and explain your changes. Your HOD will review this final version.') }}</p>

        <div class="rounded-xl border border-gray-200 bg-white p-6">
            <h2 class="text-sm font-semibold text-gray-900">{{ __('Moderation feedback') }}</h2>
            <p class="mt-1 text-xs text-gray-500">{{ __('Read-only comments from assigned moderators.') }}</p>
            <ul class="mt-4 space-y-3">
                @forelse ($moderationReviews as $moderationReview)
                    <li class="rounded-lg border border-gray-100 bg-gray-50 px-3 py-3 text-sm">
                        <div class="flex flex-wrap items-center justify-between gap-2">
                            <span class="font-medium text-gray-900">{{ $moderationReview->moderator?->name }}</span>
                            <x-badge :value="$moderationReview->status" />
                        </div>
                        @if ($moderationReview->feedback)
                            <p class="mt-2 text-gray-700">{{ $moderationReview->feedback }}</p>
                        @else
                            <p class="mt-2 text-gray-500">{{ __('No written feedback.') }}</p>
                        @endif
                    </li>
                @empty
                    <li class="text-sm text-gray-500">{{ __('No moderation records found.') }}</li>
                @endforelse
            </ul>
        </div>

        <div class="rounded-xl border border-gray-200 bg-white p-6">
            <h2 class="text-sm font-semibold text-gray-900">{{ __('Current files on record') }}</h2>
            <p class="mt-1 text-xs text-gray-500">{{ __('You will replace these with new PDFs below.') }}</p>
            @forelse ($filesByVersion as $version => $files)
                <div class="mt-4 border-t border-gray-100 pt-4 first:mt-3 first:border-t-0 first:pt-0">
                    <h3 class="text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('Version') }} {{ $version }}</h3>
                    <ul class="mt-2 space-y-1 text-sm text-gray-700">
                        @foreach ($files as $file)
                            <li>{{ $file->type->label() }} — {{ $file->original_name ?? __('document') }}</li>
                        @endforeach
                    </ul>
                </div>
            @empty
                <p class="mt-3 text-sm text-gray-500">{{ __('No files yet.') }}</p>
            @endforelse
        </div>

        <form
            action="{{ route('dashboard.submissions.revise.update', $examSubmission) }}"
            method="post"
            enctype="multipart/form-data"
            class="space-y-8"
            data-ajax-submit="true"
            data-form-wizard="true"
        >
            @csrf
            @method('PUT')

            <div data-form-errors class="hidden rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800" role="alert"></div>
            <p data-wizard-indicator class="text-xs font-semibold uppercase tracking-wide text-gray-500"></p>

            <div class="h-1 w-full overflow-hidden rounded-full bg-gray-100" aria-hidden="true">
                <div class="js-upload-progress h-1 w-0 rounded-full bg-gray-900 transition-[width] duration-150"></div>
            </div>

            <div data-wizard-step class="space-y-8">
                <div class="rounded-xl border border-gray-200 bg-white p-6">
                    <h2 class="text-sm font-semibold text-gray-900">{{ __('Submission details') }}</h2>
                    <div class="mt-5 grid gap-5 sm:grid-cols-2">
                        <div>
                            <label for="academic_year" class="block text-sm font-medium text-gray-700">{{ __('Academic year') }}</label>
                            <select id="academic_year" name="academic_year" required class="mt-1 w-full rounded-xl border border-gray-200 bg-white px-3 py-2 text-sm focus:border-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-200">
                                <option value="">{{ __('Select academic year') }}</option>
                                @foreach (($academicYearOptions ?? []) as $academicYearOption)
                                    <option value="{{ $academicYearOption }}" @selected(old('academic_year', $examSubmission->academic_year) === $academicYearOption)>{{ $academicYearOption }}</option>
                                @endforeach
                            </select>
                            @error('academic_year')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="semester" class="block text-sm font-medium text-gray-700">{{ __('Semester') }}</label>
                            <select id="semester" name="semester" required class="mt-1 w-full rounded-xl border border-gray-200 bg-white px-3 py-2 text-sm focus:border-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-200">
                                <option value="">{{ __('Select semester') }}</option>
                                @foreach (($semesterOptions ?? []) as $semesterOption)
                                    <option value="{{ $semesterOption }}" @selected(old('semester', $examSubmission->semester) === $semesterOption)>{{ ucfirst($semesterOption) }}</option>
                                @endforeach
                            </select>
                            @error('semester')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="sm:col-span-2">
                            <label for="students_count" class="block text-sm font-medium text-gray-700">{{ __('Number of students') }}</label>
                            <input id="students_count" type="number" name="students_count" min="0" value="{{ old('students_count', $examSubmission->students_count) }}" required class="mt-1 w-full max-w-xs rounded-xl border border-gray-200 px-3 py-2 text-sm focus:border-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-200" />
                            @error('students_count')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="rounded-xl border border-gray-200 bg-white p-6">
                    <h2 class="text-sm font-semibold text-gray-900">{{ __('Revision notes') }}</h2>
                    <p class="mt-1 text-xs text-gray-500">{{ __('Summarize what you changed for the HOD.') }}</p>
                    <textarea name="revision_notes" rows="4" required class="mt-3 w-full rounded-xl border border-gray-200 px-3 py-2 text-sm focus:border-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-200" placeholder="{{ __('e.g. Updated questions 3–5 per moderator comments; refreshed marking rubric.') }}">{{ old('revision_notes') }}</textarea>
                    @error('revision_notes')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex flex-wrap gap-3">
                    <x-button type="button" variant="primary" data-wizard-next>{{ __('Next') }}</x-button>
                    <x-button href="{{ route('dashboard.submissions.show', $examSubmission) }}" variant="secondary">{{ __('Cancel') }}</x-button>
                </div>
            </div>

            <div data-wizard-step class="hidden space-y-8">
                <div class="rounded-xl border border-gray-200 bg-white p-6">
                    <h2 class="text-sm font-semibold text-gray-900">{{ __('Internal examiner compliance details') }}</h2>
                    <p class="mt-1 text-xs text-gray-500">{{ __('Capture the same information provided in the paper compliance form.') }}</p>

                    <div class="mt-5 space-y-5">
                        <div>
                            <p class="text-sm font-medium text-gray-800">{{ __('Documents received from moderator') }}</p>
                            <div class="mt-3 grid gap-3 sm:grid-cols-2">
                                @foreach ([
                                    'received_moderated_questions' => __('Moderated questions'),
                                    'received_moderated_marking_scheme' => __('Moderated marked scheme'),
                                    'received_course_outline' => __('Course outline'),
                                    'received_moderator_comment_sheet' => __('Moderator comment sheet'),
                                ] as $name => $label)
                                    <label class="inline-flex items-center gap-2 text-sm text-gray-700">
                                        <input type="hidden" name="{{ $name }}" value="0" />
                                        <input
                                            type="checkbox"
                                            name="{{ $name }}"
                                            value="1"
                                            class="rounded border-gray-300 text-gray-900 focus:ring-gray-300"
                                            @checked((bool) old($name))
                                        />
                                        <span>{{ $label }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <div class="grid gap-5 sm:grid-cols-2">
                            <div>
                                <label for="received_from_moderator_on" class="block text-sm font-medium text-gray-700">{{ __('Date received from moderator / head of assurance') }}</label>
                                <input id="received_from_moderator_on" type="date" name="received_from_moderator_on" value="{{ old('received_from_moderator_on') }}" required class="mt-1 w-full rounded-xl border border-gray-200 px-3 py-2 text-sm focus:border-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-200" />
                                @error('received_from_moderator_on')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="moderator_general_comment" class="block text-sm font-medium text-gray-700">{{ __('Moderator general comment') }}</label>
                                <select id="moderator_general_comment" name="moderator_general_comment" required class="mt-1 w-full rounded-xl border border-gray-200 px-3 py-2 text-sm focus:border-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-200">
                                    <option value="">{{ __('Select…') }}</option>
                                    <option value="minor_corrections" @selected(old('moderator_general_comment') === 'minor_corrections')>{{ __('Accepted with minor corrections') }}</option>
                                    <option value="with_modifications" @selected(old('moderator_general_comment') === 'with_modifications')>{{ __('Accepted with some modifications') }}</option>
                                    <option value="rejected_new_questions" @selected(old('moderator_general_comment') === 'rejected_new_questions')>{{ __('Rejected; new questions to be set') }}</option>
                                </select>
                                @error('moderator_general_comment')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div>
                            <label for="response_action_taken" class="block text-sm font-medium text-gray-700">{{ __('Your response(s) to the comment and action taken') }}</label>
                            <textarea id="response_action_taken" name="response_action_taken" rows="4" required class="mt-1 w-full rounded-xl border border-gray-200 px-3 py-2 text-sm focus:border-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-200">{{ old('response_action_taken') }}</textarea>
                            @error('response_action_taken')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="flex flex-wrap gap-3">
                    <x-button type="button" variant="secondary" data-wizard-prev>{{ __('Back') }}</x-button>
                    <x-button type="button" variant="primary" data-wizard-next>{{ __('Next') }}</x-button>
                </div>
            </div>

            <div data-wizard-step class="hidden space-y-8">
                <div class="rounded-xl border border-gray-200 bg-white p-6">
                    <h2 class="text-sm font-semibold text-gray-900">{{ __('New PDFs (replace current set)') }}</h2>
                    <p class="mt-1 text-xs text-gray-500">{{ __('PDF only, max 1 MB each.') }}</p>
                    <div class="mt-5 space-y-5">
                        @foreach ([
                            'file_questions' => __('Exam questions'),
                            'file_marking_scheme' => __('Marking scheme'),
                            'file_outline' => __('Course outline'),
                        ] as $name => $label)
                            <div>
                                <p class="text-sm font-medium text-gray-800">{{ $label }} <span class="text-red-600">*</span></p>
                                <div class="js-dropzone mt-2 rounded-xl border border-dashed border-gray-300 bg-gray-50 px-4 py-6 text-center transition-colors hover:border-gray-400">
                                    <input type="file" name="{{ $name }}" id="{{ $name }}_input" accept="application/pdf,.pdf" class="sr-only" />
                                    <label for="{{ $name }}_input" class="cursor-pointer text-sm text-gray-600">
                                        <span class="js-dropzone-label font-medium text-gray-700" data-default="{{ __('Drop PDF here or click to browse') }}">{{ __('Drop PDF here or click to browse') }}</span>
                                        <span class="mt-1 block text-xs text-gray-500">{{ __('Max 1 MB') }}</span>
                                    </label>
                                </div>
                                @error($name)
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        @endforeach
                        <div>
                            <p class="text-sm font-medium text-gray-800">{{ __('Supporting document') }} <span class="text-gray-400">({{ __('optional') }})</span></p>
                            <div class="js-dropzone mt-2 rounded-xl border border-dashed border-gray-300 bg-gray-50 px-4 py-6 text-center transition-colors hover:border-gray-400">
                                <input type="file" name="file_supporting" id="file_supporting_input" accept="application/pdf,.pdf" class="sr-only" />
                                <label for="file_supporting_input" class="cursor-pointer text-sm text-gray-600">
                                    <span class="js-dropzone-label font-medium text-gray-700" data-default="{{ __('Drop PDF here or click to browse') }}">{{ __('Drop PDF here or click to browse') }}</span>
                                    <span class="mt-1 block text-xs text-gray-500">{{ __('Max 1 MB') }}</span>
                                </label>
                            </div>
                            @error('file_supporting')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="flex flex-wrap gap-3">
                    <x-button type="button" variant="secondary" data-wizard-prev>{{ __('Back') }}</x-button>
                    <x-button type="submit" variant="primary" class="js-submit-btn">{{ __('Submit revision') }}</x-button>
                    <x-button href="{{ route('dashboard.submissions.show', $examSubmission) }}" variant="secondary">{{ __('Cancel') }}</x-button>
                </div>
            </div>
        </form>
    </div>
@endsection
