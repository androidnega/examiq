@extends('layouts.app', ['header' => __('Submission')])

@section('content')
    <div class="w-full space-y-6">
        <div class="rounded-xl border border-gray-200 bg-white p-6 text-sm">
            <h2 class="text-base font-semibold text-gray-900">{{ __('Submission overview') }}</h2>
            <dl class="mt-4 grid gap-3 sm:grid-cols-2">
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
                    <dt class="text-gray-500">{{ __('Current version') }}</dt>
                    <dd class="font-medium text-gray-900">{{ $examSubmission->current_version }}</dd>
                </div>
                <div>
                    <dt class="text-gray-500">{{ __('Status') }}</dt>
                    <dd class="mt-1"><x-badge :value="$examSubmission->status" /></dd>
                </div>
            </dl>
        </div>

        <div class="rounded-xl border border-gray-200 bg-white p-6">
            <h2 class="text-sm font-semibold text-gray-900">{{ __('Version history & files') }}</h2>
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
                <p class="mt-3 text-sm text-gray-500">{{ __('No files on record.') }}</p>
            @endforelse
        </div>

        <div class="rounded-xl border border-gray-200 bg-white p-6">
            <h2 class="text-sm font-semibold text-gray-900">{{ __('Moderator assignments') }}</h2>
            <ul class="mt-3 space-y-2">
                @forelse ($moderationAssignments as $moderationAssignment)
                    <li class="rounded-lg border border-gray-100 bg-gray-50 px-3 py-2 text-sm">
                        <span class="font-medium text-gray-900">{{ $moderationAssignment->moderator?->name }}</span>
                        <span class="mt-0.5 block text-xs text-gray-500">{{ __('Assigned :date', ['date' => $moderationAssignment->assigned_at?->format('M j, Y')]) }}</span>
                    </li>
                @empty
                    <li class="text-sm text-gray-500">{{ __('None.') }}</li>
                @endforelse
            </ul>
        </div>

        <div class="rounded-xl border border-gray-200 bg-white p-6">
            <h2 class="text-sm font-semibold text-gray-900">{{ __('Moderation feedback') }}</h2>
            <ul class="mt-3 space-y-3">
                @forelse ($moderationReviews as $moderationReview)
                    <li class="rounded-lg border border-gray-100 bg-gray-50 px-3 py-3 text-sm">
                        <div class="flex flex-wrap items-center justify-between gap-2">
                            <span class="font-medium text-gray-900">{{ $moderationReview->moderator?->name }}</span>
                            <x-badge :value="$moderationReview->status" />
                        </div>
                        @if ($moderationReview->feedback)
                            <p class="mt-2 text-gray-700">{{ $moderationReview->feedback }}</p>
                        @endif
                    </li>
                @empty
                    <li class="text-sm text-gray-500">{{ __('No reviews recorded.') }}</li>
                @endforelse
            </ul>
        </div>

        <div class="rounded-xl border border-gray-200 bg-white p-6">
            <h2 class="text-sm font-semibold text-gray-900">{{ __('Revision notes') }}</h2>
            <ul class="mt-3 space-y-3">
                @forelse ($submissionRevisions as $submissionRevision)
                    <li class="rounded-lg border border-gray-100 bg-gray-50 px-3 py-3 text-sm">
                        <p class="text-xs text-gray-500">{{ $submissionRevision->lecturer?->name }} · {{ $submissionRevision->created_at?->format('M j, Y g:i a') }}</p>
                        <p class="mt-1 text-gray-800">{{ $submissionRevision->notes }}</p>
                        <div class="mt-3 rounded-lg border border-gray-200 bg-white px-3 py-3">
                            <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('Internal examiner compliance') }}</p>
                            <dl class="mt-2 grid gap-2 text-xs text-gray-700 sm:grid-cols-2">
                                <div>
                                    <dt class="text-gray-500">{{ __('Date received') }}</dt>
                                    <dd class="font-medium text-gray-900">{{ $submissionRevision->received_from_moderator_on?->format('M j, Y') ?? __('Not provided') }}</dd>
                                </div>
                                <div>
                                    <dt class="text-gray-500">{{ __('Moderator comment') }}</dt>
                                    <dd class="font-medium text-gray-900">
                                        {{ match ($submissionRevision->moderator_general_comment) {
                                            'minor_corrections' => __('Accepted with minor corrections'),
                                            'with_modifications' => __('Accepted with some modifications'),
                                            'rejected_new_questions' => __('Rejected; new questions to be set'),
                                            default => __('Not provided'),
                                        } }}
                                    </dd>
                                </div>
                            </dl>
                            <p class="mt-2 text-xs text-gray-500">{{ __('Documents received') }}</p>
                            <ul class="mt-1 list-disc space-y-0.5 pl-5 text-xs text-gray-700">
                                <li>{{ __('Moderated questions') }}: {{ $submissionRevision->received_moderated_questions ? __('Yes') : __('No') }}</li>
                                <li>{{ __('Moderated marked scheme') }}: {{ $submissionRevision->received_moderated_marking_scheme ? __('Yes') : __('No') }}</li>
                                <li>{{ __('Course outline') }}: {{ $submissionRevision->received_course_outline ? __('Yes') : __('No') }}</li>
                                <li>{{ __('Moderator comment sheet') }}: {{ $submissionRevision->received_moderator_comment_sheet ? __('Yes') : __('No') }}</li>
                            </ul>
                            <p class="mt-2 text-xs text-gray-500">{{ __('Action taken') }}</p>
                            <p class="mt-0.5 text-sm text-gray-800">{{ $submissionRevision->response_action_taken ?? __('Not provided') }}</p>
                        </div>
                    </li>
                @empty
                    <li class="text-sm text-gray-500">{{ __('No revision notes yet.') }}</li>
                @endforelse
            </ul>
        </div>

        @if ($canApprove || $canReject)
            <div class="rounded-xl border border-gray-200 bg-white p-6">
                <h2 class="text-sm font-semibold text-gray-900">{{ __('Final decision') }}</h2>
                <p class="mt-1 text-xs text-gray-500">{{ __('Approving releases this pack to the exam officer. Rejecting closes the submission.') }}</p>
                <div class="mt-4 flex flex-col gap-3 sm:flex-row sm:flex-wrap">
                    @if ($canApprove)
                        <form method="post" action="{{ route('dashboard.department.approve', $examSubmission) }}">
                            @csrf
                            <x-button type="submit" variant="primary" class="border-emerald-700 bg-emerald-700 text-white hover:bg-emerald-800">{{ __('Approve') }}</x-button>
                        </form>
                    @endif
                    @if ($canReject)
                        <form method="post" action="{{ route('dashboard.department.reject', $examSubmission) }}" class="flex flex-1 flex-col gap-2 sm:max-w-md">
                            @csrf
                            <label for="rejection_notes" class="text-xs font-medium text-gray-600">{{ __('Rejection notes (optional)') }}</label>
                            <textarea id="rejection_notes" name="rejection_notes" rows="2" class="rounded-xl border border-gray-200 px-3 py-2 text-sm">{{ old('rejection_notes') }}</textarea>
                            <x-button type="submit" variant="secondary" class="border-red-300 bg-red-50 text-red-800 hover:bg-red-100">{{ __('Reject') }}</x-button>
                        </form>
                    @endif
                </div>
            </div>
        @endif

        <div class="flex flex-wrap gap-2">
            <x-button href="{{ route('dashboard.submissions.moderation-forms.index', $examSubmission) }}" variant="secondary">{{ __('Moderation forms') }}</x-button>
            <x-button href="{{ route('dashboard.approvals.index') }}" variant="secondary">{{ __('Approvals queue') }}</x-button>
            <x-button href="{{ route('dashboard.department.index') }}" variant="ghost">{{ __('All submissions') }}</x-button>
            <x-button href="{{ route('dashboard') }}" variant="ghost">{{ __('Dashboard') }}</x-button>
        </div>
    </div>
@endsection
