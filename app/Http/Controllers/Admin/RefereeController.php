<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreRefereeRequest;
use App\Http\Requests\Admin\UpdateRefereeRequest;
use App\Models\Referee;

class RefereeController extends Controller
{
    public function index()
    {
        $query = Referee::query()->orderBy('last_name')->orderBy('first_name');

        if ($search = request('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('first_name',  'like', '%' . $search . '%')
                  ->orWhere('last_name', 'like', '%' . $search . '%')
                  ->orWhere('nationality', 'like', '%' . $search . '%');
            });
        }

        $referees = $query->paginate(15)->withQueryString();

        return view('admin.referees.index', compact('referees'));
    }

    public function create()
    {
        return view('admin.referees.create');
    }

    public function store(StoreRefereeRequest $request)
    {
        Referee::create($request->validated());

        return redirect()->route('admin.referees.index')
            ->with('success', 'Referee created successfully.');
    }

    public function edit(Referee $referee)
    {
        return view('admin.referees.edit', compact('referee'));
    }

    public function update(UpdateRefereeRequest $request, Referee $referee)
    {
        $referee->update($request->validated());

        return redirect()->route('admin.referees.index')
            ->with('success', 'Referee updated successfully.');
    }

    public function destroy(Referee $referee)
    {
        $name = $referee->first_name . ' ' . $referee->last_name;
        $referee->delete();

        return redirect()->route('admin.referees.index')
            ->with('success', "Referee \"{$name}\" deleted.");
    }
}
