@extends('navbar', ['title' => $title, 'description' => $description, 'logo_url' => $logo_url, 'user' => $user, 'status' => $status])

@section('links')
    @parent
    <link rel="stylesheet" type="text/css" href="{{ asset('css/home.css') }}" />
    <script src="//unpkg.com/alpinejs" defer></script>
@endsection


@if (Session::has('message'))
    <div x-data="{show: true}" x-init="setTimeout(() => show = false, 5000)" x-show="show">
        <p class="alert">{{Session::get('message')}}</p>
    </div>
@endif

@section('body')
    <main>
        <div class="container-fluid bg-limit">
            <div class="row justify-content-around">
                <div class="col-sm-auto">
                    <p class="homeTitle">
                        HOME PAGE
                    </p>  
                    <p class="homeText">Welcome to MaiMai Scoreviewer!</p>
                    <p class="homeText">Maimai is an arcade rhythm game series developed <br>
                        and distributed by Sega, in which the player interacts with objects<br>
                        on a touchscreen and executes dance-like movements. The game supports<br>
                        both single-player and multiplayer gameplay with up to 4 players.</p>
                    <p class="homeText">MaiMai Scoreviewer enchances your gameplay experience!</p>
                    <p class="homeText">Select any of the icons below to begin!</p>
                </div>
                <div class="col-sm-auto">
                    <img class="homePicture" src="{{ asset('/images/home_icons/home_picture.png') }} " alt="home_picture">
                </div>                   
            </div>
            <div class="row justify-content-center">
                <div class="col-sm-4 icons">
                    <a href="/achievements">
                        <img class="achievement_icon" src="{{ asset('/images/home_icons/achievement.jpg') }} " alt="achievement_icon"> 
                    </a>
                    <p class="iconText">View Your Achievements!</p>
                </div>
                <div class="col-sm-4 icons">
                    <a href="/recommendations">
                        <img class="recommendation_icon" src="{{ asset('/images/home_icons/recommendation.jpg') }} " alt="recommendation_icon">
                    </a>
                    <p class="iconText">Be recommended songs to play!</p>
                </div>
                <div class="col-sm-4 icons">
                    <a href="/songs">
                        <img class="songs_icon" src="{{ asset('/images/home_icons/songs.png') }} " alt="songs_icon">
                    </a>
                    <p class="iconText">Find Songs!</p>
                </div>
            </div>
        </div>
    </main>
@endsection

@extends('footer')