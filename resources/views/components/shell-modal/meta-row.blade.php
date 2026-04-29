@props([
    'label' => '',
])

<div {{ $attributes->merge(['class' => 'flex flex-col gap-0.5 text-left']) }}>
    <span class="text-[11px] font-semibold uppercase tracking-wide text-slate-400">{{ $label }}</span>
    <div class="text-sm font-medium leading-snug text-slate-800">
        {{ $slot }}
    </div>
</div>
