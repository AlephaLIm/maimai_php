<?php 

namespace App\Models;

class Chart {
    public $id;
    public $name;
    public $img;
    public $level;
    public $diff;
    public $type;
    public $type_col;
    public $scoregrade;
    public $score;
    public $rating;
    public $color;

    public static function create_chart($id, $name, $img, $level, $diff, $type, ?float $scoregrade = null, ?float $score = null, ?int $rating = null) {
        $d_search = ["Basic"=>"BASIC", "Advanced"=>"ADVANCED", "Expert"=>"EXPERT", "Master"=>"MASTER", "Remaster"=>"Re:MASTER"];

        $c_sets = [
            "BASIC" => ["base"=>"#02A726", "bg"=>"#1F3615", "submain"=>"#1BD644","accent"=>"#81D955", "text"=>"#B00000"],
            "ADVANCED"=> ["base"=>"#D57100", "bg"=>"#3E1600", "submain"=>"#F8B709", "accent"=>"#CBA560", "text"=>"#00BBD8"],
            "EXPERT"=> ["base"=>"#AB0000", "bg"=>"#2E1A1A", "submain"=>"#AD2E24", "accent"=>"#FE8994", "text"=>"#CBC400"],
            "MASTER"=> ["base"=>"#6D00C1", "bg"=>"#180020", "submain"=>"#8000E2", "accent"=>"#C99FC9", "text"=>"#F0C400"],
            "Re:MASTER"=> ["base"=>"#743978", "bg"=>"#270E1D", "submain"=>"#DADADA", "accent"=>"#E3ABF4", "text"=>"#E71D36"]
        ];

        $type_color = ["Standard"=>"#00ABDA", "DX"=>"#FFBF00"];
        
        $chart = new Chart();
        $chart->id = $id;
        $chart->name = $name;
        $chart->img = $img;
        $chart->level = $level;
        $chart->diff = $d_search[$diff];
        $chart->scoregrade = $scoregrade ?? "---";
        $chart->score = $score ?? "---.----";
        $chart->rating = $rating ?? "---";
        $chart->color = $c_sets[$d_search[$diff]];
        $chart->type_col = $type_color[$type];

        if ($type == "Standard") {
            $chart->type = "SD";
        }
        else {
            $chart->type = "DX";
        }


        return $chart;
    }
}