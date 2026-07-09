<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Tournament;

class TournamentController extends Controller
{
    public function index()
    {
        $query = Tournament::query()->orderByDesc('year');

        if ($year = request('year')) {
            $query->where('year', $year);
        }

        if ($status = request('status')) {
            $query->where('status', $status);
        }

        $tournaments = $query->paginate(12)->withQueryString();

        // Pluck unique years for the filter dropdown
        $years = Tournament::select('year')->distinct()->orderByDesc('year')->pluck('year');
        $statuses = ['UPCOMING', 'ONGOING', 'COMPLETED', 'CANCELLED'];

        return view('public.tournaments.index', compact('tournaments', 'years', 'statuses'));
    }

    public function show(Tournament $tournament)
    {
        $tournament->load(['groups', 'teams']);

        return view('public.tournaments.show', compact('tournament'));
    }
}
