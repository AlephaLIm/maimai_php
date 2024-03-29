<?php

namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Password;


class ProfileController extends Controller
{
    public function updateProfile(Request $request)
    {
        

        
        //Obtains the relevant JSON object which stores it in an array of objects
        
        
        $userData = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'new_email' => 'nullable|email|unique:users,email',
            'new_password' => [
                'nullable',
                Password::min(8)
                    ->mixedCase()
                    ->letters()
                    ->numbers()
                    ->symbols()
                    ->uncompromised()],
            ]);
        $friendcode = $request->user()->friendcode;
        //$validate = DB::select("SELECT * FROM users WHERE email = ? AND password = ?", [$userData['email'],bcrypt($userData['password'])]);
     
        
        if (auth()->attempt(['email' => $userData['email'],'password' => $userData['password']])) {

            if ($userData['new_email'] != null) {
                DB::update("UPDATE users SET email = ? WHERE friendcode = ?",[$userData['new_email'],$friendcode]);
            }
            
            if ($userData['new_password'] != null) {
                DB::update("UPDATE users SET password = ? WHERE friendcode = ?",[bcrypt($userData['new_password']),$friendcode]);

            }

            $request->session()->regenerateToken();

            return redirect()->back()->with("emailsuccess","Email updated successfully")->with("passwordsuccess","Password updated successfully");
        }
        else {
            return redirect()->back()->withErrors(['email' => 'Email does not match','password' => 'Password does not match']);
        }
        
        
        
        // Return a JSON response with the new user's data
        
    }
    
    public static function deleteUser(Request $request) {

        $userData = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'checkbox' => 'required'
        ]);
       
        if (auth()->attempt(['email' => $userData['email'],'password' => $userData['password']])) {
            $friendcode = $request->user()->friendcode;
            DB::delete("DELETE FROM users WHERE friendcode = ?",[$friendcode]);
            auth()->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect('/')->with('message', 'Your account has been deleted');

        }
    
    }
    
}