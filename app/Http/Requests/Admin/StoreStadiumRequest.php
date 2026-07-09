<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreStadiumRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'name'         => ['required', 'string', 'max:150', Rule::unique('stadiums', 'name')],
            'city'         => ['required', 'string', 'max:100'],
            'country'      => ['required', 'string', 'max:100'],
            'capacity'     => ['required', 'integer', 'min:1000', 'max:200000'],
            'surface_type' => ['required', Rule::in(['GRASS', 'ARTIFICIAL', 'HYBRID', 'NATURAL GRASS'])],
        ];
    }

    public function messages(): array
    {
        return [
            'name.unique'          => 'A stadium with that name already exists.',
            'capacity.min'         => 'Capacity must be at least 1,000.',
            'surface_type.in'      => 'Surface type must be GRASS, ARTIFICIAL, or HYBRID.',
        ];
    }
}
