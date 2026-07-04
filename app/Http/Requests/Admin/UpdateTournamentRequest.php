<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTournamentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        // Ignore the current tournament's year when checking uniqueness
        $tournamentId = $this->route('tournament');

        return [
            'name'         => ['required', 'string', 'max:100'],
            'year'         => [
                'required',
                'integer',
                'digits:4',
                'min:1900',
                'max:2100',
                Rule::unique('tournaments', 'year')->ignore($tournamentId, 'tournament_id'),
            ],
            'host_country' => ['required', 'string', 'max:100'],
            'start_date'   => ['required', 'date', 'before:end_date'],
            'end_date'     => ['required', 'date', 'after:start_date'],
            'total_teams'  => ['required', 'integer', 'min:2', 'max:48'],
            'status'       => ['required', Rule::in(['PLANNED', 'ONGOING', 'COMPLETED', 'CANCELLED'])],
        ];
    }

    public function messages(): array
    {
        return [
            'year.digits'       => 'Year must be a 4-digit number (e.g. 2026).',
            'year.unique'       => 'A tournament for that year already exists.',
            'start_date.before' => 'Start date must be before end date.',
            'end_date.after'    => 'End date must be after start date.',
        ];
    }
}
