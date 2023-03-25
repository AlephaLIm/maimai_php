@extends('navbar', ['title' => $title, 'description' => $description, 'user' => $user, 'status' => $status])

@section('links')
    @parent
    <link rel="stylesheet" type="text/css" href="{{ asset('css/songfilter.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ asset('css/songbox.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ asset('css/songmodal.css') }}" />
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.3.js" integrity="sha256-nQLuAZGRRcILA+6dMBOvcRh5Pe310sBpanc6+QBmyVM=" crossorigin="anonymous"></script>
    <script defer src="{{ asset('js/song_finder.js') }}"></script>
    <script defer src="{{ asset('js/radar.js') }}"></script>
@endsection

@section('body')
    <main>
        <div class="container">
            <div class="filterwrapper">
                <form class="filter-form" id="song_form" action="/songs/" method="GET">
                    <div class="sbox">
                        <input class="searchinput" type="text" id="sinput" name="search" placeholder="Search for a Song">
                        <button type="button" class="searchsubmit">Search</button>
                    </div>
                    <div class="fbox">
                        <div class="filterlabel">GENRE</div>
                        <div class="filterrow">
                            @foreach ($genres as $genre)
                                <button type="button" class="filters {{ $genre->status }}" name="genre" form="song_form" value="{{ $genre->value }}">{{ $genre->name }}</button>
                            @endforeach
                        </div>
                    </div>
                    <div class="fbox">
                        <div class="filterlabel">VERSION</div>
                        <div class="filterrow">
                            @foreach ($versions as $version)
                                <button type="button" class="filters {{ $version->status }}" name="version" form="song_form" value="{{ $version->value }}">{{ $version->name }}</button>
                            @endforeach
                        </div>
                    </div>
                    <div class="fbox">
                        <div class="filterlabel special">DIFFICULTY</div>
                        <div class="filterrow">
                            @foreach ($difficulties as $diff)
                                <button type="button" class="filters {{ $diff->status }}" name="difficulty" form="songform" value="{{ $diff->value }}">{{ $diff->name }}</button>
                            @endforeach
                        </div>
                    </div> 
                    <div class="fbox">
                        <div class="filterlabel" id="level">LEVELS</div>
                        <div class="filterrow">
                            @foreach ($levels as $level)
                                <button type="button" class="filters {{ $level->status }}" name="level" form="song_form" value="{{ $level->value }}">{{ $level->name }}</button>
                            @endforeach
                        </div>
                    </div>
                    <div class="fbox">
                        <div class="filterlabel special">SORT BY</div>
                        <div class="filterrow">
                            @foreach ($sorts as $sort)
                                <button type="button" class="filters {{ $sort->status }}" name="sort" form="song_form" value="{{ $sort->value }}">{{ $sort->name }}</button>
                            @endforeach
                            <button type="button" class="filters {{ $key }} material-symbols-outlined" id="key{{ $key }}" name="key" form="song_form" value="selected">eject</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        @if ($charts->isEmpty())
        <div class="msg">There are no songs found.</div>
        @else
        <div class="songsbox" id="songbox">
            @foreach ($charts as $chart) 
                @include('songbox', [ 'chart'=>$chart ])
                @include('songmodal', [ 'chart'=>$chart ])
            @endforeach
            @if ($charts->onLastPage())
            <div class="msg">You have reached the end of the results.</div>
            @endif
        </div>
        <div id="links" class="d-flex justify-content-center">
            {{ $charts->appends($_GET)->links() }}
        </div>
        @endif
    </main>
@endsection