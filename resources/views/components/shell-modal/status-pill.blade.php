@props([
    'tone' => 'slate', // slate | violet | teal | sky | amber
])

@php
    $tones = [
        'slate' => 'bg-slate-100 text-slate-700 ring-slate-200/80',
        'violet' => 'bg-violet-100 text-violet-800 ring-violet-200/80',
        'teal' => 'bg-teal-100 text-teal-900 ring-teal-200/80',
        'sky' => 'bg-sky-100 text-sky-900 ring-sky-200/80',
        'amber' => 'bg-amber-100 text-amber-900 ring-amber-200/80',
    ];
    $cls = $tones[$tone] ?? $tones['slate'];
@endphp

<span
    {{ $attributes->merge([
        'class' => 'inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold ring-1 ring-inset '.$cls,
    ]) }}
>
    {{ $slot }}
</span>
