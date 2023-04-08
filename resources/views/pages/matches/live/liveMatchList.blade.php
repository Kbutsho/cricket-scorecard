@extends('layouts.app')
@section('title', 'all live match')
@section('style')
    <style>
        .live {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            column-gap: 25px;
        }

        .live-card {
            padding: 20px;
            box-shadow: rgba(0, 0, 0, 0.35) 0px 2px 5px;
            margin: 0px 0 25px 0;
            border-radius: 5px;
            transition: 0.3s;
            text-decoration: none;
        }

        .live-card:hover {
            box-shadow: rgba(0, 0, 0, 0.35) 0px 5px 10px;
        }

        .dot {
            height: 20px;
            width: 20px;
            background: green;
            border-radius: 100%;
            animation: pulse 1.5s ease-in-out infinite;
        }

        @keyframes pulse {
            0% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.3);
            }

            100% {
                transform: scale(1);
            }
        }
    </style>
@endsection

@section('content')
    @include('layouts.navbar')
    <div class="container">
        <div id="message">
            @if (session('danger'))
                <div class="alert alert-danger fw-bold my-2"> {{ session('danger') }}</div>
            @endif
        </div>
        <h3 class="py-5 fw-bold text-primary text-uppercase">Live match {{ count($liveMatches) }}</h3>
        <div class="live">
            @foreach ($liveMatches as $match)
                <a class="live-card" href="{{ route('get.live.match.squad', ['id' => $match->id]) }}">
                    <div class="d-flex justify-content-between">
                        <h6 class="fw-bold">{{ $match->teamA->name }} vs {{ $match->teamB->name }}</h6>
                        <small class="fw-bold text-success ms-3">{{ $match->format }}</small>
                        <div class="dot"></div>
                    </div>

                    {{-- @if (isset($matchInningsInProgress[$match->id]))
                        <h6 class="fw-bold" style="color: red"> {{ $matchInningsInProgress[$match->id] }}</h6>
                    @endif --}}

                    <span class="text-dark">{{ $match->venue }} national cricket stadium</span>

                    <div class="d-flex justify-content-between text-dark">
                        <span class="fw-bold">Local time</span>
                        <span>{{ date('g:i A', strtotime($match->time)) }} (+06 GTM)</span>
                        <i style="font-size: 20px" class="me-1 fas fa-cog"></i>
                    </div>
                </a>
            @endforeach


        </div>
    </div>
    @include('layouts.footer')
@endsection

@section('script')
    <script>
        $(document).ready(function() {
            const $battingTeams = $('input[name="batting_team"]');
            const $bowlingTeams = $('input[name="bowling_team"]');

            $battingTeams.change(function() {
                const battingTeam = $('input[name="batting_team"]:checked').val();
                const bowlingTeam = $('input[name="bowling_team"]:checked').val();
                if (battingTeam === bowlingTeam) {
                    alert("You can't select the same team for batting and bowling.");
                    $(this).prop('checked', false);
                }
            });
            $bowlingTeams.change(function() {
                const battingTeam = $('input[name="batting_team"]:checked').val();
                const bowlingTeam = $('input[name="bowling_team"]:checked').val();

                if (battingTeam === bowlingTeam) {
                    alert("You can't select the same team for batting and bowling.");
                    $(this).prop('checked', false);
                }
            });
        });
    </script>
@endsection
