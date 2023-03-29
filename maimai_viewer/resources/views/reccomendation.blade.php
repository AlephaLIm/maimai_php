@extends('navbar', ['title' => $title, 'description' => $description, 'user' => $user, 'status' => $status])

@section('links')
    @parent
    <link rel="stylesheet" type="text/css" href="{{ asset('css/songfilter.css') }}" >
    <link rel="stylesheet" type="text/css" href="{{ asset('css/songbox.css') }}" >
    <link rel="stylesheet" type="text/css" href="{{ asset('css/recmodal.css') }}" >
    <script src="https://code.jquery.com/jquery-3.6.3.js" integrity="sha256-nQLuAZGRRcILA+6dMBOvcRh5Pe310sBpanc6+QBmyVM="
        crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script defer src="{{ asset('js/radar.js') }}"></script>
    <link rel="stylesheet" type="text/css" href="{{ asset('css/rating.css') }}" >
@endsection


@section('body')
    <main>
    <div class="jumbotron text-center" id="headtitle">
        <h2 class="display-4">RECOMMENDATIONS:</h1>
        <h3>{{ $user->name }}</h2>
    </div>
    <div class="container">
        <div class="dropdown">
            <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownList" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                {{ $rate->option }}
            </button>
            <div class="dropdown-menu dropdown-menu-dark" aria-labelledby="dropdownList">
                <span class="dropdown-item-text">----------Select filter----------</span>
                <a class="dropdown-item {{ $rate->new }}" href="/recommendation?filter=new">For new songs</a>
                <a class="dropdown-item {{ $rate->old }}" href="/recommendation?filter=old">For old songs</a>
            </div>
        </div>
    </div>
    <hr class="achieve_hr">
    <div class="songs_box" id="songbox">
        @foreach ($songs as $song)
            @include('songbox', ['chart' => $song])
            @include('ratingmodal', ['chart' => $song])
        @endforeach
    </div>
    </main>
@endsection
