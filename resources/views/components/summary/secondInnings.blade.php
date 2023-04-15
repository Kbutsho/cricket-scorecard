<div class="col-md-6">
    <div class="box p-3" style="min-height: 1500px">
        <div class="alert alert-success text-center fw-bold text-uppercase">Second Innings</div>
        <div class="box p-3 my-3">
            <div class=" d-flex justify-content-between align-items-end">
                <div style="font-size:14px">
                    <h6 style="font-size:14px" class="fw-bold text-uppercase">
                        <span class="text-primary">{{ substr($firstBowlingTeamName, 0, 3) }}</span>
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
                            class="text-primary">{{ $secondHighestRunScorer['batsmanName'] }}</span> </h6>
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
                            <span class="text-primary">{{ $secondMostEconomicalBowler['bowlerName'] }}</span>
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
                        <td>{{ $loop->index + 1 }}</td>
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
                @foreach ($secondBowlingIndividualScore as $index => $score)
                    <tr>
                        <td>{{ $loop->index + 1 }}</td>
                        <td>{{ $score['bowlerName'] }}</td>
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
</div>
