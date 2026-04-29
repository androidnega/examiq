<footer
    {{ $attributes->merge([
        'class' => 'flex shrink-0 flex-wrap items-center justify-end gap-2 border-t border-slate-100 bg-white px-5 py-4 sm:gap-3 sm:px-6',
    ]) }}
>
    {{ $slot }}
</footer>
