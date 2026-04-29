@extends('layouts.app', ['header' => __('Monitoring Settings')])

@section('content')
    <p class="mb-6 text-sm text-slate-500">{{ __('Control the compliance notice shown to every signed-in user.') }}</p>

    <form method="post" action="{{ route('dashboard.monitoring.update') }}" class="w-full space-y-6 rounded-xl border border-slate-100 bg-white p-6">
        @csrf
        @method('PUT')
        <div class="flex items-start gap-3">
            <input type="hidden" name="monitoring_banner_enabled" value="0" />
            <input
                id="monitoring_banner_enabled"
                type="checkbox"
                name="monitoring_banner_enabled"
                value="1"
                class="mt-1 h-4 w-4 rounded border-slate-300 text-teal-600 focus:ring-teal-500"
                @checked(old('monitoring_banner_enabled', $monitoringBannerEnabled))
            />
            <div>
                <label for="monitoring_banner_enabled" class="text-sm font-medium text-slate-900">{{ __('Show monitoring notice banner') }}</label>
                <p class="mt-1 text-xs text-slate-500">{{ __('When enabled, users see a short message that activity may be logged.') }}</p>
            </div>
        </div>
        <x-button type="submit" variant="primary">{{ __('Save') }}</x-button>
    </form>
@endsection
