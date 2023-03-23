<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Filterbox;
use App\Models\Filters;

class Songfinder extends Controller
{   
    public static function extract_params(Request $request, $type) {
        $request_str = $request->get($type);
        $params = explode(',', $request_str);

        return $params;
    }

    public static function initialize_genre(Request $request) {
        $genres = Filters::get_list('genre');
        $genre_list = [];
        $selected = self::extract_params($request, "genre");
        foreach ($genres as $genre) {
            $instance = Filterbox::initialize($genre, $genre);
            array_push($genre_list, $instance);
            if (in_array($genre, $selected)){
                $instance->status = "selected";
            }
        }
        return $genre_list;
    }
    
    public static function initialize_ver(Request $request) {
        $versions = Filters::get_list('version');
        $version_list = [];
        $selected = self::extract_params($request, "version");
        foreach ($versions as $version) {
            $instance = Filterbox::initialize($version, $version);
            array_push($version_list, $instance);
            if (in_array($version, $selected)){
                $instance->status = "selected";
            }
        }
        return $version_list;
    }
    
    public static function initialize_diff(Request $request) {
        $difficulties = Filters::get_list('difficulty');
        $diff_filter = Filters::get_filter('difficulty');
        $diff_list = [];
        $selected = self::extract_params($request, "difficulty");
        foreach (array_combine($difficulties, $diff_filter) as $diff => $diff_val) {
            $instance = Filterbox::initialize($diff, $diff_val);
            array_push($diff_list, $instance);
            if (in_array($diff_val, $selected)){
                $instance->status = "selected";
            }
        }
        return $diff_list;
    }
    
    public static function initialize_level(Request $request) {
        $levels = Filters::get_list('level');
        $level_list = [];
        $selected = self::extract_params($request, "level");
        foreach ($levels as $level) {
            $instance = Filterbox::initialize($level, $level);
            array_push($level_list, $instance); 
            if (in_array($level, $selected)){
                $instance->status = "selected";
            }
        }
        return $level_list;
    }
    
    public static function initialize_sorts(Request $request) {
        $sorts = Filters::get_list('sort');
        $sort_filter = Filters::get_filter('sort');
        $sort_list = [];
        $selected = self::extract_params($request, "sort");
        foreach (array_combine($sorts, $sort_filter) as $sort => $sort_val) {
            $instance = Filterbox::initialize($sort, $sort_val);
            array_push($sort_list, $instance); 
            if (in_array($sort_val, $selected)){
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
