<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class DatabaseController extends Controller
{
    public function data(Request $request)
    {
        function CalculateRating($score, $chart_constant)
        {
            $score = floatval($score);
            // echo "Score in rating calculator is $score<br>";  // Uncomment to print the score
            $chart_constant = floatval($chart_constant);
            if ($score < 80) {
                return 0;
            } elseif ($score >= 100.5) {
                return intval(22.512 * $chart_constant);
            }

            // echo "$score, ".gettype($score)."<br>";  // Uncomment to print the score and its type
            $result = GetValues($score);
            // print_r($result);  // Uncomment to print the result of GetValues()

            $car = $result[0]; // Current achievement range
            $nar = $result[1]; // Next achievement range
            $FactorL = $result[2][0]; // Factor lower value
            $FactorH = $result[2][1]; // Factor higher value

            // echo "$car, $nar, $FactorL, $FactorH<br>";  // Uncomment to print the variables

            $score_diff = $score - $car;
            $score_progress = $score_diff / ($nar - $car);
            $multiplier = ($score_progress * ($FactorH - $FactorL)) + $FactorL;
            $rating = intval($multiplier * $chart_constant);

            return $rating;
        }
        function GetValues($score)
        {
            $achievement_range = [80.0, 90.0, 94.0, 97.0, 98.0, 99.0, 99.5, 100.0, 100.5];
            $factor = [[10.880, 12.240], [13.680, 14.288], [15.792, 16.296], [19.4, 19.6], [19.894, 20.097], [20.592, 20.696], [20.995, 21.1], [21.6, 21.708]];

            for ($i = 0; $i < count($achievement_range); $i++) {
                if (floatval($score) < 80.0) {
                    return [null, 80.0, [0.0, 0.0]];
                } elseif (floatval($score) < floatval($achievement_range[$i])) {
                    return [$achievement_range[$i - 1], $achievement_range[$i], $factor[$i - 1]];
                }
            }
        }

        function insertScore($values,$difficulty,$friendcode) {
            if ($values != null) {
                foreach ($values as $score) {
                    //$score = $values[$i];
                    $songid = DB::select('SELECT songid FROM songs where name=? and type=?;', [$score["title"], $score["type"]]);
                    //when there is no song this will throw error
                    if (count($songid) > 0) {
                        $songidString = $songid[0]->songid;
                        $chartid = $songidString . $difficulty;
                        $chart_array = DB::select('SELECT constant FROM charts where chartid=?;', [$chartid]);
                        $chart_constant = (float) $chart_array[0]->constant;
                        $rating = CalculateRating($score["score"], $chart_constant);
                        $check_score = DB::select('SELECT rowid FROM scores where chartid=? AND friendcode=?;', [$chartid,$friendcode]);
                        if (count($check_score) == 0) {
                            $score = DB::insert(
                                'INSERT INTO scores(score, dxscore, combograde, syncgrade, scoregrade, chartid, chartrating, friendcode) VALUES (?,?,?,?,?,?,?,?)',
                                [
                                    $score["score"],
                                    $score["dxscore"],
                                    $score["combo"],
                                    $score["sync"],
                                    $score["scoregrade"],
                                    $chartid,
                                    $rating,
                                    $friendcode,
                                ]
                            );
                        } 
                        else {
                            $score = DB::insert(
                                'UPDATE scores set score=?, dxscore=?, scoregrade=?, combograde=?, syncgrade=?, chartrating=? where chartid=?;',
                                [
                                    $score["score"],
                                    $score["dxscore"],
                                    $score["scoregrade"],
                                    $score["combo"],
                                    $score["sync"],
                                    $rating,
                                    $chartid,
                                ]
                            );
                        }
                    }
                    //accounts for the no title song
                    else {
                        $songid = DB::select('SELECT songid FROM songs where name=? and type=?;', ["", $score["type"]]);
                        if (count($songid) > 0) {
                            $songidString = $songid[0]->songid;
                            $chartid = $songidString . $difficulty;
                            $chart_array = DB::select('SELECT constant FROM charts where chartid=?;', [$chartid]);
                            $chart_constant = (float) $chart_array[0]->constant;
                            $rating = CalculateRating($score["score"], $chart_constant);
                            $check_score = DB::select('SELECT rowid FROM scores where chartid=?;', [$chartid]);
                            if (count($check_score) == 0) {
                                $score = DB::insert(
                                    'INSERT INTO scores(score, dxscore, combograde, syncgrade, scoregrade, chartid, chartrating, friendcode) VALUES (?,?,?,?,?,?,?,?)',
                                    [
                                        $score["score"],
                                        $score["dxscore"],
                                        $score["combo"],
                                        $score["sync"],
                                        $score["scoregrade"],
                                        $chartid,
                                        $rating,
                                        $friendcode,
                                    ]
                                );
                            } 
                            else {
                                $score = DB::insert(
                                    'UPDATE scores set score=?, dxscore=?, scoregrade=?, combograde=?, syncgrade=?, chartrating=? where chartid=?;',
                                    [
                                        $score["score"],
                                        $score["dxscore"],
                                        $score["scoregrade"],
                                        $score["combo"],
                                        $score["sync"],
                                        $rating,
                                        $chartid,
                                    ]
                                );
                            }
                    }
                    
                }
            }
    
        }

    }
        //Obtains the relevant JSON object which stores it in an array of objects
        $userData = $request->input("user");
        $basicData = $request->input("basicScores");
        $advancedData = $request->input("advancedScores");
        $expertData = $request->input("expertScores");
        $masterData = $request->input("masterScores");
        $remasterData = $request->input("remasterScores");
        $image = base64_decode($userData["picture"]);

        //Inserting of userData
        // print_r($userData);
        // $user = DB::insert(
        //     'INSERT INTO users(username, rating, playcount, picture, friendcode, classrank, courserank, title, email, password) values (?,?,?,?,?,?,?,?,?,?) ON DUPLICATE KEY UPDATE `friendcode` = ?;',
        //     [$userData["name"], $userData["rating"], $userData["playcount"], null, $userData["friendcode"], $userData["classrank"], $userData["courserank"], $userData["title"], "test@gmail", "password", $userData["friendcode"]]
        // );
        //print_r($_POST);
     
        $user = DB::select('SELECT * FROM users where friendcode=?;', [$userData["friendcode"]]);
        if (count($user) == 1) {
            DB::update(
                'UPDATE users set username=?, picture=?, rating=?, title=?, playcount=?, classrank=?, courserank=? where friendcode=?;',
                [
                    $userData["name"],
                    $image,
                    $userData["rating"],
                    $userData["title"],
                    $userData["playcount"],
                    $userData["classrank"],
                    $userData["courserank"],
                    $userData["friendcode"],
                ]
            );
    
            insertScore($basicData,"Basic",$userData["friendcode"]);
            insertScore($advancedData,"Advanced",$userData["friendcode"]);
            insertScore($expertData,"Expert",$userData["friendcode"]);
            insertScore($masterData,"Master",$userData["friendcode"]);
            insertScore($remasterData,"Remaster",$userData["friendcode"]);
            return response()->json([
                'message' => 'success',
            ], 201);
        }
        else {
            return response()->json([
                'message' => 'User does not exist',
            ], 409);
        }
        
        
        
        // Return a JSON response with the new user's data
        
    }
    public function connection(Request $request)
    {
        try {
            DB::connection()->getPdo();
            return "Connection to database established successfully!";
        } catch (\Exception $e) {
            return "Could not connect to database: " . $e->getMessage();
        }
    }
}