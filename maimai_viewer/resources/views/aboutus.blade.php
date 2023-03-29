@extends('navbar', ['title' => $title, 'description' => $description, 'user' => $user, 'status' => $status])

@section('links')
    @parent
    <link rel="stylesheet" type="text/css" href="{{ asset('css/aboutus.css') }}" >
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
                    <hr class="white">
        
                </div>
                
                <div class="about_wrapper">
                    <div class="about">
                        <h2 class="headur">Built for Maimai Players, By Maimai Players</h2>
                        <br><br>
                        <p>
                            Maiviewer was built with one purpose in mind, to give the maimai community better access and insight into their score data. <br>Maiviewer provides the community with utilities that leverages score data to provide better insights about the player,<br> as well as providing better access to song and chart information.<br>
                            Features: 
                        </p>
                        <br>
                        <div class="about_wrapper">
                          <div class="about"><h3 class="shiro">Features</h3> </div>
                          <br>Song Search
                          <br>Score Viewer
                          <br>Rating Breakdown
                          <br>Chart Recommendations

                    </div>
                </div>
                
                <hr class="white">
                <div class="about_wrapper">
                    <div class="about"><h3 class="shiro">Meet the team</h3> </div>
                    
                    <div class="row">
                      <div class="col-lg-6 col-md-6 text-center">
                        <div class="shiro">
                          <img src="https://avatars.githubusercontent.com/u/59667279?v=4" class="pfp" alt="">
                          <h3><a href="https://github.com/OhsterTohster">@@OhsterTohster</a></h3>
                          <p>Resident Rhythm Game Enjoyer</p>
                        </div>
                      </div>
                      <div class="col-lg-6 col-md-6 text-center">
                        <div class="shiro">
                          <img src="https://avatars.githubusercontent.com/u/51186021?v=4" class="pfp" alt="">
                          <h3 class="shiro"><a href="https://github.com/AlephaLIm">@@AlephaLIm</a></h3>
                          <p>Resident Gacha Game Enjoyer</p>
                        </div>
                      </div>
                      <div class="col-lg-4 col-md-6 text-center">
                        <div class="shiro">
                          <img src="https://avatars.githubusercontent.com/u/126781541?v=4" class="pfp" alt="">
                          <h3 class="shiro"><a href="https://github.com/brdley">@@brdley</a></h3>
                          <p>Full Stack Engineer</p>
                        </div>
                      </div>
                      <div class="col-lg-4 col-md-6 text-center">
                        <div class="shiro">
                          <img src="https://avatars.githubusercontent.com/u/123640583?v=4" class="pfp" alt="">
                          <h3 class="shiro"><a href="https://github.com/SimYanZhe">@@SimYanZhe</a></h3>
                          <p>Full Stack Engineer</p>
                        </div>
                      </div>
                      <div class="col-lg-4 col-md-6 text-center">
                        <div class="shiro">
                          <img src="https://avatars.githubusercontent.com/u/122660764?v=4" class="pfp" alt="">
                          <h3 class="shiro"><a href="https://github.com/Nydrel2">@@Nydrel2</a></h3>
                          <p>Full Stack Engineer</p>
                        </div>
                      </div>
                      
                    </div>
                  </div>
                  <br>
                  <br>
                  <br>
                  
            </div>
        </div>
    </main>
@endsection
