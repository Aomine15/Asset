<?php

namespace App\Http\Controllers;

use App\Models\Socials;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Auth;

class SocialController extends Controller
{
    // Google SSO
    public function RedirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallBack()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
            
            $socialAccount = Socials::where('google_id', $googleUser->id)->first();

            if ($socialAccount) {
                Auth::login($socialAccount->user);
                request()->session()->regenerate();

                return redirect('/dashboard')->with('success', 'Login Successful');
            }

            // Check if user exists
            $user = User::where('email', $googleUser->email)->first();

            if (!$user) {
                $user = User::create([
                    'name' => $googleUser->name,
                    'email' => $googleUser->email ?? 'no-email-' . uniqid() . '@google.com',
                    'password' => Hash::make('P@ssw0rd')
                ]);
            }

            // Create or update social record
            Socials::updateOrCreate(
                ['google_id' => $googleUser->id],
                ['user_id' => $user->id]
            );

            Auth::login($user);
            request()->session()->regenerate();

            return redirect('/dashboard')->with('success', 'Login Successfully!');
        } catch (\Exception $e) {
            return redirect('/')->with('fail', $e->getMessage());
        }
    }

    // Facebook SSO
    public function redirectToFacebook()
    {
        return Socialite::driver('facebook')->redirect();
    }

    public function handleFacebookCallBack()
    {
        try {
            $facebookUser = Socialite::driver('facebook')->user();
        
            // Check if social account exists
            $socialAccount = Socials::where('facebook_id', $facebookUser->id)->first();

            if ($socialAccount) {
                Auth::login($socialAccount->user);
                request()->session()->regenerate();

                return redirect('/dashboard')->with('success', 'Login Successful');
            }

            // Check if user exists
            $user = User::where('email', $facebookUser->email)->first();

            if (!$user) {
                $user = User::create([
                    'name' => $facebookUser->name,
                    'email' => $facebookUser->email ?? 'no-email-' . uniqid() . '@facebook.com',
                    'password' => Hash::make('P@ssw0rd')
                ]);
            }

            // Create or update social record
            Socials::updateOrCreate(
                ['facebook_id' => $facebookUser->id],
                ['user_id' => $user->id]
            );

            Auth::login($user);
            request()->session()->regenerate();

            return redirect('/dashboard')->with('success', 'Login Successfully!');
        } catch (\Exception $e) {
            return redirect('/')->with('fail', $e->getMessage());
        }
    }
}
