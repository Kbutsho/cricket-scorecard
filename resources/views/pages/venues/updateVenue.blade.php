@extends('layouts.app')
@section('title', 'update venue')
@section('style')
    <style>
        .update-venue-form .form {
            box-shadow: rgba(0, 0, 0, 0.35) 0px 5px 15px;
            border-radius: 10px;
            transition: 0.3s;
        }

        .update-venue-form .form:hover {
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
        <div class="update-venue-form d-flex justify-content-center align-items-center" style="min-height: 90vh">
            <div class="form px-4 py-5">
                <div class="mb-3 d-flex justify-content-between align-items-center alert alert-primary">
                    <h6 class="fw-bold text-primary text-uppercase">update venue</h6>
                    <a href="{{ route('venues') }}" class="fw-bold text-danger">
                        <i class="fas fa-angle-double-left" style="font-size: 24px"></i>
                    </a>
                </div>
                <form method="post" action="{{ route('venue.update') }}">
                    @csrf
                    @method('PATCH')
                    <input type="number" name="id" value="{{ $venue->id }}" hidden>
                    <div class="mb-3">
                        <label for="name" class="fw-bold my-1">Venue Name</label>
                        <input type="text" name="name" value="{{ $venue->name }}" placeholder="name"
                            class="form-control" style="width: 300px">
                        @error('name')
                            <span>{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="location" class="fw-bold my-1">Venue Location</label>
                        <input type="text" name="location" value="{{ $venue->location }}" placeholder="location"
                            class="form-control">
                        @error('location')
                            <span>{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="capacity" class="fw-bold my-1">Venue Capacity</label>
                        <input type="text" name="capacity" value="{{ $venue->capacity }}" placeholder="capacity"
                            class="form-control">
                        @error('capacity')
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
@endsection
