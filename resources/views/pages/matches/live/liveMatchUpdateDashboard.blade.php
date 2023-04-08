{{-- @extends('layouts.app')
@section('title', 'Dashboard')
@section('style')
    <style>
        .score-card {
            padding: 20px;
            box-shadow: rgba(0, 0, 0, 0.35) 0px 2px 5px;
            width: 250px;
        }

        .extra,
        .score,
        .wkt,
        .run {
            font-size: 14px;
            font-weight: bold;
            border-radius: 100%;
            box-shadow: rgba(0, 0, 0, 0.35) 0px 1px 2px;
            height: 30px;
            width: 30px;
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 0 5px;
            transition: 0.3s;
        }

        .extra:hover,
        .run:hover,
        .score:hover,
        .wkt:hover {
            cursor: pointer;
            box-shadow: rgba(0, 0, 0, 0.35) 0px 5px 8px;
        }

        span {
            font-weight: bold;
            color: red;
        }
    </style>
@endsection

@section('content')
    @include('layouts.navbar')
    <div class="container py-5">
        <form action="{{ route('match.update.score', ['id' => $match->id]) }}" method="post">
            @csrf

            <div class="row">
                <div class="col-md-8">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="score-card">
                                <div class="d-flex justify-content-between">
                                    <h6 class="fw-bold">{{ $match->teamA->name }} vs {{ $match->teamB->name }}</h6>
                                    <a href="{{ route('get.live.match.squad', ['id' => $match->id]) }}"
                                        class="fw-bold text-danger">
                                        <i class="fas fa-angle-double-left" style="font-size: 16px"></i>
                                    </a>
                                </div>
                                <h6 class="fw-bold">BAN {{ $totalRuns }}/{{ $totalWicket }} </h6>
                                <h6 class="text-uppercase fw-bold">over {{ $totalOver }}.{{ $overCarry }} (20)</h6>
                            </div>
                            <table class="my-5">
                                <thead>
                                    <tr>
                                        <th class="pe-4">Batsman</th>
                                        <th class="pe-4"> Run</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($individualRuns as $batsmanName => $runSum)
                                        <tr>
                                            <td class="pe-4">{{ $batsmanName }}</td>
                                            <td class="pe-4">{{ $runSum }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>

                        </div>
                        <div class="col-md-6">
                            <h6 class="my-3">Run</h6>
                            <div class="run-card d-flex">
                                <button class="run" value="1" type="submit" name="run">1</button>
                                <button class="run" value="2" type="submit" name="run">2</button>
                                <button class="run" value="3" type="submit" name="run">3</button>
                                <button class="run" value="4" type="submit" name="run">4</button>
                                <button class="run" value="5" type="submit" name="run">5</button>
                                <button class="run" value="6" type="submit" name="run">6</button>
                            </div>

                            <h6 class="my-3">Extra</h6>
                            <div class="extra-card d-flex">
                                <div class="extra">wd</div>
                                <div class="extra">nb</div>
                                <div class="extra">lb</div>
                                <div class="extra">b</div>
                            </div>

                            <h6 class="my-3">Wicket</h6>
                            <div class="wkt-card d-flex">
                                <div class="wkt">+</div>
                            </div>

                            <h6>End Innings</h6>
                            <button id="update-innings-btn" data-innings-id="{{ $match->id }}">End Innings</button>

                        </div>

                    </div>

                    <input type="number" hidden value="{{ $match->id }}" name="matchId">
                    <input type="number" hidden value="{{ $battingTeam->id }}" name="battingTeam">
                    <input type="number" hidden value="{{ $bowlingTeam->id }}" name="bowlingTeam">


                </div>
                <div class="col-md-4">
                    <div class="row py-4">
                        <div class="col-md-6">
                            <div>
                                <h6>Batting {{ $battingTeam->name }}</h6>
                                    @foreach ($battingSquad as $player)
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="batsman_id[]"
                                                value="{{ $player['player_id'] }}"
                                                @if (in_array($player['player_id'], old('batsman_id', []))) checked @endif
                                                onclick="saveSelection(this, 'batsman')">
                                            {{ $player['player_name'] }}
                                        </div>
                                    @endforeach

                            </div>
                        </div>
                        <div class="col-md-6">
                            <div>
                                    <h6>Bowling {{ $bowlingTeam->name }}</h6>
                                    @foreach ($bowlingSquad as $player)
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="bowler_id[]"
                                                value="{{ $player['player_id'] }}"
                                                @if (in_array($player['player_id'], old('bowler_id', []))) checked @endif
                                                onclick="saveSelection(this, 'bowler')">
                                            {{ $player['player_name'] }}
                                        </div>
                                    @endforeach
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <h6 class="my-3">Score Line</h6>
            <div class="score-line d-flex row">
                @foreach ($newScore as $score)
                    <div class="score col-md-12">{{ $score->run }}</div>
                @endforeach
            </div>
        </form>
    </div>
    @include('layouts.footer')
@endsection

@section('script')

 
    <script>
        // remain check when add new score
        function saveSelection(checkbox, type) {
            var key = type + '_' + checkbox.value;
            if (checkbox.checked) {
                localStorage.setItem(key, 'true');
            } else {
                localStorage.removeItem(key);
            }
        }
        window.addEventListener('load', function() {
            var checkboxes = document.getElementsByName('batsman_id[]');
            for (var i = 0; i < checkboxes.length; i++) {
                var key = 'batsman_' + checkboxes[i].value;
                if (localStorage.getItem(key)) {
                    checkboxes[i].checked = true;
                }
            }
            checkboxes = document.getElementsByName('bowler_id[]');
            for (var i = 0; i < checkboxes.length; i++) {
                var key = 'bowler_' + checkboxes[i].value;
                if (localStorage.getItem(key)) {
                    checkboxes[i].checked = true;
                }
            }
        });
          // Check if at least one bowler and one batsman are selected
        function validateSelection() {
            if (!$('input[name="bowler_id[]"]:checked').length && !$('input[name="batsman_id[]"]:checked').length) {
                swal({
                    title: "warning",
                    text: "Please select one bowler and one batsman",
                    icon: "warning",
                    button: "Ok",
                });
                return false;
            } else if (!$('input[name="bowler_id[]"]:checked').length) {
                swal({
                    title: "warning",
                    text: "Please select a bowler",
                    icon: "warning",
                    button: "Ok",
                });
                return false;
            } else if (!$('input[name="batsman_id[]"]:checked').length) {
                swal({
                    title: "warning",
                    text: "Please select a batsman",
                    icon: "warning",
                    button: "Ok",
                });
                return false;
            }
            return true;
        }

        $('form').on('submit', function(e) {
            if (!validateSelection()) {
                e.preventDefault(); 
            }
        });
    </script>
   
    

@endsection --}}
