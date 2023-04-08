@extends('layouts.app')
@section('title', 'score card')
@section('style')
    <!-- CSS only -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css"
        integrity="sha384-DXfcGqN3qylE6/Ikic1wzHxBvKx6pR/6LOaDyAGoUbHvAJEMqGksQPe6UZwONAYf" crossorigin="anonymous">

    <!-- JavaScript Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-/CGpa8F98W47Qc7lhNn1/VdVryGnwSSZbi2eZnMIBaHyfLg8fKThj9n1z4pQiJKR" crossorigin="anonymous">
    </script>

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
            font-size: 12px;
            font-weight: bold;
            border-radius: 100%;
            box-shadow: rgba(0, 0, 0, 0.35) 0px 1px 2px;
            height: 24px;
            width: 24px;
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

        .squad {
            font-size: 12px;
            font-weight: bold;
            color: red;
            display: flex;
        }

        .box {
            box-shadow: rgba(0, 0, 0, 0.35) 0px 3px 8px;
            border-radius: 10px;
            transition: 0.3s;
        }

        .box:hover {
            box-shadow: rgba(0, 0, 0, 0.7) 0px 3px 8px;
        }

        #firstInnings,
        #secondInnings {
            font-size: 14px;
            font-weight: bold;
        }

        table {
            font-size: 13px;
            font-weight: bold;
        }

         .hr-style {
            background-color: #fff;
            border-top: 3px dashed #8c8b8b;
            margin: 20px 0;
        }
    </style>
@endsection

@section('content')
    @include('layouts.navbar')
    <div class="container py-5">
        <div class="message">
            @if (session('danger'))
                <div class="alert alert-danger fw-bold">{{ session('danger') }}</div>
            @elseif(session('success'))
                <div class="alert alert-success mb-3 text-center text-danger w-100 fw-bold">{{ session('success') }}</div>
            @endif
        </div>

        <form action="{{ route('post.live.match.score', ['id' => $match->id]) }}" method="post">
            @csrf
            <input type="number" hidden name="matchId" value="{{ $match->id }}">
            <div class="row mb-5">
                <div class="col-md-4">
                    <div class="box p-3">
                        <div class="alert alert-success pb-1">
                            <h6 class="fw-bold text-center text-uppercase py-1">1st innings squad</h6>
                            {{-- <h5 class="fw-bold" id="innings-title">1st innings squad</h5>
                            <i class="btn fas fa-sliders-h" id="toggle-btn"></i> --}}
                        </div>
                        <div id="firstInnings">
                            <div class="row">
                                <div class="col-md-6">
                                    @if ($firstBattingSquad && count($firstBattingSquad) > 0)
                                        <h6 class="fw-bold btn btn-primary w-100 btn-sm">
                                            {{ $firstBattingSquad[0]['team_name'] }} Batting
                                            XI
                                        </h6>
                                        @foreach ($firstBattingSquad as $index => $player)
                                            <div class="d-flex">
                                                <small
                                                    class="me-2 fw-bold">{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</small>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="batsman_id[]"
                                                        value="{{ $player['player_id'] }}"
                                                        @if (in_array($player['player_id'], old('batsman_id', []))) checked @endif
                                                        onclick="handleCheckboxClick(this, 'batsman')">
                                                    <input type="checkbox" value="{{ $player['team_id'] }}" hidden
                                                        name="battingTeamId[]">
                                                    <small>{{ $player['player_name'] }}</small>
                                                </div>
                                            </div>
                                        @endforeach
                                    @else
                                        <small class="squad">1st innings - no batsman found!</small>
                                    @endif
                                </div>

                                <div class="col-md-6">
                                    @if ($firstBowlingSquad && count($firstBowlingSquad) > 0)
                                        <h6 class="fw-bold btn btn-primary w-100 btn-sm">
                                            {{ $firstBowlingSquad[0]['team_name'] }} Bowling
                                            XI
                                        </h6>
                                        @foreach ($firstBowlingSquad as $index => $player)
                                            <div class="d-flex">
                                                <small
                                                    class="me-2 fw-bold">{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</small>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="bowler_id[]"
                                                        value="{{ $player['player_id'] }}"
                                                        @if (in_array($player['player_id'], old('bowler_id', []))) checked @endif
                                                        onclick="handleCheckboxClick(this, 'bowler')">
                                                    <input type="checkbox" value="{{ $player['team_id'] }}" hidden
                                                        name="bowlingTeamId[]">
                                                    <small>{{ $player['player_name'] }}</small>
                                                </div>
                                            </div>
                                        @endforeach
                                    @else
                                        <small class="squad">1st innings - no bowler found!</small>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="box p-3">
                        <div class="alert alert-success pb-1">
                            <h6 class="fw-bold text-center text-uppercase py-1">2nd innings squad</h6>
                        </div>
                        <div id="secondInnings">
                            <div class=" row">
                                <div class="col-md-6">
                                    @if ($secondBattingSquad && count($secondBattingSquad) > 0)
                                        <h6 class="fw-bold btn btn-primary w-100 btn-sm">
                                            {{ $secondBattingSquad[0]['team_name'] }} Bating
                                            XI
                                        </h6>
                                        @foreach ($secondBattingSquad as $index => $player)
                                            <div class="d-flex">
                                                <small
                                                    class="me-2 fw-bold">{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</small>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="batsman_id[]"
                                                        value="{{ $player['player_id'] }}"
                                                        @if (in_array($player['player_id'], old('batsman_id', []))) checked @endif
                                                        onclick="handleCheckboxClick(this, 'batsman')">
                                                    <input type="checkbox" value="{{ $player['team_id'] }}" hidden
                                                        name="battingTeamId[]">
                                                    <small>{{ $player['player_name'] }}</small>
                                                </div>
                                            </div>
                                        @endforeach
                                    @else
                                        <small class="squad">2nd innings - no batsman found!</small>
                                    @endif
                                </div>
                                <div class="col-md-6">
                                    @if ($secondBowlingSquad && count($secondBowlingSquad) > 0)
                                        <h6 class="fw-bold btn btn-primary w-100 btn-sm">
                                            {{ $secondBowlingSquad[0]['team_name'] }} Bowling
                                            XI</h6>
                                        @foreach ($secondBowlingSquad as $index => $player)
                                            <div class="d-flex">
                                                <small
                                                    class="me-2 fw-bold">{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</small>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="bowler_id[]"
                                                        value="{{ $player['player_id'] }}"
                                                        @if (in_array($player['player_id'], old('bowler_id', []))) checked @endif
                                                        onclick="handleCheckboxClick(this, 'bowler')">
                                                    <input type="checkbox" value="{{ $player['team_id'] }}" hidden
                                                        name="bowlingTeamId[]">
                                                    <small>{{ $player['player_name'] }}</small>
                                                </div>
                                            </div>
                                        @endforeach
                                    @else
                                        <small class="squad">2nd innings - no bowler found!</small>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="p-3 box" style="height: 425px">
                        <div class="run-card d-flex mb-4 alert alert-success text-uppercase fw-bold">
                            <span style="font-size:14px; height:24px; color: #485132" class=" pt-1 me-2">Run</span>
                            <button class="run" value="0" type="submit" name="run">0</button>
                            <button class="run" value="1" type="submit" name="run">1</button>
                            <button class="run" value="2" type="submit" name="run">2</button>
                            <button class="run" value="3" type="submit" name="run">3</button>
                            <button class="run" value="4" type="submit" name="run">4</button>
                            <button class="run" value="5" type="submit" name="run">5</button>
                            <button class="run" value="6" type="submit" name="run">6</button>
                        </div>
                        <div class="run-card d-flex mb-4 alert alert-success  fw-bold">
                            <span style="font-size:14px; height:24px; color: #485132" class="text-uppercase pt-1 me-2">
                                No Ball</span>
                            <button class="extra">1</button>
                            <button class="extra">2</button>
                            <button class="extra">3</button>
                            <button class="extra">4</button>
                            <button class="extra">5</button>
                            <button class="extra">6</button>
                            <button class="extra">7</button>
                        </div>
                        <div class="run-card d-flex mb-4 alert alert-success  fw-bold">
                            <span style="font-size:14px; height:24px; color: #485132" class="text-uppercase pt-1 me-2">
                                Wide</span>
                            <button class="extra">1</button>
                            <button class="extra">2</button>
                            <button class="extra">3</button>
                            <button class="extra">4</button>
                            <button class="extra">5</button>
                        </div>
                        <div class="run-card d-flex mb-4 alert alert-success  fw-bold">
                            <span style="font-size:14px; height:24px; color: #485132" class="text-uppercase pt-1 me-2">
                                BYE RUN</span>
                            <button class="extra">1</button>
                            <button class="extra">2</button>
                            <button class="extra">3</button>
                            <button class="extra">4</button>
                            <button class="extra">5</button>
                            <button class="extra">6</button>
                            {{-- <button class="extra">7</button> --}}
                        </div>
                        <div class="run-card d-flex mb-4 alert alert-success  fw-bold">
                            <span style="font-size:14px; height:24px; color: #485132" class="text-uppercase pt-1 me-2">
                                Wicket</span>
                            <button class="wkt">+</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="box p-3" style="">
                        <div class="alert alert-success text-center fw-bold text-uppercase">First Innings</div>
                        <div class="box p-3 my-3">
                            <div class=" d-flex justify-content-between align-items-end">
                                <div style="font-size:14px">
                                    <h6 style="font-size:14px" class="fw-bold text-uppercase">
                                        <span
                                            class="text-primary">{{ substr($firstBattingSquad[0]['team_name'], 0, 3) }}</span>
                                        {{ $firstTeamTotalRuns }}/{{ $firstTeamTotalWicket }}
                                    </h6>
                                    <h6 style="font-size:14px" class="fw-bold"><span class="text-primary">Over
                                        </span>{{ $firstTotalOver }}.{{ $firstTeamOverCarry }}
                                        (20)
                                    </h6>

                                </div>
                                <div>
                                    <h6 style="font-size:14px" class="fw-bold">
                                        @php
                                            $firstTeamTotalBalls = $firstTeamIndividualScore->sum('balls');
                                            $firstTeamRunRate = $firstTeamTotalBalls > 0 ? $firstTeamTotalRuns / ($firstTeamTotalBalls / 6) : 0;
                                        @endphp
                                        <span class="text-primary">RR</span> {{ round($firstTeamRunRate, 2) }}
                                    </h6>
                                    <h6 style="font-size:14px" class="fw-bold"><span class="text-primary">Extra
                                        </span>{{ $firstTeamTotalExtraRuns }}

                                    </h6>
                                    {{-- <h6 style="font-size:14px" class="fw-bold"><span class="text-primary">Target</span>
                                        {{ $firstTeamTotalRuns + 1 }}</h6> --}}

                                </div>
                                <div>
                                    <h6 style="font-size:14px" class="fw-bold "><span class="text-primary">Total
                                            4S</span>
                                        {{ $firstTeamTotalFours }}</h6>
                                    <h6 style="font-size:14px" class="fw-bold "><span class="text-primary">Total 6S
                                        </span>{{ $firstTeamTotalSixes }}</h6>

                                </div>
                            </div>
                            <div class="hr-style"></div>
                            <div class="row">
                                <div class="col-md-6">
                                    <h6 style="font-size:14px" class="fw-bold"> Highest Run <span
                                            class="text-primary">{{ $firstHighestRunScorer['name'] }}</span> </h6>
                                    <h6 style="font-size:14px">(R {{ $firstHighestRunScorer['runs'] }}, B
                                        {{ $firstHighestRunScorer['balls'] }},
                                        SR {{ $firstHighestRunScorer['strike_rate'] }} )</h6>
                                    <h6 style="font-size:14px">(4S {{ $firstHighestRunScorer['fours'] }}, 6S
                                        {{ $firstHighestRunScorer['sixes'] }}) </h6>
                                </div>
                                <div class="col-md-6">
                                    <h6 style="font-size:14px" class="fw-bold">Most ECO Baller <span
                                            class="text-primary">{{ $firstMostEconomicalBowler['name'] }}</span></h6>
                                    <h6 style="font-size:14px">(R {{ $firstMostEconomicalBowler['runs'] }}, O
                                        {{ $firstMostEconomicalBowler['totalOvers'] }}, ECO
                                        {{ $firstMostEconomicalBowler['economyRate'] }})</h6>
                                    <h6 style="font-size:14px">(EXT {{ $firstMostEconomicalBowler['totalExtra'] }}, NB
                                        {{ $firstMostEconomicalBowler['totalNoBallRuns'] }}, WD
                                        {{ $firstMostEconomicalBowler['totalWideRuns'] }})</h6>
                                </div>
                            </div>
                        </div>

                        <h6 class="btn btn-primary w-100 fw-bold text-uppercase">Batting summary</h6>
                        <table class="table table-striped">
                            <thead class="bg-success text-white">
                                <tr>
                                    <th>#</th>
                                    <th>BATSMAN</th>
                                    <th> RUN</th>
                                    <th>BALL</th>
                                    <th>4s</th>
                                    <th>6s</th>
                                    <th>SR</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($firstTeamIndividualScore as $batsmanName => $score)
                                    <tr>
                                        <td>{{ $loop->index + 1 }}</td>
                                        <td>{{ $batsmanName }}</td>
                                        <td>{{ $score['runs'] }}</td>
                                        <td>{{ $score['balls'] }}</td>
                                        <td>{{ $score['fours'] }}</td>
                                        <td>{{ $score['sixes'] }}</td>
                                        <td>{{ round(($score['runs'] / $score['balls']) * 100, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                        <h6 class="btn btn-primary w-100 fw-bold text-uppercase mt-3">Bowling summary</h6>
                        <table class="table table-striped">
                            <thead class="bg-success text-white">
                                <tr>
                                    <th>#</th>
                                    <th>BOWLER</th>
                                    <th class="text-center">RUN</th>
                                    <th class="text-center">OVER</th>
                                    <th class="text-center">WICKET</th>
                                    <th class="text-center">4s</th>
                                    <th class="text-center">6s</th>
                                    <th class="text-center">NB</th>
                                    <th class="text-center">WIDE</th>
                                    <th class="text-center">EXTRA</th>
                                    <th class="text-center">ECO</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($firstBowlingIndividualScore as $player => $score)
                                    <tr>
                                        <td>{{ $loop->index + 1 }}</td>
                                        <td>{{ $player }}</td>
                                        <td class="text-center">{{ $score['runs'] }}</td>
                                        <td class="text-center">{{ $score['totalOvers'] }}</td>
                                        <td class="text-center">{{ $score['wickets'] }}</td>
                                        <td class="text-center">{{ $score['fours'] }}</td>
                                        <td class="text-center">{{ $score['sixes'] }}</td>
                                        <td class="text-center">{{ $score['totalNoBallRuns'] }}</td>
                                        <td class="text-center">{{ $score['totalWideRuns'] }}</td>
                                        <td class="text-center">{{ $score['totalExtra'] }}</td>
                                        <td class="text-center">{{ $score['economyRate'] }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                        <div class="score-line my-3">
                            <h6 class="fw-bold text-uppercase text-primary">Score Line</h6>
                            <div class="d-flex flex-wrap">
                                @foreach ($firstTeamScoreLine as $scoreLine)
                                    <span class="text-dark fw-bold my-1 score">{{ $scoreLine->run }}</span>
                                @endforeach
                            </div>
                        </div>


                    </div>
                </div>
                <div class="col-md-6">
                    <div class="box p-3" style="height: 1355px">
                        <div class="alert alert-success text-center fw-bold text-uppercase">Second Innings</div>
                        <div class="d-flex justify-content-between p-3 my-3">
                            <div>
                                <h6 style="font-size:14px" class="fw-bold text-uppercase">
                                    <span
                                        class="text-primary">{{ substr($secondBattingSquad[0]['team_name'], 0, 3) }}</span>
                                    {{ $secondTeamTotalRuns }}/{{ $secondTeamTotalWicket }}
                                </h6>
                                <h6 style="font-size:14px" class="fw-bold"><span class="text-primary">Over
                                    </span>{{ $secondTotalOver }}.{{ $secondTeamOverCarry }}
                                    (20)
                                </h6>
                            </div>
                            <div>
                                <h6 style="font-size:14px" class="fw-bold">
                                    @php
                                        $secondTeamTotalBalls = $secondTeamIndividualScore->sum('balls');
                                        $secondTeamRunRate = $secondTeamTotalBalls > 0 ? $secondTeamTotalRuns / ($secondTeamTotalBalls / 6) : 0;
                                    @endphp
                                    <span class="text-primary">RR</span> {{ round($secondTeamRunRate, 2) }}
                                </h6>
                                <h6 style="font-size:14px" class="fw-bold"><span class="text-primary">Target</span>
                                    {{ $firstTeamTotalRuns + 1 }}</h6>
                            </div>
                        </div>
                        <h6 class="btn btn-primary w-100 fw-bold text-uppercase">Batting summary</h6>
                        <table class="table table-striped">
                            <thead class="bg-success text-white">
                                <tr>
                                    <th>#</th>
                                    <th>BATSMAN</th>
                                    <th> RUN</th>
                                    <th>BALL</th>
                                    <th>4s</th>
                                    <th>6s</th>
                                    <th>SR</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($secondTeamIndividualScore as $batsmanName => $score)
                                    <tr>
                                        <td>{{ $loop->index + 1 }}</td>
                                        <td>{{ $batsmanName }}</td>
                                        <td>{{ $score['runs'] }}</td>
                                        <td>{{ $score['balls'] }}</td>
                                        <td>{{ $score['fours'] }}</td>
                                        <td>{{ $score['sixes'] }}</td>
                                        <td>{{ round(($score['runs'] / $score['balls']) * 100, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <h6 class="btn btn-primary w-100 fw-bold text-uppercase mt-3">Bowling summary</h6>
                        <table class="table table-striped">
                            <thead class="bg-success text-white">
                                <tr>
                                    <th>#</th>
                                    <th>BOWLER</th>
                                    <th class="text-center">RUN</th>
                                    <th class="text-center">OVER</th>
                                    <th class="text-center">WICKET</th>
                                    <th class="text-center">4s</th>
                                    <th class="text-center">6s</th>
                                    <th class="text-center">NB</th>
                                    <th class="text-center">WIDE</th>
                                    <th class="text-center">EXTRA</th>
                                    <th class="text-center">ECO</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($secondBowlingIndividualScore as $player => $score)
                                    <tr>
                                        <td>{{ $loop->index + 1 }}</td>
                                        <td>{{ $player }}</td>
                                        <td class="text-center">{{ $score['runs'] }}</td>
                                        <td class="text-center">{{ $score['totalOvers'] }}</td>
                                        <td class="text-center">{{ $score['wickets'] }}</td>
                                        <td class="text-center">{{ $score['fours'] }}</td>
                                        <td class="text-center">{{ $score['sixes'] }}</td>
                                        <td class="text-center">{{ $score['totalNoBallRuns'] }}</td>
                                        <td class="text-center">{{ $score['totalWideRuns'] }}</td>
                                        <td class="text-center">{{ $score['totalExtra'] }}</td>
                                        <td class="text-center">{{ $score['economyRate'] }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div class="score-line my-3">
                            <h6 class="fw-bold text-uppercase text-primary">Score Line</h6>
                            <div class="d-flex flex-wrap">
                                @foreach ($secondTeamScoreLine as $scoreLine)
                                    <span class="text-dark fw-bold my-1 score">{{ $scoreLine->run }}</span>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
    </form>
    </div>
    @include('layouts.footer')
@endsection

@section('script')
    <script>
        $(document).ready(function() {
            $('#toggle-btn').click(function() {
                $('#firstInnings').toggle();
                $('#secondInnings').toggle(!$('#firstInnings').is(':visible'));
                let inningsTitle = $('#innings-title').text() === "2nd innings squad" ?
                    "1st innings squad" : "2nd innings squad";
                $('#innings-title').text(inningsTitle);
                $(this).text(buttonText);
            });
        });
    </script>

    <script>
        function checkOtherFields(checkbox) {
            const formCheck = checkbox.parentNode;
            const otherCheckboxes = formCheck.querySelectorAll(
                'input[type="checkbox"]:not([name="bastman_id[]"]):not([name="bowler_id[]"])');
            otherCheckboxes.forEach((cb) => {
                if (checkbox.checked) {
                    // cb.disabled = false;
                    cb.checked = true;
                } else {
                    // cb.disabled = true;
                    cb.checked = false;
                }
            });
        }
        // remain check when add new score
        function saveSelection(checkbox, type) {
            var key = type + '_' + checkbox.value;
            if (checkbox.checked) {
                localStorage.setItem(key, 'true');

                let battingTeamId = checkbox.parentNode.querySelector('input[name="battingTeamId[]"]').value;
                localStorage.setItem(key + '_battingTeamId', battingTeamId);

                let bowlingTeamId = checkbox.parentNode.querySelector('input[name="bowlingTeamId[]"]').value;
                localStorage.setItem(key + '_bowlingTeamId', bowlingTeamId);
            } else {
                localStorage.removeItem(key);
                localStorage.removeItem(key + '_battingTeamId');
                localStorage.removeItem(key + '_bowlingTeamId');
            }
        }
        window.addEventListener('load', function() {
            let checkboxes = document.getElementsByName('batsman_id[]');
            for (let i = 0; i < checkboxes.length; i++) {
                let key = 'batsman_' + checkboxes[i].value;
                if (localStorage.getItem(key)) {
                    checkboxes[i].checked = true;
                    let battingTeamId = localStorage.getItem(key + '_battingTeamId');
                    let battingTeamIdCheckbox = checkboxes[i].parentNode.querySelector(
                        'input[name="battingTeamId[]"]');
                    battingTeamIdCheckbox.checked = true;
                    localStorage.setItem('battingTeamId_' + battingTeamId, 'true');
                }
            }
            checkboxes = document.getElementsByName('bowler_id[]');
            for (let i = 0; i < checkboxes.length; i++) {
                let key = 'bowler_' + checkboxes[i].value;
                if (localStorage.getItem(key)) {
                    checkboxes[i].checked = true;
                    let bowlingTeamId = localStorage.getItem(key + '_bowlingTeamId');
                    let bowlingTeamIdCheckbox = checkboxes[i].parentNode.querySelector(
                        'input[name="bowlingTeamId[]"]');
                    bowlingTeamIdCheckbox.checked = true;
                    localStorage.setItem('bowlingTeamId_' + bowlingTeamId, 'true');
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

        function handleCheckboxClick(checkbox, type) {
            checkOtherFields(checkbox);
            saveSelection(checkbox, type);
        }
    </script>
@endsection
