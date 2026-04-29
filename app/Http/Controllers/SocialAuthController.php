<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class SocialAuthController extends Controller
{
    // Redirect to Google OAuth
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    // Handle Google callback
    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (\Exception $e) {
            return redirect()->route('login')->withErrors([
                'email' => 'Login Google gagal. Silakan coba lagi.',
            ]);
        }

        // Find existing user by google_id first, then by email
        $user = User::where('google_id', $googleUser->getId())->first()
             ?? User::where('email', $googleUser->getEmail())->first();

        if ($user) {
            // Link Google account to existing email-registered user
            $user->update([
                'google_id'        => $googleUser->getId(),
                'google_avatar'    => $googleUser->getAvatar(),
                // Mark email as verified since Google already verified it
                'email_verified_at' => $user->email_verified_at ?? now(),
            ]);
        } else {
            // Create brand-new user from Google
            $user = User::create([
                'name'             => $googleUser->getName(),
                'email'            => $googleUser->getEmail(),
                'google_id'        => $googleUser->getId(),
                'google_avatar'    => $googleUser->getAvatar(),
                'password'         => null,
                'role'             => 'user',
                'email_verified_at' => now(),
                'has_seen_onboarding' => false,
            ]);
        }

        Auth::login($user, remember: true);

        return $user->role === 'admin'
            ? redirect('/admin')
            : redirect('/dashboard');
    }
}
