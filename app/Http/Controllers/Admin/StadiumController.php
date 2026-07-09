<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreStadiumRequest;
use App\Http\Requests\Admin\UpdateStadiumRequest;
use App\Models\Stadium;

class StadiumController extends Controller
{
    public function index()
    {
        $query = Stadium::query()->orderBy('name');

        if ($search = request('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name',    'like', '%' . $search . '%')
                  ->orWhere('city',  'like', '%' . $search . '%')
                  ->orWhere('country', 'like', '%' . $search . '%');
            });
        }

        if ($surface = request('surface_type')) {
            $query->where('surface_type', $surface);
        }

        $stadiums     = $query->paginate(15)->withQueryString();
        $surfaceTypes = ['GRASS', 'ARTIFICIAL', 'HYBRID'];

        return view('admin.stadiums.index', compact('stadiums', 'surfaceTypes'));
    }

    public function create()
    {
        $surfaceTypes = ['GRASS', 'ARTIFICIAL', 'HYBRID'];
        return view('admin.stadiums.create', compact('surfaceTypes'));
    }

    public function store(StoreStadiumRequest $request)
    {
        Stadium::create($request->validated());

        return redirect()->route('admin.stadiums.index')
            ->with('success', 'Stadium created successfully.');
    }

    public function edit(Stadium $stadium)
    {
        $surfaceTypes = ['GRASS', 'ARTIFICIAL', 'HYBRID'];
        return view('admin.stadiums.edit', compact('stadium', 'surfaceTypes'));
    }

    public function update(UpdateStadiumRequest $request, Stadium $stadium)
    {
        $stadium->update($request->validated());

        return redirect()->route('admin.stadiums.index')
            ->with('success', 'Stadium updated successfully.');
    }

    public function destroy(Stadium $stadium)
    {
        $name = $stadium->name;
        $stadium->delete();

        return redirect()->route('admin.stadiums.index')
            ->with('success', "Stadium \"{$name}\" deleted.");
    }
}
