<div class="col-md-7">
    <div class="box p-3">
        <div class="alert alert-success pb-1 d-flex justify-content-between align-items-center">
            <h5 class="fw-bold text-center text-uppercase">1st innings squad</h5>
            <a class="btn btn-outline-danger btn-sm fw-bold mb-2 text-uppercase"
               href="{{ route('update.live.match.innings', ['matchId' => $match->id]) }}">
                <i class="fas fa-external-link-alt me-1"></i>end innings
            </a>
        </div>
        <div id="firstInnings">
            <div class="row">
                <div class="col-md-6">
                    @if ($firstBattingSquad && count($firstBattingSquad) > 0)
                        <h6 class="fw-bold btn btn-primary w-100 btn-sm">
                            {{ $firstBattingSquad[0]['team_name'] }} Batting XI
                        </h6>
                        @foreach ($firstBattingSquad as $index => $player)
                            <div class="d-flex">
                                <small class="me-2 fw-bold">{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</small>
                                <div class="form-check">
                                    <input class="form-check-input batsman-checkbox" type="checkbox" name="batsman_id[]"
                                           value="{{ $player['player_id'] }}"
                                           @if (in_array($player['player_id'], old('batsman_id', [])) || in_array($player['player_id'], $outBatsmanList)) disabled
                                           @endif
                                           onclick="handleCheckboxClick(this, 'batsman', {{ json_encode($outBatsmanList) }})">
                                    <input type="checkbox" value="{{ $player['team_id'] }}" hidden
                                           name="battingTeamId[]">
                                    <small>{{ $player['player_name'] }}
                                        <span style="color: red; font-size:14px; font-weight:bold">
                                            @if (in_array($player['player_id'], $outBatsmanList))
                                                (out)
                                            @endif
                                        </span>
                                    </small>
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
                            {{ $firstBowlingSquad[0]['team_name'] }} Bowling XI
                        </h6>
                        @foreach ($firstBowlingSquad as $index => $player)
                            <div class="d-flex">
                                <small class="me-2 fw-bold">{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</small>
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
