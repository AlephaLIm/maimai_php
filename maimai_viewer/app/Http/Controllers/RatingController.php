<?php

namespace App\Http\Controllers;

use App\Models\Chart;
use App\Models\Predict;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Sorted;

class RatingController extends Controller
{
    public static function getDB($state,?Request $request = null)
    {
        if (!is_null($request)) {
            $id = $request->user()->friendcode;
        }
        
        if ($state == 'new') {
            $charts = DB::select("
            select distinct charts.chartid, songs.songid, songs.name, songs.jacket, songs.artist, songs.genre, songs.version, songs.bpm, songs.type, charts.level,
                            charts.constant, charts.difficulty, charts.totalnotecount, charts.tapcount, charts.slidecount, charts.holdcount, charts.breakcount,
                            charts.touchcount, charts.excount, scores.score, scores.dxscore, scores.scoregrade, scores.combograde, scores.syncgrade, scores.chartrating 
            FROM charts
            INNER JOIN songs ON charts.parentsong = songs.songid
            INNER JOIN scores ON charts.chartid = scores.chartid
            INNER JOIN users on scores.friendcode = users.friendcode
            AND users.friendcode = ?
            AND songs.version = \"FESTiVAL\"
            ORDER BY scores.chartrating DESC 
            LIMIT 15
            ", [$id]);
        } 
        elseif ($state == 'old') {
            $charts = DB::select("
            select distinct charts.chartid, songs.songid, songs.name, songs.jacket, songs.artist, songs.genre, songs.version, songs.bpm, songs.type, charts.level,
                            charts.constant, charts.difficulty, charts.totalnotecount, charts.tapcount, charts.slidecount, charts.holdcount, charts.breakcount,
                            charts.touchcount, charts.excount, scores.score, scores.dxscore, scores.scoregrade, scores.combograde, scores.syncgrade, scores.chartrating 
            FROM charts
            INNER JOIN songs ON charts.parentsong = songs.songid
            INNER JOIN scores ON charts.chartid = scores.chartid
            INNER JOIN users on scores.friendcode = users.friendcode
            AND users.friendcode = ?
            AND songs.version != \"FESTiVAL\"
            ORDER BY scores.chartrating DESC 
            LIMIT 35
            ", [$id]);
        }
        else {
            $fescharts = DB::select("
            select distinct charts.chartid, songs.songid, songs.name, songs.jacket, songs.artist, songs.genre, songs.version, songs.bpm, songs.type, charts.level,
                            charts.constant, charts.difficulty, charts.totalnotecount, charts.tapcount, charts.slidecount, charts.holdcount, charts.breakcount,
                            charts.touchcount, charts.excount, scores.score, scores.dxscore, scores.scoregrade, scores.combograde, scores.syncgrade, scores.chartrating 
            FROM charts
            INNER JOIN songs ON charts.parentsong = songs.songid
            INNER JOIN scores ON charts.chartid = scores.chartid
            INNER JOIN users on scores.friendcode = users.friendcode
            AND users.friendcode = ?
            AND songs.version = \"FESTiVAL\"
            ORDER BY scores.chartrating DESC 
            LIMIT 15
            ", [$id]);
            $oldcharts = DB::select("
            select distinct charts.chartid, songs.songid, songs.name, songs.jacket, songs.artist, songs.genre, songs.version, songs.bpm, songs.type, charts.level,
                            charts.constant, charts.difficulty, charts.totalnotecount, charts.tapcount, charts.slidecount, charts.holdcount, charts.breakcount,
                            charts.touchcount, charts.excount, scores.score, scores.dxscore, scores.scoregrade, scores.combograde, scores.syncgrade, scores.chartrating 
            FROM charts
            INNER JOIN songs ON charts.parentsong = songs.songid
            INNER JOIN scores ON charts.chartid = scores.chartid
            INNER JOIN users on scores.friendcode = users.friendcode
            AND users.friendcode = ?
            AND songs.version != \"FESTiVAL\"
            ORDER BY scores.chartrating DESC 
            LIMIT 35
            ", [$id]);
            $charts = array();
            $charts = array_merge($fescharts, $oldcharts);
            usort($charts, function ($a, $b) {
                return $b->chartrating <=> $a->chartrating;
            });
        }
        return $charts;
    }
    public static function rating(Request $request)
    {
        $chart_list = [];
        $state = $request->get('filter');
        $charts = self::getDB($state, $request);

        foreach ($charts as $chart) {
            $chart_obj = Chart::create_chart($chart->chartid, $chart->name, $chart->artist, $chart->genre, $chart->bpm, $chart->version, $chart->jacket, $chart->level, $chart->constant, $chart->difficulty, $chart->type, $chart->scoregrade, $chart->score, $chart->chartrating, $chart->combograde, $chart->syncgrade);
            $chart_obj->set_dx($chart->dxscore);
            $chart_obj->set_notes($chart->totalnotecount, $chart->tapcount, $chart->slidecount, $chart->holdcount, $chart->breakcount, $chart->touchcount, $chart->excount);

            array_push($chart_list, $chart_obj);
        }

        return $chart_list;
    }

    public static function recommendation(Request $request)
    {
        $chart_list = [];
        $predict_list = [];
        $friendcode = $request->user()->friendcode;
        $state = $request->get('filter');
        $fes15 = self::getDB('new', $request);
        $old35 = self::getDB('old', $request);
        $predicts = Predict::generateValues($friendcode, $fes15, $old35);
        $fesPredict = array();
        $oldPredict = array();
        foreach ($predicts[0] as $fes) {
            array_push($fesPredict,$fes['chartid']);
        }
        foreach ($predicts[1] as $old) {
            array_push($oldPredict,$old['chartid']);
        }

        $len_fes = count($fesPredict);
        $len_old = count($oldPredict);
    
        array_unshift($fesPredict,$friendcode);
        array_unshift($oldPredict,$friendcode);
        
        $starter_str = "
        select distinct charts.chartid, songs.songid, songs.name, songs.jacket, songs.artist, songs.genre, songs.version, songs.bpm, songs.type, charts.level,
        charts.constant, charts.difficulty, charts.totalnotecount, charts.tapcount, charts.slidecount, charts.holdcount, charts.breakcount,
        charts.touchcount, charts.excount, scores.score, scores.dxscore, scores.scoregrade, scores.combograde, scores.syncgrade, scores.chartrating 
        FROM charts
        INNER JOIN songs ON charts.parentsong = songs.songid
        INNER JOIN scores ON charts.chartid = scores.chartid
        AND scores.friendcode = ?
        WHERE ";
        
        $fes_str = $starter_str;
        $old_str = $starter_str;

        for ($i = 0; $i < $len_fes; $i++) {
            $fes_str = $fes_str."charts.chartid = ? OR ";
        }
        $fes_statement =  rtrim($fes_str, "OR ");

        for ($i = 0; $i < $len_old; $i++) {
            $old_str = $old_str."charts.chartid = ? OR ";
        }
        $old_statement =  rtrim($old_str, "OR ");
       
        $fescharts = DB::select($fes_statement, $fesPredict);

        $oldcharts = DB::select($old_statement, $oldPredict);

        if ($state == 'old') {
            foreach ($oldcharts as $chart) {
                $chart_obj = Chart::create_chart($chart->chartid, $chart->name, $chart->artist, $chart->genre, $chart->bpm, $chart->version, $chart->jacket, $chart->level, $chart->constant, $chart->difficulty, $chart->type, $chart->scoregrade, $chart->score, $chart->chartrating, $chart->combograde, $chart->syncgrade);
                $chart_obj->set_dx($chart->dxscore);
                $chart_obj->set_notes($chart->totalnotecount, $chart->tapcount, $chart->slidecount, $chart->holdcount, $chart->breakcount, $chart->touchcount, $chart->excount);
                foreach ($predicts[1] as $old) {
                    if ($old['chartid'] == $chart_obj->id) {
                        $chart_obj->set_recommendation($old['nextRange'], $old['potentialRatingGain'], $old['weight']);
                        break;
                    }
                }
                array_push($chart_list, $chart_obj);
            }
        }
       else {
           foreach ($fescharts as $chart) {
               $chart_obj = Chart::create_chart($chart->chartid, $chart->name, $chart->artist, $chart->genre, $chart->bpm, $chart->version, $chart->jacket, $chart->level, $chart->constant, $chart->difficulty, $chart->type, $chart->scoregrade, $chart->score, $chart->chartrating, $chart->combograde, $chart->syncgrade);
               $chart_obj->set_dx($chart->dxscore);
               $chart_obj->set_notes($chart->totalnotecount, $chart->tapcount, $chart->slidecount, $chart->holdcount, $chart->breakcount, $chart->touchcount, $chart->excount);
               foreach ($predicts[0] as $fes) {
                   if ($fes['chartid'] == $chart_obj->id) {
                       $chart_obj->set_recommendation($fes['nextRange'], $fes['potentialRatingGain'], $fes['weight']);
                       break;
                   }
               }
               array_push($chart_list, $chart_obj);
           }
       }


        usort($chart_list, function($a, $b) {
            return $b->weight <=> $a->weight;
        });

        $stored_charts = Sorted::set_ordered($chart_list);
        return $chart_list;
    }

}