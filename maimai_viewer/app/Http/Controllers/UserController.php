<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    // register form
    public function create(){
        return view('users.register',[
            'title'=> 'Register',
            'description'=> "Create a new user",
        ]);
    }

    // create user
    public function store(Request $request){
        $userData = $request->validate([
            'friendcode' => 'required|min:13|max:13|unique:users,friendcode',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|confirmed|min:8',
        ]);

        // encrypt password
        $userData['password'] = bcrypt($userData['password']);
        

        $user = User::create($userData);
        auth()->login($user);

        //DB::statement(
        //    "UPDATE users SET username=?, friendcode=?, password=?, picture=?, rating=?, title=?, playcount=?, classrank=?, courserank=?  WHERE email=?",
        //    [null, $userData["friendcode"], $userData["password"], null, null, null, null, null, null, $userData["email"]]
        //);

        // return to homepage
        return redirect('/')->with('message', 'User created and logged in');
    }

    // logout user
    public function logout(Request $request){
        auth()->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect('/')->with('message', 'You have been logged out!');
    }

    // login form
    public function login(){
        return view('users.login',[
            'title'=> 'Login',
            'description'=> "Log in as existing user",
        ]);
    }

    public function authenticate(Request $request){
        $formFields = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if(auth()->attempt($formFields)){
            $request->session()->regenerate();

            return redirect('/')->with('message', 'You are now logged in!');
        }

        return back()->withErrors(['email' => 'Invalid Credentials'])->onlyInput('email');
    }

}
