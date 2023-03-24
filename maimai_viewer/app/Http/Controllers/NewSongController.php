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
                $eachSong["idx"],
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
                $eachSong["idx"],
                $eachSong["name"],
            ]);

            if ($eachSong["basicLevel"] != null){
                if(str_contains($eachSong["basicLevel"],"+")){
                    $constant = floatval(str_replace("+",".7",$eachSong["basicLevel"]));
                }else{
                    $constant = floatval($eachSong["basicLevel"]);
                }
                $chartid = $eachSong["idx"] . "Basic";
                DB::insert('INSERT INTO charts(chartid, level, difficulty, constant, parentsong) values (?,?,?,?,?)',
                [
                    $chartid,
                    $eachSong["basicLevel"],
                    "Basic",
                    $constant,
                    $eachSong["idx"],
                ]);
            }

            if ($eachSong["advancedLevel"] != null){
                if(str_contains($eachSong["advancedLevel"],"+")){
                    $constant = floatval(str_replace("+",".7",$eachSong["advancedLevel"]));
                }else{
                    $constant = floatval($eachSong["advancedLevel"]);
                }
                $chartid = $eachSong["idx"] . "Advanced";
                DB::insert('INSERT INTO charts(chartid, level, difficulty, constant, parentsong) values (?,?,?,?,?)',
                [
                    $chartid,
                    $eachSong["advancedLevel"],
                    "Advanced",
                    $constant,
                    $eachSong["idx"],
                ]);
            }

            if ($eachSong["expertLevel"] != null){
                if(str_contains($eachSong["expertLevel"],"+")){
                    $constant = floatval(str_replace("+",".7",$eachSong["expertLevel"]));
                }else{
                    $constant = floatval($eachSong["expertLevel"]);
                }
                $chartid = $eachSong["idx"] . "Expert";
                DB::insert('INSERT INTO charts(chartid, level, difficulty, constant, parentsong) values (?,?,?,?,?)',
                [
                    $chartid,
                    $eachSong["expertLevel"],
                    "Expert",
                    $constant,
                    $eachSong["idx"],
                ]);
            }

            if ($eachSong["masterLevel"] != null){
                if(str_contains($eachSong["masterLevel"],"+")){
                    $constant = floatval(str_replace("+",".7",$eachSong["masterLevel"]));
                }else{
                    $constant = floatval($eachSong["masterLevel"]);
                }
                $chartid = $eachSong["idx"] . "Master";
                DB::insert('INSERT INTO charts(chartid, level, difficulty, constant, parentsong) values (?,?,?,?,?)',
                [
                    $chartid,
                    $eachSong["masterLevel"],
                    "Master",
                    $constant,
                    $eachSong["idx"],
                ]);
            }

            if ($eachSong["remasterLevel"] != null){
                if(str_contains($eachSong["remasterLevel"],"+")){
                    $constant = floatval(str_replace("+",".7",$eachSong["remasterLevel"]));
                }else{
                    $constant = floatval($eachSong["remasterLevel"]);
                }
                $chartid = $eachSong["idx"] . "Remaster";
                DB::insert('INSERT INTO charts(chartid, level, difficulty, constant, parentsong) values (?,?,?,?,?)',
                [
                    $chartid,
                    $eachSong["remasterLevel"],
                    "Remaster",
                    $constant,
                    $eachSong["idx"],
                ]);
            }
        }
        return response()->json([
            'message' => 'success',
        ], 201);
    }
}