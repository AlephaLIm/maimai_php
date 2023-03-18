@extends('metadata', ['title' => $title, 'description' => $description])

@section('links')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" 
    rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" 
    crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" 
    integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" 
    crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@48,400,1,0" />
    <link rel="stylesheet" type="text/css" href="{{ asset('css/nav.css') }}" />
@endsection

@section('header')
    <div class="container-fluid" id="nav-wrapper">
        <nav class="navbar navbar-expand-lg bg-dark navbar-dark" aria-label="nav_bg" id="logo-wrapper">
        <div class="header-logo" style="background: linear-gradient(rgba(0, 0, 0, 0.40), rgba(0, 0, 0, 0.40)), url({{ asset('images/nav_bg/bg_img.jpg') }}), no-repeat;">
            <a class="navbar-brand" href="#">
                <img src="{{ $logo_url }} " alt="Logo">
                <h1>Maimai<br>Scoreviewer</h1>
            </a>
        </div>
    </nav>
    <nav class="navbar navbar-expand-lg bg-dark navbar-dark" aria-label="nav_panel" id="nav-row">
        <div class="navbar-brand" id="profile-box">
            <img id="profile-img" src="{{ asset($user->profile_img) }}" alt="Profile Photo">
            <div class="profile-user">
                <h4>{{ $user->name }}</h4>
                <div class="ratings">
                    <p>Rating: {{ $user->rating }}</p>
                </div>
                <div class="title-box">
                    <h5>{{ $user->title }}</h5>
                </div>
            </div>
        </div>
         <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#Navlinks"
            aria-controls="Navlinks" aria-expanded="false" aria-label="Go to...">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="Navlinks">
            <div class="navbar-nav">
                <a class="nav-link ms-auto {{ $status['achievements'] }}" href="./achievements"><p>Achievements</p></a>
                <a class="nav-link ms-auto {{ $status['songs'] }}" href="./songs"><p>Songs</p></a>
                <a class="nav-link ms-auto {{ $status['recommendations'] }}" href="./recommendations"><p>Recommendations</p></a>
                <a class="nav-link ms-auto" href="./login"><span class="material-symbols-outlined">logout</span></a>
            </div>
        </div>
    </nav>
@endsection