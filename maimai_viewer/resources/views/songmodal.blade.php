<div class="modal fade" id="modal_{{ str_replace(' ', '', $chart->id) }}" tabindex="-1"
    title="{{ $chart->name }}_{{ $chart->diff }}" aria-labelledby="modal_label" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header" style="background-color: {{ $chart->color['bg'] }};">
                <h1 class="modal-title modal_label">{{ $chart->name }}</h1>
                <h2 class="cons_label"
                    style="color: {{ $chart->color['text'] }}; text-shadow: 2px 2px 3px {{ $chart->color['accent'] }};">
                    {{ $chart->level }}<span class="material-symbols-outlined">double_arrow</span>{{ $chart->constant }}
                </h2>
            </div>
            <div class="modal-body">
                <img src="{{ $chart->img }}" class="song_img"
                    style="box-shadow: 0 0 5px 5px {{ $chart->color['base'] }};" alt="Song_Image">
                <div class="user_stats">
                    <h3 class="score_header">CURRENT SCORE:</h3>
                    <div class="dx">
                        @for ($i = 0; $i < $chart->dxscore; $i++)
                            <span class="material-symbols-sharp">star</span>
                        @endfor
                    </div>
                    @if ($chart->scoregrade != '---')
                        <img class="rank_label" src="{{ asset('/images/stats_icons/'.urlencode($chart->scoregrade).'.png') }}" alt="{{ $chart->combo_grade }}">
                    @endif
                    <h5 class="score_label">Score: {{ $chart->score }}%</h5>
                </div>
                <div class="clear_badges">
                    <div class="ap_badges">
                        <h4>COMBO</h4>
                        <div class="empty_badges">
                            @if (!empty($chart->combo_grade))
                                <img class="medal" src="{{ asset('/images/stats_icons/'.urlencode($chart->combo_grade).'.png') }}" alt="{{ $chart->combo_grade }}">
                            @endif
                        </div>
                    </div>
                    <div class="fsd_badges">
                        <h4>SYNC</h4>
                        <div class="empty_badges">
                            @if (!empty($chart->sync_grade))
                                <img class="medal" src="{{ asset('/images/stats_icons/'.urlencode($chart->sync_grade).'.png') }}" alt="{{ $chart->sync_grade }}">
                            @endif
                        </div>
                    </div>
                </div>
                <div class="note_stats">
                    <h3 style="color:{{ $chart->color['text'] }};">Artist: <p style="color:white;">
                            {{ $chart->artist }}</p>
                    </h3>
                    <h3 style="color:{{ $chart->color['text'] }};">Genre: <p style="color:white;">{{ $chart->genre }}
                        </p>
                    </h3>
                    <h3 style="color:{{ $chart->color['text'] }};">BPM: <p style="color:white;">{{ $chart->bpm }}
                        </p>
                    </h3>
                    <h3 style="color:{{ $chart->color['text'] }};">Version: <p style="color:white;">
                            {{ $chart->version }}</p>
                    </h3>
                </div>
                <div class="radar_div">
                    @if (is_null($chart->notecount))
                    <img class="error_radar" src="{{ asset('/images/error/no_chart.jpg') }}" alt="error_no_data">
                    <p>No note data available!</p>
                    @else
                        <canvas class="radar" data-tap="{{ $chart->tap }}" data-slide="{{ $chart->slide }}"
                            data-hold="{{ $chart->hold }}" data-break="{{ $chart->break }}"
                            data-touch="{{ $chart->touch }}" data-ex="{{ $chart->ex }}"></canvas>
                    @endif
                </div>
            </div>
            <div class="modal-footer">
                <button style="background:{{ $chart->color['bg'] }};" type="button" class="btn btn-secondary"
                    data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
