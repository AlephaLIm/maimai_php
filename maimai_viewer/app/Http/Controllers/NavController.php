<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Navbar;

class NavController extends Controller
{
    public static function get_user(Request $request) {
        if (auth()->check()) {
            $id = array($request->user()->friendcode);
            $user = DB::select('select username, rating, title, picture from users where friendcode = ?', $id);
            
            if (is_null($user[0]->picture)) {
                $base64encoded = null;
            }
            else {
                $base64encoded = "data:image/png;base64, ".base64_encode($user[0]->picture);
            }

            $user_obj = Navbar::retrieveuser($user[0]->username, $user[0]->title, $user[0]->rating, $base64encoded);
            return $user_obj;
        }
        else {
            $user_obj = Navbar::retrieveuser();
            return $user_obj;
        }
    }
}
