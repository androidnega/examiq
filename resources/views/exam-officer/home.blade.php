@extends('layouts.app', ['header' => __('Dashboard')])

@php
    $bs = $byStatus;
    $pending = $bs[\App\Enums\SubmissionStatus::Pending->value] ?? 0;
    $underReview = $bs[\App\Enums\SubmissionStatus::UnderReview->value] ?? 0;
    $underRevision = $bs[\App\Enums\SubmissionStatus::UnderRevision->value] ?? 0;
    $awaiting = $bs[\App\Enums\SubmissionStatus::AwaitingHodApproval->value] ?? 0;
    $moderated = $bs[\App\Enums\SubmissionStatus::Moderated->value] ?? 0;
    $approved = $bs[\App\Enums\SubmissionStatus::Approved->value] ?? 0;
    $rejected = $bs[\App\Enums\SubmissionStatus::Rejected->value] ?? 0;

@endphp

@section('content')
    <div class="space-y-6">
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <x-stat-card :label="__('Approved records')" :value="number_format($registryTotalExams)" icon="table" tint="slate" />
            <x-stat-card :label="__('Students covered')" :value="number_format($registryTotalStudents)" icon="users" tint="slate" />
            <x-stat-card :label="__('Academic years')" :value="number_format($registryAcademicYears)" icon="calendar" tint="slate" />
            <x-stat-card :label="__('Publication rate')" :value="$overallProgress.'%'" icon="chart" tint="slate" />
        </div>

        <div class="rounded-xl border border-slate-200 bg-white p-5">
            <h2 class="text-sm font-semibold text-slate-900">{{ __('Core actions') }}</h2>
            <p class="mt-1 text-sm text-slate-600">{{ __('Use the registry to manage approved exam packs, and search to quickly find a record.') }}</p>
            <div class="mt-4 flex flex-wrap gap-2">
                <x-button href="{{ route('dashboard.registry') }}" variant="primary">{{ __('Open registry') }}</x-button>
                <x-button href="{{ route('dashboard.search') }}" variant="secondary">{{ __('Search records') }}</x-button>
            </div>
        </div>

        <div class="rounded-xl border border-slate-200 bg-white">
            <div class="border-b border-slate-200 px-5 py-3">
                <h2 class="text-sm font-semibold text-slate-900">{{ __('Workflow status') }}</h2>
            </div>
            <div class="p-5">
                <dl class="grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
                    @foreach ([
                        [__('Pending'), $pending],
                        [__('Under review'), $underReview],
                        [__('Under revision'), $underRevision],
                        [__('Awaiting HOD approval'), $awaiting],
                        [__('Moderated'), $moderated],
                        [__('Approved'), $approved],
                        [__('Rejected'), $rejected],
                        [__('Total pipeline'), $totalPipeline],
                    ] as [$label, $value])
                        <div class="rounded-lg bg-slate-50 px-3 py-2">
                            <dt class="text-xs text-slate-500">{{ $label }}</dt>
                            <dd class="mt-1 text-base font-semibold tabular-nums text-slate-900">{{ number_format($value) }}</dd>
                        </div>
                    @endforeach
                </dl>
            </div>
        </div>
    </div>
@endsection
