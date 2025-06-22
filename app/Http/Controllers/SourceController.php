<?php

namespace App\Http\Controllers;

use App\Http\Requests\SourceStoreRequest;
use App\Http\Requests\SourceUpdateRequest;
use App\Models\Source;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Models\User;

class SourceController extends Controller
{

    public function index(): View
    {
        $sources = Source::latest()->paginate(10);
        return view('source.index', compact('sources'));
    }

    public function create(): View
    {
        // Get all users for the owner dropdown
        $users = \App\Models\User::all();

        // Get all user owners for the user as owner dropdown
        // Adjust this based on your actual model name for user owners
        $userOwners = \App\Models\User::whereHas('roles', function($q) {
            $q->where('name', 'owner');
        })->get();

        return view('source.create', compact('users', 'userOwners'));
    }
//SourceStoreRequest
    public function store(SourceStoreRequest $request): RedirectResponse
    {
        // 1. Get the validated data from the form request
        $validated = $request->validated();

        // 2. Check if owner_id was NOT provided in the request
        //    (This means a normal user without 'manage_source' permission submitted the form)
        if (!isset($validated['owner_id'])) {
            // 3. Add the default values for a normal user
            $validated['owner_id'] = auth()->id();
            $validated['status'] = 'pending';
        }

        // 4. Create the Source record.
        //    If an admin submitted, $validated already has the correct values.
        //    If a normal user submitted, we just added the default values.
        try {
            Source::create($validated);
            return redirect()
                ->route('sources.index')
                ->with('success', 'Source created successfully.');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Failed to create source. ' . $e->getMessage());
        }
    }

    public function show(Source $source): View
    {
        return view('source.show', compact('source'));
    }

    public function edit(Source $source): View
    {
        return view('source.edit', compact('source'));
    }

    public function update(SourceUpdateRequest $request, Source $source): RedirectResponse
    {
        try {
            $source->update($request->validated());
            return redirect()
                ->route('sources.index')
                ->with('success', 'Source updated successfully.');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Failed to update source. ' . $e->getMessage());
        }
    }

    public function destroy(Source $source): RedirectResponse
    {
        try {
            $source->delete();
            return redirect()
                ->route('sources.index')
                ->with('success', 'Source deleted successfully.');
        } catch (\Exception $e) {
            return back()
                ->with('error', 'Failed to delete source. ' . $e->getMessage());
        }
    }
}
