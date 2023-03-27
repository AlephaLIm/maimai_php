<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;

class Predict
{
    public $id;
    public $name;
    public $artist;
    public $genre;
    public $bpm;
    public $version;
    public $img;
    public $level;
    public $constant;
    public $diff;
    public $type;
    public $type_col;
    public $scoregrade;
    public $score;
    public $dxscore;
    public $sync_grade;
    public $combo_grade;
    public $rating;
    public $color;
    public $chartrating;

    function getAR($score)
    {
        $achievement_range = [80.0, 90.0, 94.0, 97.0, 98.0, 99.0, 99.5, 100.0, 100.5];
        $factor = [
            [10.880, 12.240],
            [13.680, 14.288],
            [15.792, 16.296],
            [19.4, 19.6],
            [19.894, 20.097],
            [20.592, 20.696],
            [20.995, 21.1],
            [21.6, 21.708],
            [22.512, 22.512]
        ];

        for ($i = 0; $i < count($achievement_range); $i++) {
            if (floatval($score) < 80.0) {
                return [null, 80.0, [0.0, 0.0], [10.880, 12.240]];
            } elseif (floatval($score) < floatval($achievement_range[$i])) {
                return [$achievement_range[$i - 1], $achievement_range[$i], $factor[$i - 1], $factor[$i]];
            } elseif (floatval($score) >= 100.5) {
                return [null, null, [22.512, 22.512], null];
            }
        }
    }

    function CalculateRating($score, $chart_constant)
    {
        $score = floatval($score);
        if ($score < 80) {
            return 0;
        } elseif ($score >= 100.5) {
            return intval(22.512 * $chart_constant);
        }
        $chart_constant = floatval($chart_constant);
        $result = self::getAR($score);
        $car = $result[0]; // Current achievement range
        $nar = $result[1]; // Next achievement range
        $FactorL = $result[2][0]; // Factor lower value
        $FactorH = $result[2][1]; // Factor higher value
        $score_diff = $score - $car;
        $score_progress = $score_diff / ($nar - $car);
        $multiplier = ($score_progress * ($FactorH - $FactorL)) + $FactorL;
        $rating = intval($multiplier * $chart_constant);
        return $rating;
    }
    function generateValues($userid, $charts)
    {
        $scores = DB::select("
        SELECT *
        FROM scores
        JOIN songs ON charts.parentsong = songs.songid
        WHERE userid = ?
        ORDER BY id ASC
        ", [$userid]);

        $potential = array();

        foreach ($scores as $score) {
            $weight = 0;
            $currentScore = (float) $score['score'];
            $constant = (float) $score['chartid']['constant'];
            $name = $score['chartid']['parentsong']['name'];
            $version = $score['chartid']['parentsong']['version'];
            $target = end($charts);

            if ($currentScore >= 100.5) {
                continue;
            } else {
                $arValues = self::getAR($currentScore);
                $nextRange = $arValues[1];
                $nextFactorRange = $arValues[3];
                $currentRating = self::CalculateRating($currentScore, $constant);
                $nextRating = self::CalculateRating($nextRange, $constant);

                if ($nextRating > $target['rating']) {
                    if ($nextRange - $currentScore <= 0.1) {
                        $weight += 5;
                    } elseif ($nextRange - $currentScore <= 0.2) {
                        $weight += 4;
                    } elseif ($nextRange - $currentScore <= 0.3) {
                        $weight += 3;
                    } elseif ($nextRange - $currentScore <= 0.4) {
                        $weight += 2;
                    } elseif ($nextRange - $currentScore <= 0.5) {
                        $weight += 1;
                    } else {
                        $weight += 0.5;
                    }

                    $potentialRatingGain = $nextRating - $target['rating'];

                    if ($potentialRatingGain >= 10) {
                        $weight += 3;
                    } elseif ($potentialRatingGain >= 7) {
                        $weight += 2;
                    } elseif ($potentialRatingGain >= 4) {
                        $weight += 1;
                    } else {
                        $weight += 0.5;
                    }
                }

                $potential[] = array($score, $weight, $nextRange, $potentialRatingGain);
            }
        }

        usort($potential, function ($a, $b) {
            return $b[1] <=> $a[1];
        });

        return array_slice($potential, 0, 10);
    }
}