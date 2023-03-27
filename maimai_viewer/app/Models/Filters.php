<?php 

namespace App\Models;

class Filters {
    public static function get_list($type) {
        $genres = ["POPS＆ANIME", "niconico＆VOCALOID™", "東方Project", "GAME＆VARIETY", "maimai", "オンゲキ＆CHUNITHM"];
        $versions = ["maimai", "maimai PLUS", "GreeN", "GreeN PLUS", "ORANGE", "ORANGE PLUS", "PiNK", "PiNK PLUS", "MURASAKi", "MURASAKi PLUS",
                    "MiLK", "MiLK PLUS", "FiNALE", "でらっくす", "でらっくす PLUS", "Splash", "Splash PLUS", "UNiVERSE", "UNiVERSE PLUS", "FESTiVAL"];
        $difficulties = ["BASIC", "ADVANCED", "EXPERT", "MASTER", "Re:MASTER"];
        $levels = ["1", "2", "3", "4", "5", "6", "7", "7+", "8", "8+", "9", "9+", "10", "10+", "11", "11+", "12", "12+", "13", "13+", "14", "14+", "15"];
        $sorts = ["Level", "Constant", "Score", "DX Score", "Sync Grade", "Combo Grade"];

        if ($type == 'genre') {
            return $genres;
        }
        elseif ($type == 'version') {
            return $versions;
        }
        elseif ($type == 'difficulty') {
            return $difficulties;
        }
        elseif ($type == 'level') {
            return $levels;
        }
        elseif ($type == 'sort') {
            return $sorts;
        }
    }
    public static function get_filter($type) {
        $genres = ["POPS＆ANIME", "niconico＆VOCALOID™", "東方Project", "GAME＆VARIETY", "maimai", "オンゲキ＆CHUNITHM"];
        $versions = ["maimai", "maimai PLUS", "GreeN", "GreeN PLUS", "ORANGE", "ORANGE PLUS", "PiNK", "PiNK PLUS", "MURASAKi", "MURASAKi PLUS",
                    "MiLK", "MiLK PLUS", "FiNALE", "でらっくす", "でらっくす PLUS", "Splash", "Splash PLUS", "UNiVERSE", "UNiVERSE PLUS", "FESTiVAL"];
        $difficulties = ["Basic", "Advanced", "Expert", "Master", "Remaster"];
        $levels = ["1", "2", "3", "4", "5", "6", "7", "7+", "8", "8+", "9", "9+", "10", "10+", "11", "11+", "12", "12+", "13", "13+", "14", "14+", "15"];
        $sorts = ["level_val", "constant", "score_val", "dxscore", "sync_val", "combo_val"];

        if ($type == 'genre') {
            return $genres;
        }
        elseif ($type == 'version') {
            return $versions;
        }
        elseif ($type == 'difficulty') {
            return $difficulties;
        }
        elseif ($type == 'level') {
            return $levels;
        }
        elseif ($type == 'sort') {
            return $sorts;
        }
    }
}