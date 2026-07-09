<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreMatchRequest;
use App\Http\Requests\Admin\UpdateMatchRequest;
use App\Models\GameMatch;
use App\Models\Referee;
use App\Models\Stadium;
use App\Models\Team;
use App\Models\TeamTournament;
use App\Models\Tournament;
use App\Models\TournamentGroup;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MatchController extends Controller
{
    private const STAGES = [
        'GROUP', 'ROUND_OF_16', 'QUARTER_FINAL', 'SEMI_FINAL', 'THIRD_PLACE', 'FINAL',
    ];

    private const STATUSES = [
        'SCHEDULED', 'LIVE', 'COMPLETED', 'POSTPONED', 'CANCELLED',
    ];

    // ── Index ──────────────────────────────────────────────────────────────

    public function index()
    {
        $query = GameMatch::with(['tournament', 'homeTeam', 'awayTeam', 'stadium', 'group'])
            ->orderByDesc('match_date')
            ->orderByDesc('match_id');

        if ($tid = request('tournament_id')) {
            $query->where('tournament_id', $tid);
        }
        if ($stage = request('stage')) {
            $query->where('stage', $stage);
        }
        if ($status = request('status')) {
            $query->where('status', $status);
        }

        $matches     = $query->paginate(20)->withQueryString();
        $tournaments = Tournament::orderByDesc('year')->get();

        return view('admin.matches.index', [
            'matches'     => $matches,
            'tournaments' => $tournaments,
            'stages'      => self::STAGES,
            'statuses'    => self::STATUSES,
        ]);
    }

    // ── Create & Store ─────────────────────────────────────────────────────

    public function create()
    {
        return view('admin.matches.create', $this->formData());
    }

    public function store(StoreMatchRequest $request)
    {
        $data = $request->validated();

        // Clear group_id for non-group stages
        if ($data['stage'] !== 'GROUP') {
            $data['group_id'] = null;
        }

        $data['has_extra_time'] = 'N';
        $data['has_penalties']  = 'N';

        GameMatch::create($data);

        return redirect()->route('admin.matches.index')
            ->with('success', 'Match scheduled successfully.');
    }

    // ── Edit & Update ──────────────────────────────────────────────────────

    public function edit(GameMatch $match)
    {
        return view('admin.matches.edit', array_merge(
            $this->formData($match->tournament_id),
            ['match' => $match]
        ));
    }

    public function update(UpdateMatchRequest $request, GameMatch $match)
    {
        $data = $request->validated();

        if ($data['stage'] !== 'GROUP') {
            $data['group_id'] = null;
        }

        $match->update($data);

        return redirect()->route('admin.matches.index')
            ->with('success', 'Match updated successfully.');
    }

    // ── Delete ─────────────────────────────────────────────────────────────

    public function destroy(GameMatch $match)
    {
        $label = ($match->homeTeam->abbreviation ?? '?') . ' vs ' . ($match->awayTeam->abbreviation ?? '?');
        $match->delete();

        return redirect()->route('admin.matches.index')
            ->with('success', "Match \"{$label}\" deleted.");
    }

    // ── Enter Result ───────────────────────────────────────────────────────

    public function enterResult(GameMatch $match)
    {
        return view('admin.matches.result', [
            'match'    => $match->load(['homeTeam', 'awayTeam', 'tournament']),
            'statuses' => self::STATUSES,
        ]);
    }

    public function storeResult(Request $request, GameMatch $match)
    {
        $validated = $request->validate([
            'home_score'     => ['required', 'integer', 'min:0', 'max:99'],
            'away_score'     => ['required', 'integer', 'min:0', 'max:99'],
            'has_extra_time' => ['nullable', 'boolean'],
            'has_penalties'  => ['nullable', 'boolean'],
            'status'         => ['required', Rule::in(self::STATUSES)],
        ]);

        $match->update([
            'home_score'     => $validated['home_score'],
            'away_score'     => $validated['away_score'],
            'has_extra_time' => ! empty($validated['has_extra_time']) ? 'Y' : 'N',
            'has_penalties'  => ! empty($validated['has_penalties'])  ? 'Y' : 'N',
            'status'         => $validated['status'],
        ]);

        return redirect()->route('admin.matches.index')
            ->with('success', 'Result saved. Group standings will reflect this result automatically.');
    }

    // ── API: teams for a tournament (used by JS in create/edit form) ───────

    public function teamsForTournament(Tournament $tournament)
    {
        $teams = TeamTournament::where('tournament_id', $tournament->tournament_id)
            ->with('team')
            ->get()
            ->map(fn ($tt) => [
                'team_id'      => $tt->team->team_id,
                'country_name' => $tt->team->country_name,
                'abbreviation' => $tt->team->abbreviation,
            ])
            ->sortBy('country_name')
            ->values();

        $groups = TournamentGroup::where('tournament_id', $tournament->tournament_id)
            ->orderBy('group_name')
            ->get(['group_id', 'group_name']);

        return response()->json([
            'teams'  => $teams,
            'groups' => $groups,
        ]);
    }

    // ── Helper ─────────────────────────────────────────────────────────────

    private function formData(?int $tournamentId = null): array
    {
        $tournaments = Tournament::orderByDesc('year')->get();
        $stadiums    = Stadium::orderBy('name')->get();
        $referees    = Referee::orderBy('last_name')->orderBy('first_name')->get();

        // Pre-load teams + groups for the selected tournament (for edit mode / old() fallback)
        $tournamentTeams  = collect();
        $tournamentGroups = collect();

        if ($tournamentId) {
            $tournamentTeams = TeamTournament::where('tournament_id', $tournamentId)
                ->with('team')
                ->get()
                ->sortBy('team.country_name');

            $tournamentGroups = TournamentGroup::where('tournament_id', $tournamentId)
                ->orderBy('group_name')
                ->get();
        }

        return compact(
            'tournaments', 'stadiums', 'referees',
            'tournamentTeams', 'tournamentGroups'
        ) + [
            'stages'   => self::STAGES,
            'statuses' => self::STATUSES,
        ];
    }
}
