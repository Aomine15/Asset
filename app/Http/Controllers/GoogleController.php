<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;

class GoogleController extends Controller
{
    public function RedirectToGoogle(){
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallBack(){
        try{

            $googleUser = Socialite::driver('google')->user();
            $user = User::where('google_id', $googleUser->id)->first();

            if($user){
                return redirect('/');
            }else{
                $userData = User::create([
                    'name' => $googleUser->name,
                    'email' => $googleUser->email,
                    'google_id' => $googleUser->id,
                    'password' => Hash::make('P@ssw0rd')
                ]);

                if($userData){
                    return redirect('/');
                }
            }

        }catch(\Exception $e){
            return redirect('/login')->with('fail', $e->getMessage());
        }
    }

}
