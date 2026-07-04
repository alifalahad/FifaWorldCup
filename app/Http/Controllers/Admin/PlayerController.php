<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StorePlayerRequest;
use App\Http\Requests\Admin\UpdatePlayerRequest;
use App\Models\Player;

class PlayerController extends Controller
{
    public function index()
    {
        $query = Player::query()->orderBy('last_name')->orderBy('first_name');

        if ($search = request('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', '%' . $search . '%')
                  ->orWhere('last_name',   'like', '%' . $search . '%')
                  ->orWhere('nationality', 'like', '%' . $search . '%');
            });
        }

        if ($position = request('position')) {
            $query->where('position', $position);
        }

        $players   = $query->paginate(25)->withQueryString();
        $positions = ['GK', 'DF', 'MF', 'FW'];

        return view('admin.players.index', compact('players', 'positions'));
    }

    public function create()
    {
        $positions = ['GK', 'DF', 'MF', 'FW'];
        return view('admin.players.create', compact('positions'));
    }

    public function store(StorePlayerRequest $request)
    {
        Player::create($request->validated());

        return redirect()->route('admin.players.index')
            ->with('success', 'Player created successfully.');
    }

    public function edit(Player $player)
    {
        $positions = ['GK', 'DF', 'MF', 'FW'];
        return view('admin.players.edit', compact('player', 'positions'));
    }

    public function update(UpdatePlayerRequest $request, Player $player)
    {
        $player->update($request->validated());

        return redirect()->route('admin.players.index')
            ->with('success', 'Player updated successfully.');
    }

    public function destroy(Player $player)
    {
        $name = $player->first_name . ' ' . $player->last_name;
        $player->delete();

        return redirect()->route('admin.players.index')
            ->with('success', "Player \"{$name}\" deleted.");
    }
}
