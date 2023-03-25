<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Songfinder;
use App\Models\Sorted;
use App\Models\Chart;
use App\Models\Filters;

class ChartLoader extends Controller
{
    public static function retrieve_result(Request $request) {
        $filters = ["genre", "version", "difficulty", "level", "sort"];
        $sql_statement = "select distinct charts.chartid, songs.songid, songs.name, songs.jacket, songs.artist, songs.genre, songs.version, songs.bpm, songs.type, charts.level,
                     charts.constant, charts.difficulty, charts.totalnotecount, charts.tapcount, charts.slidecount, charts.holdcount, charts.breakcount,
                     charts.touchcount, charts.excount 
                     from charts inner join songs on charts.parentsong = songs.songid";
        $list_items = [];
        $key = "desc";
        $chart_list = [];
        $param = '';

        foreach ($filters as $filter) {
            if ($request->filled($filter)) {
                $list_items[$filter] = implode(',', Songfinder::extract_params($request, $filter));
                $param_count = count(explode(',', $list_items[$filter]));

                if ($filter == 'sort') {
                    continue;
                }
                else {
                    $param .= $list_items[$filter].',';
                    $sql_statement = "select distinct * from (".$sql_statement.") as instance where instance.".$filter." =";
                    for ($i = 0; $i < $param_count; $i++) {
                        $sql_statement = $sql_statement."? or instance.".$filter." =";
                    }
                    $sql_statement =  rtrim($sql_statement, "or instance.".$filter." =");
                }
            }
            elseif ($filter == 'sort') {
                $list_items[$filter] = Filters::get_filter($filter);
            }
        }

        if ($request->filled('key')) {
            $key = "asc";
        }

        if ($request->has('search')) {
            $sql_statement = "select distinct search.chartid, search.songid, search.name, search.jacket, search.artist, search.genre, search.version, search.bpm, search.type, search.level,
                    search.constant, search.difficulty, search.totalnotecount, search.tapcount, search.slidecount, search.holdcount, search.breakcount,
                    search.touchcount, search.excount 
            from (".$sql_statement." ) as search inner join alias on search.songid = alias.songid where alias.alias like concat('%', ?, '%')";
            $param .= $request->get('search').',';
        }

        if (empty($param)) {
            $charts = DB::select($sql_statement);
        }
        else {
            $params = rtrim($param, ',');
            $charts = DB::select($sql_statement, explode(',', $params));
        }

        foreach ($charts as $chart) {
            $chart_obj = Chart::create_chart($chart->chartid, $chart->name, $chart->artist, $chart->genre, $chart->bpm, $chart->version, $chart->jacket, $chart->level, $chart->constant, $chart->difficulty, $chart->type);
            $chart_obj->set_notes($chart->totalnotecount, $chart->tapcount, $chart->slidecount, $chart->holdcount, $chart->breakcount, $chart->touchcount, $chart->excount);
            array_push($chart_list, $chart_obj);
        }

        $charts_ordered = self::sort_charts($chart_list, $list_items['sort'], $key);
        $stored_charts = Sorted::set_ordered($charts_ordered);

        return $stored_charts;
    }

    private static function sort_charts($array, $keys, $order) {
        $count = count($keys) - 1;
        usort($array, function($former, $latter) use ($keys, $order, $count){
            return self::helper_sort($former, $latter, $keys, $order, $count);
        });
        return $array;
    }

    private static function helper_sort($former, $latter, $keys, $order, $count, ?int $index = 0) {
        $param = $keys[$index];
        if ($former->$param == $latter->$param) {
            if ($index < $count) {
                return self::helper_sort($former, $latter, $keys, $order, $count, $index + 1);
            }
            else {
                return 0;
            }
        }
        elseif ($former->$param > $latter->$param) {
            if ($order == 'desc') {
                return -1;
            }
            else {
                return 1;
            }
        }
        else {
            if ($order == 'desc') {
                return 1;
            }
            else {
                return -1;
            }
        }
    }
}