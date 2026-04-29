@extends('layouts.app', ['header' => __('Moderator dashboard')])

@section('content')
    <p class="mb-6 text-sm text-slate-500">{{ __('Work assigned to you by your HOD.') }}</p>

    <div class="grid gap-4 sm:grid-cols-2">
        <x-stat-card :label="__('Open assignments')" :value="$openAssignments" icon="clipboard" tint="amber" />
        <x-stat-card :label="__('Completed reviews')" :value="$completedReviews" icon="check" tint="emerald" />
    </div>

    <div class="mt-6 flex flex-wrap gap-2">
        <x-button href="{{ route('dashboard.reviews.index') }}" variant="primary">{{ __('Open review queue') }}</x-button>
    </div>

    <div class="mt-8">
        <h2 class="text-sm font-bold text-slate-900">{{ __('Recent assignments') }}</h2>
        <x-table class="mt-3">
            <thead class="bg-slate-50 text-xs font-semibold uppercase tracking-wide text-slate-500">
                <tr>
                    <th class="px-4 py-3">{{ __('Course') }}</th>
                    <th class="px-4 py-3">{{ __('Assigned') }}</th>
                    <th class="px-4 py-3 text-right">{{ __('Open') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($moderationAssignments as $moderationAssignment)
                    <tr class="bg-white">
                        <td class="px-4 py-3 font-medium text-slate-900">
                            {{ $moderationAssignment->examSubmission?->course?->code }} — {{ $moderationAssignment->examSubmission?->course?->name }}
                        </td>
                        <td class="px-4 py-3 text-slate-600">{{ $moderationAssignment->assigned_at?->format('M j, Y') }}</td>
                        <td class="px-4 py-3 text-right">
                            <x-button href="{{ route('dashboard.reviews.show', $moderationAssignment->examSubmission) }}" variant="ghost">{{ __('Review') }}</x-button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="px-4 py-8 text-center text-sm text-slate-500">{{ __('No assignments yet.') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </x-table>
        <div class="mt-4">
            {{ $moderationAssignments->links() }}
        </div>
    </div>
@endsection
