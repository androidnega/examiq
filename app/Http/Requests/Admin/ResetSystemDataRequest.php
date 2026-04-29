<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ResetSystemDataRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isSuperAdmin() ?? false;
    }

    public function rules(): array
    {
        return [
            'confirmation_text' => ['required', 'string', 'in:RESET ALL DATA'],
            'reset_level' => ['required', 'string', 'in:default,entire_system'],
        ];
    }
}
