<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreTeamRequest;
use App\Http\Requests\Admin\UpdateTeamRequest;
use App\Models\Team;

class TeamController extends Controller
{
    public function index()
    {
        $query = Team::query()->orderBy('country_name');

        if ($search = request('search')) {
            $query->where('country_name', 'like', '%' . $search . '%')
                  ->orWhere('abbreviation', 'like', '%' . strtoupper($search) . '%');
        }

        if ($continent = request('continent')) {
            $query->where('continent', $continent);
        }

        $teams      = $query->paginate(20)->withQueryString();
        $continents = ['AFC', 'CAF', 'CONCACAF', 'CONMEBOL', 'OFC', 'UEFA'];

        return view('admin.teams.index', compact('teams', 'continents'));
    }

    public function create()
    {
        $continents = ['AFC', 'CAF', 'CONCACAF', 'CONMEBOL', 'OFC', 'UEFA'];
        return view('admin.teams.create', compact('continents'));
    }

    public function store(StoreTeamRequest $request)
    {
        $data = $request->validated();
        $data['abbreviation'] = strtoupper($data['abbreviation']);
        Team::create($data);

        return redirect()->route('admin.teams.index')
            ->with('success', 'Team created successfully.');
    }

    public function edit(Team $team)
    {
        $continents = ['AFC', 'CAF', 'CONCACAF', 'CONMEBOL', 'OFC', 'UEFA'];
        return view('admin.teams.edit', compact('team', 'continents'));
    }

    public function update(UpdateTeamRequest $request, Team $team)
    {
        $data = $request->validated();
        $data['abbreviation'] = strtoupper($data['abbreviation']);
        $team->update($data);

        return redirect()->route('admin.teams.index')
            ->with('success', 'Team updated successfully.');
    }

    public function destroy(Team $team)
    {
        $name = $team->country_name;
        $team->delete();

        return redirect()->route('admin.teams.index')
            ->with('success', "Team \"{$name}\" deleted.");
    }
}
