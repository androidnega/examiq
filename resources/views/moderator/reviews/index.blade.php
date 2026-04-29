@extends('layouts.app', ['header' => __('My reviews')])

@section('content')
    <p class="mb-6 text-sm text-gray-500">{{ __('Submissions you have been assigned to moderate.') }}</p>

    <x-table>
        <thead class="bg-gray-50 text-xs font-medium uppercase tracking-wide text-gray-500">
            <tr>
                <th class="px-4 py-3">{{ __('Course') }}</th>
                <th class="px-4 py-3">{{ __('Submission status') }}</th>
                <th class="px-4 py-3">{{ __('Your review') }}</th>
                <th class="px-4 py-3 text-right">{{ __('Open') }}</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse ($examSubmissions as $examSubmission)
                @php
                    $recordedReview = $examSubmission->moderations->first();
                @endphp
                <tr class="bg-white">
                    <td class="px-4 py-3 font-medium text-gray-900">
                        {{ $examSubmission->course?->code }} — {{ $examSubmission->course?->name }}
                    </td>
                    <td class="px-4 py-3">
                        <x-badge :value="$examSubmission->status" />
                    </td>
                    <td class="px-4 py-3">
                        @if ($recordedReview)
                            <x-badge :value="$recordedReview->status" />
                        @else
                            <span class="text-sm text-gray-500">{{ __('Pending') }}</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-right">
                        <x-button href="{{ route('dashboard.reviews.show', $examSubmission) }}" variant="ghost">{{ __('Review') }}</x-button>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="px-4 py-8 text-center text-sm text-gray-500">{{ __('You have no assigned submissions.') }}</td>
                </tr>
            @endforelse
        </tbody>
    </x-table>

    <div class="mt-4">
        {{ $examSubmissions->links() }}
    </div>
@endsection
