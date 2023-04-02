@extends('layouts.app')
@section('title', 'player list')
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
    <div class="container">
        <div>
            <div class="d-flex justify-content-between mt-5 mb-3 alert alert-primary">
                <h5 class="fw-bold text-uppercase text-primary"> player {{ $players }}</h5>
                <h5 class="text-uppercase fw-bold text-primary">player list</h5>
                <a class="h5 fw-bold d-flex justify-content-center align-items-center text-primary"
                    href="{{ route('get.add-player') }}">
                    <span class="text-uppercase">add</span><i class="fas fa-plus ms-2"></i>
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
                        {{-- <th>Biography</th>
                        <th>Created at</th>
                        <th>Updated at</th> --}}
                        <th>Action</th>
                    </tr>
                </thead>
            </table>
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
        $(document).ready(function() {
            $('#data').DataTable({
                ajax: '{{ route('players') }}',
                processing: true,
                serverSide: true,
                language: {
                    "processing": "<div class='my-5' style='height: 25vh'></div>"
                },
                lengthMenu: [10, 25, 50, 100],
                initComplete: function() {
                    console.log(this.api().ajax.json());
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
                        render: (data)=>{
                            return (data.split(' '))[0]
                        }
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
                        render: (data)=>{
                            return (data.split(' ')[0]).split('-').reverse().join("-")
                        }
                    },
                    // {
                    //     data: 'biography',
                    //     name: 'biography',
                    //     className: 'text-center',
                    // },

                    // {
                    //     data: 'created_at',
                    //     name: 'created_at',
                    //     className: 'text-center',
                    //     render: function(data) {
                    //         let date = new Date(data);
                    //         let year = date.getFullYear();
                    //         let month = ('0' + (date.getMonth() + 1)).slice(-2);
                    //         let day = ('0' + date.getDate()).slice(-2);
                    //         return year + '-' + month + '-' + day;
                    //     }
                    // },
                    // {
                    //     data: 'updated_at',
                    //     name: 'updated_at',
                    //     className: 'text-center',
                    //     render: function(data) {
                    //         let date = new Date(data);
                    //         let year = date.getFullYear();
                    //         let month = ('0' + (date.getMonth() + 1)).slice(-2);
                    //         let day = ('0' + date.getDate()).slice(-2);
                    //         return year + '-' + month + '-' + day;
                    //     }
                    // },
                    {
                        data: 'actions',
                        name: 'actions',
                        className: 'text-center'
                    }
                ]
            });
        });
    </script>
@endsection
