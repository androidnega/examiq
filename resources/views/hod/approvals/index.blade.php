@extends('layouts.app', ['header' => __('Final approvals')])

@section('content')
    <p class="mb-6 text-sm text-gray-500">{{ __('Submissions ready for your sign-off after moderation.') }}</p>

    <x-table>
        <thead class="bg-gray-50 text-xs font-medium uppercase tracking-wide text-gray-500">
            <tr>
                <th class="px-4 py-3">{{ __('Course') }}</th>
                <th class="px-4 py-3">{{ __('Lecturer') }}</th>
                <th class="px-4 py-3">{{ __('Version') }}</th>
                <th class="px-4 py-3 text-right">{{ __('Open') }}</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse ($examSubmissions as $examSubmission)
                <tr class="bg-white">
                    <td class="px-4 py-3 font-medium text-gray-900">
                        {{ $examSubmission->course?->code }} — {{ $examSubmission->course?->name }}
                    </td>
                    <td class="px-4 py-3 text-gray-700">{{ $examSubmission->lecturer?->name }}</td>
                    <td class="px-4 py-3 tabular-nums text-gray-700">{{ $examSubmission->current_version }}</td>
                    <td class="px-4 py-3 text-right">
                        <x-button href="{{ route('dashboard.department.show', $examSubmission) }}" variant="ghost">{{ __('Review') }}</x-button>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="px-4 py-8 text-center text-sm text-gray-500">{{ __('Nothing awaiting approval right now.') }}</td>
                </tr>
            @endforelse
        </tbody>
    </x-table>

    <div class="mt-4">
        {{ $examSubmissions->links() }}
    </div>
@endsection
