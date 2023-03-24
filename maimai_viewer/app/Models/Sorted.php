<?php 

namespace App\Models;
use App\Models\Chart;

class Sorted {
    public static function set_ordered($charts) {
        $array = [];
        for ($i = 0; $i < count($charts); $i++) {
            $array[$i] = $charts[$i];
        }
        return $array;
    }
}