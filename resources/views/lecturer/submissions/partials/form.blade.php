@php
    /** @var \Illuminate\Support\Collection<int, \App\Models\Course>|null $courses */
    /** @var \App\Models\ExamSubmission|null $submission */
    $isEdit = $submission !== null;
    $action = $isEdit ? route('dashboard.submissions.update', $submission) : route('dashboard.submissions.store');
@endphp

<form
    action="{{ $action }}"
    method="post"
    enctype="multipart/form-data"
    class="space-y-8"
    data-ajax-submit="true"
>
    @csrf
    @if ($isEdit)
        @method('PUT')
    @endif

    <div data-form-errors class="hidden rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800" role="alert"></div>

    <div class="h-1 w-full overflow-hidden rounded-full bg-gray-100" aria-hidden="true">
        <div class="js-upload-progress h-1 w-0 rounded-full bg-gray-900 transition-[width] duration-150"></div>
    </div>

    <div class="rounded-xl border border-gray-200 bg-white p-6">
        <h2 class="text-sm font-semibold text-gray-900">{{ __('Course & session') }}</h2>
        <p class="mt-1 text-xs text-gray-500">{{ __('Required details for this examination pack.') }}</p>
        <div class="mt-5 grid gap-5 sm:grid-cols-2">
            <div class="sm:col-span-2">
                <label for="course_id" class="block text-sm font-medium text-gray-700">{{ __('Course') }}</label>
                @if ($isEdit)
                    <input type="hidden" name="course_id" value="{{ $submission->course_id }}" />
                    <p class="mt-2 rounded-xl border border-gray-100 bg-gray-50 px-3 py-2 text-sm text-gray-900">
                        {{ $submission->course?->code }} — {{ $submission->course?->name }}
                    </p>
                @else
                    <select id="course_id" name="course_id" required class="mt-1 w-full rounded-xl border border-gray-200 bg-white px-3 py-2 text-sm focus:border-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-200">
                        <option value="">{{ __('Select course') }}</option>
                        @foreach ($courses ?? [] as $course)
                            <option value="{{ $course->id }}" @selected(old('course_id') === $course->id)>
                                {{ $course->code }} — {{ $course->name }}
                            </option>
                        @endforeach
                    </select>
                @endif
                @error('course_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="academic_year" class="block text-sm font-medium text-gray-700">{{ __('Academic year') }}</label>
                <select id="academic_year" name="academic_year" required class="mt-1 w-full rounded-xl border border-gray-200 bg-white px-3 py-2 text-sm focus:border-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-200">
                    <option value="">{{ __('Select academic year') }}</option>
                    @foreach (($academicYearOptions ?? []) as $academicYearOption)
                        <option value="{{ $academicYearOption }}" @selected(old('academic_year', $submission?->academic_year ?? null) === $academicYearOption)>
                            {{ $academicYearOption }}
                        </option>
                    @endforeach
                </select>
                @error('academic_year')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="semester" class="block text-sm font-medium text-gray-700">{{ __('Semester') }}</label>
                <select id="semester" name="semester" required class="mt-1 w-full rounded-xl border border-gray-200 bg-white px-3 py-2 text-sm focus:border-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-200">
                    <option value="">{{ __('Select semester') }}</option>
                    @foreach (($semesterOptions ?? []) as $semesterOption)
                        <option value="{{ $semesterOption }}" @selected(old('semester', $submission?->semester ?? null) === $semesterOption)>
                            {{ ucfirst($semesterOption) }}
                        </option>
                    @endforeach
                </select>
                @error('semester')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div class="sm:col-span-2">
                <label for="students_count" class="block text-sm font-medium text-gray-700">{{ __('Number of students') }}</label>
                <input id="students_count" type="number" name="students_count" min="0" value="{{ old('students_count', $submission?->students_count ?? 0) }}" required class="mt-1 w-full max-w-xs rounded-xl border border-gray-200 px-3 py-2 text-sm focus:border-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-200" />
                @error('students_count')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>
    </div>

    <div class="rounded-xl border border-gray-200 bg-white p-6">
        <h2 class="text-sm font-semibold text-gray-900">{{ __('Exam materials (PDF)') }}</h2>
        <p class="mt-1 text-xs text-gray-500">{{ __('PDF only. Maximum 1 MB per file. Required documents must be included.') }}</p>

        <div class="mt-5 space-y-5">
            @foreach ([
                'file_questions' => __('Exam questions'),
                'file_marking_scheme' => __('Marking scheme'),
                'file_outline' => __('Course outline'),
            ] as $name => $label)
                <div>
                    <p class="text-sm font-medium text-gray-800">{{ $label }} <span class="text-red-600">*</span></p>
                    <div class="js-dropzone mt-2 rounded-xl border border-dashed border-gray-300 bg-gray-50 px-4 py-6 text-center transition-colors hover:border-gray-400 hover:bg-gray-50/80">
                        <input type="file" name="{{ $name }}" id="{{ $name }}_input" accept="application/pdf,.pdf" class="sr-only" />
                        <label for="{{ $name }}_input" class="cursor-pointer text-sm text-gray-600">
                            <span class="js-dropzone-label font-medium text-gray-700" data-default="{{ __('Drop PDF here or click to browse') }}">{{ __('Drop PDF here or click to browse') }}</span>
                            <span class="mt-1 block text-xs text-gray-500">{{ __('Max 1 MB') }}</span>
                        </label>
                    </div>
                    @error($name)
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            @endforeach

            <div>
                <p class="text-sm font-medium text-gray-800">{{ __('Supporting document') }} <span class="text-gray-400">({{ __('optional') }})</span></p>
                <div class="js-dropzone mt-2 rounded-xl border border-dashed border-gray-300 bg-gray-50 px-4 py-6 text-center transition-colors hover:border-gray-400">
                    <input type="file" name="file_supporting" id="file_supporting_input" accept="application/pdf,.pdf" class="sr-only" />
                    <label for="file_supporting_input" class="cursor-pointer text-sm text-gray-600">
                        <span class="js-dropzone-label font-medium text-gray-700" data-default="{{ __('Drop PDF here or click to browse') }}">{{ __('Drop PDF here or click to browse') }}</span>
                        <span class="mt-1 block text-xs text-gray-500">{{ __('Max 1 MB') }}</span>
                    </label>
                </div>
                @error('file_supporting')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>
    </div>

    <div class="flex flex-wrap gap-3">
        <x-button type="submit" variant="primary" class="js-submit-btn">{{ $isEdit ? __('Save new version') : __('Submit') }}</x-button>
        <x-button href="{{ $isEdit ? route('dashboard.submissions.show', $submission) : route('dashboard.submissions.index') }}" variant="secondary">{{ __('Cancel') }}</x-button>
    </div>
</form>
