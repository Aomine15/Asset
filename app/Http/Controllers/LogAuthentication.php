<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class LogAuthentication extends Controller
{
    public function Login(){
        return view('Auth.login');
    }

    public function Register(){
        return view('Auth.register');
    }

    public function Forgot_password(){
        return view('Auth.forgot_password');
    }

    public function LoginAuth(Request $request){

        $request->validate([

            'email' => 'required|email',
            'password' => 'required|string'

        ]);


        $user = User::where('email', $request->email)->first();

        if($user && Hash::check($request->password, $user->password)){

            return redirect('/')->with('success', 'Login Successful');

        }

        return redirect('/login')->with('fail', 'Invalid username or password!');

    }

    public function RegisterAuth(Request $request){

        $request->validate([

            'email' => 'required|email:unique:users',
            'username' => 'required|string|min:6|max:50',
            'password' => 'required|string|min:6|max:20',

        ]);

        try{

            $user = ([
                'name' => $request->username,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            if(User::create($user)){
                return redirect('/login')->with('success', 'Registered Successfully!');
            }

        }catch(\Exception $e){

            return redirect('/register')->with('fail', $e->getMessage());

        }

    }


}
