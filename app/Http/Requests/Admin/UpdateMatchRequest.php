<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateMatchRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'tournament_id' => ['required', 'integer', 'exists:tournaments,tournament_id'],
            'stadium_id'    => ['required', 'integer', 'exists:stadiums,stadium_id'],
            'referee_id'    => ['nullable', 'integer', 'exists:referees,referee_id'],
            'home_team_id'  => ['required', 'integer', 'exists:teams,team_id'],
            'away_team_id'  => [
                'required', 'integer', 'exists:teams,team_id',
                function ($attribute, $value, $fail) {
                    if ((int)$value === (int)$this->input('home_team_id')) {
                        $fail('The away team must be different from the home team.');
                    }
                },
            ],
            'group_id'      => [
                'nullable', 'integer', 'exists:tournament_groups,group_id',
                function ($attribute, $value, $fail) {
                    $stage = $this->input('stage');
                    if ($stage === 'GROUP' && empty($value)) {
                        $fail('A group must be selected for Group Stage matches.');
                    }
                    if ($stage !== 'GROUP' && !empty($value)) {
                        $fail('Group must not be set for knockout stage matches.');
                    }
                },
            ],
            'match_date'    => ['required', 'date'],
            'stage'         => ['required', Rule::in([
                'GROUP', 'ROUND_OF_16', 'QUARTER_FINAL', 'SEMI_FINAL', 'THIRD_PLACE', 'FINAL',
            ])],
            'status'        => ['required', Rule::in([
                'SCHEDULED', 'LIVE', 'COMPLETED', 'POSTPONED', 'CANCELLED',
            ])],
        ];
    }

    public function messages(): array
    {
        return [
            'away_team_id.required' => 'Please select the away team.',
            'home_team_id.required' => 'Please select the home team.',
            'stage.in'   => 'Stage must be one of: GROUP, ROUND_OF_16, QUARTER_FINAL, SEMI_FINAL, THIRD_PLACE, FINAL.',
            'status.in'  => 'Status must be one of: SCHEDULED, LIVE, COMPLETED, POSTPONED, CANCELLED.',
        ];
    }
}
