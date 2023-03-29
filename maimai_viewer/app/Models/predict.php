<?php

namespace App\Models;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class Predict
{
    public $score;
    public $weight;
    public $nextRange;
    public $potentialRatingGain;
    public $id;

    public static function getAR($score)
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

    public static function CalculateRating($score, $chart_constant)
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
        $rating = floor($multiplier * $chart_constant);
        return $rating;
    }
    public static function generateValues($friendcode, $fes15, $old15)
    {
        
        $results = DB::select("SELECT scores.score,scores.chartid, charts.constant, songs.version, scores.chartrating
        FROM scores INNER JOIN charts ON scores.chartid = charts.chartid INNER JOIN songs ON songs.songid = charts.parentsong WHERE friendcode = ?",[$friendcode]);


        $ret = array();
        $festival = array();
        $nonFestival = array();
        $fesFloor = end($fes15)->chartrating;
        $oldFloor = end($old15)->chartrating;

        foreach ($results as $result) {
            $weight = 0;
            $version = $result->version;
            if ($version == "FESTiVAL") {
                $target = $fesFloor;
                foreach($fes15 as $fes) {
                    if ($fes->chartid == $result->chartid) {
                        $target = $result->chartrating;
                    }
                } 
            }
            else {
                $target = $oldFloor;
                foreach($old15 as $old) {
                    if ($old->chartid == $result->chartid) {
                        $target = $result->chartrating;
                    }
                } 
            }
            
            $currentScore = (float) $result->score;
            $chartId = $result->chartid;
            $constant = (float) $result->constant;
           
            

            if ($currentScore >= 100.5) {
                continue;
            } else {
                
                $arValues = self::getAR($currentScore);
                $nextRange = $arValues[1];
                $nextFactorRange = $arValues[3];
                $currentRating = self::CalculateRating($currentScore, $constant);
                $nextRating = self::CalculateRating($nextRange, $constant);
                $potentialRatingGain = 0;

                if ($nextRating > $target) {
                    
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
                    
                    $potentialRatingGain = $nextRating - $target;
                    
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
    
                $candidate = ['weight' => $weight, 'nextRange'=> $nextRange,'potentialRatingGain'=> $potentialRatingGain, 'chartid' => $chartId];
                if ($version == "FESTiVAL") {
                    array_push($festival,$candidate);
                }
                else {
                    array_push($nonFestival,$candidate);
                }

            }
        }

        usort($festival, function ($a, $b) {
            return $b['weight'] <=> $a['weight'];
        });
        usort($nonFestival, function ($a, $b) {
            return $b['weight'] <=> $a['weight'];
        });
        $fest15 = array_slice($festival, 0, 15);
        $old15 = array_slice($nonFestival, 0, 15);
        array_push($ret,$fest15);
        array_push($ret,$old15);
        return $ret;
        
    }

    public static function create_predict($score, $weight, $nextRange, $potentialRatingGain, $id)
    {

        $predict = new Predict();
        $predict->score = $score;
        $predict->weight = $weight;
        $predict->nextRange = $nextRange;
        $predict->potentialRatingGain = $potentialRatingGain;
        $predict->id = $id;

        return $predict;
    }
}