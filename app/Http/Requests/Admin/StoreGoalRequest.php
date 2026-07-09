<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreGoalRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'scorer_player_id' => ['required', 'integer', 'exists:players,player_id'],
            'assist_player_id' => ['nullable', 'integer', 'exists:players,player_id'],
            'team_id'          => ['required', 'integer', 'exists:teams,team_id'],
            'goal_minute'      => ['required', 'integer', 'min:1', 'max:150'],
            'goal_type'        => ['required', Rule::in(['OPEN_PLAY', 'PENALTY', 'OWN_GOAL', 'FREE_KICK', 'HEADER'])],
            'half'             => ['required', Rule::in(['1ST', '2ND', 'ET1', 'ET2'])],
        ];
    }
}
