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
        }
    </style>
@endsection

@section('content')
    @include('layouts.navbar')
    <div class="container">
        <div class="message">
            @if (session('success'))
                <div class="alert alert-success fw-bold"> {{ session('success') }}</div>
            @elseif(session('danger'))
                <div class="alert alert-danger fw-bold"> {{ session('danger') }}</div>
            @endif
        </div>
        <h3 class="py-5 fw-bold text-primary text-uppercase">Live match {{ count($liveMatches) }}</h3>
        <div class="live">
            @foreach ($liveMatches as $match)
                <a class="live-card" href="{{ route('get.live.match.squad', ['id' => $match->id]) }}">
                    <div class="d-flex justify-content-between">
                        <h6 class="fw-bold">{{ $match->teamA->name }} vs {{ $match->teamB->name }}</h6>
                        <small class="fw-bold text-success ms-3">{{ $match->format }}</small>
                        <div class="dot me-1"></div>
                    </div>
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

@endsection
