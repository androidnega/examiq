@props([
    'label',
    'value',
    'icon' => null,
    'tint' => 'teal',
    'variant' => null,
])
@php
    $variant = $variant ?? ($statCardVariant ?? 'staff');

    $tints = [
        'teal' => ['wrap' => 'bg-teal-50', 'icon' => 'text-teal-600', 'square' => 'bg-teal-500'],
        'sky' => ['wrap' => 'bg-sky-50', 'icon' => 'text-sky-600', 'square' => 'bg-blue-500'],
        'violet' => ['wrap' => 'bg-violet-50', 'icon' => 'text-violet-600', 'square' => 'bg-violet-500'],
        'amber' => ['wrap' => 'bg-amber-50', 'icon' => 'text-amber-700', 'square' => 'bg-amber-500'],
        'rose' => ['wrap' => 'bg-rose-50', 'icon' => 'text-rose-600', 'square' => 'bg-rose-500'],
        'emerald' => ['wrap' => 'bg-emerald-50', 'icon' => 'text-emerald-600', 'square' => 'bg-emerald-500'],
        'slate' => ['wrap' => 'bg-slate-100', 'icon' => 'text-slate-600', 'square' => 'bg-slate-600'],
    ];
    $ti = $tints[$tint] ?? $tints['slate'];
@endphp
@if ($variant === 'admin')
    <div
        {{ $attributes->merge(['class' => 'rounded-xl border border-slate-100/80 bg-white p-4 shadow-md shadow-slate-200/50 sm:p-5']) }}
    >
        <div class="flex items-center gap-4">
            @if ($icon)
                <div
                    class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl sm:h-14 sm:w-14 {{ $ti['square'] }}"
                >
                    <x-dashboard-icon :name="$icon" class="h-6 w-6 text-white sm:h-7 sm:w-7" />
                </div>
            @endif
            <div class="min-w-0 flex-1">
                <p class="text-xs font-medium text-slate-500">{{ $label }}</p>
                <p class="mt-0.5 text-2xl font-bold tabular-nums tracking-tight text-slate-900">{{ $value }}</p>
            </div>
        </div>
    </div>
@elseif ($variant === 'boxed')
    <div
        {{ $attributes->merge(['class' => 'rounded-2xl border border-slate-100 bg-white p-5 shadow-sm shadow-slate-200/40']) }}
    >
        <div class="flex items-start justify-between gap-3">
            <div class="min-w-0 flex-1">
                <p class="text-[11px] font-semibold uppercase tracking-wide text-slate-400">{{ $label }}</p>
                <p class="mt-1 text-2xl font-bold tabular-nums tracking-tight text-slate-900">{{ $value }}</p>
            </div>
            @if ($icon)
                <div
                    class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full {{ $ti['wrap'] }}"
                >
                    <x-dashboard-icon :name="$icon" class="h-5 w-5 {{ $ti['icon'] }}" />
                </div>
            @endif
        </div>
    </div>
@else
    <div {{ $attributes->merge(['class' => 'rounded-xl border border-slate-100 bg-white p-5 shadow-sm']) }}>
        <div class="flex items-start justify-between gap-3">
            <div class="min-w-0 flex-1">
                <p class="text-xs font-medium text-slate-500">{{ $label }}</p>
                <p class="mt-1 text-2xl font-bold tabular-nums tracking-tight text-slate-900">{{ $value }}</p>
            </div>
            @if ($icon)
                <div
                    class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full {{ $ti['wrap'] }}"
                >
                    <x-dashboard-icon :name="$icon" class="h-5 w-5 {{ $ti['icon'] }}" />
                </div>
            @endif
        </div>
    </div>
@endif
