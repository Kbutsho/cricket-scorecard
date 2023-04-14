@extends('layouts.app')
@section('title', 'add player')
@section('style')
    <style>
        .player-form .form {
            box-shadow: rgba(0, 0, 0, 0.35) 0px 5px 15px;
            border-radius: 10px;
            transition: 0.3s;
        }

        .player-form .form:hover {
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
        <div class="player-form d-flex justify-content-center align-items-center" style="min-height: 90vh">
            <div class="form px-4 py-4">
                <div class="mb-3 d-flex justify-content-between align-items-center alert alert-primary">
                    <h6 class="fw-bold text-primary text-uppercase">Add new player</h6>
                    <a href="{{ route('players') }}" class="fw-bold text-danger">
                        <i class="fas fa-angle-double-left" style="font-size: 24px"></i>
                    </a>
                </div>
                <div id="message">
                    @if (session('success'))
                        <div class="alert alert-success fw-bold my-2"> {{ session('success') }}</div>
                    @endif
                </div>
                <form method="post" action="{{ route('add-player') }}">
                    @csrf
                    <div class="d-flex justify-content-around">
                        <div class="px-2">
                            <div class="mb-1">
                                <label for="name" class="fw-bold my-1">Player name</label>
                                <input type="text" name="name" value="{{ old('name') }}" placeholder="name"
                                    class="form-control" style="width: 300px">
                                @error('name')
                                    <span>{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="mb-1">
                                <label for="team_id" class="fw-bold my-1">Team</label>
                                <select name="team_id" class="form-select">
                                    <option value="" disabled selected>select a team</option>
                                    @foreach ($teams as $team)
                                        <option value="{{ $team->id }}"
                                            @if ($team->id == old('team_id')) selected @endif>
                                            {{ $team->name }}</option>
                                    @endforeach
                                </select>
                                @error('team_id')
                                    <span>{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="mb-1">
                                <label for="role" class="fw-bold my-1">Player role</label>
                                <select name="role" class="form-select">
                                    <option value="" disabled selected>select a role</option>
                                    @foreach ($roles as $role)
                                        <option value="{{ $role }}"
                                            @if ($role == old('role')) selected @endif>
                                            {{ $role }}</option>
                                    @endforeach
                                </select>
                                @error('role')
                                    <span>{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="mb-1">
                                <label for="batting_style" class="fw-bold my-1">Batting style</label>
                                <select name="batting_style" class="form-select">
                                    <option value="" disabled selected>select batting style</option>
                                    @foreach ($battingStyle as $bat)
                                        <option value="{{ $bat }}"
                                            @if ($bat == old('batting_style')) selected @endif>
                                            {{ $bat }}</option>
                                    @endforeach
                                </select>
                                @error('batting_style')
                                    <span>{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="mb-1">
                                <label for="bowling_style" class="fw-bold my-1">BowlingS style</label>
                                <select name="bowling_style" class="form-select">
                                    <option value="" disabled selected>select bowling style</option>
                                    @foreach ($bowlingStyle as $ball)
                                        <option value="{{ $ball }}"
                                            @if ($ball == old('bowling_style')) selected @endif>
                                            {{ $ball }}</option>
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
                                <input type="date" name="born" value="{{ old('born') }}" placeholder="born"
                                    class="form-control" style="width: 300px">
                                @error('born')
                                    <span>{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="mb-1">
                                <label for="image" class="fw-bold my-1">Profile image</label>
                                <input type="file" name="image" placeholder="image" class="form-control"
                                    style="width: 300px">
                                @error('image')
                                    <span>{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="mb-1">
                                <label for="biography" class="fw-bold my-1">Biography</label>
                                <textarea name="biography" style="width: 300px" placeholder="write biography" rows="7" cols="50" type="text"
                                class="form-control">{{ old('biography') }}</textarea>
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
