<?php

namespace App\Http\Requests;

use App\Models\ExamSubmission;
use App\Support\SubmissionSessionOptions;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SubmitRevisionRequest extends FormRequest
{
    public function authorize(): bool
    {
        $submission = $this->route('submission');

        return $submission instanceof ExamSubmission
            && $this->user()?->can('revise', $submission);
    }

    public function rules(): array
    {
        return [
            'academic_year' => ['required', 'string', 'max:32', Rule::in(SubmissionSessionOptions::academicYears())],
            'semester' => ['required', 'string', 'max:32', Rule::in(SubmissionSessionOptions::semesters())],
            'students_count' => ['required', 'integer', 'min:0', 'max:99999'],
            'revision_notes' => ['required', 'string', 'max:10000'],
            'received_moderated_questions' => ['nullable', 'boolean'],
            'received_moderated_marking_scheme' => ['nullable', 'boolean'],
            'received_course_outline' => ['nullable', 'boolean'],
            'received_moderator_comment_sheet' => ['nullable', 'boolean'],
            'received_from_moderator_on' => ['required', 'date'],
            'moderator_general_comment' => [
                'required',
                'string',
                Rule::in(['minor_corrections', 'with_modifications', 'rejected_new_questions']),
            ],
            'response_action_taken' => ['required', 'string', 'max:10000'],
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
            'revision_notes' => __('revision notes'),
            'received_moderated_questions' => __('moderated questions'),
            'received_moderated_marking_scheme' => __('moderated marked scheme'),
            'received_course_outline' => __('course outline'),
            'received_moderator_comment_sheet' => __('moderator comment sheet'),
            'received_from_moderator_on' => __('receipt date'),
            'moderator_general_comment' => __('moderator general comment'),
            'response_action_taken' => __('response and action taken'),
        ];
    }

    public function messages(): array
    {
        return [
            'file_questions.required' => __('Please upload the exam questions PDF (max 1 MB).'),
            'file_marking_scheme.required' => __('Please upload the marking scheme PDF (max 1 MB).'),
            'file_outline.required' => __('Please upload the course outline PDF (max 1 MB).'),
            'revision_notes.required' => __('Please describe what you changed in this revision.'),
            'received_from_moderator_on.required' => __('Please provide the date you received the moderated pack.'),
            'moderator_general_comment.required' => __('Please select the moderator general comment.'),
            'response_action_taken.required' => __('Please provide your response and action taken.'),
        ];
    }
}
