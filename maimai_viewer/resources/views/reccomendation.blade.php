@extends('navbar', ['title' => $title, 'description' => $description, 'logo_url' => $logo_url, 'user' => $user, 'status' => $status])

@section('links')
    @parent
    <link rel="stylesheet" type="text/css" href="{{ asset('css/songfilter.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ asset('css/songbox.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ asset('css/songmodal.css') }}" />
    <script src="https://code.jquery.com/jquery-3.6.3.js" integrity="sha256-nQLuAZGRRcILA+6dMBOvcRh5Pe310sBpanc6+QBmyVM="
        crossorigin="anonymous"></script>
    <script defer src="{{ asset('js/festival.js') }}"></script>
@endsection


@section('body')
    <main>
        <form class="festival-form" id="festival_form" action="/rating" method="GET">
            <button class="festivalsubmit" name="festival" value="festival">Festival</button>
            <button class="returnsubmit">Non-Festival</button>
        </form>
        <div class="songsbox" id="songbox">
            <ul>
                @foreach ($songs as $song)
                    @include('songbox', ['chart' => $song])
                    @include('ratingmodal', ['chart' => $song])
                @endforeach
            </ul>
        </div>
    </main>
@endsection
