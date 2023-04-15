@extends('layouts.app')
@section('title', 'score card dashboard')
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
            border: 0;
        }

        .score-box {
            border-radius: 100%;
            box-shadow: rgba(0, 0, 0, 0.35) 0px 1px 2px;
            height: 24px;
            width: 24px;
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 0 5px;
            transition: 0.3s;
            border: 0;
        }

        .extra:hover,
        .run:hover,
        .score:hover,
        .wkt:hover,
        .score-box:hover {
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

        .run-box {
            box-shadow: rgba(0, 0, 0, 0.3) 0px 1px 3px;
            border-radius: 10px;
            transition: 0.3s;
        }

        .run-box:hover {
            box-shadow: rgba(0, 0, 0, 0.7) 0px 1px 3px;
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
            font-size: 16px;
            font-weight: bold;
        }

        table {
            font-size: 14px;
            font-weight: bold;
        }

        .hr-style {
            background-color: #fff;
            border-top: 3px dashed #8c8b8b;
            margin: 20px 0;
        }

        .batsman-checkbox[disabled] {
            display: none;
        }
    </style>
@endsection

@section('content')
    @include('layouts.navbar')
    <div class="container py-5">
        <div class="message">
            @if (session('success'))
                <div class="alert alert-success w-100 fw-bold text-center">{{ session('success') }}</div>
            @elseif(session('danger'))
                <div class="alert alert-danger w-100 fw-bold text-center">{{ session('danger') }}</div>
            @endif
        </div>
        <form action="{{ route('post.live.match.score', ['id' => $match->id]) }}" method="post">
            @csrf
            <input type="number" hidden name="matchId" value="{{ $match->id }}">
            <div class="row mb-5">
                @if ($inningsStatus['inningsOne'] == 1)
                    @include('components.squad.firstInnings')
                    @elseif($inningsStatus['inningsTwo'] == 1)
                   @include('components.squad.secondinnings')
                @else
                    <div class="col-md-12">
                        <div class="btn btn-danger w-100 fw-bold py-2">match end!</div>
                        @if ($innings1BattingScore['totalRuns'] > $innings2BattingScore['totalRuns'])
                            <div class="alert alert-success mt-3 text-center fw-bold">
                                {{ $firstBattingSquad[0]['team_name'] }} won by
                                {{ $innings1BattingScore['totalRuns'] - $innings2BattingScore['totalRuns'] }} runs
                            </div>
                        @elseif($firstTeamTotalRuns < $secondTeamTotalRuns)
                            <div class="alert alert-success mt-3 text-center fw-bold">
                                {{ $secondBattingSquad[0]['team_name'] }} won by {{ 10 - $innings2BattingScore['totalWickets'] }} wickets
                            </div>
                        @else
                            <div class="alert alert-success mt-3 text-center fw-bold">
                                match draw!
                            </div>
                        @endif
                    </div>
                @endif
                @if ($inningsStatus['inningsOne'] == 1 || $inningsStatus['inningsTwo'] == 1)
                    @include('components.score.scoreBoard')
                @endif
            </div>
            <div class="row">
                @include('components.summary.firstInnings')
                @include('components.summary.secondInnings')
                {{-- <div class="col-md-6">
                    <div class="box p-3" style="min-height: 1500px">
                        <div class="alert alert-success text-center fw-bold text-uppercase">First Innings</div>
                        <div class="box p-3 my-3">
                            <div class=" d-flex justify-content-between align-items-end">
                                <div style="font-size:14px">
                                    <h6 style="font-size:14px" class="fw-bold text-uppercase">
                                        <span
                                            class="text-primary">{{ substr($firstBattingTeamName, 0, 3) }}</span>
                                        {{ $innings1BattingScore['totalRuns'] }}/{{ $innings1BattingScore['totalWickets'] }}
                                    </h6>
                                    <h6 style="font-size:14px" class="fw-bold"><span class="text-primary">Over
                                        </span>{{ $innings1BattingScore['totalOvers'] }}
                                        ({{ $match->over }})
                                    </h6>

                                </div>
                                <div>
                                    <h6 style="font-size:14px" class="fw-bold">
                                        <span class="text-primary">RR</span> {{ $innings1BattingScore['runRate'] }}
                                    </h6>
                                    <h6 style="font-size:14px" class="fw-bold"><span class="text-primary">Extra
                                        </span>{{ $innings1BattingScore['totalExtraRuns'] }}
                                    </h6>
                                </div>
                                <div>
                                    <h6 style="font-size:14px" class="fw-bold "><span class="text-primary">Total
                                            4S</span>
                                        {{ $innings1BattingScore['totalFours'] }}</h6>
                                    <h6 style="font-size:14px" class="fw-bold "><span class="text-primary">Total 6S
                                        </span>{{ $innings1BattingScore['totalSixes'] }}</h6>

                                </div>
                            </div>
                            <div class="hr-style"></div>
                            <div class="row">
                                <div class="col-md-6">
                                    <h6 style="font-size:14px" class="fw-bold"> Highest Run <span
                                            class="text-primary">{{ $firstHighestRunScorer['name'] }}</span> </h6>
                                    <h6 style="font-size:14px" class="fw-bold">(R {{ $firstHighestRunScorer['runs'] }}, B
                                        {{ $firstHighestRunScorer['balls'] }},
                                        SR {{ $firstHighestRunScorer['strike_rate'] }} )</h6>
                                    <h6 style="font-size:14px" class="fw-bold">(4S {{ $firstHighestRunScorer['fours'] }},
                                        6S
                                        {{ $firstHighestRunScorer['sixes'] }}) </h6>
                                </div>
                                <div class="col-md-6">

                                    <h6 style="font-size:14px" class="fw-bold">Most ECO Bowler
                                        @if ($firstMostEconomicalBowler['name'] != 0)
                                            <span class="text-primary">{{ $firstMostEconomicalBowler['name'] }}</span>
                                        @endif
                                    </h6>
                                    <h6 style="font-size:14px" class="fw-bold">(R
                                        {{ $firstMostEconomicalBowler['runs'] }}, O
                                        {{ $firstMostEconomicalBowler['totalOvers'] }}, ECO
                                        {{ $firstMostEconomicalBowler['economyRate'] }})</h6>
                                    <h6 style="font-size:14px" class="fw-bold">(EXT
                                        {{ $firstMostEconomicalBowler['totalExtra'] }}, NB
                                        {{ $firstMostEconomicalBowler['totalNoCount'] }}, WD
                                        {{ $firstMostEconomicalBowler['totalWideCount'] }})</h6>
                                </div>
                            </div>
                        </div>

                        <h6 class="btn btn-primary w-100 fw-bold text-uppercase">Batting summary</h6>
                        <table class="table table-striped">
                            <thead class="bg-success text-white">
                                <tr>
                                    <th>#</th>
                                    <th>BATSMAN </th>
                                    <th class="text-center">RUN</th>
                                    <th class="text-center">BALL</th>
                                    <th class="text-center">4s</th>
                                    <th class="text-center">6s</th>
                                    <th class="text-center">SR</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($firstTeamIndividualScore as $index => $score)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>
                                            {{ $score['batsmanName'] }}
                                            @if ($score['outByBowlerName'])
                                                <span style="font-size:12px">
                                                    <span style="color:red">(out</span>
                                                    <span class="text-dark"> - {{ $score['outByBowlerName'] }})</span>
                                                </span>
                                            @else
                                                <span style="color:blue; font-size:12px">(not out)</span>
                                            @endif
                                        </td>
                                        <td class="text-center">{{ $score['runs'] }}</td>
                                        <td class="text-center"> {{ $score['balls'] }}</td>
                                        <td class="text-center">{{ $score['fours'] }}</td>
                                        <td class="text-center">{{ $score['sixes'] }}</td>
                                        <td class="text-center">{{ $score['strike_rate'] }}</td>
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
                                        <td class="text-center">{{ $score['totalNoCount'] }}</td>
                                        <td class="text-center">{{ $score['totalWideCount'] }}</td>
                                        <td class="text-center">{{ $score['totalExtra'] }}</td>
                                        <td class="text-center">{{ $score['economyRate'] }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                        <div class="score-line my-3">
                            <h6 class="fw-bold text-uppercase text-primary mb-3">Score Line</h6>
                            <div class="d-flex flex-wrap">

                                @php
                                    $ballCount = 0;
                                    $overCount = 0;
                                    
                                @endphp
                                <span style="font-size: 10px" class="w-100 text-uppercase my-1 fw-bold text-dark">over
                                    {{ $overCount + 1 }}</span>
                                @foreach ($firstTeamScoreLine as $key => $score)
                                    @if (strpos($score->score_line, 'NB') === 0)
                                    @elseif(strpos($score->score_line, 'WD') === 0)
                                    @else
                                        @php
                                            $ballCount++;
                                        @endphp
                                    @endif
                                    <span class="text-dark fw-bold my-1 score-box ">
                                        <small
                                            style="font-size: 10px; color:{{ $score->score_line == 'W' || strpos($score->score_line, 'NB') === 0 || strpos($score->score_line, 'WD') === 0 || strpos($score->score_line, 'LB') === 0 || strpos($score->score_line, 'B') === 0 ? 'red' : 'black' }}">
                                            {{ $score->score_line }}
                                        </small>
                                    </span>
                                    @if ($ballCount == 6)
                                        @php
                                            $overCount++;
                                            $ballCount = 0;
                                        @endphp
                                        <div class="w-100 my-1 fw-bold text-uppercase  ">
                                            <span class="text-dark" style="font-size: 10px">Over
                                                {{ $overCount + 1 }}</span>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div> --}}
                {{-- <div class="col-md-6">
                    <div class="box p-3" style="min-height: 1500px">
                        <div class="alert alert-success text-center fw-bold text-uppercase">Second Innings</div>
                        <div class="box p-3 my-3">
                            <div class=" d-flex justify-content-between align-items-end">
                                <div style="font-size:14px">
                                    <h6 style="font-size:14px" class="fw-bold text-uppercase">
                                        <span
                                            class="text-primary">{{ substr($firstBowlingTeamName, 0, 3) }}</span>
                                        {{ $innings2BattingScore['totalRuns'] }}/{{ $innings2BattingScore['totalWickets'] }}
                                    </h6>
                                    <h6 style="font-size:14px" class="fw-bold"><span class="text-primary">Over
                                        </span>{{ $innings2BattingScore['totalOvers'] }}
                                        ({{ $match->over }})
                                    </h6>
                                </div>
                                <div>
                                    <h6 style="font-size:14px" class="fw-bold">
                                        <span class="text-primary">RR</span> {{ $innings2BattingScore['runRate'] }}
                                    </h6>
                                    <h6 style="font-size:14px" class="fw-bold"><span class="text-primary">Extra
                                        </span>{{ $innings2BattingScore['totalExtraRuns'] }}
                                    </h6>
                                </div>
                                <div>
                                    <h6 style="font-size:14px" class="fw-bold "><span class="text-primary">Total
                                            4S</span>
                                        {{ $innings2BattingScore['totalFours'] }}</h6>
                                    <h6 style="font-size:14px" class="fw-bold "><span class="text-primary">Total 6S
                                        </span>{{ $innings2BattingScore['totalSixes'] }}</h6>

                                </div>
                            </div>
                            <div class="hr-style"></div>
                            <div class="row">
                                <div class="col-md-6">
                                    <h6 style="font-size:14px" class="fw-bold"> Highest Run <span
                                            class="text-primary">{{ $secondHighestRunScorer['name'] }}</span> </h6>
                                    <h6 style="font-size:14px" class="fw-bold">(R {{ $secondHighestRunScorer['runs'] }},
                                        B
                                        {{ $secondHighestRunScorer['balls'] }},
                                        SR {{ $secondHighestRunScorer['strike_rate'] }} )</h6>
                                    <h6 style="font-size:14px" class="fw-bold">(4S
                                        {{ $secondHighestRunScorer['fours'] }},
                                        6S
                                        {{ $secondHighestRunScorer['sixes'] }}) </h6>
                                </div>
                                <div class="col-md-6">
                                    <h6 style="font-size:14px" class="fw-bold">Most ECO Bowler
                                        @if ($secondMostEconomicalBowler['name'] != 0)
                                            <span class="text-primary">{{ $secondMostEconomicalBowler['name'] }}</span>
                                        @endif
                                    </h6>
                                    <h6 style="font-size:14px" class="fw-bold">(R
                                        {{ $secondMostEconomicalBowler['runs'] }}, O
                                        {{ $secondMostEconomicalBowler['totalOvers'] }}, ECO
                                        {{ $secondMostEconomicalBowler['economyRate'] }})</h6>
                                    <h6 style="font-size:14px" class="fw-bold">(EXT
                                        {{ $secondMostEconomicalBowler['totalExtra'] }}, NB
                                        {{ $secondMostEconomicalBowler['totalNoCount'] }}, WD
                                        {{ $secondMostEconomicalBowler['totalWideCount'] }})</h6>
                                </div>
                            </div>
                        </div>
                        <h6 class="btn btn-primary w-100 fw-bold text-uppercase">Batting summary</h6>
                        <table class="table table-striped">
                            <thead class="bg-success text-white">
                                <tr>
                                    <th>#</th>
                                    <th>BATSMAN</th>
                                    <th class="text-center">RUN</th>
                                    <th class="text-center">BALL</th>
                                    <th class="text-center">4s</th>
                                    <th class="text-center">6s</th>
                                    <th class="text-center">SR</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($secondTeamIndividualScore as $index => $score)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>
                                            {{ $score['batsmanName'] }}
                                            @if ($score['outByBowlerName'])
                                                <span style="font-size:12px">
                                                    <span style="color:red">(out</span>
                                                    <span class="text-dark"> - {{ $score['outByBowlerName'] }})</span>
                                                </span>
                                            @else
                                                <span style="color:blue; font-size:12px">(not out)</span>
                                            @endif
                                        </td>
                                        <td class="text-center">{{ $score['runs'] }}</td>
                                        <td class="text-center"> {{ $score['balls'] }}</td>
                                        <td class="text-center">{{ $score['fours'] }}</td>
                                        <td class="text-center">{{ $score['sixes'] }}</td>
                                        <td class="text-center">{{ $score['strike_rate'] }}</td>
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
                                        <td class="text-center">{{ $score['totalNoCount'] }}</td>
                                        <td class="text-center">{{ $score['totalWideCount'] }}</td>
                                        <td class="text-center">{{ $score['totalExtra'] }}</td>
                                        <td class="text-center">{{ $score['economyRate'] }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div class="score-line my-3">
                            <h6 class="fw-bold text-uppercase text-primary mb-3">Score Line</h6>
                            <div class="d-flex flex-wrap">
                                @php
                                    $ballCount = 0;
                                    $overCount = 0;
                                @endphp
                                <span style="font-size: 10px" class="w-100 text-uppercase my-1 fw-bold text-dark">over
                                    {{ $overCount + 1 }}</span>
                                @foreach ($secondTeamScoreLine as $key => $score)
                                    @if (strpos($score->score_line, 'NB') === 0)
                                    @elseif(strpos($score->score_line, 'WD') === 0)
                                    @else
                                        @php
                                            $ballCount++;
                                        @endphp
                                    @endif
                                    <span class="text-dark fw-bold my-1 score-box ">
                                        <small
                                            style="font-size: 10px; color:{{ $score->score_line == 'W' || strpos($score->score_line, 'NB') === 0 || strpos($score->score_line, 'WD') === 0 || strpos($score->score_line, 'LB') === 0 || strpos($score->score_line, 'B') === 0 ? 'red' : 'black' }}">
                                            {{ $score->score_line }}
                                        </small>
                                    </span>
                                    @if ($ballCount == 6)
                                        @php
                                            $overCount++;
                                            $ballCount = 0;
                                        @endphp
                                        <div class="w-100 my-1 fw-bold text-uppercase  ">
                                            <span class="text-dark" style="font-size: 10px">Over
                                                {{ $overCount + 1 }}</span>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div> --}}
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
        // function validateSelection() {
        //     if (!$('input[name="bowler_id[]"]:checked').length && !$('input[name="batsman_id[]"]:checked').length) {
        //         swal({
        //             title: "warning",
        //             text: "Please select one bowler and one batsman",
        //             icon: "warning",
        //             button: "Ok",
        //         });
        //         return false;
        //     } else if (!$('input[name="bowler_id[]"]:checked').length) {
        //         swal({
        //             title: "warning",
        //             text: "Please select a bowler",
        //             icon: "warning",
        //             button: "Ok",
        //         });
        //         return false;
        //     } else if (!$('input[name="batsman_id[]"]:checked').length) {
        //         swal({
        //             title: "warning",
        //             text: "Please select a batsman",
        //             icon: "warning",
        //             button: "Ok",
        //         });
        //         return false;
        //     }
        //     return true;
        // }
        // $('form').on('submit', function(e) {
        //     if (!validateSelection()) {
        //         e.preventDefault();
        //     }
        // });

        function handleCheckboxClick(checkbox, type) {
            checkOtherFields(checkbox);
            saveSelection(checkbox, type);
        }
    </script>
@endsection
