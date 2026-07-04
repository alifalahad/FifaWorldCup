<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCoachRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'first_name'       => ['required', 'string', 'max:50'],
            'last_name'        => ['required', 'string', 'max:50'],
            'nationality'      => ['required', 'string', 'max:100'],
            'coaching_license' => ['nullable', 'string', 'max:50'],
        ];
    }
}
