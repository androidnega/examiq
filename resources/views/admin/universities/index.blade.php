@extends('layouts.app', ['header' => __('Universities')])

@section('content')
    @if ($errors->has('delete'))
        <div class="mb-4 rounded-lg border border-red-100 bg-red-50 px-4 py-3 text-sm text-red-800">{{ $errors->first('delete') }}</div>
    @endif

    <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
        <p class="text-sm text-slate-500">{{ __('Top-level institutions.') }}</p>
        <x-button href="{{ route('dashboard.universities.create') }}" variant="primary">{{ __('Add university') }}</x-button>
    </div>

    <x-table>
        <thead class="bg-slate-50 text-xs font-semibold uppercase tracking-wide text-slate-500">
            <tr>
                <th class="px-4 py-3">{{ __('Name') }}</th>
                <th class="px-4 py-3">{{ __('Faculties') }}</th>
                <th class="px-4 py-3 text-right">{{ __('Actions') }}</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            @forelse ($universities as $row)
                <tr class="bg-white">
                    <td class="px-4 py-3 font-medium text-slate-900">{{ $row->name }}</td>
                    <td class="px-4 py-3 tabular-nums text-slate-700">{{ $row->faculties_count }}</td>
                    <td class="px-4 py-3 text-right">
                        <x-button href="{{ route('dashboard.universities.edit', $row) }}" variant="ghost">{{ __('Edit') }}</x-button>
                        <form method="post" action="{{ route('dashboard.universities.destroy', $row) }}" class="inline" onsubmit="return confirm(@js(__('Delete this university?')));">
                            @csrf
                            @method('DELETE')
                            <x-button type="submit" variant="secondary">{{ __('Delete') }}</x-button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" class="px-4 py-8 text-center text-sm text-slate-500">{{ __('No universities yet.') }}</td>
                </tr>
            @endforelse
        </tbody>
    </x-table>

    <div class="mt-4">
        {{ $universities->links() }}
    </div>
@endsection
