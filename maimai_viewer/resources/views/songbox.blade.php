<a href="" class="_box" data-bs-toggle="modal" data-bs-target="#modal_{{ str_replace(' ', '', $chart->id) }}">
    <div class="song_container" style="background-color: {{ $chart->color['base'] }};">
        <div class="song_bg" style="background-color: {{ $chart->color['bg'] }};">
<<<<<<< HEAD
            <img class="chart_img" src="{{ url($chart->img) }}" style="box-shadow: 0 0 5px 5px {{ $chart->color['base'] }};" alt="Song_Image">
=======
            <img src="{{ url($chart->img) }}" style="box-shadow: 0 0 5px 5px {{ $chart->color['base'] }};"
                alt="Song_Image">
>>>>>>> main
            <h4 class="s_name">{{ $chart->name }}</h4>
            <h4 class="chart_const"
                style="border: 2px solid {{ $chart->color['submain'] }}; color: {{ $chart->color['text'] }};">
                {{ $chart->level }}</h4>
            <h5 class="chart_diff" style="background-color: {{ $chart->color['base'] }};">{{ $chart->diff }}</h5>
            <h5 class="c_type" style="background: {{ $chart->type_col }};">{{ $chart->type }}</h5>
            @if (strcmp($chart->scoregrade, '---') == 0)
                <h4 class="badge">{{ $chart->scoregrade }}</h4>
            @else 
                <img class="badge" src="{{ asset('/images/stats_icons/'.urlencode($chart->scoregrade).'.png') }}" alt="{{ $chart->combo_grade }}">
            @endif
            <h4 class="score">Score: {{ $chart->score }}%</h4>
            <h3 class="rating_val"
                style="box-shadow: 0 0 2px 2px {{ $chart->color['base'] }} inset; background-color: {{ $chart->color['submain'] }}; color: {{ $chart->color['text'] }};">
                {{ $chart->rating }}</h3>
        </div>
    </div>
</a>
