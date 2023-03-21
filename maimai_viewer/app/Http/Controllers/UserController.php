<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Navbar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use App\Models\Page_status;

class UserController extends Controller
{
    // register form
    public function create(){
        return view('users.register',[
            'title'=> 'Register',
            'description'=> "Create a new user",
            'logo_url'=> URL::asset('/images/nav_icons/bearhands.png'),
            'user'=> Navbar::retrieveuser(),
            'status'=>Page_status::set_status('')
        ]);
    }


    // create user
    public function store(Request $request){
        $formFields = $request->validate([
            'friendcode' => 'required|min:3|max:8|unique:users,friendcode',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|confirmed|min:8',
        ]);

        // encrypt password
        $formFields['password'] = bcrypt($formFields['password']);

        // create user
        $user = User::create($formFields);

        // login
        auth()->login($user);

        // return to homepage
        return redirect('/')->with('message', 'User created and logged in');
    }
}
