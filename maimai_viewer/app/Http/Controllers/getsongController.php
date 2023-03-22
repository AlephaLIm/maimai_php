<?php

namespace App\Http\Controllers;
use DB;
use Illuminate\Http\Request;

class getsongController extends Controller
{
    public function get(){
        $songs = DB::select('SELECT name,type FROM maimai_db.songs where version="FESTiVAL";');
        // print_r($songs);
        return response()->json(
            $songs
            , 201);
    }
}
