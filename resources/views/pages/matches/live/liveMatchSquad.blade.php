{{-- @extends('layouts.app')
@section('title', 'Dashboard')
@section('style')
@endsection

@section('content')
    @include('layouts.navbar')
    <div class="container">

        <div class=" py-5 d-flex justify-content-center align-items-center" style="min-height: 90vh;">
            <form method="post" action="{{ route('save.live-match.squad', ['id' => $match_id]) }}">
                @csrf
                <input hidden name="innings" type="text" value={{ $innings }}>
                <input type="number" hidden value={{ $battingTeam->id }} name="battingTeamId">
                <input type="number" hidden value={{ $bowlingTeam->id }} name="bowlingTeamId">
                <div class="row" style="min-width: 600px">


                    <div class="col-md-6">
                        <h6>1st innings</h6>
                        <ul>
                            <li>Batting {{ $battingTeam->name }}</li>
                            <li>Bowling {{ $bowlingTeam->name }}</li>
                        </ul>
                        <h6>Select {{ $battingTeam->name }} Squad</h6>
                        @if (isset($battingTeam))
                            @foreach ($battingTeam->teamPlayers as $player)
                                <div class="form-check">
                                    <input class="form-check-input batting" type="checkbox" name="player_id[]"
                                        value="{{ $player->id }}" onclick="checkOtherFields(this)">
                                    {{ $player->name }}
                                   
                                    <input type="checkbox" hidden name="player_name[]" value="{{ $player->name }}">
                                    <input type="checkbox" hidden name="team_id[]" value="{{ $battingTeam->id }}">
                                    <input type="checkbox" hidden name="team_name[]" value="{{ $battingTeam->name }}">
                                </div>
                            @endforeach
                        @endif
                    </div>
                    <div class="col-md-6">
                        <h6>2nd innings</h6>
                        <ul>
                            <li>Batting {{ $bowlingTeam->name }}</li>
                            <li>Bowling {{ $battingTeam->name }}</li>
                        </ul>
                        <h6>Select {{ $bowlingTeam->name }} Squad</h6>
                        @if (isset($bowlingTeam))
                            @foreach ($bowlingTeam->teamPlayers as $player)
                                <div class="form-check">
                                    <input class="form-check-input balling" type="checkbox" name="player_id[]"
                                        value="{{ $player->id }}" onclick="checkOtherFields(this)">
                                    {{ $player->name }}
                                    <input type="checkbox" hidden name="role[]" value="ball">
                                    <input type="checkbox" hidden name="player_name[]" value="{{ $player->name }}">
                                    <input type="checkbox" hidden name="team_id[]" value="{{ $bowlingTeam->id }}">
                                    <input type="checkbox" hidden name="team_name[]" value="{{ $bowlingTeam->name }}">
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>
                <input type="submit" value="Make squad" class="btn btn-primary w-100 mt-5">
            </form>

        </div>
    </div>
    @include('layouts.footer')
@endsection

@section('script')
    <script>
        // for check hidden checkbok
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

        // $(document).ready(function() {
        //     $('form').submit(function() {
        //         const selectedBatter = $('input[name="role2[]"][value="bat"]:checked').length;
        //         const selectedBowlers = $('input[name="role2[]"][value="ball"]:checked').length;
        //         if (selectedBatter !== 11 && selectedBowlers < 5) {
        //             swal({
        //                 title: "warning",
        //                 text: "select 11 player for each team!",
        //                 icon: "warning",
        //                 button: "Ok",
        //             });
        //             return false;
        //         } 
        //     });
        // });
    </script>
@endsection --}}
