<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Songfinder;
use App\Models\Chart;
use App\Models\Filters;

class ChartLoader extends Controller
{
    public static function retrieve_result(Request $request) {
        $filters = ["genre", "version", "difficulty", "level", "sort"];
        $list_items = [];
        $key = "desc";
        $chart_list = [];
        foreach ($filters as $filter) {
            if ($request->filled($filter)) {
                $list_items[$filter] = Songfinder::extract_params($request, $filter);
            }
            else {
                $list_items[$filter] = Filters::get_filter($filter);
            }
        }
        if ($request->filled('key')) {
            $key = "asc";
        }

        if ($request->filled('search')) {
            $charts = DB::table('Charts')
                                    ->join('Songs', 'Charts.parentsong', '=', 'Songs.songid')
                                    ->join('Alias', 'Songs.songid', '=', 'Alias.songid')
                                    ->select('Charts.chartid', 'Songs.name', 'Songs.jacket', 'Charts.level', 'Charts.constant', 'Charts.difficulty', 'Songs.type', 'Songs.artist', 'Songs.genre', 'Songs.version', 'Songs.bpm')
                                    ->whereIn('Songs.genre', $list_items['genre'])
                                    ->whereIn('Songs.version', $list_items['version'])
                                    ->whereIn('Charts.difficulty', $list_items['difficulty'])
                                    ->whereIn('Charts.level', $list_items['level'])
                                    ->where(DB::raw('lower(Alias.alias)'), 'like', '%'.$request->get('search').'%')
                                    ->get();
        }
        else {
            $charts = DB::table('Charts')
                                ->join('Songs', 'Charts.parentsong', '=', 'Songs.songid')
                                ->select('Charts.chartid', 'Songs.name', 'Songs.jacket', 'Charts.level', 'Charts.constant', 'Charts.difficulty', 'Songs.type', 'Songs.artist', 'Songs.genre', 'Songs.version', 'Songs.bpm')
                                ->whereIn('Songs.genre', $list_items['genre'])
                                ->whereIn('Songs.version', $list_items['version'])
                                ->whereIn('Charts.difficulty', $list_items['difficulty'])
                                ->whereIn('Charts.level', $list_items['level'])
                                ->get();
        }

        foreach ($charts as $chart) {
            $chart_obj = Chart::create_chart($chart->chartid, $chart->name, $chart->artist, $chart->genre, $chart->bpm, $chart->version, $chart->jacket, $chart->level, $chart->constant, $chart->difficulty, $chart->type);
            $chart_obj->set_dx('99/100');
            array_push($chart_list, $chart_obj);
        }

        return $chart_list;
    }
}
