@extends('layouts.app', ['header' => __('Moderation forms')])

@section('content')
    <div class="w-full space-y-6">
        <div class="rounded-xl border border-gray-200 bg-white p-6">
            <h2 class="text-sm font-semibold text-gray-900">{{ $submission->course?->code }} — {{ $submission->course?->name }}</h2>
            <p class="mt-1 text-sm text-gray-500">{{ __('Lecturer') }}: {{ $submission->lecturer?->name }}</p>
        </div>

        <div class="rounded-xl border border-gray-200 bg-white p-6">
            <h3 class="text-sm font-semibold text-gray-900">{{ __('Available moderation forms') }}</h3>
            <ul class="mt-4 space-y-2">
                @forelse ($moderations as $moderation)
                    <li class="flex flex-wrap items-center justify-between gap-2 rounded-lg border border-gray-100 bg-gray-50 px-3 py-3 text-sm">
                        <div>
                            <p class="font-medium text-gray-900">{{ $moderation->moderator?->name ?? __('Moderator') }}</p>
                            <p class="text-xs text-gray-500">{{ __('Outcome') }}: {{ str($moderation->status->value)->replace('_', ' ')->title() }}</p>
                        </div>
                        <x-button href="{{ route('dashboard.submissions.moderation-forms.print', [$submission, $moderation]) }}" variant="secondary">{{ __('Open print view') }}</x-button>
                    </li>
                @empty
                    <li class="text-sm text-gray-500">{{ __('No moderation form has been submitted yet.') }}</li>
                @endforelse
            </ul>
        </div>
    </div>
@endsection
