<?php

use Illuminate\Support\Facades\URL;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\NavController;
use App\Http\Controllers\Songfinder;
use App\Http\Controllers\ChartLoader;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StatsController;
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

//Route::get('/', function () {
//    return view('welcome');
//});

// home page
Route::get('/', function(Request $request) {
    return view('home', [
        'title'=> 'Home Page',
        'description'=> "Welcome to Mai Mai",
        'user'=> NavController::get_user($request),
        'status'=>Page_status::set_status('home')
    ]);
}); 
 
Route::get('/stats/{id}', function(Request $request, $id) {
    return view('stats', [
        'title'=> 'Statistics Page',
        'description'=> "View your Mai Mai Statistics.",
        'levelArray'=> StatsController::stats($id),
        'user'=> NavController::get_user($request),
        'status'=>Page_status::set_status('profile')
    ]);
})->middleware('auth'); 
//Post API for scorescrapper.js
Route::post('/data', 'App\Http\Controllers\DatabaseController@data');
//Get API to send song information to scorescrapper.js
Route::post('/songinfo', 'App\Http\Controllers\getsongController@get');
//Post API for new songs
Route::post('/newsongs', 'App\Http\Controllers\NewSongController@songs');

Route::get('/edit_profile', function() {
    return view('users/modify', [
        'title' => 'Modify User',
        'description' => 'Change email and password',
        'status'=>Page_status::set_status('edit')
    ]);
})->middleware('auth');

Route::get('/deleteUser', function() {
    return view('users/delete', [
        'title' => 'Delete User',
        'description' => 'Remove your account',
        'status'=>Page_status::set_status('edit')
    ]);
})->middleware('auth');

Route::post('/update_profile', [ProfileController::class, 'updateProfile'])->middleware('auth');

Route::post('/delete_user', [ProfileController::class, 'deleteUser'])->middleware('auth');

Route::get('/songs', function(Request $request) {
    
    $sorted = Chartloader::retrieve_result($request);
    $charts = Paginate::generate_pagination($request, $sorted);

    return view('songs', [
        'title'=> 'Songs Finder',
        'request'=>$request,
        'description'=> "Search for songs in the Maimai database",
        'user'=> NavController::get_user($request),
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

