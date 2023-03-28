@extends('navbar', ['title' => $title, 'description' => $description, 'user' => $user, 'status' => $status])

@section('links')
    @parent
    <link rel="stylesheet" type="text/css" href="{{ asset('css/home.css') }}" />
    <script src="//unpkg.com/alpinejs" defer></script>
@endsection



@section('body')
<main>
        @if (Session::has('message'))
            <div x-data="{show: true}" x-init="setTimeout(() => show = false, 3000)" x-show="show">
                <p class="alert">{{Session::get('message')}}</p>
            </div>
        @endif
        <div class="bg-limit" style="background: linear-gradient(rgba(0, 0, 0, 0.40), rgba(0, 0, 0, 0.40)), url({{ asset('images/nav_bg/bg_img.jpg') }}), no-repeat;">
            <div class="container">
                <div class="welcome">
                    <h1>Welcome, {{ $user->name }}</h1>
                    <h2>What would you like to do?</h2>
                    <hr>
                </div>
                <div class="routes">
                    <div class="achieve">
                        <a href="/achievements">
                            <div class="img_wrapper">
                                <img src="{{ asset('/images/home_icons/songs.jpg') }}" alt="Achievements">
                                <div class="desc_text"><p>Achievements</p>View your best songs!</div>
                            </div>
                        </a>
                    </div>
                    <div class="profile">
                        @if (auth()->check())
                        <a href="/stats/{{ $user->friendcode }}">
                        @else
                        <a href="/login">
                        @endif
                            <div class="img_wrapper">
                                <img src="{{ asset('/images/home_icons/songs.jpg') }}" alt="Profile">
                                <div class="desc_text"><p>Profile</p>View your stats and details!</div>
                            </div>
                        </a>
                    </div>
                    <div class="songs">
                        <a href="/songs">
                            <div class="img_wrapper">
                                <img src="{{ asset('/images/home_icons/songs.jpg') }}" alt="Songs Finder">
                                <div class="desc_text"><p>Song Finder</p>Searching for a song?</div>
                            </div>
                        </a>
                    </div>
                    <div class="recommendation">
                        <a href="/recommmendation">
                            <div class="img_wrapper">
                                <img src="{{ asset('/images/home_icons/songs.jpg') }}" alt="Recommendations">
                                <div class="desc_text"><p>Recommendations</p>What song should you play next?</div>
                            </div>
                        </a>
                    </div>
                </div>
                <hr>
                <div class="about_wrapper">
                    <div class="about">
                        <h3>Welcome to Maimai Scoreviewer!</h3>
                        <p>
                            Maimai is an arcade rhythm game series developed <br>
                            and distributed by Sega, in which the player interacts with objects<br>
                            on a touchscreen and executes dance-like movements.<br> 
                            The game supports both single-player and multiplayer gameplay with up to 4 players.
                        </p>
                        <p>
                            This app is a community project <br>
                            that aims to enchance your experience by <br>
                            extending more features to the official Maimai website.
                        </p>
                    </div>
                    <div class="about_icon">
                        <img src="{{ asset('/images/home_icons/about_us.png') }}" alt="Maimai Scoreviewer chan">
                    </div>
                </div>
                <hr>
                <div class="about_wrapper" id="instructions">
                    <div class="about_icon">
                        <img src="{{ asset('/images/home_icons/instruct.png') }}" alt="Maimai Scoreviewer chan">
                    </div>
                    <div class="instructions">
                        <h3>How to setup your account!</h3>
                        <p>
                            1. Create a new bookmark in your browser <br>
                            2. Copy this code and insert it into the URL segment of the bookmark <br>
                            <code>javascript:(d=>{let s=d.createElement("script"); s.type="text/javascript"; <br> s.src="https://maiviewer.ordinarymagician.com/scorescraper.js";<br> d.head.appendChild(s);})(document);</code> <br>
                            3. Log into Mainet with your account <br>
                            4. While logged in, click on your newly created bookmark
                        </p>
                    </div>
                </div>
            </div>
        </div> 
</main>
@endsection
