<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(Request $request): View
    {
        $referralCode = $request->query('ref');

        if ($referralCode) {
            session(['referral_code' => $referralCode]);
        } else {
            $referralCode = session('referral_code');
        }

        return view('auth.register', compact('referralCode'));
    }

    public function store(Request $request): RedirectResponse
    {
        // Honeypot validation - if any honeypot field is filled, it's likely a bot
        $honeypotFields = ['website', 'phone_alt', 'company'];
        $filledHoneypotFields = [];
        
        foreach ($honeypotFields as $field) {
            if (!empty($request->input($field))) {
                $filledHoneypotFields[] = $field;
            }
        }
        
        // Time-based honeypot - if form is submitted too quickly (less than 3 seconds), it's likely a bot
        $registrationTime = $request->input('registration_time');
        $currentTime = time();
        $timeDifference = $currentTime - $registrationTime;
        
        if (!empty($filledHoneypotFields) || $timeDifference < 3) {
            \Log::warning('Bot detected during registration attempt', [
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'filled_honeypot_fields' => $filledHoneypotFields,
                'time_difference' => $timeDifference,
                'timestamp' => now(),
            ]);
            
            // Return a generic error message to avoid revealing the honeypot
            return redirect()->back()
                ->withInput($request->except('password', 'password_confirmation'))
                ->withErrors(['email' => 'Registration failed. Please try again.']);
        }

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', 'unique:'.User::class],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'phone' => ['required', 'string', 'max:20'],
            'country' => ['required', 'string', 'max:100'],
            'currency' => ['required', 'string', 'in:USD,EUR,GBP,JPY,CAD,AUD,CHF,CNY,INR,BRL'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $hasReferralCode = Schema::hasColumn('users', 'referral_code');
        $hasReferredBy = Schema::hasColumn('users', 'referred_by');

        $referrer = null;
        if ($hasReferralCode && $hasReferredBy && $request->filled('ref')) {
            $referrer = User::where('referral_code', $request->ref)->first();
        }

        $attributes = [
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'phone' => $request->phone,
            'country' => $request->country,
            'currency' => $request->currency,
            'password' => Hash::make($request->password),
            'email_verified_at' => now(),
        ];

        if ($hasReferredBy) {
            $attributes['referred_by'] = $referrer?->id;
        }

        $user = User::create($attributes);

        if ($referrer) {
            $referrer->createNotification(
                'new_referral',
                'New Referral Signup',
                "{$user->name} just signed up using your referral link. You will earn a reward when they make their first deposit.",
                [
                    'referred_user_id' => $user->id,
                    'referred_user_name' => $user->name,
                ]
            );
        }

        Auth::login($user);

        // Notification polling can leave an AJAX endpoint in the guest
        // session as the intended URL. Registration must always finish on the
        // user dashboard, never on a JSON endpoint.
        $request->session()->forget('url.intended');

        return redirect()->route('user.dashboard');
    }
}
