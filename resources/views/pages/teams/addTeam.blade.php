@extends('layouts.app')
@section('title', 'add team')
@section('style')
    <style>
        .team-form .form {
            box-shadow: rgba(0, 0, 0, 0.35) 0px 5px 15px;
            border-radius: 10px;
            transition: 0.3s;
        }

        .team-form .form:hover {
            box-shadow: rgba(0, 0, 0, 0.7) 0px 5px 15px;
        }

        span {
            font-size: 12px;
            color: red;
            font-weight: bold;
        }
        a{
            text-decoration: none;
        }
    </style>
@endsection

@section('content')
    @include('layouts.navbar')
    <div class="container">
        <div class="team-form d-flex justify-content-center align-items-center" style="min-height: 90vh">
            <div class="form px-4 py-5">
                <div class="mb-3 d-flex justify-content-between align-items-center alert alert-primary">
                    <h6 class="fw-bold text-primary text-uppercase">Add new team</h6>
                    <a href="{{ route('teams') }}" class="fw-bold text-danger">
                        <i class="fas fa-angle-double-left" style="font-size: 24px"></i>
                    </a>
                </div>
                <div id="message">
                    @if (session('success'))
                        <div class="alert alert-success fw-bold my-2"> {{ session('success') }}</div>
                    @endif
                </div>
                <form method="post" action="{{ route('add-team') }}">
                    @csrf
                    <div class="mb-3">
                        <label for="name" class="fw-bold my-1">Team name</label>
                        <input type="text" name="name" value="{{ old('name') }}" placeholder="name"
                            class="form-control" style="width: 300px">
                        @error('name')
                            <span>{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="head_coach" class="fw-bold my-1">Team head coach</label>
                        <input type="text" name="head_coach" value="{{ old('head_coach') }}" placeholder="head coach"
                            class="form-control">
                        @error('head_coach')
                            <span>{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="home_venue_id" class="fw-bold my-1">Team home ground</label>
                        <select name="home_venue_id" class="form-select">
                            <option value="" disabled selected>Select a Venue</option>
                            @foreach($venues as $venue)
                                <option value="{{ $venue->id }}" @if($venue->id == old('home_venue_id')) selected @endif>{{ $venue->name }}</option>
                            @endforeach
                        </select>
                        
                        @error('home_venue_id')
                            <span>{{ $message }}</span>
                        @enderror
                    </div>

                    <input type="submit" class="btn btn-primary w-100 fw-bold mt-1" value="submit">
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
