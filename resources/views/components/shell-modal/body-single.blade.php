{{-- Single-column modal body (no sidebar). Use between header and footer. --}}
<div
    {{ $attributes->merge([
        'class' => 'min-h-0 flex-1 overflow-y-auto p-5 sm:p-6',
    ]) }}
>
    {{ $slot }}
</div>
