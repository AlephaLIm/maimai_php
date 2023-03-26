<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class NewSongController extends Controller
{
    public function songs(Request $request)
    {
        function insertCharts($values, $difficulty)
        {
            if ($values[$difficulty . "Level"] != null) {
                if (str_contains($values[$difficulty . "Level"], "+")) {
                    $constant = floatval(str_replace("+", ".7", $values[$difficulty . "Level"]));
                } else {
                    $constant = floatval($values[$difficulty . "Level"]);
                }
                //To capitalize the first letter
                $c_difficulty = ucfirst($difficulty);
                $chartid = $values["songid"] . $c_difficulty;
                DB::insert(
                    'INSERT INTO charts(chartid, level, difficulty, constant, parentsong) values (?,?,?,?,?)',
                    [
                        $chartid,
                        $values[$difficulty . "Level"],
                        $c_difficulty,
                        $constant,
                        $values["songid"],
                    ]
                );
            }
        }

        $songs = $request->all();
        // print_r($songs);
        foreach ($songs as $eachSong) {
            //$eachSong = $songs[$i];
            DB::insert(
                'INSERT INTO songs(songid, internalid, wikiid, name, type, version, genre, artist, bpm, jacket) values (?,?,?,?,?,?,?,?,?,?)',
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
                ]
            );

            DB::insert('INSERT INTO alias(songid, alias) values (?,?)', [
                $eachSong["songid"],
                $eachSong["name"],
            ]);

            insertCharts($eachSong, "basic");
            insertCharts($eachSong, "advanced");
            insertCharts($eachSong, "expert");
            insertCharts($eachSong, "master");
            insertCharts($eachSong, "remaster");
        }
        return response()->json([
            'message' => 'success',
        ], 201);
    }
}