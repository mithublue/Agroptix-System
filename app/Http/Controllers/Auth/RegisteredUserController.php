<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'phone' => ['nullable', 'string', 'max:20', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $needActivation = option('users_need_activation', 'yes') === 'yes';
        $activationMethod = option('users_activation_method', 'email');
        $isActive = $needActivation ? 0 : 1;

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'is_active' => $isActive,
        ]);

        event(new Registered($user));

        Auth::login($user);

        if ($needActivation) {
            if ($activationMethod === 'email') {
                $user->sendEmailVerificationNotification();
                $msg = 'Registration successful! Please check your email for the verification link.';
                return redirect()->route('verification.notice')->with('status', $msg);
            } elseif ($activationMethod === 'phone') {
                $otp = rand(100000, 999999);
                Cache::put('otp_'.$user->id, $otp, now()->addMinutes(10));
                Log::info('OTP for user '.$user->phone.': '.$otp);
                $msg = 'Registration successful! Please check your phone for the verification OTP.';
                return redirect()->route('auth.phone.verify.form')->with('status', $msg);
            } else {
                $msg = 'Registration successful! Activation method not supported.';
                return redirect(route('dashboard', absolute: false))->with('status', $msg);
            }
        }

        return redirect(route('dashboard', absolute: false))->with('status', 'Registration successful!');
    }
}
