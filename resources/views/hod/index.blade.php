@extends('layouts.app', ['header' => __('HOD dashboard')])

@section('content')
    <p class="mb-6 text-sm text-slate-500">{{ __('Department overview and workflow controls.') }}</p>

    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-5">
        <x-stat-card :label="__('Total submissions')" :value="$totalSubmissions" icon="folder" tint="slate" />
        <x-stat-card :label="__('Pending review')" :value="$pendingReview" icon="clipboard" tint="amber" />
        <x-stat-card :label="__('Moderation in progress')" :value="$moderationInProgress" icon="users" tint="sky" />
        <x-stat-card :label="__('Under revision')" :value="$underRevision" icon="grid" tint="violet" />
        <x-stat-card :label="__('Awaiting approval')" :value="$awaitingApproval" icon="check" tint="teal" />
    </div>

    <div class="mt-8 grid gap-4 sm:grid-cols-2">
        <div class="rounded-xl border border-slate-100 bg-white p-6">
            <h2 class="text-sm font-bold text-slate-900">{{ __('Moderation & assignments') }}</h2>
            <p class="mt-1 text-sm leading-relaxed text-slate-500">
                {{ __('Assign moderators, filter by session, and track department workload.') }}
            </p>
            <div class="mt-4">
                <x-button href="{{ route('dashboard.department.index') }}" variant="primary">{{ __('Open submissions & assignments') }}</x-button>
            </div>
        </div>
        <div class="rounded-xl border border-slate-100 bg-white p-6">
            <h2 class="text-sm font-bold text-slate-900">{{ __('Final approvals') }}</h2>
            <p class="mt-1 text-sm leading-relaxed text-slate-500">
                {{ __('Review moderated packs before they reach the exam officer.') }}
            </p>
            <div class="mt-4">
                <x-button href="{{ route('dashboard.approvals.index') }}" variant="secondary">{{ __('Open approvals queue') }}</x-button>
            </div>
        </div>
    </div>
@endsection
