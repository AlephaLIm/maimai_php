<?php

namespace App\Http\Controllers;

use App\Models\Chart;
use App\Models\Predict;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class RatingController extends Controller
{
    public static function getDB($state,Request $request)
    {
        $id = $request->user()->friendcode;
        
        if ($state == 'festival') {
            $charts = DB::select("
            SELECT DISTINCT charts.chartid, songs.name, songs.jacket, charts.level, charts.constant, charts.difficulty, songs.type, songs.artist, songs.genre, songs.version, songs.bpm, scores.chartrating, scores.score, scores.dxscore, scores.scoregrade, scores.combograde, scores.syncgrade  
            FROM charts
            JOIN songs ON charts.parentsong = songs.songid
            JOIN scores ON charts.chartid = scores.chartid
            JOIN users on scores.friendcode = users.friendcode
            AND users.friendcode = ?
            AND songs.version = \"FESTiVAL\"
            ORDER BY scores.chartrating DESC 
            LIMIT 15
            ", $id);
        } else {
            $charts = DB::select("
            SELECT DISTINCT charts.chartid, songs.name, songs.jacket, charts.level, charts.constant, charts.difficulty, songs.type, songs.artist, songs.genre, songs.version, songs.bpm, scores.chartrating, scores.score, scores.dxscore, scores.scoregrade, scores.combograde, scores.syncgrade 
            FROM charts
            JOIN songs ON charts.parentsong = songs.songid
            JOIN scores ON charts.chartid = scores.chartid
            JOIN users on scores.friendcode = users.friendcode
            AND users.friendcode = ?
            ORDER BY scores.chartrating DESC 
            LIMIT 35
            ", $id);
        }
        return $charts;
    }
    public static function rating(Request $request)
    {
        $chart_list = [];
        $state = $request->get('festival');
        $charts = self::getDB($state, $request);

        foreach ($charts as $chart) {
            $chart_obj = Chart::create_chart($chart->chartid, $chart->name, $chart->artist, $chart->genre, $chart->bpm, $chart->version, $chart->jacket, $chart->level, $chart->constant, $chart->difficulty, $chart->type, $chart->scoregrade, $chart->score, $chart->chartrating, $chart->combograde, $chart->syncgrade);
            $chart_obj->set_dx($chart->dxscore);
            array_push($chart_list, $chart_obj);
        }

        return $chart_list;
    }

    public static function recommendation(Request $request)
    {
        $chart_list = [];
        $predict_list = [];
        $id = $request->user()->friendcode;
        $state = $request->get('festival');
        $fchart = self::getDB($state, $id);
        $predicts = Predict::generateValues($id, $fchart);

        foreach ($predicts as $predict) {
            $predict_obj = Predict::create_predict($predict->score, $predict->weight, $predict->nextRange, $predict->potentialRatingGain, $id->id);
            array_push($predict_list, $predict_obj);
        }
        if ($state == 'festival') {
            $charts = DB::select("
            SELECT DISTINCT charts.chartid, songs.name, songs.jacket, charts.level, charts.constant, charts.difficulty, songs.type, songs.artist, songs.genre, songs.version, songs.bpm, scores.chartrating, scores.score, scores.dxscore, scores.scoregrade, scores.combograde, scores.syncgrade 
            FROM charts
            JOIN songs ON charts.parentsong = songs.songid
            JOIN scores ON charts.chartid = scores.chartid
            JOIN users on scores.friendcode = users.friendcode
            AND users.friendcode = ?
            AND songs.version = \"FESTiVAL\"
            AND songs.songid = ?
            ORDER BY scores.chartrating DESC 
            LIMIT 15
            ", $id, $predict_obj->id);
        } else {
            $charts = DB::select("
            SELECT DISTINCT charts.chartid, songs.name, songs.jacket, charts.level, charts.constant, charts.difficulty, songs.type, songs.artist, songs.genre, songs.version, songs.bpm, scores.chartrating, scores.score, scores.dxscore, scores.scoregrade, scores.combograde, scores.syncgrade 
            FROM charts
            JOIN songs ON charts.parentsong = songs.songid
            JOIN scores ON charts.chartid = scores.chartid
            JOIN users on scores.friendcode = users.friendcode
            AND users.friendcode = ?
            AND songs.songid = ?
            ORDER BY scores.chartrating DESC 
            LIMIT 15
            ", $id, $predict_obj->id);
        }

        foreach ($charts as $chart) {
            $chart_obj = Chart::create_chart($chart->chartid, $chart->name, $chart->artist, $chart->genre, $chart->bpm, $chart->version, $chart->jacket, $chart->level, $chart->constant, $chart->difficulty, $chart->type, $chart->scoregrade, $chart->score, $chart->chartrating, $chart->combograde, $chart->syncgrade);
            $chart_obj->set_dx($chart->dxscore);
            array_push($chart_list, $chart_obj);
        }

        return $chart_list;
    }

}