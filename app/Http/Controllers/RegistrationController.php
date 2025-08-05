<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

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

        // Activation logic
        $needActivation = option('users_need_activation', 'yes') === 'yes';
        $activationMethod = option('users_activation_method', 'email');
        $isActive = $needActivation ? 0 : 1;

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'password' => Hash::make($validated['password']),
            'is_active' => $isActive,
        ]);

        // Assign 'Farmer' role if it exists
        if (\Spatie\Permission\Models\Role::where('name', 'Farmer')->exists()) {
            $user->assignRole('Farmer');
        }

        if ($needActivation) {
            if ($activationMethod === 'email') {
                // Send email verification (using Laravel's built-in)
                $user->sendEmailVerificationNotification();
                $msg = 'Registration successful! Please check your email for the verification link.';
            } elseif ($activationMethod === 'phone') {
                // Generate OTP and store in user meta or cache (stub)
                $otp = rand(100000, 999999);
                cache()->put('otp_'.$user->id, $otp, now()->addMinutes(10));
                // Simulate SMS sending (stub)
                \Log::info('OTP for user '.$user->phone.': '.$otp);
                $msg = 'Registration successful! Please check your phone for the verification OTP.';
            } else {
                $msg = 'Registration successful! Activation method not supported.';
            }
        } else {
            $msg = 'Registration successful!';
        }

        return redirect()->route('farmers.create')
            ->with('status', $msg);
    }
}
