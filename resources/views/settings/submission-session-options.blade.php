@extends('layouts.app', ['header' => __('Submission session options')])

@section('content')
    <div class="w-full space-y-6" x-data="{
        academicYears: @js(old('academic_year_options', $academicYears)),
        semesters: @js(old('semester_options', $semesters)),
        addAcademicYear() { this.academicYears.push(''); },
        removeAcademicYear(index) {
            if (this.academicYears.length > 1) this.academicYears.splice(index, 1);
        },
        addSemester() { this.semesters.push(''); },
        removeSemester(index) {
            if (this.semesters.length > 1) this.semesters.splice(index, 1);
        }
    }">
        <p class="text-sm text-slate-500">
            {{ __('Manage the centralized list for academic years and semesters. Lecturers can only select from these options.') }}
        </p>

        <div class="rounded-xl border border-slate-200 bg-white p-6">
            <form method="post" action="{{ $isHodPage ? route('dashboard.department.session-options.update') : route('dashboard.system.session-options.update') }}" class="space-y-5">
                @csrf
                @method('PUT')

                <div>
                    <label for="academic_year_options" class="block text-sm font-medium text-slate-700">{{ __('Academic year options') }}</label>
                    <p class="mt-1 text-xs text-slate-500">{{ __('Add one or more academic years.') }}</p>
                    <div class="mt-2 space-y-2">
                        <template x-for="(year, index) in academicYears" :key="'year-'+index">
                            <div class="flex items-center gap-2">
                                <input
                                    type="text"
                                    name="academic_year_options[]"
                                    x-model="academicYears[index]"
                                    placeholder="2025/2026"
                                    class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm"
                                />
                                <button type="button" @click="removeAcademicYear(index)" class="rounded-lg border border-slate-200 px-3 py-2 text-xs text-slate-600 hover:bg-slate-50">
                                    {{ __('Remove') }}
                                </button>
                            </div>
                        </template>
                    </div>
                    <button type="button" @click="addAcademicYear" class="mt-2 rounded-lg border border-slate-200 px-3 py-2 text-xs font-medium text-slate-700 hover:bg-slate-50">
                        {{ __('Add academic year') }}
                    </button>
                    @error('academic_year_options')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    @error('academic_year_options.*')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="semester_options" class="block text-sm font-medium text-slate-700">{{ __('Semester options') }}</label>
                    <p class="mt-1 text-xs text-slate-500">{{ __('Add one or more semester options.') }}</p>
                    <div class="mt-2 space-y-2">
                        <template x-for="(semester, index) in semesters" :key="'semester-'+index">
                            <div class="flex items-center gap-2">
                                <input
                                    type="text"
                                    name="semester_options[]"
                                    x-model="semesters[index]"
                                    placeholder="first"
                                    class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm"
                                />
                                <button type="button" @click="removeSemester(index)" class="rounded-lg border border-slate-200 px-3 py-2 text-xs text-slate-600 hover:bg-slate-50">
                                    {{ __('Remove') }}
                                </button>
                            </div>
                        </template>
                    </div>
                    <button type="button" @click="addSemester" class="mt-2 rounded-lg border border-slate-200 px-3 py-2 text-xs font-medium text-slate-700 hover:bg-slate-50">
                        {{ __('Add semester') }}
                    </button>
                    @error('semester_options')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    @error('semester_options.*')
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
