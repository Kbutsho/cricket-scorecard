@extends('layouts.app')
@section('title', 'Dashboard')
@section('style')
    <style>
        .dashboard {
            min-width: 100%;
            min-height: 50vh;
            align-items: center;
        }
        .dashboard a {
            display: flex;
            justify-content: center;
            align-items: center;
            width: 300px;
            height: 100px;
            box-shadow: rgba(0, 0, 0, 0.35) 0px 2px 5px;
            text-decoration: none;
            font-weight: bold;
            transition: 0.3s;
            border-radius: 5px;
        }
        .dashboard a:hover {
            box-shadow: rgba(0, 0, 0, 0.35) 0px 5px 10px;
        }
        span{
            text-transform: uppercase;
        }
    </style>
@endsection

@section('content')
    @extends('layouts.navbar')
    <div class="container">
        {{-- message --}}
        <div id="message">
            @if (session('success'))
                <div class="alert alert-success fw-bold"> {{ session('success') }}</div>
            @elseif(session('danger'))
                <div class="alert alert-danger fw-bold"> {{ session('danger') }}</div>
            @endif
        </div>
        <div>
            <h3 class="py-5 fw-bold text-primary">Dashboard</h3>
            <div class="d-flex dashboard justify-content-between">
                <a class="h5" href="{{ route('teams') }}">
                    <i class="me-1 fas fa-cog"></i>
                    <span>teams</span>
                </a>
                <a class="h5" href="{{ route('players') }}">
                    <i class="me-1 fas fa-cog"></i>
                    <span>players</span>
                </a>
                <a class="h5" href="{{ route('matches') }}">
                    <i class="me-1 fas fa-cog"></i>
                    <span>matches</span>
                </a>
                <a class="h5" href="{{ route('venues') }}">
                    <i class="me-1 fas fa-cog"></i>
                    <span>venus</span>
                </a>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(function() {
                let message = document.getElementById('message');
                message.parentNode.removeChild(message);
            }, 3000);
        });
    </script>
@endsection
