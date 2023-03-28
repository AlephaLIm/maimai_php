<?php
namespace App\Models;
use Illuminate\Support\Facades\URL;

class Navbar {
    public $name;
    public $title;
    public $rating;
    public $profile_img;
    
    public static function retrieveuser(?string $name = null, ?string $title = null, ?string $rating = null, ?string $img = null) {
        $user = new Navbar();
        $user->name = $name ?? " Guest_User";
        $user->title = $title ?? "-- No Title --";
        $user->rating = $rating ?? "------";
        $user->profile_img = $img ?? URL::asset("/images/nav_icons/guest.jpg");

        return $user;
    }
};

