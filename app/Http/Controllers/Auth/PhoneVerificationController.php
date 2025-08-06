<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PhoneVerificationController extends Controller
{
    /**
     * Show the OTP verification form for phone activation.
     */
    public function show(Request $request)
    {
        return view('auth.verify-phone');
    }

    /**
     * Handle the OTP verification for phone activation.
     */
    public function verify(Request $request)
    {
        $request->validate([
            'otp' => 'required|digits:6',
        ]);
        $user = Auth::user();
        $key = 'otp_' . $user->id;
        $expectedOtp = cache()->get($key);
        if ($expectedOtp && $request->otp == $expectedOtp) {
            $user->is_active = 1;
            $user->save();
            cache()->forget($key);
            return redirect()->route('dashboard')->with('status', 'Phone verified successfully! Your account is now active.');
        } else {
            return back()->withErrors(['otp' => 'Invalid or expired OTP.']);
        }
    }
}
