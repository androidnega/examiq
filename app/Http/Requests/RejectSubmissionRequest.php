<?php

namespace App\Http\Requests;

use App\Models\ExamSubmission;
use Illuminate\Foundation\Http\FormRequest;

class RejectSubmissionRequest extends FormRequest
{
    public function authorize(): bool
    {
        $examSubmission = $this->route('examSubmission');

        return $examSubmission instanceof ExamSubmission
            && $this->user()?->can('reject', $examSubmission);
    }

    public function rules(): array
    {
        return [
            'rejection_notes' => ['nullable', 'string', 'max:5000'],
        ];
    }
}
