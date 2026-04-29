<?php

namespace App\Http\Requests;

use App\Enums\ModerationStatus;
use App\Models\ExamSubmission;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SubmitModerationReviewRequest extends FormRequest
{
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
            'marking_scheme_assessment' => [
                'required',
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
