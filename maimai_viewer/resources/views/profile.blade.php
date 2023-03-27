@extends('navbar', ['title' => $title, 'description' => $description, 'user' => $user, 'status' => $status])

@section('links')
    @parent
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"
        integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.14.7/dist/umd/popper.min.js"
        integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous">
    </script>
    <link rel="stylesheet" type="text/css" href="{{ asset('css/profile.css') }}" />
    <script src="//unpkg.com/alpinejs" defer></script>
@endsection

@section('body')
    <main>
        @if (Session::has('message'))
            <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 5000)" x-show="show">
                <p class="alert">{{ Session::get('message') }}</p>
            </div>
        @endif
        {{-- <h1>{{ $profile->username }}</h1>
        <p>{{ $profile->email }}</p>
        <p>{{ $profile->friendcode }}</p>
        <p>{{ $profile->rating }}</p> --}}
        <div class="bg-limit"
            style="background: linear-gradient(rgba(0, 0, 0, 0.40), rgba(0, 0, 0, 0.40)), url({{ asset('images/nav_bg/bg_img.jpg') }}), no-repeat;">
            <div class="row justify-content-center"  style="padding-top: 1.5em;">
                <div class="col-auto">
                    <table class="table table-bordered table-dark">
                        <thead class="thead-light">
                            <tr>
                                <th scope="col">Level</th>
                                <th scope="col">SS+</th>
                                <th scope="col">SSS</th>
                                <th scope="col">SSS+</th>
                                <th scope="col">AP</th>
                                <th scope="col">AP+</th>
                                <th scope="col">Average Score</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($levelArray as $level)
                                <tr>
                                    <th scope="row">{{ $level['Level'] }}</th>
                                    <td>{{ $level['SS+'] }}</td>
                                    <td>{{ $level['SSS'] }}</td>
                                    <td>{{ $level['SSS+'] }}</td>
                                    <td>{{ $level['AP'] }}</td>
                                    <td>{{ $level['AP+'] }}</td>
                                    <td>{{ $level['avg'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>
@endsection
