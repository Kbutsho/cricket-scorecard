@extends('layouts.app')
@section('title', 'update player')
@section('style')
    <style>
        .update-player-form .form {
            box-shadow: rgba(0, 0, 0, 0.35) 0px 5px 15px;
            border-radius: 10px;
            transition: 0.3s;
        }

        .update-player-form .form:hover {
            box-shadow: rgba(0, 0, 0, 0.7) 0px 5px 15px;
        }

        span {
            font-size: 12px;
            color: red;
            font-weight: bold;
        }

        a {
            text-decoration: none;
        }
    </style>
@endsection

@section('content')
    @include('layouts.navbar')
    <div class="container">
        <div class="update-player-form d-flex justify-content-center align-items-center" style="min-height: 90vh">
            <div class="form px-4 py-4">
                <div class="mb-3 d-flex justify-content-between align-items-center alert alert-primary">
                    <h6 class="fw-bold text-primary text-uppercase">update player</h6>
                    <a href="{{ route('players') }}" class="fw-bold text-danger">
                        <i class="fas fa-angle-double-left" style="font-size: 24px"></i>
                    </a>
                </div>
                <div id="message">
                    @if (session('success'))
                        <div class="alert alert-success fw-bold my-2"> {{ session('success') }}</div>
                    @endif
                </div>
                <form method="post" action="{{ route('player.update') }}">
                    <input type="number" name="id" value="{{ $player->id }}" hidden>
                    @csrf
                    @method('PATCH')
                    <div class="d-flex justify-content-around">
                        <div class="px-2">
                            <div class="mb-1">
                                <label for="name" class="fw-bold my-1">Player name</label>
                                <input type="text" name="name" value="{{ $player->name }}" placeholder="name"
                                    class="form-control" style="width: 300px">
                                @error('name')
                                    <span>{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="mb-1">
                                <label for="team_id" class="fw-bold my-1">Team</label>
                                <select name="team_id" class="form-select">
                                    @foreach ($teams as $t)
                                        <option
                                            value="{{ $t->id }}"{{ old('team_id', $team) == $t->id ? ' selected' : '' }}>
                                            {{ $t->name }}</option>
                                    @endforeach
                                </select>
                                @error('team_id')
                                    <span>{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="mb-1">
                                <label for="role" class="fw-bold my-1">Player role</label>
                                <select name="role" class="form-select">
                                    @foreach ($roles as $r)
                                        <option
                                            value="{{ $r }}"{{ old('role', $role) == $r ? ' selected' : '' }}>
                                            {{ $r }}</option>
                                    @endforeach
                                </select>
                                @error('role')
                                    <span>{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="mb-1">
                                <label for="batting_style" class="fw-bold my-1">Batting style</label>
                                <select name="batting_style" class="form-select">
                                    @foreach ($battingStyle as $b)
                                        <option
                                            value="{{ $b }}"{{ old('batting_style', $batting) == $b ? ' selected' : '' }}>
                                            {{ $b }}</option>
                                    @endforeach
                                </select>
                                @error('batting_style')
                                    <span>{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="mb-1">
                                <label for="bowling_style" class="fw-bold my-1">Batting style</label>
                                <select name="bowling_style" class="form-select">
                                    @foreach ($bowlingStyle as $bo)
                                        <option
                                            value="{{ $bo }}"{{ old('bowling_style', $bowling) == $bo ? ' selected' : '' }}>
                                            {{ $bo }}</option>
                                    @endforeach
                                </select>
                                @error('bowling_style')
                                    <span>{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="px-2">

                            <div class="mb-1">
                                <label for="born" class="fw-bold my-1">Birth date</label>
                                <input type="date" name="born" value="{{ \Carbon\Carbon::parse($player->born)->format('Y-m-d') }}" placeholder="born"
                                    class="form-control" style="width: 300px">
                                @error('born')
                                    <span>{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="mb-1">
                                <label for="status" class="fw-bold my-1">Status</label>
                                <select name="status" class="form-select">
                                    @foreach ($status as $key => $value)
                                        <option value="{{ $value }}">
                                            {{ $key }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('status')
                                    <span>{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="mb-1">
                                <label for="biography" class="fw-bold my-1">Biography</label>
                                <textarea name="biography" style="width: 300px" placeholder="write biography" rows="7" cols="50"
                                    type="text" class="form-control">{{ $player->biography }}</textarea>
                                @error('biography')
                                    <span>{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <input type="submit" class="btn btn-primary w-100 fw-bold mt-3" value="submit">
                </form>
            </div>
        </div>
    </div>
    @include('layouts.footer')
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
