<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCardRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'player_id'   => ['required', 'integer', 'exists:players,player_id'],
            'team_id'     => ['required', 'integer', 'exists:teams,team_id'],
            'card_type'   => ['required', Rule::in(['YELLOW', 'RED', 'SECOND_YELLOW'])],
            'card_minute' => ['required', 'integer', 'min:1', 'max:150'],
            'reason'      => ['nullable', 'string', 'max:255'],
        ];
    }
}
