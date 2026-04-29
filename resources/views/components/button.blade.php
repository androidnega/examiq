@props([
    'href' => null,
    'variant' => 'primary',
])
@php
    $base = 'inline-flex items-center justify-center rounded-lg border px-4 py-2 text-sm font-semibold transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-teal-400/40 focus-visible:ring-offset-2';
    $variants = [
        'primary' => 'border-teal-700 bg-teal-700 text-white hover:border-teal-800 hover:bg-teal-800',
        'secondary' => 'border-slate-200 bg-white text-slate-700 hover:border-slate-300 hover:bg-slate-50',
        'outline' => 'border-slate-300 bg-white font-medium text-slate-700 hover:border-slate-400 hover:bg-slate-50',
        'danger' => 'border-red-200 bg-white font-medium text-red-700 hover:border-red-300 hover:bg-red-50',
        'ghost' => 'border-transparent bg-transparent font-medium text-slate-600 hover:bg-slate-100 hover:text-slate-900',
    ];
    $classes = $base.' '.($variants[$variant] ?? $variants['primary']);
@endphp
@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>{{ $slot }}</a>
@else
    <button
        type="{{ $attributes->get('type', 'button') }}"
        {{ $attributes->except('type')->merge(['class' => $classes]) }}
    >{{ $slot }}</button>
@endif
