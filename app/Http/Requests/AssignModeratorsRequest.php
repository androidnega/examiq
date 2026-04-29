<?php

namespace App\Http\Requests;

use App\Enums\UserRole;
use App\Models\Course;
use App\Models\ExamSubmission;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AssignModeratorsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role === UserRole::Hod;
    }

    public function rules(): array
    {
        return [
            'submission_ids' => ['required', 'array', 'min:1'],
            'submission_ids.*' => ['uuid', 'exists:exam_submissions,id'],
            'moderator_ids' => ['required', 'array', 'min:1'],
            'moderator_ids.*' => ['uuid', 'exists:users,id'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator): void {
            $hod = $this->user();
            if (! $hod || $hod->department_id === null) {
                return;
            }

            $submissionIds = $this->input('submission_ids', []);
            $moderatorIds = $this->input('moderator_ids', []);

            if (! is_array($submissionIds) || ! is_array($moderatorIds)) {
                return;
            }

            foreach ($submissionIds as $id) {
                $submission = ExamSubmission::query()->with('course')->find($id);
                if (! $submission || ! $submission->course || $submission->course->department_id !== $hod->department_id) {
                    $validator->errors()->add('submission_ids', __('Each submission must belong to your department.'));

                    return;
                }
            }

            foreach ($moderatorIds as $id) {
                $moderator = User::query()->find($id);
                if (! $moderator || $moderator->role !== UserRole::Moderator) {
                    $validator->errors()->add('moderator_ids', __('Only moderators can be assigned.'));

                    return;
                }
                if ($moderator->department_id !== $hod->department_id) {
                    $validator->errors()->add('moderator_ids', __('Moderators must be in your department.'));

                    return;
                }
            }
        });
    }
}
