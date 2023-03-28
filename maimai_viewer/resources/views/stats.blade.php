@extends('navbar', ['title' => $title, 'description' => $description, 'user' => $user, 'status' => $status])

@section('links')
    @parent
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"
        integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.14.7/dist/umd/popper.min.js"
        integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous">
    </script>
    <link rel="stylesheet" type="text/css" href="{{ asset('css/stats.css') }}" >
    <script src="//unpkg.com/alpinejs" defer></script>
@endsection

@section('body')
    <main>
        @if (Session::has('message'))
            <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 5000)" x-show="show">
                <p class="alert">{{ Session::get('message') }}</p>
            </div>
        @endif
        <div class="bg-limit">
            <div class="row justify-content-center" style="padding-top: 1.5em;">
                <div class="col-auto">
                    <div class="table-responsive" tabindex="0">
                        <table class="table table-dark">
                            <thead class="thead-light">
                                <tr style="text-align: center">
                                    <th scope="col">LvL</th>
                                    <th colspan="3" scope="colgroup">Score</th>
                                    <th colspan="3" scope="colgroup">Combo</th>
                                    <th scope="col">Average Score</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($levelArray as $level)
                                    <tr style="text-align: center">
                                        <th scope="row">LvL{{ $level['Level'] }}</th>
                                        <td>{{ $level['SSP'] }}<img src="{{ asset ('/images/stats_icons/'.urlencode("SS+").'.png') }}"
                                                alt="SS+"></td>
                                        <td>{{ $level['SSS'] }}<img src="{{ asset('/images/stats_icons/SSS.png') }}"
                                                alt="SSS"></td>
                                        <td>{{ $level['SSSP'] }}<img src="{{ asset('/images/stats_icons/'.urlencode("SSS+").'.png') }}"
                                                alt="SSS+"></td>
                                        <td>{{ $level['FCP'] }}<img src="{{ asset('/images/stats_icons/'.urlencode("FC+").'.png') }}"
                                                alt="FC+"></td>
                                        <td>{{ $level['AP'] }}<img src="{{ asset('/images/stats_icons/AP.png') }}"
                                                alt="AP"></td>
                                        <td>{{ $level['APP'] }}<img src="{{ asset('/images/stats_icons/'.urlencode("AP+").'.png') }}"
                                                alt="AP+"></td>
                                        <td>{{ $level['avg'] }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection
