<?php

namespace App\Models;

class Filterbox {
    public $name;
    public $status;

    public static function initialize($name) {
        $filter = new Filterbox();
        $filter->name = $name;
        $filter->status = '';

        return $filter;
    }
}