@extends('layouts.app')
@section('title', 'venue list')
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
                <h5 class="fw-bold text-uppercase text-primary">Total {{ $venues }}</h5>
                <h5 class="text-uppercase fw-bold text-primary">venue list</h5>
                <a class="h5 fw-bold d-flex justify-content-center align-items-center text-primary"
                    href="{{ route('get.add-venue') }}">
                    <span class="text-uppercase">add </span><i class="fas fa-plus ms-2"></i>
                </a>
            </div>
            <div class="message">
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
                        <th>Venue Name</th>
                        <th>Venue Location</th>
                        <th>Venue Capacity</th>
                        <th>Created At</th>
                        <th>Updated At</th>
                        <th>Action</th>
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
                ajax: '{{ route('venues') }}',
                processing: true,
                serverSide: true,
                language: {
                    "processing": "<div class='my-5' style='height: 25vh'></div>"
                },
                lengthMenu: [10, 25, 50, 100],
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
                        data: 'location',
                        name: 'location',
                        className: 'text-center'
                    },
                    {
                        data: 'capacity',
                        name: 'capacity',
                        className: 'text-center'
                    },
                    {
                        data: 'created_at',
                        name: 'created_at',
                        className: 'text-center',
                        render: function(data) {
                            let date = new Date(data);
                            let year = date.getFullYear();
                            let month = ('0' + (date.getMonth() + 1)).slice(-2);
                            let day = ('0' + date.getDate()).slice(-2);
                            return year + '-' + month + '-' + day;
                        }
                    },
                    {
                        data: 'updated_at',
                        name: 'updated_at',
                        className: 'text-center',
                        render: function(data) {
                            let date = new Date(data);
                            let year = date.getFullYear();
                            let month = ('0' + (date.getMonth() + 1)).slice(-2);
                            let day = ('0' + date.getDate()).slice(-2);
                            return year + '-' + month + '-' + day;
                        }
                    },
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
