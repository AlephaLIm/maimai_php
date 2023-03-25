<div class="modal fade" id="modal_{{ $chart->id }}" tabindex="-1" title="{{ $chart->name }}_{{ $chart->diff }}" aria-labelledby="modal_label" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header" style="background-color: {{ $chart->color['bg'] }};">
                <h1 class="modal-title modal_label" >{{ $chart->name }}</h1>
                <h2 class="cons_label" style="color: {{ $chart->color['text'] }}; text-shadow: 2px 2px 3px {{ $chart->color['accent'] }};">
                    {{ $chart->level }}<span class="material-symbols-outlined">double_arrow</span>{{ $chart->constant }}
                </h2>
            </div>
            <div class="modal-body">
                <img src="{{ $chart->img }}" class="song_img" style="box-shadow: 0 0 5px 5px {{ $chart->color['base'] }};" alt="Song_Image">
                <div class="user_stats">
                    <h3 class="score_header">CURRENT SCORE:</h3>
                    <div class="dx">
                        @for ($i = 0; $i < $chart->dxscore; $i++)
                        <span class="material-symbols-sharp">star</span>
                        @endfor
                    </div>
                    <h4 class="rank_label">{{ $chart->scoregrade }}</h4>
                    <h5 class="score_label">Score: {{ $chart->score }}%</h5>
                </div>
                <div class="clear_badges">
                    <div class="ap_badges">
                        <h4>COMBO</h4>
                        <div class="empty_badges">
                            @if (strcmp($chart->combo_grade,"FC") == 0 || strcmp($chart->combo_grade,"FC+") == 0)
                                <h5 class="medal" style="color: #00fd00; border: 6px solid #00fd00;">{{ $chart->combo_grade }}</h5>
                            @elseif (strcmp($chart->combo_grade,"AP") == 0 || strcmp($chart->combo_grade,"AP+") == 0)
                                <h5 class="medal" style="color: orange; border: 6px solid orange;">{{ $chart->combo_grade }}</h5>
                            @endif
                        </div>
                    </div>
                    <div class="fsd_badges">
                        <h4>SYNC</h4>
                        <div class="empty_badges">
                            @if (strcmp($chart->sync_grade,"FS") == 0 || strcmp($chart->sync_grade,"FS+") == 0)
                                <h5 class="medal" style="color: cyan; border: 6px solid cyan;">{{ $chart->sync_grade }}</h5>
                            @elseif (strcmp($chart->sync_grade,"FSD") == 0 || strcmp($chart->sync_grade,"FSD+") == 0)
                                <h5 class="medal" style="color: orange; border: 6px solid orange;">{{ $chart->sync_grade }}</h5>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="note_stats">
                    <h3 style="color:{{ $chart->color['text'] }};">Artist: <p style="color:white;">{{ $chart->artist }}</p></h3>
                    <h3 style="color:{{ $chart->color['text'] }};">Genre: <p style="color:white;">{{ $chart->genre }}</p></h3>
                    <h3 style="color:{{ $chart->color['text'] }};">BPM: <p style="color:white;">{{ $chart->bpm }}</p></h3>
                    <h3 style="color:{{ $chart->color['text'] }};">Version: <p style="color:white;">{{ $chart->version }}</p></h3>
                </div>
                <div class="radar_div">
                    <canvas class="radar" data-tap="{{ $chart->tap }}" data-slide="{{ $chart->slide }}" data-hold="{{ $chart->hold }}" data-break="{{ $chart->break }}" data-touch="{{ $chart->touch }}" data-ex="{{ $chart->ex }}"></canvas>
                </div>
            </div>
            <div class="modal-footer">
<<<<<<< HEAD
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="background: {{ $chart->color['bg'] }};">Close</button>
=======
                <button style="background:{{ $chart->color['bg'] }};" type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
>>>>>>> b67be14 (recoded and sumamrised the code logic for DatabaseController.php into 1 function, fixing bug caused by nameless song)
            </div>
        </div>
    </div>
</div>