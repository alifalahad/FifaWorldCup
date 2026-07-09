<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreGoalRequest;
use App\Http\Requests\Admin\StoreCardRequest;
use App\Models\GameMatch;
use App\Models\Goal;
use App\Models\Card;

class MatchEventController extends Controller
{
    public function storeGoal(StoreGoalRequest $request, GameMatch $match)
    {
        $match->goals()->create($request->validated());

        return redirect()->route('admin.matches.edit', $match->match_id)
            ->with('success', 'Goal added successfully.');
    }

    public function destroyGoal(GameMatch $match, Goal $goal)
    {
        if ($goal->match_id !== $match->match_id) {
            abort(404);
        }

        $goal->delete();

        return redirect()->route('admin.matches.edit', $match->match_id)
            ->with('success', 'Goal deleted successfully.');
    }

    public function storeCard(StoreCardRequest $request, GameMatch $match)
    {
        $match->cards()->create($request->validated());

        return redirect()->route('admin.matches.edit', $match->match_id)
            ->with('success', 'Card added successfully.');
    }

    public function destroyCard(GameMatch $match, Card $card)
    {
        if ($card->match_id !== $match->match_id) {
            abort(404);
        }

        $card->delete();

        return redirect()->route('admin.matches.edit', $match->match_id)
            ->with('success', 'Card deleted successfully.');
    }
}
