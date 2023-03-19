<?php

namespace App\Models;

class Filterbox {
    public $name;
    public $value;
    public $status;

    public static function initialize($name, $value) {
        $filter = new Filterbox();
        $filter->name = $name;
        $filter->value = $value;
        $filter->status = '';

        return $filter;
    }
}