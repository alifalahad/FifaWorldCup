<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCoachRequest;
use App\Http\Requests\Admin\UpdateCoachRequest;
use App\Models\Coach;

class CoachController extends Controller
{
    public function index()
    {
        $query = Coach::query()->orderBy('last_name')->orderBy('first_name');

        if ($search = request('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', '%' . $search . '%')
                  ->orWhere('last_name',  'like', '%' . $search . '%')
                  ->orWhere('nationality','like', '%' . $search . '%');
            });
        }

        $coaches = $query->paginate(20)->withQueryString();

        return view('admin.coaches.index', compact('coaches'));
    }

    public function create()
    {
        return view('admin.coaches.create');
    }

    public function store(StoreCoachRequest $request)
    {
        Coach::create($request->validated());

        return redirect()->route('admin.coaches.index')
            ->with('success', 'Coach created successfully.');
    }

    public function edit(Coach $coach)
    {
        return view('admin.coaches.edit', compact('coach'));
    }

    public function update(UpdateCoachRequest $request, Coach $coach)
    {
        $coach->update($request->validated());

        return redirect()->route('admin.coaches.index')
            ->with('success', 'Coach updated successfully.');
    }

    public function destroy(Coach $coach)
    {
        $name = $coach->first_name . ' ' . $coach->last_name;
        $coach->delete();

        return redirect()->route('admin.coaches.index')
            ->with('success', "Coach \"{$name}\" deleted.");
    }
}
