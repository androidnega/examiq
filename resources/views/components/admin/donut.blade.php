@props([
    'percent' => 0,
    'primaryStroke' => '#0d9488',
    'trackStroke' => '#e2e8f0',
])
@php
    $pct = max(0, min(100, (int) $percent));
    $r = 40;
    $cx = 48;
    $cy = 48;
    $c = 2 * M_PI * $r;
    $dash = round($pct / 100 * $c, 2);
@endphp
<svg viewBox="0 0 96 96" class="{{ $attributes->get('class', 'h-36 w-36 shrink-0') }}" aria-hidden="true">
    <circle
        cx="{{ $cx }}"
        cy="{{ $cy }}"
        r="{{ $r }}"
        fill="none"
        stroke="{{ $trackStroke }}"
        stroke-width="10"
    />
    <circle
        cx="{{ $cx }}"
        cy="{{ $cy }}"
        r="{{ $r }}"
        fill="none"
        stroke="{{ $primaryStroke }}"
        stroke-width="10"
        stroke-dasharray="{{ $dash }} {{ $c }}"
        stroke-linecap="round"
        transform="rotate(-90 {{ $cx }} {{ $cy }})"
    />
</svg>
