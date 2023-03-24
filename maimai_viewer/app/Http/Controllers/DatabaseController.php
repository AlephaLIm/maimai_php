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

        // SELECT songid FROM maimai_db.songs where name='Backyun! －悪い女－' and type='Standard';
        // print_r($userData);
        // print("hello world\n");
        // for ($i = 0; $i < count($masterData); $i++) {
        //     $masterScore = $masterData[$i];
        //     print($masterScore["score"]);
        // }
        // $basicScore = $basicData[0];
        // $songid = DB::select('SELECT songid FROM maimai_db.songs where name=? and type=?;', [$basicScore["title"], $basicScore["type"]]);
        // $songidString = (string) $songid[0]->songid;
        // $chartid = $songidString . "Basic";
        // $chart_array = DB::select('SELECT constant FROM maimai_db.charts where chartid=?;', [$chartid]);
        // $chart_constant = (float) $chart_array[0]->constant;
        // $rating = CalculateRating($basicScore["score"], $chart_constant);
        // print_r($songidString);


        //Obtains the relevant JSON object which stores it in an array of objects
        $userData = $request->input("user");
        $basicData = $request->input("basicScores");
        $advancedData = $request->input("advancedScores");
        $expertData = $request->input("expertScores");
        $masterData = $request->input("masterScores");
        $remasterData = $request->input("remasterScores");

        //Inserting of userData
        // print_r($userData);
        $user = DB::insert(
            'INSERT INTO users(username, rating, playcount, picture, friendcode, classrank, courserank, title, email, password) values (?,?,?,?,?,?,?,?,?,?) ON DUPLICATE KEY UPDATE `friendcode` = ?;',
            [$userData["name"], $userData["rating"], $userData["playcount"], null, $userData["friendcode"], $userData["classrank"], $userData["courserank"], $userData["title"], "test@gmail", "password", $userData["friendcode"]]
        );

        if ($basicData != null) {
            for ($i = 0; $i < count($basicData); $i++) {
                //Assign each array to basicscore
                $basicScore = $basicData[$i];

                //Obtaining Chartid for insert statement
                //From basicscore array select the song using title and type
                $songid = DB::select('SELECT songid FROM maimai_db.songs where name=? and type=?;', [$basicScore["title"], $basicScore["type"]]);
                //songidString is a stdobject in an array with a key of 0, the string is extracted using the below statement
                $songidString = (string) $songid[0]->songid;
                //chartid value is the combination of songid + difficulty
                $chartid = $songidString . "Basic";

                //Calculating of chartrating for insert statement
                $chart_array = DB::select('SELECT constant FROM maimai_db.charts where chartid=?;', [$chartid]);
                $chart_constant = (float) $chart_array[0]->constant;
                $rating = CalculateRating($basicScore["score"], $chart_constant);

                //Inserting of scores with the relevant data
                $basicScore = DB::insert(
                    'INSERT INTO scores(score, dxscore, combograde, syncgrade, scoregrade, chartid, chartrating, friendcode) VALUES (?,?,?,?,?,?,?,?)',
                    [
                        $basicScore["score"],
                        $basicScore["dxscore"],
                        $basicScore["combo"],
                        $basicScore["sync"],
                        $basicScore["scoregrade"],
                        $chartid,
                        $rating,
                        $userData["friendcode"],
                    ]
                );
            }
        }

        if ($advancedData != null) {
            for ($i = 0; $i < count($advancedData); $i++) {
                $advancedScore = $advancedData[$i];
                $songid = DB::select('SELECT songid FROM maimai_db.songs where name=? and type=?;', [$advancedScore["title"], $advancedScore["type"]]);
                $songidString = (string) $songid[0]->songid;
                $chartid = $songidString . "Advanced";
                $chart_array = DB::select('SELECT constant FROM maimai_db.charts where chartid=?;', [$chartid]);
                $chart_constant = (float) $chart_array[0]->constant;
                $rating = CalculateRating($advancedScore["score"], $chart_constant);
                $advancedScore = DB::insert(
                    'INSERT INTO scores(score, dxscore, combograde, syncgrade, scoregrade, chartid, chartrating, friendcode) VALUES (?,?,?,?,?,?,?,?)',
                    [
                        $advancedScore["score"],
                        $advancedScore["dxscore"],
                        $advancedScore["combo"],
                        $advancedScore["sync"],
                        $advancedScore["scoregrade"],
                        $chartid,
                        $rating,
                        $userData["friendcode"],
                    ]
                );
            }
        }

        if ($expertData != null) {
            for ($i = 0; $i < count($expertData); $i++) {
                $expertScore = $expertData[$i];
                $songid = DB::select('SELECT songid FROM maimai_db.songs where name=? and type=?;', [$expertScore["title"], $expertScore["type"]]);
                $songidString = (string) $songid[0]->songid;
                $chartid = $songidString . "Expert";
                $chart_array = DB::select('SELECT constant FROM maimai_db.charts where chartid=?;', [$chartid]);
                $chart_constant = (float) $chart_array[0]->constant;
                $rating = CalculateRating($expertScore["score"], $chart_constant);
                $expertScore = DB::insert(
                    'INSERT INTO scores(score, dxscore, combograde, syncgrade, scoregrade, chartid, chartrating, friendcode) VALUES (?,?,?,?,?,?,?,?)',
                    [
                        $expertScore["score"],
                        $expertScore["dxscore"],
                        $expertScore["combo"],
                        $expertScore["sync"],
                        $expertScore["scoregrade"],
                        $chartid,
                        $rating,
                        $userData["friendcode"],
                    ]
                );
            }
        }

        if ($masterData != null) {
            for ($i = 0; $i < count($masterData); $i++) {
                $masterScore = $masterData[$i];
                $songid = DB::select('SELECT songid FROM maimai_db.songs where name=? and type=?;', [$masterScore["title"], $masterScore["type"]]);
                //when there is no song this will throw error
                $songidString = (string) $songid[0]->songid;
                $chartid = $songidString . "Master";
                $chart_array = DB::select('SELECT constant FROM maimai_db.charts where chartid=?;', [$chartid]);
                $chart_constant = (float) $chart_array[0]->constant;
                $rating = CalculateRating($masterScore["score"], $chart_constant);
                $masterScore = DB::insert(
                    'INSERT INTO scores(score, dxscore, combograde, syncgrade, scoregrade, chartid, chartrating, friendcode) VALUES (?,?,?,?,?,?,?,?)',
                    [
                        $masterScore["score"],
                        $masterScore["dxscore"],
                        $masterScore["combo"],
                        $masterScore["sync"],
                        $masterScore["scoregrade"],
                        $chartid,
                        $rating,
                        $userData["friendcode"],
                    ]
                );
            }
        }

        if ($remasterData != null) {
            for ($i = 0; $i < count($remasterData); $i++) {
                $remasterScore = $remasterData[$i];
                $songid = DB::select('SELECT songid FROM maimai_db.songs where name=? and type=?;', [$remasterScore["title"], $remasterScore["type"]]);
                $songidString = (string) $songid[0]->songid;
                $chartid = $songidString . "Remaster";
                $chart_array = DB::select('SELECT constant FROM maimai_db.charts where chartid=?;', [$chartid]);
                $chart_constant = (float) $chart_array[0]->constant;
                $rating = CalculateRating($remasterScore["score"], $chart_constant);
                $remasterScore = DB::insert(
                    'INSERT INTO scores(score, dxscore, combograde, syncgrade, scoregrade, chartid, chartrating, friendcode) VALUES (?,?,?,?,?,?,?,?)',
                    [
                        $remasterScore["score"],
                        $remasterScore["dxscore"],
                        $remasterScore["combo"],
                        $remasterScore["sync"],
                        $remasterScore["scoregrade"],
                        $chartid,
                        $rating,
                        $userData["friendcode"],
                    ]
                );
            }
        }
        // Return a JSON response with the new user's data
        return response()->json([
            'message' => 'success',
        ], 201);
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