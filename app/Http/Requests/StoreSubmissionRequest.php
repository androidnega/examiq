<?php

namespace App\Http\Requests;

use App\Models\Course;
use App\Models\ExamSubmission;
use App\Support\SubmissionSessionOptions;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSubmissionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', ExamSubmission::class) ?? false;
    }

    public function rules(): array
    {
        return [
            'course_id' => ['required', 'uuid', 'exists:courses,id'],
            'academic_year' => ['required', 'string', 'max:32', Rule::in(SubmissionSessionOptions::academicYears())],
            'semester' => ['required', 'string', 'max:32', Rule::in(SubmissionSessionOptions::semesters())],
            'students_count' => ['required', 'integer', 'min:0', 'max:99999'],
            'file_questions' => ['required', 'file', 'mimes:pdf', 'max:1024'],
            'file_marking_scheme' => ['required', 'file', 'mimes:pdf', 'max:1024'],
            'file_outline' => ['required', 'file', 'mimes:pdf', 'max:1024'],
            'file_supporting' => ['nullable', 'file', 'mimes:pdf', 'max:1024'],
        ];
    }

    public function attributes(): array
    {
        return [
            'file_questions' => __('exam questions PDF'),
            'file_marking_scheme' => __('marking scheme PDF'),
            'file_outline' => __('course outline PDF'),
            'file_supporting' => __('supporting document PDF'),
        ];
    }

    public function messages(): array
    {
        return [
            'file_questions.required' => __('Please upload the exam questions PDF (max 1 MB).'),
            'file_marking_scheme.required' => __('Please upload the marking scheme PDF (max 1 MB).'),
            'file_outline.required' => __('Please upload the course outline PDF (max 1 MB).'),
            'file_questions.mimes' => __('Exam questions must be a PDF file.'),
            'file_marking_scheme.mimes' => __('Marking scheme must be a PDF file.'),
            'file_outline.mimes' => __('Course outline must be a PDF file.'),
            'file_supporting.mimes' => __('Supporting document must be a PDF file.'),
            'file_questions.max' => __('Each file must be 1 MB or smaller.'),
            'file_marking_scheme.max' => __('Each file must be 1 MB or smaller.'),
            'file_outline.max' => __('Each file must be 1 MB or smaller.'),
            'file_supporting.max' => __('Each file must be 1 MB or smaller.'),
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator): void {
            $user = $this->user();
            if ($user && $user->department_id === null) {
                $validator->errors()->add('course_id', __('You must belong to a department to create a submission.'));
            }

            $courseId = $this->input('course_id');
            if (! $user || ! $courseId || $user->department_id === null) {
                return;
            }

            $belongs = Course::query()
                ->whereKey($courseId)
                ->where('department_id', $user->department_id)
                ->exists();

            if (! $belongs) {
                $validator->errors()->add('course_id', __('Select a course from your department.'));
            }
        });
    }
}
