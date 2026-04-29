@props([
    'value',
])
@php
    $raw = $value instanceof \BackedEnum ? $value->value : (string) $value;
    $styles = match ($raw) {
        'pending' => 'border-gray-200 bg-gray-100 text-gray-700',
        'under_review' => 'border-amber-200 bg-amber-50 text-amber-800',
        'under_revision' => 'border-violet-200 bg-violet-50 text-violet-800',
        'awaiting_hod_approval' => 'border-blue-200 bg-blue-50 text-blue-900',
        'moderated' => 'border-sky-200 bg-sky-50 text-sky-800',
        'approved' => 'border-emerald-200 bg-emerald-50 text-emerald-800',
        'rejected' => 'border-red-200 bg-red-50 text-red-800',
        'accepted' => 'border-emerald-200 bg-emerald-50 text-emerald-800',
        'minor_changes' => 'border-amber-200 bg-amber-50 text-amber-900',
        'major_changes' => 'border-orange-200 bg-orange-50 text-orange-900',
        default => 'border-gray-200 bg-gray-50 text-gray-600',
    };
    $label = match ($raw) {
        'pending' => __('Pending'),
        'under_review' => __('Under review'),
        'under_revision' => __('Under revision'),
        'awaiting_hod_approval' => __('Awaiting HOD approval'),
        'moderated' => __('Moderated'),
        'approved' => __('Approved'),
        'rejected' => __('Rejected'),
        'accepted' => __('Accepted'),
        'minor_changes' => __('Minor changes'),
        'major_changes' => __('Major changes'),
        default => str_replace('_', ' ', $raw),
    };
@endphp
<span {{ $attributes->merge(['class' => 'inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-medium '.$styles]) }}>
    {{ $label }}
</span>
