<?php
namespace App\Models;

class RateOption {
    public $all = '';
    public $new = '';
    public $old = '';
    public $option;

    public static function getRate(?string $parameter = null, ?string $option = null) {
        $rate = new RateOption();
        if ($parameter == 'new') {
            $rate->new = 'active';
            $rate->option = $option ?? 'New songs rating';
        }
        elseif ($parameter == 'old') {
            $rate->old = 'active';
            $rate->option = $option ?? 'Old songs rating';
        }
        else {
            $rate->all = 'active';
            $rate->option = 'All songs rating';
        }
        return $rate;
    }
}