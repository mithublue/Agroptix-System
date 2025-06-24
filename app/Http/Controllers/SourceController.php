<?php

namespace App\Http\Controllers;


use App\Http\Requests\SourceStoreRequest;
use App\Http\Requests\SourceUpdateRequest;
use App\Models\Source;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
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

    public function store(Request $request): RedirectResponse
    {
        // Define validation rules
        //    to get access to its rules and authorization logic.
        $formRequest = new SourceStoreRequest();

        // 2. Manually check authorization (IMPORTANT: This is NOT done automatically now)
        if (!$formRequest->authorize()) {
            abort(403, 'This action is unauthorized.');
        }

        // 3. Get the validation rules from your Form Request class.
        $rules = $formRequest->rules();

        // 4. Create a new validator instance manually.
        $validator = Validator::make($request->all(), $rules);

        // Validate the request
        if ($validator->fails()) {
            // 6. Manually handle the failure.
            //    To make it work with Hotwired Turbo, we must set the 422 status code.
            return back()->withErrors($validator)->withInput()->setStatusCode(422);
        }

        // 7. If validation passes, get the validated data.
        $validatedData = $validator->validated();

        // Set default values for non-admin users
        if (!isset($validatedData['owner_id'])) {
            $validatedData['owner_id'] = auth()->id();
            $validatedData['status'] = 'pending';
        }

        // Create the Source record
        try {
            Source::create($validatedData);
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
