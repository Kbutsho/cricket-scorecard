@extends('layouts.app')
@section('title', 'match list')
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
                <h5 class="fw-bold text-uppercase text-primary"> matches {{ $matches }}</h5>
                <h5 class="text-uppercase fw-bold text-primary">match list</h5>
                <a class="h5 fw-bold d-flex justify-content-center align-items-center text-primary"
                    href="{{ route('get.add-match') }}">
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
                        <th class="text-center">ID</th>
                        <th>Match</th>
                        <th class="text-center">Venue</th>
                        <th class="text-center">Date</th>
                        <th class="text-center">Time</th>
                        <th class="text-center">Format</th>
                        <th class="text-center">Over</th>
                        <th class="text-center">Status</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
@endsection

@section('script')
    {{-- for time formating: am - pm --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
    <script>
        $(document).ready(function() {
            let table = $('#data').DataTable({
                ajax: '{{ route('matches') }}',
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
                        data: 'team_a_name',
                        name: 'team_a_name',
                        render: function(data, type, row) {
                            return '<span>' + row.team_a_name + ' vs ' + row.team_b_name + '</span>';
                        }
                    },
                    {
                        data: 'venue',
                        name: 'venue',
                        className: 'text-center',
                    },
                    {
                        data: 'time',
                        name: 'time',
                        className: 'text-center',
                        render: (data) => {
                            return (data.split(' ')[0]).split('-').reverse().join('-')
                        }
                    },
                    {
                        data: 'time',
                        name: 'time',
                        className: 'text-center',
                        render: (data) => {
                            let time = data.split(' ')[1]
                            return moment(time, 'HH:mm:ss').format('hh:mm A');
                        }
                    },
                    {
                        data: 'format',
                        name: 'format',
                        className: 'text-center',
                    },
                    {
                        data: 'over',
                        name: 'over',
                        className: 'text-center',
                    },
                    {
                        data: 'status',
                        name: 'status',
                        className: 'text-center fw-bold',
                        createdCell: function(cell, cellData, rowData, rowIndex, colIndex) {
                            if (cellData == 'upcoming') {
                                $(cell).attr('style', 'color: red;');
                            } else if (cellData == 'ongoing') {
                                $(cell).attr('style', 'color: green;');
                            }else if (cellData == 'finished') {
                                $(cell).attr('style', 'color: blue;');
                            }
                        }
                    },

                    // {
                    //     data: 'actions',
                    //     name: 'actions',
                    //     className: 'text-center'
                    // }
                ],
                // createdRow: function(row, data, dataIndex) {
                //     let matchStartTime = moment(data.time, 'YYYY-MM-DD HH:mm:ss');
                //     if (matchStartTime.isBefore(moment())) {
                //         $(row).hide();
                //     }
                // }
            });
            setInterval(function() {
                table.ajax.reload();
            }, 10000); // 1/6 minute
        });
    </script>
@endsection
