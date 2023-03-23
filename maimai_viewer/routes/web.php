<?php

use Illuminate\Support\Facades\URL;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Songfinder;
use App\Http\Controllers\ChartLoader;
use App\Http\Controllers\Paginate;
use App\Models\Sorted;
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

//Post API for scorescrapper.js
Route::post('/data', 'App\Http\Controllers\DatabaseController@data');
//Get API to send song information to scorescrapper.js
Route::get('/songinfo', 'App\Http\Controllers\getsongController@get');

Route::get('/songs', function(Request $request) {
    
    $sorted = Chartloader::retrieve_result($request);
    $charts = Paginate::generate_pagination($request, $sorted);

    return view('songs', [
        'title'=> 'Songs Finder',
        'description'=> "Search for songs in the Maimai database",
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

// register form
Route::get('/register', [UserController::class, 'create'])->middleware('guest');

// create user
Route::post('/users', [UserController::class, 'store']);

// logout user
Route::get('/logout', [UserController::class, 'logout'])->middleware('auth');

// login form
Route::get('/login', [UserController::class, 'login'])->name('login')->middleware('guest');

// login user
Route::post('/users/authenticate', [UserController::class, 'authenticate']);

