@extends('navbar', ['title' => $title, 'description' => $description, 'user' => $user, 'status' => $status])

@section('links')
    @parent
    <link rel="stylesheet" type="text/css" href="{{ asset('css/home.css') }}" >
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
                        <a href="/ratings">
                            <div class="img_wrapper">
                                <img src="{{ asset('/images/home_icons/achieve.png') }}" alt="Achievements">
                                <div class="desc_text"><p>Achievements</p>View your best songs!</div>
                            </div>
                        </a>
                    </div>
                    <div class="profile">
                        <a href="/stats">
                            <div class="img_wrapper">
                                <img src="{{ asset('/images/home_icons/profile.jpg') }}" alt="Profile">
                                <div class="desc_text"><p>Profile</p>View your stats and details!</div>
                            </div>
                        </a>
                    </div>
                    <div class="songs">
                        <a href="/songs">
                            <div class="img_wrapper">
                                <img src="{{ asset('/images/home_icons/songs.png') }}" alt="Songs Finder">
                                <div class="desc_text"><p>Song Finder</p>Searching for a song?</div>
                            </div>
                        </a>
                    </div>
                    <div class="recommendation">
                        <a href="/recommendation">
                            <div class="img_wrapper">
                                <img src="{{ asset('/images/home_icons/recommendations.png') }}" alt="Recommendations">
                                <div class="desc_text"><p>Recommendations</p>What song should you play next?</div>
                            </div>
                        </a>
                    </div>
                </div>
                <hr>
                <div class="about_wrapper">
                    <div class="about">
                        <h3>Welcome to Maiviewer!</h3>
                        <p>
                            Log in with your account to view your scores and stats.
                            <br>
                            <br>
                            If you're new, register an account with us and use the javascript bookmark below to scrape your maimai data.
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
