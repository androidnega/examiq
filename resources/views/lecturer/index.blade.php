@extends('layouts.app', ['header' => __('Lecturer dashboard')])

@section('content')
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <p class="text-sm text-slate-500">{{ __('Signed in as :name', ['name' => auth()->user()->name]) }}</p>
        <div class="flex flex-wrap gap-2">
            <x-button href="{{ route('dashboard.submissions.index') }}" variant="secondary">{{ __('My submissions') }}</x-button>
            <x-button href="{{ route('dashboard.submissions.create') }}" variant="primary">
                {{ __('New submission') }}
            </x-button>
        </div>
    </div>

    <div class="grid gap-4 sm:grid-cols-3">
        <x-stat-card :label="__('Total submissions')" :value="$totalSubmissions" icon="folder" tint="slate" />
        <x-stat-card :label="__('Under review')" :value="$pendingReviews" icon="clipboard" tint="slate" />
        <x-stat-card :label="__('Revisions needed')" :value="$revisionsNeeded" icon="grid" tint="slate" />
    </div>

    <div class="mt-8 rounded-xl border border-slate-200 bg-white px-5 py-4">
        <p class="text-sm text-slate-600">
            {{ __('Use My submissions to view status updates and revision feedback for your exam papers.') }}
        </p>
        <div class="mt-3">
            <x-button href="{{ route('dashboard.submissions.index') }}" variant="secondary">
                {{ __('Open My submissions') }}
            </x-button>
        </div>
    </div>
@endsection
