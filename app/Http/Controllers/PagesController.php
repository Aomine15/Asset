<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class PagesController extends Controller
{
    public function Dashboard(){
        return view('welcome');
    }

    public function Profile(){
        return view('Profile.profile');
    }

    public function Logout(){
        return redirect('/')->with('success', 'Logout Successful!');
    }

    public function ViewUser(Request $request)
    {
        $all_users = User::paginate(5);

        if ($request->ajax()) {
            return response()->json([
                'all_users' => $all_users,
                'pagination' => (string) $all_users->links()
            ]);
        }

        return view('Pages.users', compact('all_users'));
    }

    public function AddUser(Request $request){

        $validator = Validator::make( $request->all(), [

            'full_name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|confirmed|min:6|max:20',

        ]);

        if($validator->fails()){

            return response()->json([
                'status' => 400,
                'errors'=>$validator->messages(),
            ]);

        }else{

            $user = new User();

            $user->name = $request->full_name;
            $user->email = $request->email;
            $user->password = Hash::make($request->password);
            $user->save();

            return response()->json([
                'status' => 200,
                'message'=>'User added successfully',
            ]);

        }
    }
}
