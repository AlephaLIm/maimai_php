<?php 

namespace App\Models;

class Chart {
    public $id;
    public $name;
    public $artist;
    public $genre;
    public $bpm;
    public $version;
    public $img;
    public $level;
    public $level_val;
    public $constant;
    public $notecount;
    public $tap;
    public $slide;
    public $hold;
    public $break;
    public $touch;
    public $ex;
    public $chart_view = 'disabled';
    public $diff;
    public $type;
    public $type_col;
    public $scoregrade;
    public $score;
    public $score_val;
    public $dxscore = 0;
    public $sync_grade;
    public $sync_val;
    public $combo_grade;
    public $combo_val;
    public $rating;
    public $color;

    public static function create_chart($id, $name, $artist, $genre, $bpm, $version, $img, $level, $constant, $diff, $type, ?float $scoregrade = null, ?float $score = null, ?int $rating = null, ?string $combo_grade = null, ?string $sync_grade = null) {
        $d_search = ["Basic"=>"BASIC", "Advanced"=>"ADVANCED", "Expert"=>"EXPERT", "Master"=>"MASTER", "Remaster"=>"Re:MASTER"];
        $sync_combo_map = ['' => 0, 'FC' => 1, 'FC+' => 2, 'AP' => 3, 'AP+' => 4, 'FS' => 1, 'FS+' => 2, 'FSD' => 3, 'FSD+' => 4];
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
        $chart->artist = $artist;
        $chart->genre = $genre;
        $chart->bpm = $bpm;
        $chart->version = $version;
        $chart->img = $img;
        $chart->level = $level;
        $chart->constant = $constant;
        $chart->diff = $d_search[$diff];
        $chart->scoregrade = $scoregrade ?? "---";
        $chart->score = $score ?? "---.----";
        $chart->rating = $rating ?? "---";
        $chart->combo_grade = $combo_grade ?? '';
        $chart->sync_grade = $sync_grade ?? '';
        $chart->color = $c_sets[$d_search[$diff]];
        $chart->type_col = $type_color[$type];

        $chart->score_val = $score ?? 0;
        $chart->level_val = floatval(str_replace("+", ".5", $level));
        $chart->combo_val = $sync_combo_map[$chart->combo_grade];
        $chart->sync_val = $sync_combo_map[$chart->sync_grade];

        if ($type == "Standard") {
            $chart->type = "SD";
        }
        else {
            $chart->type = "DX";
        }


        return $chart;
    }

    public function set_dx($dxscore_val) {
        $params = explode('/', $dxscore_val);
        $dx = floatval($params[0]) / floatval($params[1]);
        if ($dx >= 0.97) {
            $this->dxscore = 5;
        }
        elseif ($dx >= 0.95) {
            $this->dxscore = 4;
        }
        elseif ($dx >= 0.93) {
            $this->dxscore = 3;
        }
        elseif ($dx >= 0.90) {
            $this->dxscore = 2;
        }
        elseif ($dx >= 0.85) {
            $this->dxscore = 1;
        }
        else {
            $this->dxscore = 0;
        }
    }

    public function set_notes($notecount, $tapnotes, $slidenotes, $holdnotes, $breaknotes, $touchnotes, $exnotes) {
        $this->notecount = $notecount;
        $this->tap = $tapnotes;
        $this->slide = $slidenotes;
        $this->hold = $holdnotes;
        $this->break = $breaknotes;
        $this->touch = $touchnotes;
        $this->ex = $exnotes;

        $this->chart_view = "enabled";
    }
}