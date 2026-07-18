<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreTournamentRequest;
use App\Http\Requests\Admin\UpdateTournamentRequest;
use App\Models\Coach;
use App\Models\Team;
use App\Models\TeamTournament;
use App\Models\Tournament;
use App\Models\TournamentGroup;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TournamentController extends Controller
{
    /**
     * List tournaments with optional search + filter.
     */
    public function index()
    {
        $query = Tournament::query()->orderByDesc('year');

        // Search by name
        if ($search = request('search')) {
            $query->where('name', 'like', '%' . $search . '%');
        }

        // Filter by status
        if ($status = request('status')) {
            $query->where('status', $status);
        }

        $tournaments = $query->paginate(15)->withQueryString();

        $statuses = ['PLANNED', 'ONGOING', 'COMPLETED', 'CANCELLED'];

        return view('admin.tournaments.index', compact('tournaments', 'statuses'));
    }

    /**
     * Show the create form.
     */
    public function create()
    {
        $statuses = ['PLANNED', 'ONGOING', 'COMPLETED', 'CANCELLED'];
        return view('admin.tournaments.create', compact('statuses'));
    }

    /**
     * Store a new tournament.
     */
    public function store(StoreTournamentRequest $request)
    {
        Tournament::create($request->validated());

        return redirect()
            ->route('admin.tournaments.index')
            ->with('success', 'Tournament created successfully.');
    }

    /**
     * Show a single tournament's detail, groups, and teams.
     */
    public function show(Tournament $tournament)
    {
        $tournament->load([
            'groups',
            'teamTournaments.team',
            'teamTournaments.coach',
            'teamTournaments.group',
        ]);

        return view('admin.tournaments.show', compact('tournament'));
    }

    /**
     * Show the edit form.
     */
    public function edit(Tournament $tournament)
    {
        $tournament->load('groups');
        $statuses = ['PLANNED', 'ONGOING', 'COMPLETED', 'CANCELLED'];
        return view('admin.tournaments.edit', compact('tournament', 'statuses'));
    }

    /**
     * Update the tournament.
     */
    public function update(UpdateTournamentRequest $request, Tournament $tournament)
    {
        $tournament->update($request->validated());

        return redirect()
            ->route('admin.tournaments.show', $tournament)
            ->with('success', 'Tournament updated successfully.');
    }

    /**
     * Delete the tournament.
     * Cascades to tournament_groups and team_tournament (via DB FK cascades).
     */
    public function destroy(Tournament $tournament)
    {
        $name = $tournament->name;
        $tournament->delete();

        return redirect()
            ->route('admin.tournaments.index')
            ->with('success', "Tournament \"{$name}\" deleted.");
    }

    /**
     * Add a new group to this tournament.
     */
    public function addGroup(Request $request, Tournament $tournament)
    {
        $validated = $request->validate([
            'group_name' => [
                'required',
                'string',
                'max:10',
                // A group name must be unique within the same tournament
                Rule::unique('tournament_groups', 'group_name')
                    ->where('tournament_id', $tournament->tournament_id),
            ],
        ], [
            'group_name.unique' => 'Group "' . $request->group_name . '" already exists for this tournament.',
        ]);

        TournamentGroup::create([
            'tournament_id' => $tournament->tournament_id,
            'group_name'    => strtoupper(trim($validated['group_name'])),
        ]);

        return redirect()
            ->route('admin.tournaments.show', $tournament->tournament_id)
            ->with('success', 'Group "' . strtoupper($request->group_name) . '" added successfully.');
    }

    /**
     * Remove a group from this tournament.
     */
    public function removeGroup(Tournament $tournament, TournamentGroup $group)
    {
        if ($group->tournament_id !== $tournament->tournament_id) {
            abort(403, 'This group does not belong to this tournament.');
        }

        $name = $group->group_name;
        $group->delete();

        return redirect()
            ->route('admin.tournaments.show', $tournament->tournament_id)
            ->with('success', 'Group "' . $name . '" removed.');
    }

    /**
     * Show the Register Team form for a given tournament.
     */
    public function registerTeam(Tournament $tournament)
    {
        $teams   = Team::orderBy('country_name')->get();
        $coaches = Coach::orderBy('last_name')->orderBy('first_name')->get();
        $groups  = $tournament->groups()->orderBy('group_name')->get();

        // Teams already registered for this tournament
        $registeredTeamIds = TeamTournament::where('tournament_id', $tournament->tournament_id)
            ->pluck('team_id')
            ->toArray();

        return view('admin.tournaments.register-team', compact(
            'tournament', 'teams', 'coaches', 'groups', 'registeredTeamIds'
        ));
    }

    /**
     * Store a new TeamTournament registration.
     */
    public function storeRegisteredTeam(Request $request, Tournament $tournament)
    {
        $validated = $request->validate([
            'team_id'           => [
                'required',
                'integer',
                'exists:teams,team_id',
                // Friendly duplicate check
                Rule::unique('team_tournament', 'team_id')
                    ->where('tournament_id', $tournament->tournament_id),
            ],
            'group_id'          => [
                'nullable', 'integer',
                // Ensure group belongs to THIS tournament
                Rule::exists('tournament_groups', 'group_id')
                    ->where('tournament_id', $tournament->tournament_id),
            ],
            'coach_id'          => ['nullable', 'integer', 'exists:coaches,coach_id'],
            'seed_position'     => ['nullable', 'integer', 'min:1', 'max:48'],
            'elimination_stage' => [
                'nullable',
                Rule::in([
                    'GROUP', 'ROUND_OF_16', 'QUARTER_FINAL',
                    'SEMI_FINAL', 'THIRD_PLACE', 'FINAL', 'CHAMPION',
                ]),
            ],
        ], [
            'team_id.unique'          => 'That team is already registered for this tournament.',
            'group_id.exists'         => 'The selected group does not belong to this tournament.',
            'elimination_stage.in'    => 'Invalid elimination stage value.',
        ]);

        $validated['tournament_id'] = $tournament->tournament_id;
        TeamTournament::create($validated);

        return redirect()
            ->route('admin.tournaments.show', $tournament->tournament_id)
            ->with('success', 'Team registered for tournament successfully.');
    }
}
