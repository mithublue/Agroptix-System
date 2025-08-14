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

    public function index(Request $request): View
    {
        $this->authorize('viewAny', Source::class);

        // Get filter values from request
        $filters = $request->only(['type', 'production_method', 'status', 'owner_id', 'q']);

        // Get the sources based on user's permissions and filters
        $sources = Source::when(!auth()->user()->can('manage_source'), function($query) {
                // For non-admin users, only show sources they own
                return $query->where('owner_id', auth()->id());
            })
            ->when($request->filled('type'), function($query) use ($request) {
                return $query->where('type', $request->type);
            })
            ->when($request->filled('production_method'), function($query) use ($request) {
                return $query->where('production_method', $request->production_method);
            })
            ->when($request->filled('status'), function($query) use ($request) {
                return $query->where('status', $request->status);
            })
            ->when($request->filled('owner_id'), function($query) use ($request) {
                return $query->where('owner_id', $request->owner_id);
            })
            // Type-sensitive search
            ->when($request->filled('q'), function ($query) use ($request) {
                $q = trim((string) $request->q);
                $query->where(function ($sub) use ($q) {
                    $sub->where('type', 'like', "%{$q}%")
                        ->orWhere('production_method', 'like', "%{$q}%")
                        ->orWhere('address_line1', 'like', "%{$q}%")
                        ->orWhere('state', 'like', "%{$q}%")
                        ->orWhere('country_code', 'like', "%{$q}%");
                    if (is_numeric($q)) {
                        $sub->orWhere('area', '=', (float) $q)
                            ->orWhere('id', '=', (int) $q);
                    }
                    $sub->orWhereHas('owner', function ($o) use ($q) {
                        $o->where('name', 'like', "%{$q}%");
                    });
                });
            })
            ->with('owner')
            ->latest()
            ->paginate(10)
            ->withQueryString();

        // Get filter options
        $types = array_merge(['' => 'All Types'], config('at.type', []));
        $productionMethods = array_merge(['' => 'All Methods'], config('at.production_methods', []));
        $statuses = array_merge(['' => 'All Statuses'], config('at.source_status', []));

        // Get unique owners who have sources using a direct join
        $owners = ['' => 'All Owners'] +
            User::join('sources', 'users.id', '=', 'sources.owner_id')
                ->select('users.id', 'users.name')
                ->distinct()
                ->pluck('users.name', 'users.id')
                ->toArray();

        return view('source.index', compact(
            'sources',
            'filters',
            'types',
            'productionMethods',
            'statuses',
            'owners'
        ));
    }

    /**
     * Bulk delete selected sources.
     */
    public function bulkDestroy(Request $request): RedirectResponse
    {
        abort_unless(auth()->user() && auth()->user()->can('delete_source'), 403);

        $ids = array_filter(array_map('intval', (array) $request->input('ids', [])));
        if (empty($ids)) {
            return redirect()->to($request->input('redirect', route('sources.index')))
                ->with('error', 'No sources selected.');
        }

        $count = Source::whereIn('id', $ids)->count();
        if ($count > 0) {
            Source::whereIn('id', $ids)->delete();
        }

        return redirect()->to($request->input('redirect', route('sources.index')))
            ->with('success', $count . ' source(s) deleted successfully.');
    }

    public function create(): View
    {
        $this->authorize('create', Source::class);

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

        // Map country and state fields
        $validatedData['country_code'] = $validatedData['country'] ?? null;
        unset($validatedData['country']);

        // Create the Source record
        try {
            // Ensure non-fillable keys are not passed to mass assignment
            $data = $validatedData;
            unset($data['product_ids']);

            $source = Source::create($data);

            // Sync optional products pivot (validated to belong to owner)
            $productIds = (array) ($request->input('product_ids', []));
            $source->products()->sync($productIds);

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

        // Map country and state fields
        $validatedData['country_code'] = $validatedData['country'] ?? null;
        unset($validatedData['country']);

        try {
            $data = $validatedData;
            unset($data['product_ids']);

            $source->update($data);

            // Sync optional products pivot (validated to belong to owner)
            $productIds = (array) ($request->input('product_ids', []));
            $source->products()->sync($productIds);

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
        $this->authorize('manage_source' );

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

    /**
     * AJAX: List sources by owner with optional product filter.
     * Returns data formatted for TomSelect: [{ value, text }]
     */
    public function listByOwner(Request $request)
    {
        try {
            $ownerId = (int) $request->query('owner_id');
            if (!$ownerId) {
                return response()->json([
                    'success' => false,
                    'message' => 'owner_id is required'
                ], 422);
            }

            $query = Source::query()->where('owner_id', $ownerId);

            // Optional: filter by a specific product
            if ($request->filled('product_id')) {
                $productId = (int) $request->query('product_id');
                $query->whereHas('products', function ($q) use ($productId) {
                    $q->where('products.id', $productId);
                });
            }

            // Optional search term for TomSelect
            if ($request->filled('q')) {
                $search = $request->query('q');
                $query->where(function ($q) use ($search) {
                    $q->where('address_line1', 'like', "%{$search}%")
                      ->orWhere('state', 'like', "%{$search}%")
                      ->orWhere('country_code', 'like', "%{$search}%")
                      ->orWhere('type', 'like', "%{$search}%");
                });
            }

            $items = $query->select('id', 'type', 'address_line1', 'state', 'country_code')
                ->orderByDesc('id')
                ->limit(50)
                ->get()
                ->map(function ($s) {
                    $type = $s->type ?: 'Source';
                    $parts = array_filter([$s->address_line1, $s->state, $s->country_code]);
                    $label = trim($type . ' - ' . implode(', ', $parts), ' -');
                    return [
                        'value' => $s->id,
                        'text' => $label,
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $items,
            ]);
        } catch (\Exception $e) {
            \Log::error('Error fetching sources by owner: ' . $e->getMessage(), [
                'owner_id' => $request->query('owner_id'),
                'product_id' => $request->query('product_id'),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch sources.'
            ], 500);
        }
    }
}
