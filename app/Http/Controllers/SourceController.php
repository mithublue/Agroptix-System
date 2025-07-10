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
        $this->authorize('viewAny', Source::class);

        // Get the sources based on user's permissions
        $sources = Source::when(!auth()->user()->can('manage_source'), function($query) {
                // For non-admin users, only show sources they own
                return $query->where('owner_id', auth()->id());
            })
            ->latest()
            ->paginate(10);

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

    public function store(SourceStoreRequest $request): RedirectResponse
    {
        $this->authorize('create', Source::class);

        // If user doesn't have manage_source permission, set owner_id to current user
        if (!auth()->user()->can('manage_source')) {
            $request->merge(['owner_id' => auth()->id()]);
        }

        // Define validation rules
        //    to get access to its rules and authorization logic.
        $formRequest = new SourceStoreRequest();

        // 2. Manually check authorization (IMPORTANT: This is NOT done automatically now)
        if (!$formRequest->authorize()) {
            abort( 403, 'This action is unauthorized.' );
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
        $this->authorize('view', $source);
        return view('source.show', compact('source'));
    }

    public function edit(Source $source): View
    {
        $this->authorize('edit', $source);
        return view('source.edit', compact('source'));
    }

    public function update(SourceUpdateRequest $request, Source $source): RedirectResponse
    {
        $this->authorize('edit', $source);

        // If user doesn't have manage_source permission, ensure they can't change the owner
        if (!auth()->user()->can('manage_source')) {
            $request->merge(['owner_id' => $source->owner_id]);
        }

        // Define validation rules
        //    to get access to its rules and authorization logic.
        $formRequest = new SourceUpdateRequest();

        // 2. Manually check authorization (IMPORTANT: This is NOT done automatically now)
        if (!$formRequest->authorize()) {
            abort( 403, 'This action is unauthorized.' );
        }
        // 3. Get the validation rules from your Form Request class.
        $rules = $formRequest->rules();

        // Create a new validator instance manually
        $validator = Validator::make($request->all(), $rules);

        // Validate the request
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput()->setStatusCode(422);
        }

        // Get the validated data
        $validatedData = $validator->validated();

        try {
            $source->update($validatedData);
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
        $this->authorize('delete', $source);

        $source->delete();

        return redirect()->route('sources.index')
            ->with('success', 'Source deleted successfully.');
    }

    /**
     * Update the status of a source via AJAX.
     *
     * @param  \App\Models\Source  $source
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateStatus(Source $source, \Illuminate\Http\Request $request)
    {
        $this->authorize('edit', $source);
        
        $validated = $request->validate([
            'status' => ['required', 'string', 'in:' . implode(',', array_keys(config('at.source_status')))]
        ]);
        
        $source->update(['status' => $validated['status']]);
        
        return response()->json([
            'success' => true,
            'message' => 'Status updated successfully',
            'status' => $source->status,
            'status_label' => config('at.source_status')[$source->status] ?? $source->status
        ]);
    }
}
