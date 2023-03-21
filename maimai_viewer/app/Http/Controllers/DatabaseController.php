<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class DatabaseController extends Controller
{
    public function data(Request $request)
    {
        //DB::insert('INSERT INTO users(username, email, friendcode, password, picture, rating,  title, playcount,classrank, courserank) VALUES ("Marissa", "marisa@gmail.com" , "8079442114197", "password", "{}", 6842, "でびゅー", 10, "https://maimaidx-eng.com/maimai-mobile/img/class/class_rank_s_00ZqZmdpb8.png", "https://maimaidx-eng.com/maimai-mobile/img/course/course_rank_00T7GHJvGe.png");');
        // $myfile = fopen("newfile.txt", "w") or die("Unable to open file!");
        // $requestData = implode($request);
        // fwrite($myfile, $requestData);
        // fwrite($myfile, "Hello");
        // fclose($myfile);
        $userData = $request->input("user");
        $basicData = $request->input("basicScores");
        $advancedData = $request->input("advancedScores");
        $expertData = $request->input("expertScores");
        $masterData = $request->input("masterScores");
        $remasterData = $request->input("remasterScores");
        // print_r($userData);
        // print("hello world\n");
        // for ($i = 0; $i < count($masterData); $i++) {
        //     $masterScore = $masterData[$i];
        //     print($masterScore["score"]);
        // }
        $user = DB::insert(
            'INSERT INTO users(username, rating, playcount, picture, friendcode, classrank, courserank, title, email, password) values (?,?,?,?,?,?,?,?,?,?);',
            [$userData["name"], $userData["rating"], $userData["playcount"], null, $userData["friendcode"], $userData["classrank"], $userData["courserank"], $userData["title"], "test@gmail", "password"]
        );
        if (count($basicData) != 0) {
            for ($i = 0; $i < count($basicData); $i++) {
                $basicScore = $basicData[$i];
                $basicScore = DB::insert(
                    'INSERT INTO scores(score, dxscore, combograde, syncgrade, scoregrade, chartid, chartrating, friendcode) VALUES (?,?,?,?,?,?,?,?)',
                    [
                        $basicScore["score"],
                        $basicScore["dxscore"],
                        $basicScore["combo"],
                        $basicScore["sync"],
                        $basicScore["scoregrade"],
                        "123",
                        "123",
                        $userData["friendcode"],
                    ]
                );
            }
        }

        if (count($advancedData) != 0) {
            for ($i = 0; $i < count($advancedData); $i++) {
                $advancedScore = $advancedData[$i];
                $advancedScore = DB::insert(
                    'INSERT INTO scores(score, dxscore, combograde, syncgrade, scoregrade, chartid, chartrating, friendcode) VALUES (?,?,?,?,?,?,?,?)',
                    [
                        $advancedScore["score"],
                        $advancedScore["dxscore"],
                        $advancedScore["combo"],
                        $advancedScore["sync"],
                        $advancedScore["scoregrade"],
                        "123",
                        "123",
                        $userData["friendcode"],
                    ]
                );
            }
        }

        if (count($expertData) != 0) {
            for ($i = 0; $i < count($expertData); $i++) {
                $expertScore = $expertData[$i];
                $expertScore = DB::insert(
                    'INSERT INTO scores(score, dxscore, combograde, syncgrade, scoregrade, chartid, chartrating, friendcode) VALUES (?,?,?,?,?,?,?,?)',
                    [
                        $expertScore["score"],
                        $expertScore["dxscore"],
                        $expertScore["combo"],
                        $expertScore["sync"],
                        $expertScore["scoregrade"],
                        "123",
                        "123",
                        $userData["friendcode"],
                    ]
                );
            }
        }

        if (count($masterData) != 0) {
            for ($i = 0; $i < count($masterData); $i++) {
                $masterScore = $masterData[$i];
                $masterScore = DB::insert(
                    'INSERT INTO scores(score, dxscore, combograde, syncgrade, scoregrade, chartid, chartrating, friendcode) VALUES (?,?,?,?,?,?,?,?)',
                    [
                        $masterScore["score"],
                        $masterScore["dxscore"],
                        $masterScore["combo"],
                        $masterScore["sync"],
                        $masterScore["scoregrade"],
                        "123",
                        "123",
                        $userData["friendcode"],
                    ]
                );
            }
        }

        if (count($remasterData) != 0) {
            for ($i = 0; $i < count($remasterData); $i++) {
                $remasterScore = $remasterData[$i];
                $remasterScore = DB::insert(
                    'INSERT INTO scores(score, dxscore, combograde, syncgrade, scoregrade, chartid, chartrating, friendcode) VALUES (?,?,?,?,?,?,?,?)',
                    [
                        $remasterScore["score"],
                        $remasterScore["dxscore"],
                        $remasterScore["combo"],
                        $remasterScore["sync"],
                        $remasterScore["scoregrade"],
                        "123",
                        "123",
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
// public function data(Request $request) {
//     try {
//         DB::connection()->getPdo();
//         return "Connection to database established successfully!";
//     } catch (\Exception $e) {
//         return "Could not connect to database: " . $e->getMessage();
//     }
// }
}