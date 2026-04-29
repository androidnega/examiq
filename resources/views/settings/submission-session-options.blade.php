@extends('layouts.app', ['header' => __('Submission session options')])

@section('content')
    <div class="mx-auto max-w-3xl space-y-6">
        <p class="text-sm text-slate-500">
            {{ __('Manage the centralized list for academic years and semesters. Lecturers can only select from these options.') }}
        </p>

        <div class="rounded-xl border border-slate-200 bg-white p-6">
            <form method="post" action="{{ $isHodPage ? route('dashboard.department.session-options.update') : route('dashboard.system.session-options.update') }}" class="space-y-5">
                @csrf
                @method('PUT')

                <div>
                    <label for="academic_year_options" class="block text-sm font-medium text-slate-700">{{ __('Academic year options') }}</label>
                    <p class="mt-1 text-xs text-slate-500">{{ __('One per line. Example: 2025/2026') }}</p>
                    <textarea id="academic_year_options" name="academic_year_options" rows="6" class="mt-2 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm">{{ old('academic_year_options', implode("\n", $academicYears)) }}</textarea>
                    @error('academic_year_options')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="semester_options" class="block text-sm font-medium text-slate-700">{{ __('Semester options') }}</label>
                    <p class="mt-1 text-xs text-slate-500">{{ __('One per line. Example: first, second') }}</p>
                    <textarea id="semester_options" name="semester_options" rows="4" class="mt-2 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm">{{ old('semester_options', implode("\n", $semesters)) }}</textarea>
                    @error('semester_options')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex flex-wrap gap-2">
                    <x-button type="submit" variant="primary">{{ __('Save options') }}</x-button>
                    <x-button href="{{ $isHodPage ? route('dashboard') : route('dashboard.system.edit') }}" variant="secondary">{{ __('Back') }}</x-button>
                </div>
            </form>
        </div>
    </div>
@endsection
