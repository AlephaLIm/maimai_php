<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;
use App\Models\Navbar;

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

Route::post('/data', 'App\Http\Controllers\DatabaseController@data');

Route::get('/nav_test', function() {
    return view('navbar', [
        'title'=> "Navbar Test",
        'description'=> "Testing the navbar port",
        'logo_url'=> URL::asset('/images/nav_icons/bearhands.png'),
        'user'=> Navbar::retrieveuser(),
        'status'=>[
            'achievements'=>'',
            'songs'=>'',
            'recommendations'=>''
        ]
    ]);
});

Route::get('/songs', function() {
    return view('songs', [
        'title'=> "Songs Finder",
        'description'=> "Search for songs in the Maimai database",
        'logo_url'=> URL::asset('/images/nav_icons/bearhands.png'),
        'user'=> Navbar::retrieveuser(),
        'status'=>[
            'achievements'=>'',
            'songs'=>'',
            'recommendations'=>''
        ]
    ]);
});