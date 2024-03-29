<?php
namespace App\Models;

class Page_status
{
    public $home = '';
    public $aboutus = '';
    public $profile = '';
    public $edit = '';
    public $achievements = '';
    public $songs = '';
    public $rating = '';
    public $recommendation = '';

    public static function set_status($page)
    {
        $status = new Page_status();

        if ($page == 'home') {
            $status->home = 'active';
        } elseif ($page == 'aboutus') {
            $status->aboutus = 'active';
        } elseif ($page == 'profile') {
            $status->profile = 'active';
        } elseif ($page == 'edit') {
            $status->edit = 'active';
        } elseif ($page == 'achievement') {
            $status->achievements = 'active';
        } elseif ($page == 'songs') {
            $status->songs = 'active';
        } elseif ($page == 'rating') {
            $status->rating = 'active';
        } elseif ($page == 'recommendation') {
            $status->recommendation = 'active';
        }

        return $status;
    }
}