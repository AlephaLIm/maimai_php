<?php 
namespace App\Models;

class Page_status {
    public $home = '';
    public $achievements = '';
    public $songs = '';
    public $recommendations = '';

    public static function set_status($page) {
        $status = new Page_status();

        if ($page == 'home') {
            $status->home = 'active';
        }
        elseif ($page == 'achievement') {
            $status->achievements = 'active';
        }
        elseif ($page == 'songs') {
            $status->songs = 'active';
        }
        elseif ($page == 'recommendations') {
            $status->recommendations = 'active';
        }

        return $status;
    }
}