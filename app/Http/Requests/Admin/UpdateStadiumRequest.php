<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateStadiumRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        $stadiumId = $this->route('stadium');

        return [
            'name'         => ['required', 'string', 'max:150',
                Rule::unique('stadiums', 'name')->ignore($stadiumId, 'stadium_id')],
            'city'         => ['required', 'string', 'max:100'],
            'country'      => ['required', 'string', 'max:100'],
            'capacity'     => ['required', 'integer', 'min:1000', 'max:200000'],
            'surface_type' => ['required', Rule::in(['GRASS', 'ARTIFICIAL', 'HYBRID', 'NATURAL GRASS'])],
        ];
    }

    public function messages(): array
    {
        return [
            'surface_type.in' => 'Surface type must be GRASS, ARTIFICIAL, or HYBRID.',
        ];
    }
}
