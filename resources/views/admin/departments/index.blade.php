@extends('layouts.app', ['header' => __('Departments')])

@section('content')
    @if ($errors->has('delete'))
        <div class="mb-4 rounded-lg border border-red-100 bg-red-50 px-4 py-3 text-sm text-red-800">{{ $errors->first('delete') }}</div>
    @endif

    <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
        <p class="text-sm text-slate-500">{{ __('Departments belong to a faculty.') }}</p>
        <x-button href="{{ route('dashboard.departments.create') }}" variant="primary">{{ __('Add department') }}</x-button>
    </div>

    <x-table>
        <thead class="bg-slate-50 text-xs font-semibold uppercase tracking-wide text-slate-500">
            <tr>
                <th class="px-4 py-3">{{ __('Name') }}</th>
                <th class="px-4 py-3">{{ __('Faculty / University') }}</th>
                <th class="px-4 py-3">{{ __('Users') }}</th>
                <th class="px-4 py-3">{{ __('Courses') }}</th>
                <th class="px-4 py-3 text-right">{{ __('Actions') }}</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            @forelse ($departments as $row)
                <tr class="bg-white">
                    <td class="px-4 py-3 font-medium text-slate-900">{{ $row->name }}</td>
                    <td class="px-4 py-3 text-slate-700">
                        {{ $row->faculty?->name ?? '—' }}
                        @if ($row->faculty?->university)
                            <span class="text-slate-400">· {{ $row->faculty->university->name }}</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 tabular-nums text-slate-700">{{ $row->users_count }}</td>
                    <td class="px-4 py-3 tabular-nums text-slate-700">{{ $row->courses_count }}</td>
                    <td class="px-4 py-3 text-right">
                        <x-button href="{{ route('dashboard.departments.edit', $row) }}" variant="ghost">{{ __('Edit') }}</x-button>
                        <form method="post" action="{{ route('dashboard.departments.destroy', $row) }}" class="inline" onsubmit="return confirm(@js(__('Delete this department?')));">
                            @csrf
                            @method('DELETE')
                            <x-button type="submit" variant="secondary">{{ __('Delete') }}</x-button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="px-4 py-8 text-center text-sm text-slate-500">{{ __('No departments yet.') }}</td>
                </tr>
            @endforelse
        </tbody>
    </x-table>

    <div class="mt-4">
        {{ $departments->links() }}
    </div>
@endsection
