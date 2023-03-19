<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Filterbox;

class Songfinder extends Controller
{   
    private static function extract_params(Request $request, $type) {
        $request_str = $request->get($type);
        $params = explode(',', $request_str);

        return $params;
    }

    public static function initialize_genre(Request $request) {
        $genres = ["POPS＆ANIME", "niconico＆VOCALOID™", "東方Project", "GAME＆VARIETY", "maimai", "オンゲキ＆CHUNITHM"];
        $genre_list = [];
        $selected = self::extract_params($request, "genre");
        foreach ($genres as $genre) {
            $instance = Filterbox::initialize($genre);
            array_push($genre_list, $instance);
            if (in_array($genre, $selected)){
                $instance->status = "selected";
            }
        }
        return $genre_list;
    }
    
    public static function initialize_ver(Request $request) {
        $versions = ["maimai", "maimai PLUS", "GreeN", "GreeN PLUS", "ORANGE", "ORANGE PLUS", "PiNK", "PiNK PLUS", "MURASAKi", "MURASAKi PLUS",
        "MiLK", "MiLK PLUS", "FiNALE", "でらっくす", "でらっくす PLUS", "Splash", "Splash PLUS", "UNiVERSE", "UNiVERSE PLUS", "FESTiVAL"];
        $version_list = [];
        $selected = self::extract_params($request, "version");
        foreach ($versions as $version) {
            $instance = Filterbox::initialize($version);
            array_push($version_list, $instance);
            if (in_array($version, $selected)){
                $instance->status = "selected";
            }
        }
        return $version_list;
    }
    
    public static function initialize_diff(Request $request) {
        $difficulties = ["BASIC", "ADVANCED", "EXPERT", "MASTER", "Re:MASTER"];
        $diff_list = [];
        $selected = self::extract_params($request, "difficulty");
        foreach ($difficulties as $diff) {
            $instance = Filterbox::initialize($diff);
            array_push($diff_list, $instance);
            if (in_array($diff, $selected)){
                $instance->status = "selected";
            }
        }
        return $diff_list;
    }
    
    public static function initialize_level(Request $request) {
        $levels = ["1", "2", "3", "4", "5", "6", "7", "7+", "8", "8+", "9", "9+", "10", "10+", "11", "11+", "12", "12+", "13", "13+", "14", "14+", "15"];
        $level_list = [];
        $selected = self::extract_params($request, "level");
        foreach ($levels as $level) {
            $instance = Filterbox::initialize($level);
            array_push($level_list, $instance); 
            if (in_array($level, $selected)){
                $instance->status = "selected";
            }
        }
        return $level_list;
    }
    
    public static function initialize_sorts(Request $request) {
        $sorts = ["Level", "Constant", "Score", "DX Score", "Sync Grade", "Combo Grade"];
        $sort_list = [];
        $selected = self::extract_params($request, "sort");
        foreach ($sorts as $sort) {
            $instance = Filterbox::initialize($sort);
            array_push($sort_list, $instance); 
            if (in_array($sort, $selected)){
                $instance->status = "selected";
            }
        }
        return $sort_list;
    }

    public static function key(Request $request) {
        $key = $request->get("key");
        return $key;
    }
}
