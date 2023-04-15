@extends('layouts.app')
@section('title', 'live match')
@section('style')
    <style>
        .box {
            width: 60%;
            min-height: 90vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .box-content {
            border-radius: 10px;
            box-shadow: rgba(0, 0, 0, 0.35) 0px 3px 8px;
            transition: 0.3s;
        }

        .box-content:hover {
            box-shadow: rgba(0, 0, 0, 0.7) 0px 3px 8px;
        }

        span {
            font-size: 12px;
            font-weight: bold;
        }
    </style>
@endsection

@section('content')
    @include('layouts.navbar')
    <div class="container box">
        <div class="box-content p-5">
            <div class="message">
                @if (session('danger'))
                    <div class="alert alert-danger fw-bold my-2"> {{ session('danger') }}</div>
                @endif
            </div>
            <div>
                <form class="row" action="{{ route('post.live.match.squad', ['id' => $match->id]) }}" method="post">
                    @csrf
                    <div class="squad col-md-7">
                        <div class="row">
                            {{-- Team a --}}
                            <div class="col-md-6 squad-list">
                                <small class="fw-bold mb-3 btn btn-success w-100">{{ $match->teamA->name }} playing
                                    XI</small>
                                @if ($match->teamA->teamPlayers && count($match->teamA->teamPlayers) > 0)
                                    @foreach ($match->teamA->teamPlayers as $index => $player)
                                        <div class="d-flex">
                                            <span class="me-2">{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</span>
                                            <div class="form-check">
                                                <input class="form-check-input batting" type="checkbox" name="player_id[]"
                                                    value="{{ $player->id }}" onclick="checkOtherFields(this)"
                                                    @if (old('player_id') && in_array($player->id, old('player_id'))) checked @endif>
                                                {{ $player->name }}
                                                @if (old('player_id') && in_array($player->id, old('player_id')))
                                                    <input type="checkbox" hidden checked name="teamA[]"
                                                        value="{{ $player->id }}">
                                                    <input type="checkbox" hidden checked name="player_name[]" hidden
                                                        value="{{ $player->name }}">
                                                    <input type="checkbox" checked name="team_id[]" hidden
                                                        value="{{ $player->team_id }}">
                                                    <input type="checkbox" checked name="team_name[]" hidden
                                                        value="{{ $match->teamA->name }}">
                                                @else
                                                    <input type="checkbox" hidden name="teamA[]"
                                                        value="{{ $player->id }}">
                                                    <input type="checkbox" hidden name="player_name[]"
                                                        value="{{ $player->name }}">
                                                    <input type="checkbox" hidden name="team_id[]"
                                                        value="{{ $player->team_id }}">
                                                    <input type="checkbox" hidden name="team_name[]"
                                                        value="{{ $match->teamA->name }}">
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                    <div class="message">
                                        @error('teamA')
                                            <div class="pb-1 px-3 mt-3"
                                                style="color: white; background: red; border-radius: 5px">
                                                <span>{{ $message }} for {{ $match->teamA->name }}</span>
                                            </div>
                                        @enderror
                                    </div>
                                @endif
                            </div>

                            {{-- Team b --}}
                            <div class="col-md-6 squad-list">
                                <h6 class="fw-bold mb-3 btn btn-success w-100">{{ $match->teamB->name }} playing XI</h6>
                                @if ($match->teamB->teamPlayers && count($match->teamB->teamPlayers) > 0)
                                    @foreach ($match->teamB->teamPlayers as $index => $player)
                                        <div class="d-flex">
                                            <span class="me-2">{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</span>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="player_id[]"
                                                    value="{{ $player->id }}" onclick="checkOtherFields(this)"
                                                    @if (old('player_id') && in_array($player->id, old('player_id'))) checked @endif>
                                                {{ $player->name }}
                                                @if (old('player_id') && in_array($player->id, old('player_id')))
                                                    <input type="checkbox" checked name="teamB[]" hidden
                                                        value="{{ $player->team_id }}">
                                                    <input type="checkbox" checked name="player_name[]" hidden
                                                        value="{{ $player->name }}">
                                                    <input type="checkbox" checked name="team_id[]" hidden
                                                        value="{{ $player->team_id }}">
                                                    <input type="checkbox" checked name="team_name[]" hidden
                                                        value="{{ $match->teamB->name }}">
                                                @else
                                                    <input type="checkbox" hidden name="teamB[]"
                                                        value="{{ $player->team_id }}">
                                                    <input type="checkbox" hidden name="player_name[]"
                                                        value="{{ $player->name }}">
                                                    <input type="checkbox" hidden name="team_id[]"
                                                        value="{{ $player->team_id }}">
                                                    <input type="checkbox" hidden name="team_name[]"
                                                        value="{{ $match->teamB->name }}">
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                    <div class="message">
                                        @error('teamB')
                                            <div class="pb-1 px-3 mt-3"
                                                style="color: white; background: red; border-radius: 5px">
                                                <span>{{ $message }} for {{ $match->teamB->name }}</span>
                                            </div>
                                        @enderror
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="team col-md-5 d-flex flex-column justify-content-between">
                        <input type="number" name="match_id" hidden value={{ $match->id }}>
                        <div>
                            <div class="btn btn-success fw-bold text-center w-100">First Innings</div>
                            <div class="d-flex justify-content-between py-3">
                                <div>
                                    <h6 class="fw-bold">Select Batting Team</h6>
                                    <div>
                                        <input class="form-check-input" type="radio" name="firstBattingTeamId"
                                            value="{{ $match->teamA->id }}"
                                            @if (old('firstBattingTeamId') == $match->teamA->id) checked @endif>
                                        {{ $match->teamA->name }}
                                    </div>
                                    <div>
                                        <input class="form-check-input" type="radio" name="firstBattingTeamId"
                                            value="{{ $match->teamB->id }}"
                                            @if (old('firstBattingTeamId') == $match->teamB->id) checked @endif>
                                        {{ $match->teamB->name }}
                                    </div>
                                    <div class="message">
                                        @error('firstBattingTeamId')
                                            <div class="pb-1 px-3 mt-3"
                                                style="color: white; background: red; border-radius: 5px">
                                                <span>{{ $message }}</span>
                                            </div>
                                        @enderror
                                    </div>
                                </div>
                                <div>
                                    <h6 class="fw-bold">Select Bowling Team</h6>
                                    <div>
                                        <input class="form-check-input" type="radio" name="firstBowlingTeamId"
                                            value="{{ $match->teamA->id }}"
                                            @if (old('firstBowlingTeamId') == $match->teamA->id) checked @endif>
                                        {{ $match->teamA->name }}
                                    </div>
                                    <div>
                                        <input class="form-check-input" type="radio" name="firstBowlingTeamId"
                                            value="{{ $match->teamB->id }}"
                                            @if (old('firstBowlingTeamId') == $match->teamB->id) checked @endif>
                                        {{ $match->teamB->name }}
                                    </div>
                                    <div class="message">
                                        @error('firstBowlingTeamId')
                                            <div class="pb-1 px-3 mt-3"
                                                style="color: white; background: red; border-radius: 5px">
                                                <span>{{ $message }}</span>
                                            </div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div>
                            <h1 class="fw-bold" style="color: red; font-size: 12px">(Batting and Bowling teams will be
                                automatically reversed for the second innings)</h1>
                            <input type="submit" class="btn btn-primary w-100 fw-bold mt-2" value="submit">
                        </div>
                    </div>

                </form>
            </div>
        </div>
    </div>
    @include('layouts.footer')
@endsection

@section('script')
    <script>
        // for check hidden checkbok (squad)
        function checkOtherFields(checkbox) {
            const formCheck = checkbox.parentNode;
            const otherCheckboxes = formCheck.querySelectorAll('input[type="checkbox"]:not([name="player_id[]"])');
            otherCheckboxes.forEach((cb) => {
                if (checkbox.checked) {
                    cb.disabled = false;
                    cb.checked = true;
                } else {
                    cb.disabled = true;
                    cb.checked = false;
                }
            });
        }


       
        
        // for innings (prevent to select same team for batting and bowling)
        $(document).ready(function() {
            const $battingTeams = $('input[name="firstBattingTeamId"]');
            const $bowlingTeams = $('input[name="firstBowlingTeamId"]');
            $battingTeams.change(function() {
                const battingTeam = $('input[name="firstBattingTeamId"]:checked').val();
                const bowlingTeam = $('input[name="firstBowlingTeamId"]:checked').val();
                if (battingTeam === bowlingTeam) {
                    swal({
                        title: "warning",
                        text: "You can't select the same team for batting and bowling!",
                        icon: "warning",
                        button: "Ok",
                    });
                    $(this).prop('checked', false);
                    return false;
                }
            });
            $bowlingTeams.change(function() {
                const battingTeam = $('input[name="firstBattingTeamId"]:checked').val();
                const bowlingTeam = $('input[name="firstBowlingTeamId"]:checked').val();

                if (battingTeam === bowlingTeam) {
                    swal({
                        title: "warning",
                        text: "You can't select the same team for batting and bowling!",
                        icon: "warning",
                        button: "Ok",
                    });
                    $(this).prop('checked', false);
                    return false;
                }
            });
        });
    </script>
@endsection
