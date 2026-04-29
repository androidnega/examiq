@extends('layouts.app', ['header' => __('Security Logs')])

@section('content')
    <p class="mb-6 text-sm text-slate-500">{{ __('Authentication, blocks, and other security-related events only.') }}</p>

    <x-table>
        <thead class="bg-slate-50 text-xs font-semibold uppercase tracking-wide text-slate-500">
            <tr>
                <th class="px-4 py-3">{{ __('Time') }}</th>
                <th class="px-4 py-3">{{ __('User') }}</th>
                <th class="px-4 py-3">{{ __('Action') }}</th>
                <th class="px-4 py-3">{{ __('IP') }}</th>
                <th class="px-4 py-3">{{ __('Details') }}</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            @forelse ($logs as $log)
                <tr class="bg-white">
                    <td class="whitespace-nowrap px-4 py-3 text-slate-700">{{ $log->created_at?->format('Y-m-d H:i') }}</td>
                    <td class="px-4 py-3 text-slate-700">{{ $log->user?->name ?? '—' }}</td>
                    <td class="px-4 py-3 font-mono text-xs text-slate-800">{{ $log->action }}</td>
                    <td class="px-4 py-3 font-mono text-xs text-slate-600">{{ $log->ip_address ?? '—' }}</td>
                    <td class="max-w-xs truncate px-4 py-3 text-xs text-slate-500">
                        @if ($log->metadata)
                            <code title="{{ json_encode($log->metadata, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}">{{ json_encode($log->metadata) }}</code>
                        @else
                            —
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="px-4 py-8 text-center text-sm text-slate-500">{{ __('No security events yet.') }}</td>
                </tr>
            @endforelse
        </tbody>
    </x-table>

    <div class="mt-4">
        {{ $logs->links() }}
    </div>
@endsection
