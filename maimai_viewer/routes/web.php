<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/nav_test', function() {
    return view('navbar', [
        'title'=> "Navbar Test",
        'description'=> "Testing the navbar port",
        'logo_url'=> URL::asset('/images/nav_icons/bearhands.png'),
        'profile_url'=> URL::asset('/images/nav_icons/user_img.png'),
        'user'=> [
                'name'=> "H O S H I N O",
                'title'=> "響け！CHIREI MY WAY!",
                'rating'=> "15000"
        ],
        'status'=>[
            'achievements'=>'',
            'songs'=>'',
            'recommendations'=>''
        ]
    ]);
});