<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;
use App\Http\Controllers\Songfinder;
use App\Http\Controllers\ChartLoader;
use App\Models\Navbar;
use App\Models\Page_status;

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
        'title'=> 'Navbar Test',
        'description'=> "Testing the navbar port",
        'logo_url'=> URL::asset('/images/nav_icons/bearhands.png'),
        'user'=> Navbar::retrieveuser(),
        'status'=>Page_status::set_status('home')
    ]);
});

Route::get('/songs', function(Request $request) {
    if (count($request->all()) > 0) {
        $charts = Chartloader::retrieve_result($request);
    }
    else {
        $charts = [];
    }
    return view('songs', [
        'title'=> 'Songs Finder',
        'description'=> "Search for songs in the Maimai database",
        'logo_url'=> URL::asset('/images/nav_icons/bearhands.png'),
        'user'=> Navbar::retrieveuser(),
        'status'=>Page_status::set_status('songs'),
        'genres'=>Songfinder::initialize_genre($request),
        'versions'=>Songfinder::initialize_ver($request),
        'difficulties'=>Songfinder::initialize_diff($request),
        'levels'=>Songfinder::initialize_level($request),
        'sorts'=>Songfinder::initialize_sorts($request),
        'key'=>Songfinder::key($request),
        'charts'=> $charts
    ]);
});

