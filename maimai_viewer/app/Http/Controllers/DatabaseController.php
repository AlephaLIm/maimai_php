<?php

namespace App\Http\Controllers;

use DB;
use Illuminate\Http\Request;

class DatabaseController extends Controller
{
    public function data(Request $request)
    {
        // dd($request->post());
        $validatedData = $request->validate([
            'user' => 'required',
            'remasterScores' => 'required',
            'masterScores' => 'required',
            'expertScores' => 'required',
            'basicScores' => 'required',
            'advancedScores' => 'required',
        ]);
        //INSERT INTO users(username, email, friendcode, password, picture, rating,  title, playcount,classrank, courserank) VALUES ('Ｍａｒｉｓａ　α', 'marisa@gmail.com' , '8079442114197', 'password', '{}', 6842, 'でびゅー', 10, 'https://maimaidx-eng.com/maimai-mobile/img/class/class_rank_s_00ZqZmdpb8.png', 'https://maimaidx-eng.com/maimai-mobile/img/course/course_rank_00T7GHJvGe.png');
        $user = DB::insert('INSERT INTO users(username, rating, playcount, picture, friendcode, classrank, courserank, title, email, password) values (?,?,?,?,?,?,?,?,"marisa@gmail.com","password");');
        $basicScoresData = $request -> input('basicScores');
        for ($i = 0; $i <= count($basicScoresData); $i++) {
            $basicScoresData[$i] = DB::insert('INSERT INTO scores(score,dxscore,)');
        }
        // Return a JSON response with the new user's data
        return response()->json([
            'message' => 'success',
        ], 201);
    }
}