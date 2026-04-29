@extends('layouts.app', ['header' => __('System Roles')])

@section('content')
    <p class="mb-6 text-sm text-slate-500">{{ __('Built-in roles and how many users hold each. Role values are fixed in code for RBAC.') }}</p>

    <x-table>
        <thead class="bg-slate-50 text-xs font-semibold uppercase tracking-wide text-slate-500">
            <tr>
                <th class="px-4 py-3">{{ __('Role') }}</th>
                <th class="px-4 py-3">{{ __('Key') }}</th>
                <th class="px-4 py-3">{{ __('Users') }}</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            @foreach ($roles as $row)
                <tr class="bg-white">
                    <td class="px-4 py-3 font-medium text-slate-900">{{ ucfirst(str_replace('_', ' ', $row['value'])) }}</td>
                    <td class="px-4 py-3 font-mono text-xs text-slate-600">{{ $row['value'] }}</td>
                    <td class="px-4 py-3 tabular-nums text-slate-700">{{ $row['users_count'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </x-table>
@endsection
