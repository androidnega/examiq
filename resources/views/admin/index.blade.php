@extends('layouts.app', ['header' => __('Admin dashboard')])

@section('content')
    <div class="space-y-6">
        <div class="flex flex-wrap items-center justify-between gap-2">
            <p class="text-sm text-slate-600">
                {{ __('Live metrics across users, exam submissions, and audit activity.') }}
            </p>
            <p class="text-xs text-slate-500">
                @if ($autoRefreshEnabled)
                    {{ __('Auto-refresh every :seconds seconds · Last updated :time', ['seconds' => $autoRefreshSeconds, 'time' => $lastRefreshedAt->format('g:i:s A')]) }}
                @else
                    {{ __('Auto-refresh is off · Last updated :time', ['time' => $lastRefreshedAt->format('g:i:s A')]) }}
                @endif
            </p>
        </div>

        {{-- KPI row --}}
        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <div class="rounded-2xl border border-slate-200/90 bg-white p-5 shadow-sm">
                <div class="flex items-start gap-4">
                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-sky-100 shadow-sm shadow-sky-100/60">
                        <x-dashboard-icon name="users" class="h-6 w-6 text-sky-600" />
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-xs font-medium text-slate-500">{{ __('Registered users') }}</p>
                        <p class="mt-1 text-2xl font-bold tabular-nums text-slate-900">{{ number_format($usersCount) }}</p>
                        @if ($blockedUsersCount > 0)
                            <p class="mt-1 text-xs font-medium text-rose-600">
                                {{ trans_choice(':count blocked account|:count blocked accounts', $blockedUsersCount, ['count' => $blockedUsersCount]) }}
                            </p>
                        @endif
                    </div>
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200/90 bg-white p-5 shadow-sm">
                <div class="flex items-start gap-4">
                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-violet-100 shadow-sm shadow-violet-100/60">
                        <x-dashboard-icon name="folder" class="h-6 w-6 text-violet-600" />
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-xs font-medium text-slate-500">{{ __('Exam submissions') }}</p>
                        <p class="mt-1 text-2xl font-bold tabular-nums text-slate-900">{{ number_format($submissionsTotal) }}</p>
                        <p class="mt-1 text-xs text-slate-500">
                            {{ trans_choice(':count department|:count departments', $departmentsCount, ['count' => $departmentsCount]) }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200/90 bg-white p-5 shadow-sm">
                <div class="flex items-start gap-4">
                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-amber-100 shadow-sm shadow-amber-100/60">
                        <x-dashboard-icon name="clipboard" class="h-6 w-6 text-amber-600" />
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-xs font-medium text-slate-500">{{ __('Active pipeline') }}</p>
                        <p class="mt-1 text-2xl font-bold tabular-nums text-slate-900">{{ number_format($pipelineOpen) }}</p>
                        <p class="mt-1 text-xs text-slate-500">{{ __('Pending, review, or revision') }}</p>
                    </div>
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200/90 bg-white p-5 shadow-sm">
                <div class="flex items-start gap-4">
                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-emerald-100 shadow-sm shadow-emerald-100/60">
                        <x-dashboard-icon name="check" class="h-6 w-6 text-emerald-600" />
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-xs font-medium text-slate-500">{{ __('Approved') }}</p>
                        <p class="mt-1 text-2xl font-bold tabular-nums text-slate-900">{{ number_format($approvedCount) }}</p>
                        <p class="mt-1 text-xs text-slate-500">{{ __('Ready for registry') }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Main widgets --}}
        <div class="grid gap-4 lg:grid-cols-3">
            {{-- Workflow overview --}}
            <div class="rounded-2xl border border-slate-200/90 bg-white p-6 shadow-sm">
                <h2 class="text-sm font-bold text-slate-900">{{ __('Workflow overview') }}</h2>
                <p class="mt-1 text-xs text-slate-500">{{ __('Open items vs completed in the moderation lifecycle.') }}</p>

                <div class="mt-5 grid grid-cols-3 gap-3 text-center">
                    <div class="rounded-xl bg-slate-50 px-2 py-3">
                        <p class="text-lg font-bold tabular-nums text-sky-600">{{ number_format($pipelineOpen) }}</p>
                        <p class="mt-0.5 text-[11px] font-semibold uppercase tracking-wide text-slate-500">{{ __('Open') }}</p>
                    </div>
                    <div class="rounded-xl bg-slate-50 px-2 py-3">
                        <p class="text-lg font-bold tabular-nums text-emerald-600">{{ number_format($pipelineCompleted) }}</p>
                        <p class="mt-0.5 text-[11px] font-semibold uppercase tracking-wide text-slate-500">{{ __('Done') }}</p>
                    </div>
                    <div class="rounded-xl bg-slate-50 px-2 py-3">
                        <p class="text-lg font-bold tabular-nums text-amber-600">{{ number_format($pipelineHold) }}</p>
                        <p class="mt-0.5 text-[11px] font-semibold uppercase tracking-wide text-slate-500">{{ __('Hold') }}</p>
                    </div>
                </div>

                <div class="mt-6">
                    <div class="flex items-center justify-between text-xs font-medium text-slate-600">
                        <span>{{ __('Completion share') }}</span>
                        <span class="tabular-nums text-slate-900">{{ $workflowProgressPct }}%</span>
                    </div>
                    <div class="mt-2 h-3 w-full overflow-hidden rounded-full border border-teal-200/80 bg-white">
                        <div
                            class="h-full rounded-full bg-teal-500 transition-[width] duration-500 ease-out"
                            style="width: {{ $workflowProgressPct }}%"
                        ></div>
                    </div>
                    <p class="mt-2 text-center text-[11px] font-medium text-teal-800">
                        {{ __(':pct% of all submissions are completed', ['pct' => $workflowProgressPct]) }}
                    </p>
                </div>
            </div>

            {{-- Pipeline breakdown --}}
            <div class="rounded-2xl border border-slate-200/90 bg-white p-6 shadow-sm lg:col-span-1">
                <h2 class="text-sm font-bold text-slate-900">{{ __('Submission pipeline') }}</h2>
                <p class="mt-1 text-xs text-slate-500">{{ __('Counts by status with relative load.') }}</p>

                <ul class="mt-5 space-y-3">
                    @foreach ($statusRows as $row)
                        <li class="flex items-center gap-3">
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center justify-between gap-2 text-sm">
                                    <span class="font-medium text-slate-800">{{ $row['label'] }}</span>
                                    <span class="tabular-nums font-semibold {{ $row['text'] }}">{{ number_format($row['count']) }}</span>
                                </div>
                                <div class="mt-1.5 h-1.5 w-full overflow-hidden rounded-full bg-slate-100">
                                    <div
                                        class="h-full rounded-full {{ $row['bar'] }} transition-all duration-300"
                                        style="width: {{ round($row['count'] / $maxStatusCount * 100) }}%"
                                    ></div>
                                </div>
                            </div>
                        </li>
                    @endforeach
                </ul>

                <div class="mt-5 border-t border-slate-100 pt-4 text-xs text-slate-600">
                    <div class="flex justify-between font-medium">
                        <span>{{ __('Total tracked') }}</span>
                        <span class="tabular-nums text-slate-900">{{ number_format($submissionsTotal) }}</span>
                    </div>
                </div>

                <div class="mt-4">
                    <p class="mb-2 text-[11px] font-semibold uppercase tracking-wide text-slate-400">{{ __('Last 14 days — audit events') }}</p>
                    <x-admin.sparkline
                        :values="$activityTrend"
                        stroke="#7aaed1"
                        fill="#a8cde6"
                        class="h-14 w-full"
                    />
                    <p class="mt-1 text-[10px] text-slate-400">{{ __('Activity log entries per day') }}</p>
                </div>
            </div>

            {{-- Donut + throughput --}}
            <div class="rounded-2xl border border-slate-200/90 bg-white p-6 shadow-sm">
                <h2 class="text-sm font-bold text-slate-900">{{ __('Approval mix') }}</h2>
                <p class="mt-1 text-xs text-slate-500">{{ __('Share of submissions that reached approval.') }}</p>

                <div class="mt-4 flex flex-col items-center gap-4 sm:flex-row sm:items-center sm:justify-center">
                    <x-admin.donut :percent="$approvalSharePct" class="h-32 w-32 sm:h-36 sm:w-36" />
                    <div class="min-w-0 flex-1 space-y-3 text-sm">
                        <div class="flex items-center gap-2">
                            <span class="h-2.5 w-2.5 shrink-0 rounded-full bg-teal-400"></span>
                            <span class="text-slate-700">{{ __('Approved') }}</span>
                            <span class="ml-auto font-semibold tabular-nums text-slate-900">{{ $approvalSharePct }}%</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="h-2.5 w-2.5 shrink-0 rounded-full bg-rose-300"></span>
                            <span class="text-slate-700">{{ __('In progress or rejected') }}</span>
                            <span class="ml-auto font-semibold tabular-nums text-slate-900">{{ 100 - $approvalSharePct }}%</span>
                        </div>
                        <p class="text-xs leading-relaxed text-slate-500">
                            {{ __('Based on :total submissions in the system.', ['total' => number_format($submissionsTotal)]) }}
                        </p>
                    </div>
                </div>

                <div class="mt-6 border-t border-slate-100 pt-5">
                    <p class="text-[11px] font-semibold uppercase tracking-wide text-slate-400">{{ __('Volume trend (12 weeks)') }}</p>
                    <x-admin.dual-area-trend
                        :primary="$submissionsTrend"
                        :secondary="$usersTrend"
                        :max="max(max($submissionsTrend) ?: 0, max($usersTrend) ?: 0, 1)"
                        primary-color="#7ebfb7"
                        secondary-color="#9aa4dc"
                        class="mt-3 h-20 w-full"
                    />
                    <div class="mt-2 flex justify-between text-[10px] text-slate-400">
                        <span>{{ $weekLabels[0] ?? '' }}</span>
                        <span>{{ $weekLabels[count($weekLabels) - 1] ?? '' }}</span>
                    </div>
                    <div class="mt-2 flex flex-wrap gap-4 text-xs">
                        <span class="inline-flex items-center gap-1.5 text-slate-600">
                            <span class="h-2 w-2 rounded-full bg-teal-400"></span>
                            {{ __('New submissions') }}
                        </span>
                        <span class="inline-flex items-center gap-1.5 text-slate-600">
                            <span class="h-2 w-2 rounded-full bg-indigo-300"></span>
                            {{ __('New users') }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Weekly submissions focus --}}
        <div class="rounded-2xl border border-slate-200/90 bg-white p-6 shadow-sm">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <h2 class="text-sm font-bold text-slate-900">{{ __('Submission intake trend') }}</h2>
                    <p class="mt-1 text-xs text-slate-500">{{ __('Weekly created submissions — last 12 weeks.') }}</p>
                </div>
                <div class="text-right text-xs text-slate-500">
                    <p class="font-semibold text-slate-800">{{ __('Peak week') }}</p>
                    <p class="tabular-nums">{{ number_format(max($submissionsTrend) ?: 0) }} {{ __('submissions') }}</p>
                </div>
            </div>
            <x-admin.sparkline
                :values="$submissionsTrend"
                stroke="#7ebfb7"
                fill="#b7dfda"
                class="mt-4 h-16 w-full"
            />
            <div class="mt-2 flex justify-between text-[10px] text-slate-400">
                <span>{{ $weekLabels[0] ?? '' }}</span>
                <span>{{ $weekLabels[11] ?? '' }}</span>
            </div>
        </div>

        {{-- Quick links --}}
        <div class="rounded-2xl border border-slate-200/90 bg-white p-6 shadow-sm">
            <h2 class="text-sm font-bold text-slate-900">{{ __('Quick actions') }}</h2>
            <p class="mt-1 text-xs text-slate-500">{{ __('Jump to common admin tasks.') }}</p>
            <div class="mt-4 flex flex-wrap gap-2">
                <x-button href="{{ route('dashboard.users.index') }}" variant="secondary">{{ __('Users') }}</x-button>
                <x-button href="{{ route('dashboard.universities.index') }}" variant="secondary">{{ __('Universities') }}</x-button>
                <x-button href="{{ route('dashboard.activity-logs.index') }}" variant="secondary">{{ __('Activity logs') }}</x-button>
                <x-button href="{{ route('dashboard.security-logs.index') }}" variant="secondary">{{ __('Security logs') }}</x-button>
                <x-button href="{{ route('dashboard.system.edit') }}" variant="secondary">{{ __('System settings') }}</x-button>
            </div>
        </div>
    </div>

    @if ($autoRefreshEnabled)
        <script>
            window.setInterval(() => {
                window.location.reload();
            }, {{ $autoRefreshSeconds * 1000 }});
        </script>
    @endif
@endsection
