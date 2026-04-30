<?php

namespace App\Http\Requests;

use App\Enums\ModerationStatus;
use App\Models\ExamSubmission;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SubmitModerationReviewRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        $questionAssessments = array_values(array_filter((array) $this->input('question_paper_assessments')));
        $schemeAssessments = array_values(array_filter((array) $this->input('marking_scheme_assessments')));

        $this->merge([
            'question_paper_assessment' => $questionAssessments[0] ?? $this->input('question_paper_assessment'),
            'marking_scheme_assessment' => $schemeAssessments[0] ?? $this->input('marking_scheme_assessment'),
        ]);
    }

    public function authorize(): bool
    {
        $examSubmission = $this->route('examSubmission');

        return $examSubmission instanceof ExamSubmission
            && $this->user()?->can('review', $examSubmission);
    }

    public function rules(): array
    {
        $gradeRule = ['required', 'string', Rule::in(['A', 'B', 'C', 'D', 'E'])];

        return [
            'status' => [
                'required',
                'string',
                Rule::in([
                    ModerationStatus::Accepted->value,
                    ModerationStatus::MinorChanges->value,
                    ModerationStatus::MajorChanges->value,
                    ModerationStatus::Rejected->value,
                ]),
            ],
            'feedback' => ['nullable', 'string', 'max:10000'],
            'rubric_1_grade' => $gradeRule,
            'rubric_2_grade' => $gradeRule,
            'rubric_3_grade' => $gradeRule,
            'rubric_4_grade' => $gradeRule,
            'rubric_5_grade' => $gradeRule,
            'rubric_6_grade' => $gradeRule,
            'rubric_7_grade' => $gradeRule,
            'rubric_8_grade' => $gradeRule,
            'rubric_9_grade' => $gradeRule,
            'rubric_10_grade' => $gradeRule,
            'rubric_11_grade' => $gradeRule,
            'question_count_section_a' => ['required', 'integer', 'min:0'],
            'question_count_section_b' => ['required', 'integer', 'min:0'],
            'question_count_section_c' => ['required', 'integer', 'min:0'],
            'paper_duration' => ['required', 'string', 'max:64'],
            'recommend_accept_questions' => ['nullable', 'string', 'max:5000'],
            'recommend_reject_questions' => ['nullable', 'string', 'max:5000'],
            'recommend_reset_questions' => ['nullable', 'string', 'max:5000'],
            'question_paper_comments' => ['nullable', 'string', 'max:10000'],
            'marking_scheme_comments' => ['nullable', 'string', 'max:10000'],
            'question_paper_assessment' => [
                'required',
                'string',
                Rule::in([
                    'accepted_without_corrections',
                    'accepted_minor_corrections',
                    'accepted_with_modifications',
                    'rejected_new_questions',
                ]),
            ],
            'question_paper_assessments' => ['required', 'array', 'min:1'],
            'question_paper_assessments.*' => [
                'string',
                Rule::in([
                    'accepted_without_corrections',
                    'accepted_minor_corrections',
                    'accepted_with_modifications',
                    'rejected_new_questions',
                ]),
            ],
            'marking_scheme_assessment' => [
                'required',
                'string',
                Rule::in([
                    'accepted_all',
                    'to_be_reprepared',
                ]),
            ],
            'marking_scheme_assessments' => ['required', 'array', 'min:1'],
            'marking_scheme_assessments.*' => [
                'string',
                Rule::in([
                    'accepted_all',
                    'to_be_reprepared',
                ]),
            ],
            'overall_rating' => [
                'required',
                'string',
                Rule::in([
                    'excellent',
                    'very_good',
                    'good',
                    'satisfactory',
                    'unsatisfactory',
                ]),
            ],
            'improvement_comments' => ['nullable', 'string', 'max:10000'],
            'moderated_on' => ['required', 'date'],
            'moderator_signature_name' => ['required', 'string', 'max:255'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator): void {
            $status = $this->input('status');
            $feedback = $this->input('feedback');
            if ($status === ModerationStatus::Accepted->value) {
                return;
            }
            if ($feedback === null || trim((string) $feedback) === '') {
                $validator->errors()->add('feedback', __('Please add feedback for this outcome.'));
            }
        });
    }
}
