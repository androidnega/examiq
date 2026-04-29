@extends('layouts.app', ['header' => __('My submissions')])

@section('content')
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <p class="text-sm text-gray-500">{{ __('All examination packs you have created.') }}</p>
        <x-button href="{{ route('dashboard.submissions.create') }}" variant="primary">{{ __('New submission') }}</x-button>
    </div>

    <x-table>
        <thead class="bg-gray-50 text-xs font-medium uppercase tracking-wide text-gray-500">
            <tr>
                <th class="px-4 py-3">{{ __('Course') }}</th>
                <th class="px-4 py-3">{{ __('Session') }}</th>
                <th class="px-4 py-3">{{ __('Version') }}</th>
                <th class="px-4 py-3">{{ __('Status') }}</th>
                <th class="px-4 py-3 text-right">{{ __('Open') }}</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse ($submissions as $row)
                <tr class="bg-white">
                    <td class="px-4 py-3 font-medium text-gray-900">
                        {{ $row->course?->code }} — {{ $row->course?->name }}
                    </td>
                    <td class="px-4 py-3 text-gray-600">{{ $row->academic_year }} · {{ $row->semester }}</td>
                    <td class="px-4 py-3 tabular-nums text-gray-700">{{ $row->current_version }}</td>
                    <td class="px-4 py-3">
                        <x-badge :value="$row->status" />
                    </td>
                    <td class="px-4 py-3 text-right">
                        <x-button href="{{ route('dashboard.submissions.show', $row) }}" variant="ghost">{{ __('View') }}</x-button>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="px-4 py-10 text-center text-sm text-gray-500">{{ __('No submissions yet.') }}</td>
                </tr>
            @endforelse
        </tbody>
    </x-table>

    <div class="mt-4">
        {{ $submissions->links() }}
    </div>
@endsection
