<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductStoreRequest;
use App\Http\Requests\ProductUpdateRequest;
use App\Models\Product;
use App\Models\Source;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class ProductController extends Controller
{
    /**
     * AJAX: List products by owner (producer) with optional source filter.
     * Returns data formatted for TomSelect: [{ value, text }]
     */
    public function listByOwner(Request $request)
    {
        // Basic auth protection via routes; additional checks can be added if needed
        try {
            $ownerId = (int) $request->query('owner_id');
            if (!$ownerId) {
                return response()->json([
                    'success' => false,
                    'message' => 'owner_id is required'
                ], 422);
            }

            $query = Product::query()
                ->whereHas('users', function ($q) use ($ownerId) {
                    $q->where('user_id', $ownerId);
                });

            // Optional: filter by a specific source
            if ($request->filled('source_id')) {
                $sourceId = (int) $request->query('source_id');
                $query->whereHas('sources', function ($q) use ($sourceId) {
                    $q->where('sources.id', $sourceId);
                });
            }

            // Optional search term for TomSelect
            if ($request->filled('q')) {
                $search = $request->query('q');
                $query->where('name', 'like', "%{$search}%");
            }

            $items = $query->select('id', 'name')
                ->orderBy('name')
                ->limit(50)
                ->get()
                ->map(function ($p) {
                    return [
                        'value' => $p->id,
                        'text' => $p->name,
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $items,
            ]);
        } catch (\Exception $e) {
            \Log::error('Error fetching products by owner: ' . $e->getMessage(), [
                'owner_id' => $request->query('owner_id'),
                'source_id' => $request->query('source_id'),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch products.'
            ], 500);
        }
    }
    public function index(Request $request): View
    {
        $filters = $request->only(['min_price', 'max_price', 'status', 'q']);
        $query = Product::query();
        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }
        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }
        if ($request->filled('status') && in_array($request->status, ['0', '1'])) {
            $query->where('is_active', (bool)$request->status);
        }
        // Type-sensitive search across name, description, and numeric price
        if ($request->filled('q')) {
            $q = trim((string) $request->q);
            $query->where(function ($sub) use ($q) {
                $sub->where('name', 'like', "%{$q}%")
                    ->orWhere('description', 'like', "%{$q}%");
                // If numeric, allow searching exact price
                if (is_numeric($q)) {
                    $sub->orWhere('price', '=', (float) $q);
                }
            });
        }
        $products = $query->latest()->paginate(10)->withQueryString();
        return view('product.index', [
            'products' => $products,
            'filters' => $filters,
        ]);
    }

    /**
     * Bulk delete selected products.
     */
    public function bulkDestroy(Request $request): RedirectResponse
    {
        // Permission check aligns with route middleware but adds defense-in-depth
        abort_unless(auth()->user() && auth()->user()->can('delete_product'), 403);

        $ids = array_filter(array_map('intval', (array) $request->input('ids', [])));
        if (empty($ids)) {
            return redirect()->to($request->input('redirect', route('products.index')))
                ->with('error', 'No products selected.');
        }

        $count = Product::whereIn('id', $ids)->count();
        if ($count > 0) {
            Product::whereIn('id', $ids)->delete();
        }

        return redirect()->to($request->input('redirect', route('products.index')))
            ->with('success', $count . ' product(s) deleted successfully.');
    }

    public function create(Request $request): View
    {
        return view('product.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $formRequest = new ProductStoreRequest();

        // 2. Manually check authorization (IMPORTANT: This is NOT done automatically now)
        if (!$formRequest->authorize()) {
            abort(403, 'This action is unauthorized.');
        }
        $rules = $formRequest->rules();
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            // 6. Manually handle the failure.
            //    To make it work with Hotwired Turbo, we must set the 422 status code.
            return back()->withErrors($validator)->withInput()->setStatusCode(422);
        }
        $validatedData = $validator->validated();

        // Create the Source record
        try {
            Product::create($validatedData);
            return redirect()
                ->route('products.index')
                ->with('success', 'Product created successfully.');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Failed to create source. ' . $e->getMessage());
        }
    }

    public function show(Request $request, Product $product)
    {
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'data' => $product
            ]);
        }

        return view('product.show', [
            'product' => $product,
        ]);
    }

    public function edit(Request $request, Product $product): View
    {
        return view('product.edit', [
            'product' => $product,
        ]);
    }

    public function update(ProductUpdateRequest $request, Product $product): RedirectResponse
    {
        //    to get access to its rules and authorization logic.
        $formRequest = new ProductUpdateRequest();

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

        try {
            $product->update($validatedData);
            return redirect()
                ->route('products.index')
                ->with('success', 'Product updated successfully.');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Failed to update product. ' . $e->getMessage());
        }
    }

    public function destroy(Request $request, Product $product): RedirectResponse
    {
        $product->delete();

        return redirect()->route('products.index');
    }

    /**
     * Update the product status via AJAX request
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateStatus(Request $request, Product $product)
    {
        $request->validate([
            'is_active' => 'required|boolean',
        ]);

        try {
            $product->update([
                'is_active' => $request->is_active
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Product status updated successfully.',
                'is_active' => $product->is_active,
                'status_label' => $product->is_active ? 'Active' : 'Inactive',
                'status_class' => $product->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update product status.'
            ], 500);
        }
    }
}
