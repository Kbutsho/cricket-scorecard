@extends('layouts.app')
@section('title', 'add match')
@section('style')
    <style>
        .match-form .form {
            box-shadow: rgba(0, 0, 0, 0.35) 0px 5px 15px;
            border-radius: 10px;
            transition: 0.3s;
        }

        .match-form .form:hover {
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
        <div class="match-form d-flex justify-content-center align-items-center" style="min-height: 90vh">
            <div class="form p-4">
                <div class="mb-3 d-flex justify-content-between align-items-center alert alert-primary">
                    <h6 class="fw-bold text-primary text-uppercase">Add new match</h6>
                    <a href="{{ route('matches') }}" class="fw-bold text-danger">
                        <i class="fas fa-angle-double-left" style="font-size: 24px"></i>
                    </a>
                </div>
                <div id="message">
                    @if (session('success'))
                        <div class="alert alert-success fw-bold my-2"> {{ session('success') }}</div>
                    @endif
                </div>
                <form method="post" action="{{ route('add-match') }}">
                    @csrf



                    <div class="d-flex justify-content-between">
                        <div class="px-2">
                            <div class="mb-3">
                                <label for="team_a" class="fw-bold my-1">Team A</label>
                                <select style="width: 300px" id="team-a" name="team_a_id" class="form-select">
                                    <option value="" disabled selected>Select team A</option>
                                    @foreach ($teams as $team)
                                        <option value="{{ $team->id }}"
                                            @if ($team->id == old('team_a_id')) selected @endif>
                                            {{ $team->name }}</option>
                                    @endforeach
                                </select>
                                @error('team_a_id')
                                    <span>{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="team_b" class="fw-bold my-1">Team B</label>
                                <select id="team-b" name="team_b_id" class="form-select">
                                    <option value="" disabled selected>Select team B</option>
                                    @foreach ($teams as $team)
                                        <option value="{{ $team->id }}"
                                            @if ($team->id == old('team_b_id')) selected @endif>
                                            {{ $team->name }}</option>
                                    @endforeach
                                </select>
                                @error('team_b_id')
                                    <span>{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="venue" class="fw-bold my-1">Match Veune</label>
                                <select name="venue" class="form-select">
                                    <option value="" disabled selected>Select venue</option>
                                    @foreach ($venues as $venue)
                                        <option value="{{ $venue->name }}"
                                            @if ($venue->name == old('venue')) selected @endif>
                                            {{ $venue->name }}</option>
                                    @endforeach
                                </select>
                                @error('venue')
                                    <span>{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="px-2">
                            <div class="mb-3">
                                <label for="format" class="fw-bold my-1">Match Format</label>
                                <select name="format" class="form-select">
                                    <option value="" disabled selected>Select format</option>
                                    @foreach ($formats as $format)
                                        <option value="{{ $format }}"
                                            @if ($format == old('format')) selected @endif>
                                            {{ $format }}</option>
                                    @endforeach
                                </select>
                                @error('format')
                                    <span>{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label for="time" class="fw-bold my-1">Match Time</label>
                                <input style="width: 300px" type="datetime-local" class="form-control" name="time"
                                    value="{{ old('time', isset($time) ? \Carbon\Carbon::parse($time)->format('Y-m-d\TH:i') : '') }}"
                                    placeholder="time">
                                @error('time')
                                    <span>{{ $message }}</span>
                                @enderror
                            </div>

                            <input type="submit" class="btn btn-primary w-100 fw-bold mt-4" value="submit">
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
        const teamA = document.querySelector('#team-a');
        const teamB = document.querySelector('#team-b');
        teamA.addEventListener('change', () => {
            const selectedTeam = teamA.value;
            if (selectedTeam) {
                const option = teamB.querySelector(`option[value="${selectedTeam}"]`);
                if (option) {
                    option.style.display = 'none';
                }
            }
        });
        teamB.addEventListener('change', () => {
            const selectedTeam = teamB.value;
            if (selectedTeam) {
                const option = teamA.querySelector(`option[value="${selectedTeam}"]`);
                if (option) {
                    option.style.display = 'none';
                }
            }
        });
    </script>
@endsection
