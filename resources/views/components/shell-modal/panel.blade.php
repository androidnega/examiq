@props([
    'maxWidth' => 'max-w-4xl',
])

<div
    {{ $attributes->merge([
        'class' => 'flex max-h-[min(92vh,880px)] w-full flex-col overflow-hidden rounded-2xl border border-slate-200/90 bg-white shadow-xl '.$maxWidth,
    ]) }}
>
    {{ $slot }}
</div>
