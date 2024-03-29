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
use App\Http\Controllers\RatingController;
use App\Http\Controllers\Paginate;
use App\Models\Sorted;
use App\Models\Navbar;
use App\Models\Page_status;
use App\Models\RateOption;

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

Route::get('/aboutus', function(Request $request) {
    return view('aboutus', [
        'title'=> 'About Us',
        'description'=> "Welcome to Mai Mai",
        'user'=> NavController::get_user($request),
        'status'=>Page_status::set_status('aboutus')
    ]);
});

Route::get('/stats', function (Request $request) {
    return view('stats', [
        'title' => 'Statistics Page',
        'description' => "View your Mai Mai Statistics.",
        'levelArray' => StatsController::stats($request),
        'user' => NavController::get_user($request),
        'status' => Page_status::set_status('profile')
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

Route::get('/ratings', function(Request $request) {
    if ($request->has('filter')) {
        if ($request->get('filter') == 'new') {
            $param = 'new';
        }
        else {
            $param = 'old';
        }
    }
    else {
        $param = null;
    }
    return view('rating', [
        'title'=> 'Achievements',
        'description'=> "View your best played songs!",
        'user'=> NavController::get_user($request),
        'status'=> Page_status::set_status('achievement'),
        'rate'=> RateOption::getRate($param),
        'songs'=> RatingController::rating($request)
    ]);
})->middleware('auth');

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


Route::get('/recommendation', function (Request $request) {
    if ($request->has('filter')) {
        if ($request->get('filter') == 'old') {
            $param = 'old';
            $option = 'For old songs';
        }
        else {
            $param = 'new';
            $option = 'For new songs';
        }
    }
    else {
        $param = 'new';
        $option = 'For new songs';
    }
    return view('reccomendation', [
        'title' => 'Recommendations',
        'description' => "What should you play next!",
        'user' => NavController::get_user($request),
        'status' => Page_status::set_status('recommendation'),
        'rate'=> RateOption::getRate($param, $option),
        'songs' => RatingController::recommendation($request)
    ]);
})->middleware('auth');

