@if ($monitoringBannerEnabled ?? true)
    <p
        class="mb-4 rounded-lg border border-slate-200 bg-slate-50 px-4 py-2.5 text-center text-xs font-medium text-slate-600"
        role="note"
    >
        {{ __('You are being monitored for security and compliance. Activity may be logged.') }}
    </p>
@endif
