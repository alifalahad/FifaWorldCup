<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreRefereeRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'first_name'     => ['required', 'string', 'max:50'],
            'last_name'      => ['required', 'string', 'max:50'],
            'nationality'    => ['required', 'string', 'max:100'],
            'fifa_badge_year'=> ['nullable', 'integer', 'digits:4', 'min:1900', 'max:' . (date('Y') + 1)],
        ];
    }

    public function messages(): array
    {
        return [
            'fifa_badge_year.digits' => 'FIFA badge year must be a 4-digit year.',
        ];
    }
}
