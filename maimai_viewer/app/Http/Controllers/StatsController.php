<?php

namespace App\Http\Controllers;

use DB;
use Illuminate\Http\Request;

class StatsController extends Controller
{
    public static function stats(Request $request)
    {
        function select_scoregrade($id, $grade, $level)
        {
            $data = DB::select('SELECT count(scores.scoregrade) as "total" from scores inner join charts on scores.chartid=charts.chartid AND scores.friendcode = ? AND scores.scoregrade = ? AND charts.level = ?', [$id, $grade, $level]);
            $std_data = $data[0]->total;
            return $std_data;
        }
        function select_combograde($id, $grade, $level)
        {
            $data = DB::select('SELECT count(scores.combograde) as "total" from scores inner join charts on scores.chartid=charts.chartid AND scores.friendcode = ? AND scores.combograde = ? AND charts.level = ?', [$id, $grade, $level]);
            $std_data = $data[0]->total;
            return $std_data;
        }

        //SELECT charts.level, scores.score, scores.scoregrade, scores.combograde from scores inner join charts on scores.chartid=charts.chartid AND scores.friendcode = '7025818836209';
        $id = $request->user()->friendcode;
        $data = DB::select('SELECT charts.level, scores.score, scores.scoregrade, scores.combograde from scores inner join charts on scores.chartid=charts.chartid AND scores.friendcode = ? order by constant DESC;', [$id]);
        $uniqueLevels = array();
        $levelArray = array();
        for ($i = 0; $i < count($data); $i++) {
            if (!in_array($data[$i]->level, $uniqueLevels)) {
                $uniqueLevels[] = $data[$i]->level;
            }
        }
        foreach ($uniqueLevels as $level){
            $SSP_data = select_scoregrade($id,"SS+",$level);
            $SSS_data = select_scoregrade($id,"SSS",$level);
            $SSSP_data = select_scoregrade($id,"SSS+",$level);
            $FCP_data = select_combograde($id,"FC+",$level);
            $AP_data = select_combograde($id,"AP",$level);
            $APP_data = select_combograde($id,"AP+",$level);
            $data = DB::select('SELECT avg(scores.score) as "avg" from scores inner join charts on scores.chartid=charts.chartid AND scores.friendcode = ? AND charts.level = ?',[$id,$level]);
            $avg_data = $data[0]->avg;
            $levelData = [
                "Level" => $level,
                "SSP" => $SSP_data,
                "SSS" => $SSS_data,
                "SSSP" => $SSSP_data,
                "FCP" => $FCP_data,
                "AP" => $AP_data,
                "APP" => $APP_data,
                "avg" => $avg_data,
            ];
            array_push($levelArray, $levelData);
        }
        return $levelArray;
    }
}