<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class NewSongController extends Controller
{
    public function songs(Request $request)
    {
        $songs = $request->all();
        // print_r($songs);
        for ($i = 0; $i < count($songs); $i++) {
            $eachSong = $songs[$i];
            DB::insert('INSERT INTO songs(songid, internalid, wikiid, name, type, version, genre, artist, bpm, jacket) values (?,?,?,?,?,?,?,?,?,?)',
            [
                $eachSong["songid"],
                null,
                null,
                $eachSong["name"],
                $eachSong["type"],
                "FESTiVAL",
                $eachSong["genre"],
                $eachSong["artist"],
                null,
                $eachSong["jacket"],
            ]);

            DB::insert('INSERT INTO alias(songid, alias) values (?,?)',[
                $eachSong["songid"],
                $eachSong["name"],
            ]);

            if ($eachSong["basicLevel"] != null){
                if(str_contains($eachSong["basicLevel"],"+")){
                    $constant = floatval(str_replace("+",".7",$eachSong["basicLevel"]));
                }else{
                    $constant = floatval($eachSong["basicLevel"]);
                }
                $chartid = $eachSong["songid"] . "Basic";
                DB::insert('INSERT INTO charts(chartid, level, difficulty, constant, parentsong) values (?,?,?,?,?)',
                [
                    $chartid,
                    $eachSong["basicLevel"],
                    "Basic",
                    $constant,
                    $eachSong["songid"],
                ]);
            }

            if ($eachSong["advancedLevel"] != null){
                if(str_contains($eachSong["advancedLevel"],"+")){
                    $constant = floatval(str_replace("+",".7",$eachSong["advancedLevel"]));
                }else{
                    $constant = floatval($eachSong["advancedLevel"]);
                }
                $chartid = $eachSong["songid"] . "Advanced";
                DB::insert('INSERT INTO charts(chartid, level, difficulty, constant, parentsong) values (?,?,?,?,?)',
                [
                    $chartid,
                    $eachSong["advancedLevel"],
                    "Advanced",
                    $constant,
                    $eachSong["songid"],
                ]);
            }

            if ($eachSong["expertLevel"] != null){
                if(str_contains($eachSong["expertLevel"],"+")){
                    $constant = floatval(str_replace("+",".7",$eachSong["expertLevel"]));
                }else{
                    $constant = floatval($eachSong["expertLevel"]);
                }
                $chartid = $eachSong["songid"] . "Expert";
                DB::insert('INSERT INTO charts(chartid, level, difficulty, constant, parentsong) values (?,?,?,?,?)',
                [
                    $chartid,
                    $eachSong["expertLevel"],
                    "Expert",
                    $constant,
                    $eachSong["songid"],
                ]);
            }

            if ($eachSong["masterLevel"] != null){
                if(str_contains($eachSong["masterLevel"],"+")){
                    $constant = floatval(str_replace("+",".7",$eachSong["masterLevel"]));
                }else{
                    $constant = floatval($eachSong["masterLevel"]);
                }
                $chartid = $eachSong["songid"] . "Master";
                DB::insert('INSERT INTO charts(chartid, level, difficulty, constant, parentsong) values (?,?,?,?,?)',
                [
                    $chartid,
                    $eachSong["masterLevel"],
                    "Master",
                    $constant,
                    $eachSong["songid"],
                ]);
            }

            if ($eachSong["remasterLevel"] != null){
                if(str_contains($eachSong["remasterLevel"],"+")){
                    $constant = floatval(str_replace("+",".7",$eachSong["remasterLevel"]));
                }else{
                    $constant = floatval($eachSong["remasterLevel"]);
                }
                $chartid = $eachSong["songid"] . "Remaster";
                DB::insert('INSERT INTO charts(chartid, level, difficulty, constant, parentsong) values (?,?,?,?,?)',
                [
                    $chartid,
                    $eachSong["remasterLevel"],
                    "Remaster",
                    $constant,
                    $eachSong["songid"],
                ]);
            }
        }
        return response()->json([
            'message' => 'success',
        ], 201);
    }
}