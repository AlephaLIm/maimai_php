<?php
namespace App\Models;

class Page_status
{
    public $home = '';
    public $profile = '';
    public $achievements = '';
    public $songs = '';
    public $rating = '';

    public static function set_status($page)
    {
        $status = new Page_status();

        if ($page == 'home') {
            $status->home = 'active';
        } elseif ($page == 'profile') {
            $status->profile = 'active';
        } elseif ($page == 'achievement') {
            $status->achievements = 'active';
        } elseif ($page == 'songs') {
            $status->songs = 'active';
        } elseif ($page == 'rating') {
            $status->rating = 'active';
        }

        return $status;
    }
}