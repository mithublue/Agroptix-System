<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    public function showSetupWizard()
    {
        $user = auth()->user();

        // If user already has roles, redirect to dashboard
        if ($user->roles->isNotEmpty()) {
            return redirect()->route('dashboard');
        }

        $registrationRoles = collect(json_decode(option('registration_roles', '[]'), true))
            ->map(fn($role) => strtolower($role))->toArray();
        $products = [];

        // Only fetch products if supplier is an option
        if (in_array('supplier', $registrationRoles)) {
            $products = Product::where('is_active',1)->get();
        }

        return view('profile.setup', [
            'registrationRoles' => $registrationRoles,
            'products' => $products,
            'currentStep' => 1,
            'totalSteps' => in_array('supplier', $registrationRoles) ? 2 : 1
        ]);
    }

    /**
     * Save the profile setup wizard data
     */
    public function saveSetupWizard(Request $request)
    {
        $user = auth()->user();

        // Validate the request
        $registrationRoles = collect(json_decode(option('registration_roles', '[]'), true))
            ->map(fn($role) => strtolower($role))->toArray();
        $validated = $request->validate([
            'role' => 'required|string|in:' . implode(',', $registrationRoles),
            // Only require products if role is supplier
            'products' => 'sometimes|required_if:role,supplier|array',
            'products.*' => 'exists:products,id',
        ]);

        // Assign the selected role (ensure lowercase)
        $user->syncRoles([$validated['role']]);

        // If supplier, sync products; otherwise, detach any
        if ($validated['role'] === 'supplier' && isset($validated['products'])) {
            $user->products()->sync($validated['products']);
        } else {
            $user->products()->detach();
        }

        return response()->json([
            'success' => true,
            'redirect' => route('dashboard')
        ]);
    }
}
