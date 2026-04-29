@extends('layouts.app', ['header' => __('Submission')])

@section('content')
    <div class="w-full space-y-6">
        <div class="rounded-xl border border-gray-200 bg-white p-6">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                <div>
                    <h2 class="text-base font-semibold text-gray-900">{{ $submission->course?->code }} — {{ $submission->course?->name }}</h2>
                    <p class="mt-1 text-sm text-gray-500">{{ $submission->academic_year }} · {{ $submission->semester }} · {{ trans_choice(':count student|:count students', $submission->students_count, ['count' => $submission->students_count]) }}</p>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    <span class="text-xs font-medium text-gray-500">{{ __('Current version') }}</span>
                    <span class="rounded-lg border border-gray-200 bg-gray-50 px-2.5 py-1 text-sm font-semibold tabular-nums text-gray-900">{{ $submission->current_version }}</span>
                    <x-badge :value="$submission->status" />
                </div>
            </div>
            @if ($canRevise ?? false)
                <div class="mt-6 border-t border-gray-100 pt-4">
                    <x-button href="{{ route('dashboard.submissions.edit', $submission) }}" variant="primary">{{ __('Submit revision') }}</x-button>
                </div>
            @elseif ($canUpdate ?? false)
                <div class="mt-6 border-t border-gray-100 pt-4">
                    <x-button href="{{ route('dashboard.submissions.update.edit', $submission) }}" variant="primary">{{ __('Update submission') }}</x-button>
                </div>
            @endif
        </div>

        <div class="rounded-xl border border-gray-200 bg-white p-6">
            <h3 class="text-sm font-semibold text-gray-900">{{ __('Version history & files') }}</h3>
            <p class="mt-1 text-xs text-gray-500">{{ __('Filenames are shown for your reference. Files are not downloadable from this screen.') }}</p>

            @forelse ($filesByVersion as $version => $files)
                <div class="mt-6 border-t border-gray-100 pt-6 first:mt-4 first:border-t-0 first:pt-0">
                    <h4 class="text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('Version') }} {{ $version }}</h4>
                    <ul class="mt-3 space-y-2 text-sm text-gray-700">
                        @foreach ($files as $file)
                            <li class="flex flex-col gap-0.5 rounded-lg border border-gray-100 bg-gray-50 px-3 py-2 sm:flex-row sm:items-center sm:justify-between">
                                <span class="font-medium text-gray-900">{{ $file->type->label() }}</span>
                                <span class="text-xs text-gray-500">{{ $file->original_name ?? __('Stored document') }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @empty
                <p class="mt-4 text-sm text-gray-500">{{ __('No files uploaded yet.') }}</p>
            @endforelse
        </div>

        <div class="flex flex-wrap gap-2">
            <x-button href="{{ route('dashboard.submissions.moderation-forms.index', $submission) }}" variant="primary">{{ __('Moderation forms') }}</x-button>
            <x-button href="{{ route('dashboard.submissions.index') }}" variant="outline">{{ __('Back to list') }}</x-button>
        </div>
    </div>
@endsection
