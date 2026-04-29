@extends('layouts.app', ['header' => __('Blocked Users')])

@section('content')
    <p class="mb-6 text-sm text-slate-500">{{ __('Accounts that cannot sign in. Unblock to restore access.') }}</p>

    <x-table>
        <thead class="bg-slate-50 text-xs font-semibold uppercase tracking-wide text-slate-500">
            <tr>
                <th class="px-4 py-3">{{ __('Name') }}</th>
                <th class="px-4 py-3">{{ __('Phone') }}</th>
                <th class="px-4 py-3">{{ __('Role') }}</th>
                <th class="px-4 py-3">{{ __('Department') }}</th>
                <th class="px-4 py-3 text-right">{{ __('Actions') }}</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            @forelse ($users as $row)
                <tr class="bg-white">
                    <td class="px-4 py-3 font-medium text-slate-900">{{ $row->name }}</td>
                    <td class="px-4 py-3 text-slate-700">{{ $row->phone }}</td>
                    <td class="px-4 py-3"><x-badge :value="$row->role" /></td>
                    <td class="px-4 py-3 text-slate-600">{{ $row->department?->name ?? '—' }}</td>
                    <td class="px-4 py-3 text-right">
                        <form method="post" action="{{ route('dashboard.users.unblock', $row) }}" class="inline">
                            @csrf
                            <x-button type="submit" variant="primary">{{ __('Unblock') }}</x-button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="px-4 py-8 text-center text-sm text-slate-500">{{ __('No blocked users.') }}</td>
                </tr>
            @endforelse
        </tbody>
    </x-table>

    <div class="mt-4">
        {{ $users->links() }}
    </div>
@endsection
