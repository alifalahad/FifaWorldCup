<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePlayerRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'first_name'   => ['required', 'string', 'max:50'],
            'last_name'    => ['required', 'string', 'max:50'],
            'date_of_birth'=> ['required', 'date', 'before:today'],
            'nationality'  => ['required', 'string', 'max:100'],
            'position'     => ['required', Rule::in(['GK', 'DF', 'MF', 'FW'])],
            'height_cm'    => ['nullable', 'numeric', 'min:140', 'max:220'],
            'weight_kg'    => ['nullable', 'numeric', 'min:40', 'max:150'],
        ];
    }

    public function messages(): array
    {
        return [
            'date_of_birth.before' => 'Date of birth must be in the past.',
            'position.in'          => 'Position must be one of: GK, DF, MF, FW.',
        ];
    }
}
