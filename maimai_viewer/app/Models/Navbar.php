<?php
namespace App\Models;


class Navbar {
    public $name;
    public $title;
    public $rating;
    public $profile_img;
    
    public static function retrieveuser() {
        $user = new Navbar();
        $user->name = "H O S H I N O";
        $user->title = "響け！CHIREI MY WAY!";
        $user->rating = "15000";
        $user->profile_img = "/images/nav_icons/user_img.png";

        return $user;
    }
};

