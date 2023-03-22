<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Navbar;
use App\Models\Page_status;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;

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
        $formFields = $request->validate([
            'friendcode' => 'required|min:3|max:8|unique:users,friendcode',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|confirmed|min:8',
        ]);

        // encrypt password
        $formFields['password'] = bcrypt($formFields['password']);

        // create user
        $user = User::create($formFields);

        /*$userData = $request->input("user");
        $user = DB::insert(
            'INSERT INTO users(id, friendcode, email, email_verified_at, password, remember_token, created_at, updated_at) values (?,?,?,?,?,?,?,?);',
            ["1", $userData["friendcode"], "test@gmail", null, "password", null, null, null]
        );*/

        // login
        auth()->login($user);

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
