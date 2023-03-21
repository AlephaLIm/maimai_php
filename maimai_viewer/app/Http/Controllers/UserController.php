<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    // register form
    public function create(){
        return view('users.register');
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
