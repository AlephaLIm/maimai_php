<?php

namespace App\Http\Controllers;

use App\Models\Chart;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class RatingController extends Controller
{
    public static function index(Request $request)
    {
        $chart_list = [];
        $festival = $request;
        if ($festival == "festival") {
            $charts = DB::select("
        SELECT DISTINCT charts.chartid, songs.name, songs.jacket, charts.level, charts.constant, charts.difficulty, songs.type, songs.artist, songs.genre, songs.version, songs.bpm, scores.chartrating, scores.score  
        FROM charts
        JOIN songs ON charts.parentsong = songs.songid
        JOIN scores ON charts.chartid = scores.chartid
        ORDER BY scores.chartrating DESC 
        LIMIT 15
        ");
        } else {
            $charts = DB::select("
        SELECT DISTINCT charts.chartid, songs.name, songs.jacket, charts.level, charts.constant, charts.difficulty, songs.type, songs.artist, songs.genre, songs.version, songs.bpm, scores.chartrating, scores.score 
        FROM charts
        JOIN songs ON charts.parentsong = songs.songid
        JOIN scores ON charts.chartid = scores.chartid
        ORDER BY scores.chartrating DESC 
        LIMIT 35
        ");
        }


        foreach ($charts as $chart) {
            $chart_obj = Chart::create_chart($chart->chartid, $chart->name, $chart->artist, $chart->genre, $chart->bpm, $chart->version, $chart->jacket, $chart->level, $chart->constant, $chart->difficulty, $chart->type, $chart->chartrating, $chart->score);
            $chart_obj->set_dx('99/100');
            array_push($chart_list, $chart_obj);
        }

        return $chart_list;
    }
}