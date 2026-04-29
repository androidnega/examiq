<div
    {{ $attributes->merge([
        'class' => 'grid min-h-0 flex-1 grid-cols-1 divide-y divide-slate-100 overflow-y-auto lg:grid-cols-5 lg:divide-x lg:divide-y-0',
    ]) }}
>
    <div class="min-h-0 space-y-6 p-5 sm:p-6 lg:col-span-3 lg:pr-8">
        {{ $main }}
    </div>
    <aside
        class="min-h-0 space-y-5 border-slate-100 bg-slate-50/50 p-5 sm:p-6 lg:col-span-2 lg:border-l lg:border-slate-100"
    >
        {{ $aside }}
    </aside>
</div>
