<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Auth;

class GoogleController extends Controller
{
    public function RedirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallBack()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
            $user = User::where('google_id', $googleUser->id)->first();

            if ($user) {
                Auth::login($user);
                request()->session()->regenerate();

                return redirect('/dashboard')->with('success', 'Login Successful');
            } else {
                $userData = User::create([
                    'name' => $googleUser->name,
                    'email' => $googleUser->email,
                    'google_id' => $googleUser->id,
                    'password' => Hash::make('P@ssw0rd')
                ]);

                Auth::login($userData);

                return redirect('/dashboard')->with('success', 'Login Successfully!');
            }
        } catch (\Exception $e) {
            return redirect('/')->with('fail', $e->getMessage());
        }
    }
}
