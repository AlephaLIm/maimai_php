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
            $user = DB::select('select username, rating, title from users where friendcode = ?', $id);
            $user_obj = Navbar::retrieveuser($user[0]->username, $user[0]->title, $user[0]->rating, "/images/nav_icons/user_img.png");
            return $user_obj;
        }
        else {
            $user_obj = Navbar::retrieveuser();
            return $user_obj;
        }
    }
}
