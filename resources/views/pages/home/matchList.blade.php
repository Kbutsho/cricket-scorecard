@extends('layouts.app')
@section('title', 'live matches')
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
    </style>
@endsection

@section('content')
    <div class="container py-5">
        <div class="message">
            @if (session('danger'))
                <div class="alert alert-danger fw-bold my-2"> {{ session('danger') }}</div>
            @elseif(session('success'))
                <div class="alert alert-success fw-bold my-2"> {{ session('success') }}</div>
            @endif
        </div>
        <div class="live py-5">
            @if (count($liveMatches) > 0)
                @foreach ($liveMatches as $match)
                    <div class="live-card">
                        <div class="d-flex justify-content-between">
                            <h6 class="fw-bold text-primary">{{ $match->teamA->name }} vs {{ $match->teamB->name }}</h6>
                            <small class="fw-bold text-success ms-3">{{ $match->format }}</small>
                            <div class="dot me-1"></div>
                        </div>

                        <span class="text-dark">{{ $match->venue }} national cricket stadium</span>
                        <div class="d-flex justify-content-between text-dark">
                            <span class="fw-bold">Local time</span>
                            <span>{{ date('g:i A', strtotime($match->time)) }} (+06 GTM)</span>
                        </div>

                        <div class="mt-2 d-flex justify-content-between align-items-baseline">
                            @switch($match->status)
                                @case('ongoing')
                                    <h6 class="fw-bold text-dark">status <span style="color:green">{{ $match->status }}</span></h6>
                                @break

                                @case('upcoming')
                                    <h6 class="fw-bold text-dark">status <span style="color:blue">{{ $match->status }}</span></h6>
                                @break

                                @default
                                    <h6 class="fw-bold text-dark">status <span style="color:red">{{ $match->status }}</span></h6>
                            @endswitch
                            @if ($match->status != 'upcoming')
                                <a class="btn btn-outline-success btn-sm"
                                    href="{{ route('live', ['id' => $match->id]) }}">score card</a>
                            @endif
                        </div>
                    </div>
                @endforeach
            @else
                <div>
                    <h4 class="text-primary fw-bold">No live match available!</h4>
                </div>
            @endif
        </div>
    </div>
    @include('layouts.footer')
@endsection

@section('script')

@endsection
