<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class RegistrationController extends Controller
{
    /**
     * Show the registration form.
     */
    public function create_farmer()
    {
        return view('auth.register-farmer');
    }

    /**
     * Handle a registration request.
     */
    public function store_farmer(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'nullable|string|max:20|unique:users',
            'password' => 'required|string|confirmed|min:8',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'password' => Hash::make($validated['password']),
        ]);

        // Assign 'Farmer' role if it exists
        if (\Spatie\Permission\Models\Role::where('name', 'Farmer')->exists()) {
            $user->assignRole('Farmer');
        }

        return redirect()->route('farmers.create')
            ->with('status', 'Registration successful! Please wait for admin approval.');
    }
}
