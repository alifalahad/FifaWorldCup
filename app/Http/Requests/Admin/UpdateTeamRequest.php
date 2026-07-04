<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTeamRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        $teamId = $this->route('team');

        return [
            'country_name' => ['required', 'string', 'max:100',
                Rule::unique('teams', 'country_name')->ignore($teamId, 'team_id')],
            'abbreviation' => ['required', 'string', 'size:3', 'alpha',
                Rule::unique('teams', 'abbreviation')->ignore($teamId, 'team_id')],
            'continent'    => ['required', Rule::in(['AFC','CAF','CONCACAF','CONMEBOL','OFC','UEFA'])],
            'fifa_ranking' => ['nullable', 'integer', 'min:1', 'max:999'],
        ];
    }

    public function messages(): array
    {
        return [
            'abbreviation.size'  => 'Abbreviation must be exactly 3 letters.',
            'abbreviation.alpha' => 'Abbreviation must contain only letters.',
        ];
    }
}
