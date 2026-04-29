@extends('layouts.app', ['header' => __('Department submissions')])

@section('content')
    <div
        class="space-y-8"
        x-data="{
            modalOpen: false,
            selectedSubmissionIds: [],
            selectPrompt: @js(__('Select at least one submission.')),
            openAssignModal() {
                const checked = Array.from(document.querySelectorAll('input[name=\'submission_ids[]\']:checked')).map((el) => el.value);
                if (!checked.length) {
                    alert(this.selectPrompt);
                    return;
                }
                this.selectedSubmissionIds = checked;
                this.modalOpen = true;
            },
        }"
        @shell-modal-close.window="modalOpen = false"
    >
        <form method="get" action="{{ route('dashboard.department.index') }}" class="flex flex-col gap-3 rounded-xl border border-gray-200 bg-white p-4 sm:flex-row sm:flex-wrap sm:items-end">
            <div class="min-w-[10rem] flex-1">
                <label for="filter_status" class="block text-xs font-medium text-gray-500">{{ __('Status') }}</label>
                <select id="filter_status" name="status" class="mt-1 w-full rounded-xl border border-gray-200 px-3 py-2 text-sm">
                    <option value="all" @selected(($filters['status'] ?? 'all') === 'all')>{{ __('All') }}</option>
                    <option value="pending" @selected(($filters['status'] ?? '') === 'pending')>{{ __('Pending') }}</option>
                    <option value="under_review" @selected(($filters['status'] ?? '') === 'under_review')>{{ __('Under review') }}</option>
                    <option value="under_revision" @selected(($filters['status'] ?? '') === 'under_revision')>{{ __('Under revision') }}</option>
                    <option value="awaiting_hod_approval" @selected(($filters['status'] ?? '') === 'awaiting_hod_approval')>{{ __('Awaiting HOD approval') }}</option>
                    <option value="moderated" @selected(($filters['status'] ?? '') === 'moderated')>{{ __('Moderated') }}</option>
                    <option value="approved" @selected(($filters['status'] ?? '') === 'approved')>{{ __('Approved') }}</option>
                    <option value="rejected" @selected(($filters['status'] ?? '') === 'rejected')>{{ __('Rejected') }}</option>
                </select>
            </div>
            <div class="min-w-[10rem] flex-1">
                <label for="filter_semester" class="block text-xs font-medium text-gray-500">{{ __('Semester') }}</label>
                <select id="filter_semester" name="semester" class="mt-1 w-full rounded-xl border border-gray-200 px-3 py-2 text-sm">
                    <option value="all" @selected(($filters['semester'] ?? 'all') === 'all')>{{ __('All') }}</option>
                    @foreach ($semesters as $sem)
                        <option value="{{ $sem }}" @selected(($filters['semester'] ?? '') === $sem)>{{ $sem }}</option>
                    @endforeach
                </select>
            </div>
            <div class="min-w-[10rem] flex-1">
                <label for="filter_year" class="block text-xs font-medium text-gray-500">{{ __('Academic year') }}</label>
                <select id="filter_year" name="academic_year" class="mt-1 w-full rounded-xl border border-gray-200 px-3 py-2 text-sm">
                    <option value="all" @selected(($filters['academic_year'] ?? 'all') === 'all')>{{ __('All') }}</option>
                    @foreach ($academicYears as $year)
                        <option value="{{ $year }}" @selected(($filters['academic_year'] ?? '') === $year)>{{ $year }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex gap-2">
                <x-button type="submit" variant="primary">{{ __('Apply filters') }}</x-button>
                <x-button href="{{ route('dashboard.department.index') }}" variant="secondary">{{ __('Reset') }}</x-button>
            </div>
        </form>

        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <p class="text-sm text-gray-500">{{ __('Select submissions, then assign one or more moderators from your department.') }}</p>
            <x-button type="button" variant="primary" @click="openAssignModal()">{{ __('Assign moderators') }}</x-button>
        </div>

        <x-table>
            <thead class="bg-gray-50 text-xs font-medium uppercase tracking-wide text-gray-500">
                <tr>
                    <th class="w-10 px-4 py-3"></th>
                    <th class="px-4 py-3">{{ __('Course') }}</th>
                    <th class="px-4 py-3">{{ __('Lecturer') }}</th>
                    <th class="px-4 py-3">{{ __('Status') }}</th>
                    <th class="px-4 py-3 text-right">{{ __('Open') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($examSubmissions as $examSubmission)
                    <tr class="bg-white">
                        <td class="px-4 py-3">
                            <input type="checkbox" name="submission_ids[]" value="{{ $examSubmission->id }}" class="h-4 w-4 rounded border-gray-300 text-gray-900" />
                        </td>
                        <td class="px-4 py-3 font-medium text-gray-900">
                            {{ $examSubmission->course?->code }} — {{ $examSubmission->course?->name }}
                        </td>
                        <td class="px-4 py-3 text-gray-700">{{ $examSubmission->lecturer?->name }}</td>
                        <td class="px-4 py-3">
                            <x-badge :value="$examSubmission->status" />
                        </td>
                        <td class="px-4 py-3 text-right">
                            <x-button href="{{ route('dashboard.department.show', $examSubmission) }}" variant="ghost">{{ __('View') }}</x-button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-8 text-center text-sm text-gray-500">{{ __('No submissions match your filters.') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </x-table>

        <div class="mt-4">
            {{ $examSubmissions->links() }}
        </div>

        <x-shell-modal.backdrop
            x-show="modalOpen"
            x-cloak
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            @click.self="modalOpen = false"
            @keydown.escape.window="modalOpen = false"
        >
            <x-shell-modal.panel>
                <x-shell-modal.header
                    :title="__('Assign moderators')"
                    :subtitle="__('Choose reviewers for the selected exam submissions. Duplicates are ignored.')"
                />

                <x-shell-modal.body>
                    <x-slot name="main">
                        <div class="space-y-2">
                            <h3 class="text-lg font-semibold tracking-tight text-slate-900">
                                {{ __('Moderation roster') }}
                            </h3>
                            <p class="text-sm leading-relaxed text-slate-500">
                                {{ __('Only moderators in your department appear here. Each selected submission will be linked to the moderators you tick below.') }}
                            </p>
                        </div>

                        <form
                            id="hod-assign-moderators-form"
                            method="post"
                            action="{{ route('dashboard.department.assign-moderators') }}"
                            class="space-y-5"
                        >
                            @csrf
                            <template x-for="id in selectedSubmissionIds" :key="id">
                                <input type="hidden" name="submission_ids[]" :value="id" />
                            </template>

                            <fieldset>
                                <legend class="text-sm font-semibold text-slate-800">{{ __('Moderators') }}</legend>
                                <div class="mt-3 max-h-52 space-y-2 overflow-y-auto rounded-xl border border-slate-200 bg-white p-3">
                                    @forelse ($departmentModerators as $moderator)
                                        <label class="flex cursor-pointer items-center gap-3 rounded-lg px-2 py-2 text-sm text-slate-700 transition-colors hover:bg-slate-50">
                                            <input
                                                type="checkbox"
                                                name="moderator_ids[]"
                                                value="{{ $moderator->id }}"
                                                class="h-4 w-4 rounded border-slate-300 text-teal-700 focus:ring-teal-600/30"
                                            />
                                            <span class="font-medium">{{ $moderator->name }}</span>
                                        </label>
                                    @empty
                                        <p class="text-sm text-slate-500">{{ __('No moderators in your department.') }}</p>
                                    @endforelse
                                </div>
                                @error('moderator_ids')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                @error('moderator_ids.*')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </fieldset>
                        </form>
                    </x-slot>

                    <x-slot name="aside">
                        <p class="text-[11px] font-semibold uppercase tracking-wide text-slate-400">
                            {{ __('Summary') }}
                        </p>

                        <div class="flex items-center gap-3 rounded-xl border border-slate-200/80 bg-white px-3 py-3">
                            <span
                                class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-teal-100 text-sm font-bold text-teal-800"
                                x-text="selectedSubmissionIds.length"
                            ></span>
                            <div class="min-w-0 text-left">
                                <p class="text-sm font-semibold text-slate-900">{{ __('Submissions selected') }}</p>
                                <p class="text-xs text-slate-500">{{ __('Ready for moderator assignment') }}</p>
                            </div>
                        </div>

                        <x-shell-modal.meta-row :label="__('Department')">
                            {{ $department?->name ?? __('Your department') }}
                        </x-shell-modal.meta-row>

                        <x-shell-modal.meta-row :label="__('Workflow')">
                            <span class="font-normal text-slate-600">{{ __('HOD assigns → Moderator reviews → Lecturer revises if needed') }}</span>
                        </x-shell-modal.meta-row>

                        <div class="pt-1">
                            <x-shell-modal.status-pill tone="violet">{{ __('Department queue') }}</x-shell-modal.status-pill>
                        </div>
                    </x-slot>
                </x-shell-modal.body>

                <x-shell-modal.footer>
                    <x-button type="button" variant="outline" @click="modalOpen = false">{{ __('Cancel') }}</x-button>
                    <x-button type="submit" form="hod-assign-moderators-form" variant="primary">
                        {{ __('Submit assignments') }}
                    </x-button>
                </x-shell-modal.footer>
            </x-shell-modal.panel>
        </x-shell-modal.backdrop>
    </div>
@endsection
