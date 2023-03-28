@extends('navbar', ['title' => $title, 'description' => $description, 'user' => $user, 'status' => $status])

@section('links')
    @parent
    <link rel="stylesheet" type="text/css" href="{{ asset('css/aboutus.css') }}" />
    <script src="//unpkg.com/alpinejs" defer></script>
@endsection



@section('body')
    <main>
        @if (Session::has('message'))
            <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 3000)" x-show="show">
                <p class="alert">{{ Session::get('message') }}</p>
            </div>
        @endif
        <div class="bg-limit"
            style="background: linear-gradient(rgba(0, 0, 0, 0.40), rgba(0, 0, 0, 0.40)), url({{ asset('images/nav_bg/bg_img.jpg') }}), no-repeat;">
            <div class="container">
                <div class="welcome">
                    <h1>About Us</h1>
                    <hr>
                    <h2>A utility website to store and view you Maimai Scores!</h2>
                </div>
                <hr>
                <div class="about_wrapper">
                    <div class="about">
                        <h3>Passionate gamers at heart!</h3>
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
                </div>
                <hr>
                <div class="about_wrapper" id="instructions">
                    <div class="about">
                        <h3>Meet the team</h3>
                        
                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection
