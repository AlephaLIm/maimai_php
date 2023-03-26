<?php
namespace App\Models;
use Illuminate\Support\Facades\URL;

class Profile {
    public $username;
    public $email;
    public $friendcode;
    public $rating;

    
    public static function retrieveuser(?string $username = null, ?string $email = null, ?string $friendcode = null ,?string $rating = null) {
        $user = new Profile();
        $user->username = $username;
        $user->email = $email;
        $user->friendcode = $friendcode;
        $user->rating = $rating;
        return $user;
    }
};
