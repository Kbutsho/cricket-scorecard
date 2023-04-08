@extends('layouts.app')
@section('title', 'team ' . $team->name . 'players list')
@section('style')
    <style>
        a {
            text-decoration: none;
            font-weight: bold;
        }

        a i {
            font-size: 24px;
        }
    </style>
@endsection

@section('content')
    @include('layouts.navbar')
    <div class="container py-5">
        <div>
            <div class="d-flex justify-content-between mb-3 alert alert-primary">
                <h5 class="fw-bold text-uppercase text-primary"> player {{ count($players) }}</h5>
                <h5 class="fw-bold text-primary">{{ $team->name }} player list</h5>
                <a class="h5 fw-bold d-flex justify-content-center align-items-center text-danger"
                    href="{{ route('teams') }}">
                    <span class="text-uppercase"></span><i class="fas fa-angle-double-left" style="font-size: 24px"></i>
                </a>

            </div>
            <div id="message">
                @if (session('success'))
                    <div class="alert alert-success fw-bold text-center"> {{ session('success') }}</div>
                @elseif(session('danger'))
                    <div class="alert alert-danger fw-bold text-center"> {{ session('danger') }}</div>
                @endif
            </div>
            <table id="data" class="pt-3 table table-hover table-striped table-borderless">
                <thead class="bg-primary">
                    <tr class="text-center text-white">
                        <th>ID</th>
                        <th>Player Name</th>
                        <th>Team</th>
                        <th>Role</th>
                        <th>Batting Style</th>
                        <th>Bowling Style</th>
                        <th>Born</th>
                        {{-- <th>Action</th> --}}
                    </tr>
                </thead>
            </table>
        </div>
    </div>
@endsection

@section('script')
    <script>
        $(document).ready(function() {
            $('#data').DataTable({
                ajax: '{{ route('get.team-players', $team->id) }}',
                processing: true,
                serverSide: true,
                language: {
                    "processing": "<div class='my-5' style='height: 25vh'></div>"
                },
                columns: [{
                        data: 'id',
                        name: 'id',
                        className: 'text-center'
                    },
                    {
                        data: 'name',
                        name: 'name',
                        className: 'text-center'
                    },
                    {
                        data: 'team_name',
                        name: 'team_name',
                        className: 'text-center',
                        // render: (data) => {
                        //     return (data.split(' '))[0]
                        // }
                    },
                    {
                        data: 'role',
                        name: 'role',
                        className: 'text-center',
                    },
                    {
                        data: 'batting_style',
                        name: 'batting_style',
                        className: 'text-center',
                    },
                    {
                        data: 'bowling_style',
                        name: 'bowling_style',
                        className: 'text-center',
                    },
                    {
                        data: 'born',
                        name: 'born',
                        className: 'text-center',
                        render: (data) => {
                            return (data.split(' ')[0]).split('-').reverse().join("-")
                        }
                    },
                    // {
                    //     data: 'actions',
                    //     name: 'actions',
                    //     className: 'text-center'
                    // }
                ]
            });
        });
    </script>
@endsection
