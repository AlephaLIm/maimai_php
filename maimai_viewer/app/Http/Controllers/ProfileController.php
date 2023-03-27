<?php

namespace App\Http\Controllers;

use DB;
use App\Models\Profile;

class ProfileController extends Controller
{
    public static function profile($id)
    {
        //SELECT charts.level, scores.score, scores.scoregrade, scores.combograde from scores inner join charts on scores.chartid=charts.chartid AND scores.friendcode = '7025818836209';
        $user = DB::select('select * from users where friendcode = ?;', [$id]);
        $data = DB :: select('SELECT charts.level, scores.score, scores.scoregrade, scores.combograde from scores inner join charts on scores.chartid=charts.chartid AND scores.friendcode = ? AND scores.scoregrade LIKE "S%" order by constant DESC;',[$id]);
        $uniqueLevels = array();
        $levelArray = array();
        for ($i=0; $i < count($data); $i++){
            if (!in_array($data[$i]->level, $uniqueLevels)) {
                $uniqueLevels[] = $data[$i]->level;
            }
        }
        foreach ($uniqueLevels as $level){
            $data = DB::select('SELECT count(scores.scoregrade) as "SSP" from scores inner join charts on scores.chartid=charts.chartid AND scores.friendcode = ? AND scores.scoregrade = "SS+" AND charts.level = ?',[$id,$level]);
            $SSP_data = $data[0]->SSP;
            $data = DB::select('SELECT count(scores.scoregrade) as "SSS" from scores inner join charts on scores.chartid=charts.chartid AND scores.friendcode = ? AND scores.scoregrade = "SSS" AND charts.level = ?',[$id,$level]);
            $SSS_data = $data[0]->SSS;
            $data = DB::select('SELECT count(scores.scoregrade) as "SSSP" from scores inner join charts on scores.chartid=charts.chartid AND scores.friendcode = ? AND scores.scoregrade = "SSS+" AND charts.level = ?',[$id,$level]);
            $SSSP_data = $data[0]->SSSP;
            $data = DB::select('SELECT count(scores.scoregrade) as "AP" from scores inner join charts on scores.chartid=charts.chartid AND scores.friendcode = ? AND scores.combograde = "AP" AND charts.level = ?',[$id,$level]);
            $AP_data = $data[0]->AP;
            $data = DB::select('SELECT count(scores.scoregrade) as "APP" from scores inner join charts on scores.chartid=charts.chartid AND scores.friendcode = ? AND scores.combograde = "AP+" AND charts.level = ?',[$id,$level]);
            $APP_data = $data[0]->APP;
            $data = DB::select('SELECT avg(scores.score) as "avg" from scores inner join charts on scores.chartid=charts.chartid AND scores.friendcode = ? AND charts.level = ?',[$id,$level]);
            $avg_data = $data[0]->avg;
            $levelData = [
                "Level" => $level,
                "SS+" => $SSP_data,
                "SSS" => $SSS_data,
                "SSS+" => $SSSP_data,
                "AP" => $AP_data,
                "AP+" => $APP_data,
                "avg" => $avg_data,
            ];
            array_push($levelArray,$levelData);
        }
        // $user1 = $user[0];
        // $obj = Profile::retrieveuser($user1->username, $user1->email, $user1->friendcode,$user1->rating);
        return $levelArray;
    }

}