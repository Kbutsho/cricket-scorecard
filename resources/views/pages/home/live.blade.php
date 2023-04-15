@extends('layouts.app')
@section('title', 'live score')
@section('style')
    <!-- CSS only -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css"
        integrity="sha384-DXfcGqN3qylE6/Ikic1wzHxBvKx6pR/6LOaDyAGoUbHvAJEMqGksQPe6UZwONAYf" crossorigin="anonymous">

    <!-- JavaScript Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-/CGpa8F98W47Qc7lhNn1/VdVryGnwSSZbi2eZnMIBaHyfLg8fKThj9n1z4pQiJKR" crossorigin="anonymous">
    </script>

    <style>
        .score-card {
            padding: 20px;
            box-shadow: rgba(0, 0, 0, 0.35) 0px 2px 5px;
            width: 250px;
        }

        .extra,
        .score,
        .wkt,
        .run {
            font-size: 12px;
            font-weight: bold;
            border-radius: 100%;
            box-shadow: rgba(0, 0, 0, 0.35) 0px 1px 2px;
            height: 24px;
            width: 24px;
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 0 5px;
            transition: 0.3s;
            border: 0;
        }

        .score-box {
            border-radius: 100%;
            box-shadow: rgba(0, 0, 0, 0.35) 0px 1px 2px;
            height: 24px;
            width: 24px;
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 0 5px;
            transition: 0.3s;
            border: 0;
        }

        .extra:hover,
        .run:hover,
        .score:hover,
        .wkt:hover,
        .score-box:hover {
            cursor: pointer;
            box-shadow: rgba(0, 0, 0, 0.35) 0px 5px 8px;
        }

        span {
            font-weight: bold;
            color: red;
        }

        .squad {
            font-size: 12px;
            font-weight: bold;
            color: red;
            display: flex;
        }

        .box {
            box-shadow: rgba(0, 0, 0, 0.35) 0px 3px 8px;
            border-radius: 10px;
            transition: 0.3s;
        }

        .box:hover {
            box-shadow: rgba(0, 0, 0, 0.7) 0px 3px 8px;
        }

        #firstInnings,
        #secondInnings {
            font-size: 16px;
            font-weight: bold;
        }

        table {
            font-size: 14px;
            font-weight: bold;
        }

        .hr-style {
            background-color: #fff;
            border-top: 3px dashed #8c8b8b;
            margin: 20px 0;
        }
    </style>
@endsection

@section('content')
    <div class="container py-5">
        <div>
            <div class="alert alert-primary w-100 text-center mb-3 d-flex justify-content-between">
                <h5 class="fw-bold">{{ $firstBattingTeamName }} vs {{ $firstBowlingTeamName }}
                </h5>
                <a href="{{ route('home') }}" class="fw-bold text-danger">
                    <i class="fas fa-angle-double-left" style="font-size: 24px"></i>
                </a>
            </div>
            <div>
                @if ($inningsStatus['inningsOne'] == 2 && $inningsStatus['inningsTwo'] == 2)
                    @if ($innings1BattingScore['totalRuns'] > $innings2BattingScore['totalRuns'])
                        <div class="alert alert-success mt-3 text-center fw-bold">
                            {{ $firstBattingTeamName }} won by
                            {{ $innings1BattingScore['totalRuns'] - $innings2BattingScore['totalRuns'] }} runs
                        </div>
                    @elseif($innings1BattingScore['totalRuns'] < $innings2BattingScore['totalRuns'])
                        <div class="alert alert-success mt-3 text-center fw-bold">
                            {{ $firstBowlingTeamName }} won by {{ 10 - $innings2BattingScore['totalWickets'] }} wickets
                        </div>
                    @else
                        <div class="alert alert-success mt-3 text-center fw-bold">
                            match draw!
                        </div>
                    @endif
                @endif
            </div>
            <div class="row">
                @include('components.summary.firstInnings')
                @include('components.summary.secondInnings')
            </div>
        </div>
    </div>
    @include('layouts.footer')
@endsection

@section('script')
    <script>
        $(document).ready(function() {
            $('#toggle-btn').click(function() {
                $('#firstInnings').toggle();
                $('#secondInnings').toggle(!$('#firstInnings').is(':visible'));
                let inningsTitle = $('#innings-title').text() === "2nd innings squad" ?
                    "1st innings squad" : "2nd innings squad";
                $('#innings-title').text(inningsTitle);
                $(this).text(buttonText);
            });
        });
    </script>

    <script>
        function checkOtherFields(checkbox) {
            const formCheck = checkbox.parentNode;
            const otherCheckboxes = formCheck.querySelectorAll(
                'input[type="checkbox"]:not([name="bastman_id[]"]):not([name="bowler_id[]"])');
            otherCheckboxes.forEach((cb) => {
                if (checkbox.checked) {
                    // cb.disabled = false;
                    cb.checked = true;
                } else {
                    // cb.disabled = true;
                    cb.checked = false;
                }
            });
        }
        // remain check when add new score
        function saveSelection(checkbox, type) {
            var key = type + '_' + checkbox.value;
            if (checkbox.checked) {
                localStorage.setItem(key, 'true');

                let battingTeamId = checkbox.parentNode.querySelector('input[name="battingTeamId[]"]').value;
                localStorage.setItem(key + '_battingTeamId', battingTeamId);

                let bowlingTeamId = checkbox.parentNode.querySelector('input[name="bowlingTeamId[]"]').value;
                localStorage.setItem(key + '_bowlingTeamId', bowlingTeamId);
            } else {
                localStorage.removeItem(key);
                localStorage.removeItem(key + '_battingTeamId');
                localStorage.removeItem(key + '_bowlingTeamId');
            }
        }
        window.addEventListener('load', function() {
            let checkboxes = document.getElementsByName('batsman_id[]');
            for (let i = 0; i < checkboxes.length; i++) {
                let key = 'batsman_' + checkboxes[i].value;
                if (localStorage.getItem(key)) {
                    checkboxes[i].checked = true;
                    let battingTeamId = localStorage.getItem(key + '_battingTeamId');
                    let battingTeamIdCheckbox = checkboxes[i].parentNode.querySelector(
                        'input[name="battingTeamId[]"]');
                    battingTeamIdCheckbox.checked = true;
                    localStorage.setItem('battingTeamId_' + battingTeamId, 'true');
                }
            }
            checkboxes = document.getElementsByName('bowler_id[]');
            for (let i = 0; i < checkboxes.length; i++) {
                let key = 'bowler_' + checkboxes[i].value;
                if (localStorage.getItem(key)) {
                    checkboxes[i].checked = true;
                    let bowlingTeamId = localStorage.getItem(key + '_bowlingTeamId');
                    let bowlingTeamIdCheckbox = checkboxes[i].parentNode.querySelector(
                        'input[name="bowlingTeamId[]"]');
                    bowlingTeamIdCheckbox.checked = true;
                    localStorage.setItem('bowlingTeamId_' + bowlingTeamId, 'true');
                }
            }
        });

        // Check if at least one bowler and one batsman are selected
        // function validateSelection() {
        //     if (!$('input[name="bowler_id[]"]:checked').length && !$('input[name="batsman_id[]"]:checked').length) {
        //         swal({
        //             title: "warning",
        //             text: "Please select one bowler and one batsman",
        //             icon: "warning",
        //             button: "Ok",
        //         });
        //         return false;
        //     } else if (!$('input[name="bowler_id[]"]:checked').length) {
        //         swal({
        //             title: "warning",
        //             text: "Please select a bowler",
        //             icon: "warning",
        //             button: "Ok",
        //         });
        //         return false;
        //     } else if (!$('input[name="batsman_id[]"]:checked').length) {
        //         swal({
        //             title: "warning",
        //             text: "Please select a batsman",
        //             icon: "warning",
        //             button: "Ok",
        //         });
        //         return false;
        //     }
        //     return true;
        // }
        // $('form').on('submit', function(e) {
        //     if (!validateSelection()) {
        //         e.preventDefault();
        //     }
        // });

        function handleCheckboxClick(checkbox, type) {
            checkOtherFields(checkbox);
            saveSelection(checkbox, type);
        }
    </script>
@endsection
