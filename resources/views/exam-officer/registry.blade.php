@extends('layouts.app', ['header' => __('Approved exam registry')])

@section('content')
    <div class="space-y-5">
        <div class="grid gap-4 sm:grid-cols-3">
            <x-stat-card :label="__('Total records')" :value="number_format($registryTotalExams)" icon="table" tint="slate" />
            <x-stat-card :label="__('Students covered')" :value="number_format($registryTotalStudents)" icon="users" tint="slate" />
            <x-stat-card :label="__('Academic years')" :value="number_format($registryAcademicYears)" icon="calendar" tint="slate" />
        </div>

        <div class="rounded-xl border border-slate-200 bg-white p-4">
            <form method="get" action="{{ route('dashboard.registry') }}" class="flex flex-col gap-3 sm:flex-row sm:items-center">
                <input type="hidden" name="sort" value="{{ $sort }}" />
                <input type="hidden" name="dir" value="{{ $dir }}" />
                <input type="hidden" name="view" value="{{ $viewMode }}" />
                <input type="hidden" name="per_page" value="{{ $perPage }}" />
                <div class="flex min-w-0 flex-1 items-center gap-2 rounded-lg border border-slate-200 bg-white px-3 py-2">
                    <x-dashboard-icon name="search" class="h-4 w-4 shrink-0 text-slate-400" />
                    <input
                        type="search"
                        name="q"
                        value="{{ $search }}"
                        placeholder="{{ __('Search by course name or code') }}"
                        class="min-w-0 flex-1 border-0 bg-transparent text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none focus:ring-0"
                    />
                </div>
                <x-button type="submit" variant="primary">{{ __('Search') }}</x-button>
            </form>
        </div>

        <div class="overflow-hidden rounded-xl border border-slate-200 bg-white">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100 text-left text-sm">
                    <thead class="bg-slate-50">
                        <tr class="text-xs font-semibold uppercase tracking-wide text-slate-500">
                            <th class="px-5 py-3">{{ __('Course') }}</th>
                            <th class="px-5 py-3">{{ __('Academic year') }}</th>
                            <th class="px-5 py-3">{{ __('Students') }}</th>
                            <th class="px-5 py-3">{{ __('Invigilators') }}</th>
                            <th class="px-5 py-3 text-right">{{ __('Moderation') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($submissions as $row)
                            @php $inv = invigilators_required((int) $row->students_count); @endphp
                            <tr class="bg-white">
                                <td class="px-5 py-3 font-medium text-slate-900">{{ $row->course?->code }} — {{ $row->course?->name }}</td>
                                <td class="px-5 py-3 text-slate-700">{{ $row->academic_year }}</td>
                                <td class="px-5 py-3 tabular-nums text-slate-700">{{ $row->students_count }}</td>
                                <td class="px-5 py-3 tabular-nums text-slate-700">{{ $inv }}</td>
                                <td class="px-5 py-3 text-right">
                                    <x-button href="{{ route('dashboard.submissions.moderation-forms.index', $row) }}" variant="ghost">{{ __('Forms') }}</x-button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-5 py-12 text-center text-sm text-slate-500">{{ __('No approved submissions found.') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="flex justify-center sm:justify-end">
            {{ $submissions->links() }}
        </div>
    </div>
@endsection
