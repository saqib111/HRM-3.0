@extends('layout.mainlayout')
@section('css')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
<style>
    .container {
        padding: 2rem 0rem;
    }

    @media (min-width: 576px) {
        .modal-dialog {
            max-width: 400px;

            .modal-content {
                padding: 1rem;
            }
        }
    }

    .modal-header {
        .close {
            margin-top: -1.5rem;
        }
    }

    .form-title {
        margin: -2rem 0rem 2rem;
    }

    .btn-round {
        border-radius: 3rem;
    }

    .delimiter {
        padding: 1rem;
    }

    .social-buttons {
        .btn {
            margin: 0 0.5rem 1rem;
        }
    }

    .signup-section {
        padding: 0.3rem 0rem;
    }

    .progress-container {
        position: relative;
        width: 100px;
        height: 100px;
        margin: 20px auto;
    }

    .progress-circle {
        width: 100%;
        height: 100%;
        transform: rotate(-90deg);
    }

    .progress-bar {
        transition: stroke-dashoffset 0.35s;
    }

    .time-counter {
        position: absolute;
        top: 50% !important;
        left: 55px !important;
        transform: translate(-50%, -50%);
        font-size: 15px;
        font-weight: bold;
    }

    .punch-btn-section {

        min-width: 120px;
        /* Ensures buttons have a consistent size */
        font-size: 16px;
        /* Adjust font size for readability */
        padding: 10px 20px;
        /* Adjust padding for responsiveness */
        /* For alignment fallback */
    }

    .punch-btn-section .punch-btn {
        font-size: 18px !important;
        font-weight: 600;
        max-width: 100%;
        padding: 5px 20px !important;
        border-radius: 50px;
    }




    .dt-column-order {
        display: none !important;
    }

    table.dataTable th.dt-type-numeric,
    table.dataTable th.dt-type-date,
    table.dataTable td.dt-type-numeric,
    table.dataTable td.dt-type-date {
        text-align: center !important;
    }

    .fa-solid,
    .fas {
        font-size: 20px;
    }

    .fa-times-circle {
        font-size: 20px !important;
        color: #ff0000 !important;
    }

    i.fas.fa-check-circle::before {
        color: #127c1f !important;
        font-size: 20px;
    }

    .recent-activity .res-activity-list {
        height: 180px !important;
    }
</style>
@endsection
@section('content')
<div id="notification" aria-live="polite" aria-atomic="true"></div>
<div class="page-header">
    <div class="row">
        <div class="col-sm-4">
            <h3 class="page-title">Attendance </h3>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="admin-dashboard.html">Dashboard</a></li>
                <li class="breadcrumb-item active">Attendance </li>
            </ul>
        </div>
        <div class="col-sm-8">
            <span class="mt-4 text-danger text-muted" style=""> Welcome! {{auth()->user()->username}} [
                {{auth()->user()->employee_id}} ] </span>
        </div>
    </div>
</div>
<!-- /Page Header -->
<input type="hidden" name="user_id" value="{{auth()->user()->id}}" id="userID">
<input type="hidden" name="id" value="" id="id">
<div class="row">

    <div class="col-md-4">
        <div class="card punch-status">
            <div class="card-body">
                <h5 class="card-title">Timesheet <small class="text-muted" id="punchDate">{{date('dS M Y')}}</small>
                </h5>
                <div class="punch-det">
                    <h6>Punch In at</h6>
                    <p id="punchInTime">--</p>
                </div>
                <div class="progress-container">
                    <svg class="progress-circle" viewBox="0 0 110 110">
                        <circle cx="55" cy="55" r="50" fill="none" stroke="#eee" stroke-width="8"></circle>
                        <circle class="progress-bar" cx="55" cy="55" r="50" stroke-dasharray="314"
                            stroke-dashoffset="314" fill="none" stroke="#3498db" stroke-width="8"
                            stroke-linecap="round"></circle>
                    </svg>
                    <div class="time-counter" id="timeCounter">0:00 hrs</div>
                </div>
                <div class="punch-btn-section d-flex justify-content-center mt-3">
                    <button id="punchInBtn" class="btn btn-primary punch-btn mx-2">Punch In</button>
                    <button id="punchOutBtn" class="btn btn-primary punch-btn mx-2" disabled>Punch Out</button>
                </div>

            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card att-statistics">
            <div class="card-body">
                <h5 class="card-title">Statistics</h5>
                <div class="stats-list">

                    <div class="stats-info">
                        <p>Late <strong><span id="day"></span> </strong></p>

                    </div>


                    <div class="stats-info">
                        <p>Absent Fine<strong> <small><span id="absent_fine"> </span></small></strong></p>

                    </div>

                    <div class="stats-info">
                        <p>Late Fine<strong> <small><span id="late_fine"> </span></small></strong></p>
                    </div>

                    <div class="stats-info">
                        <p>Total Deduction <strong> <small><span id="total"> </span></small></strong></p>

                    </div>


                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card recent-activity">
            <div class="card-body">
                <h5 class="card-title">Today Activity</h5>
                <ul class="res-activity-list">
                    <li>
                        <p class="mb-0">Punch In at</p>
                        <p class="res-activity-time">
                            <i class="fa-regular fa-clock"></i>
                            <span id="check_in"></span>
                        </p>
                    </li>
                    <li>
                        <p class="mb-0">Punch Out at</p>
                        <p class="res-activity-time">
                            <i class="fa-regular fa-clock"></i>
                            <span id="check_out"></span>
                        </p>
                    </li>

                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Search Filter -->
<form method="post" id="searchData">
    @csrf
    <div class="row filter-row mt-5">
        <div class="col-sm-5">
            <label for=""> From date: </label>
            <div class="input-block mb-3 form-focus">
                <div class="cal-icon">
                    <input type="text" class="form-control floating datetimepicker" id="fromDate" name="fromDate">
                </div>
                <label class="focus-label">Date</label>
            </div>
        </div>
        <div class="col-sm-5">
            <label for=""> To date: </label>
            <div class="input-block mb-3 form-focus">
                <div class="cal-icon">
                    <input type="text" class="form-control floating datetimepicker" id="toDate" name="toDate">
                </div>
                <label class="focus-label">Date</label>
            </div>
        </div>

        <div class="col-sm-2  d-flex align-items-center">
            <button type="button" class="btn btn-primary me-2" id="refresh" onclick="refreshDate()">
                <i class="fa fa-refresh" aria-hidden="true"></i>
            </button>
            <button class="btn btn-primary" type="submit" id="submitButton"> Search </button>
        </div>
    </div>
</form>
<div class="row">
    <div class="col-lg-12">
        <div class="table-responsive">
            <table class="table  custom-table mb-0" id="attendance-employee">
                <thead>

                </thead>
                <tbody>

                </tbody>
            </table>
        </div>
    </div>
</div>

<!--Login Modal start-->


<div class="modal fade " id="loginModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content ">
            <div class="modal-header border-bottom-0">

            </div>
            <div class="modal-body ">
                <div class="form-title text-center">
                    <h4>Verify Employee</h4>
                </div>
                <div class="d-flex flex-column text-center ">
                    <form id="emp-check" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="form-group mb-4">
                            <input type="password" class="form-control" id="password" placeholder="Password..."
                                autocomplete="one-time-code">
                        </div>
                        <button
                            class="btn btn-info btn-block btn-round ">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Verify&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</button>
                    </form>



                    </di>
                </div>
            </div>
        </div>

    </div>
</div>
</div>

<!--End -->

<!-- PreLoader -->
<div id="loader" class="loader" style="display: none;">
    <div class="loader-animation"></div>
</div>

@endsection
@section('script-z')  
<!-- <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script> -->


<script>

    $(document).ready(function () {
        $("#fromDate").empty();
        $("#toDate").empty();

        statistics();
        dataTab();
    });

    function dataTab() {
        var date = moment();
        var format = date.format("D MMMM Y");

        $.ajax({
            url: "{{ route('attendance.detail') }}",
            type: 'GET',

            success: function (response) {
                console.log(response)

                $('#attendance-employee').DataTable({
                    destroy: true,
                    pageLength: 31,
                    data: response.data,

                    columns: [
                        {
                            data: 'verify1',
                            title: 'Verify',
                            render: function (data, type, row) {
                                let result = '';

                                if (row.verify1 == "yes") {
                                    result = `<i class="fas fa-check-circle"></i>`;
                                } else if (row.verify1 == "cross") {
                                    result = `<i class="fa fa-times-circle wt"></i>`;
                                } else {
                                    result = "No Record";
                                }
                                return `<div class="text-center">${result}</div>`;
                            }
                        },

                        {
                            data: 'DT_RowIndex',
                            orderable: false,
                            searchable: false,
                            render: function (data, type, row, meta) {
                                return meta.row + 1;
                            },
                            title: '#'
                        },
                        { data: 'start_date', title: 'Start Date' },
                        { data: 'shift_in', title: 'Shift In' },
                        { data: 'end_date', title: 'End Date' },
                        { data: 'shift_out', title: 'Shift Out' },
                        { data: 'in_date', title: 'Checkin Date' },

                        {
                            data: 'check_in',
                            title: 'Check In',
                            render: function (data, type, row) {
                                if (row.check_in == null) {

                                    return " ";
                                } else {
                                    return data;
                                }
                            }
                        },
                        { data: 'out_date', title: 'Checkout Date' },
                        {
                            data: 'check_out',
                            title: 'Check Out',
                            render: function (data, type, row) {
                                if (row.check_out == null) {
                                    return " ";
                                } else {
                                    return data;
                                }
                            }
                        },
                        {
                            data: 'duty_hours',
                            title: 'Duty Hours',
                            render: function (data, type, row) {
                                if (row.dayoff === "Yes") {
                                    return "OFF";
                                } else {
                                    if (row.color == '1') { return "AL"; }
                                    else if (row.color == '2') { return "BL"; }
                                    else if (row.color == '3') { return "ML"; }
                                    else if (row.color == '4') { return "UL"; }
                                    else if (row.color == '5') { return "HL"; }
                                    else if (row.color == '6') { return "CL"; }
                                    else if (row.color == '7') { return "MTL"; }
                                    else if (row.color == '8') { return "PL"; }
                                    else if (row.absent == "Yes") { return "Absent"; }
                                    else { return data; }
                                }
                            }
                        },

                        {
                            data: 'verify2',
                            title: 'Verify',
                            render: function (data, type, row) {
                                let result = '';

                                if (row.verify2 === "yes") {
                                    result = `<i class="fas fa-check-circle"></i>`;
                                } else if (row.verify2 === "cross") {
                                    result = `<i class="fa fa-times-circle wt"></i>`;
                                } else {
                                    result = "No Record";
                                }

                                return `<div class="text-center">${result}</div>`;
                            }

                        },

                    ], order: [],
                    createdRow: function (row, data, dataIndex) {

                        const leaveColumns = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10];

                        if (data.dayoff === "Yes") {
                            leaveColumns.forEach(function (colIndex) {
                                $(row).find('td').eq(colIndex).css({
                                    'background-color': '#767D83',   //Grey
                                    'color': 'white'
                                });
                            });
                        }

                        else if (data.color === '1') {
                            leaveColumns.forEach(function (colIndex) {
                                $(row).find('td').eq(colIndex).css({
                                    'background-color': '#1B7EC1', //Blue
                                    'color': 'white'
                                });
                            });
                        }

                        else if (data.color === '2') {
                            leaveColumns.forEach(function (colIndex) {
                                $(row).find('td').eq(colIndex).css({
                                    'background-color': '#138744', //Green
                                    'color': 'white'
                                });
                            });
                        }

                        else if (data.color === '3') {
                            leaveColumns.forEach(function (colIndex) {
                                $(row).find('td').eq(colIndex).css({
                                    'background-color': '#E3005F', //Pink
                                    'color': 'white'
                                });
                            });
                        }

                        else if (data.color === '4') {
                            leaveColumns.forEach(function (colIndex) {
                                $(row).find('td').eq(colIndex).css({
                                    'background-color': '#FF002E', //LightRed
                                    'color': 'white'
                                });
                            });
                        }

                        else if (data.color === '5') {
                            leaveColumns.forEach(function (colIndex) {
                                $(row).find('td').eq(colIndex).css({
                                    'background-color': '#b5a202', // Yellow
                                    'color': 'white'
                                });
                            });
                        }

                        else if (data.color === '6') {
                            leaveColumns.forEach(function (colIndex) {
                                $(row).find('td').eq(colIndex).css({
                                    'background-color': '#b5a202', //
                                    'color': 'white'
                                });
                            });
                        }

                        else if (data.color === '7') {
                            leaveColumns.forEach(function (colIndex) {
                                $(row).find('td').eq(colIndex).css({
                                    'background-color': '#b5a202',
                                    'color': 'white'
                                });
                            });
                        }

                        else if (data.color === '8') {
                            leaveColumns.forEach(function (colIndex) {
                                $(row).find('td').eq(colIndex).css({
                                    'background-color': '#b5a202',
                                    'color': 'white'
                                });
                            });
                        }

                        else if (data.absent === "Yes") {
                            leaveColumns.forEach(function (colIndex) {
                                $(row).find('td').eq(colIndex).css({
                                    'background-color': '#870501',
                                    'color': 'white'
                                });
                            });
                            $(row).addClass("absent");
                        }

                        else if (Date.parse(data.start_date) == Date.parse(format) && data.dayoff !== "Yes") {
                            if (data.shift_in < data.check_in || data.shift_out > data.check_out) {
                                leaveColumns.forEach(function (colIndex) {
                                    $(row).find('td').eq(colIndex).css({
                                        'background-color': '#A90500',
                                        'color': 'white'
                                    });
                                });
                                $(row).addClass("late");
                            }
                            else {
                                $(row).addClass("today");
                            }
                        }

                        else if (data.shift_in < data.check_in || data.shift_out > data.check_out) {
                            leaveColumns.forEach(function (colIndex) {
                                $(row).find('td').eq(colIndex).css({
                                    'background-color': '#A90500',
                                    'color': 'white'
                                });
                            });
                            $(row).addClass("late");
                        }
                    },

                });

            },
        });

    }
    $('#searchData').on('submit', function (event) {
        event.preventDefault();
        var date = moment();
        var format = date.format("D MMMM Y");
        var fromDate = $('#fromDate').val();
        var toDate = $('#toDate').val();
        if ((fromDate != "") && (toDate != "")) {
            var formData = new FormData();
            formData.append('from_date', fromDate);
            formData.append('to_date', toDate);
            showLoader();
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                url: "{{ route('search.record') }}",
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function (response) {
                    hideLoader();
                    var inf = response.in;
                    console.log(inf)
                    $('#day').empty();
                    $('#absent_fine').empty();
                    $('#late_fine').empty();
                    $('#total').empty();


                    $('#day').append(`${inf[0].day}  Days `);
                    $('#absent_fine').append(`${inf[0].absent_fine}% `);
                    $('#late_fine').append(`${inf[0].late_fine}% `);
                    $('#total').append(`${inf[0].total}% `);


                    $('#attendance-employee').DataTable({
                        destroy: true,
                        pageLength: 31,
                        data: response.data,

                        columns: [

                            {
                                data: 'verify1',
                                title: 'Verify',
                                render: function (data, type, row) {
                                    let result = '';

                                    if (row.verify1 == "yes") {
                                        result = `<i class="fas fa-check-circle"></i>`;
                                    } else if (row.verify1 == "cross") {
                                        result = `<i class="fa fa-times-circle wt"></i>`;
                                    } else {
                                        result = "No Record";
                                    }
                                    return `<div class="text-center">${result}</div>`;
                                }
                            },

                            {
                                data: 'DT_RowIndex',
                                orderable: false,
                                searchable: false,
                                render: function (data, type, row, meta) {
                                    return meta.row + 1;
                                },
                                title: '#'
                            },
                            { data: 'start_date', title: 'Start Date' },
                            { data: 'shift_in', title: 'Shift In' },
                            { data: 'end_date', title: 'End Date' },
                            { data: 'shift_out', title: 'Shift Out' },
                            { data: 'in_date', title: 'Checkin Date' },
                            {
                                data: 'check_in',
                                title: 'Check In',
                                render: function (data, type, row) {
                                    if (row.check_in == null) {

                                        return " ";
                                    } else {
                                        return data;
                                    }
                                }
                            },
                            { data: 'out_date', title: 'Checkout Date' },
                            {
                                data: 'check_out',
                                title: 'Check Out',
                                render: function (data, type, row) {
                                    if (row.check_out == null) {
                                        return " ";
                                    } else {
                                        return data;
                                    }
                                }
                            },
                            {
                                data: 'duty_hours',
                                title: 'Duty Hours',
                                render: function (data, type, row) {
                                    if (row.dayoff === "Yes") {
                                        return "OFF";
                                    } else {
                                        if (row.color == '1') { return "AL"; }
                                        else if (row.color == '2') { return "BL"; }
                                        else if (row.color == '3') { return "ML"; }
                                        else if (row.color == '4') { return "UL"; }
                                        else if (row.color == '5') { return "HL"; }
                                        else if (row.color == '6') { return "CL"; }
                                        else if (row.color == '7') { return "MTL"; }
                                        else if (row.color == '8') { return "PL"; }
                                        else if (row.absent == "Yes") { return "Absent"; }
                                        else { return data; }
                                    }
                                }
                            },

                            {
                                data: 'verify2',
                                title: 'Verify',
                                render: function (data, type, row) {
                                    let result = '';

                                    if (row.verify2 === "yes") {
                                        result = `<i class="fas fa-check-circle"></i>`;
                                    } else if (row.verify2 === "cross") {
                                        result = `<i class="fa fa-times-circle wt"></i>`;
                                    } else {
                                        result = "No Record";
                                    }

                                    return `<div class="text-center">${result}</div>`;
                                }
                            },


                        ], order: [],
                        createdRow: function (row, data, dataIndex) {

                            const leaveColumns = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10];

                            if (data.dayoff === "Yes") {
                                leaveColumns.forEach(function (colIndex) {
                                    $(row).find('td').eq(colIndex).css({
                                        'background-color': '#767D83',   //Grey
                                        'color': 'white'
                                    });
                                });
                            }

                            else if (data.color === '1') {
                                leaveColumns.forEach(function (colIndex) {
                                    $(row).find('td').eq(colIndex).css({
                                        'background-color': '#1B7EC1', //Blue
                                        'color': 'white'
                                    });
                                });
                            }

                            else if (data.color === '2') {
                                leaveColumns.forEach(function (colIndex) {
                                    $(row).find('td').eq(colIndex).css({
                                        'background-color': '#138744', //Green
                                        'color': 'white'
                                    });
                                });
                            }

                            else if (data.color === '3') {
                                leaveColumns.forEach(function (colIndex) {
                                    $(row).find('td').eq(colIndex).css({
                                        'background-color': '#E3005F', //Pink
                                        'color': 'white'
                                    });
                                });
                            }

                            else if (data.color === '4') {
                                leaveColumns.forEach(function (colIndex) {
                                    $(row).find('td').eq(colIndex).css({
                                        'background-color': '#FF002E', //LightRed
                                        'color': 'white'
                                    });
                                });
                            }

                            else if (data.color === '5') {
                                leaveColumns.forEach(function (colIndex) {
                                    $(row).find('td').eq(colIndex).css({
                                        'background-color': '#b5a202', // Yellow
                                        'color': 'white'
                                    });
                                });
                            }

                            else if (data.color === '6') {
                                leaveColumns.forEach(function (colIndex) {
                                    $(row).find('td').eq(colIndex).css({
                                        'background-color': '#b5a202', //
                                        'color': 'white'
                                    });
                                });
                            }

                            else if (data.color === '7') {
                                leaveColumns.forEach(function (colIndex) {
                                    $(row).find('td').eq(colIndex).css({
                                        'background-color': '#b5a202',
                                        'color': 'white'
                                    });
                                });
                            }

                            else if (data.color === '8') {
                                leaveColumns.forEach(function (colIndex) {
                                    $(row).find('td').eq(colIndex).css({
                                        'background-color': '#b5a202',
                                        'color': 'white'
                                    });
                                });
                            }

                            else if (data.absent === "Yes") {
                                leaveColumns.forEach(function (colIndex) {
                                    $(row).find('td').eq(colIndex).css({
                                        'background-color': '#870501',
                                        'color': 'white'
                                    });
                                });
                                $(row).addClass("absent");
                            }

                            else if (Date.parse(data.start_date) == Date.parse(format) && data.dayoff !== "Yes") {
                                if (data.shift_in < data.check_in || data.shift_out > data.check_out) {
                                    leaveColumns.forEach(function (colIndex) {
                                        $(row).find('td').eq(colIndex).css({
                                            'background-color': '#A90500',
                                            'color': 'white'
                                        });
                                    });
                                    $(row).addClass("late");
                                }
                                else {
                                    $(row).addClass("today");
                                }
                            }

                            else if (data.shift_in < data.check_in || data.shift_out > data.check_out) {
                                leaveColumns.forEach(function (colIndex) {
                                    $(row).find('td').eq(colIndex).css({
                                        'background-color': '#A90500',
                                        'color': 'white'
                                    });
                                });
                                $(row).addClass("late");
                            }
                        },

                    });

                },

                error: function (err) {
                    hideLoader();

                    createToast('error', 'fa-solid fa-circle-exclamation', 'Error', 'Wrong Date selected.last 3 months data Only');
                }
            });



        }


    });
</script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const punchInBtn = document.getElementById('punchInBtn');
        const punchOutBtn = document.getElementById('punchOutBtn');
        const punchInTimeDisplay = document.getElementById('punchInTime');
        const timeCounter = document.getElementById('timeCounter');
        const progressCircle = document.querySelector('.progress-bar');
        let punchInTime = null;
        let shiftDuration = 9;
        let intervalId = null;
        let endShift = null;
        let shitFinish = null;
        fetch('/get-punch-time')
            .then(response => response.json())
            .then(data => {
                console.log(data)
                if (data.punch_in_time == "nothing") {

                    clearInterval(intervalId);
                    punchInTime = null;
                    punchInTimeDisplay.textContent = '';
                    timeCounter.textContent = '0:00 hrs';
                    progressCircle.style.strokeDashoffset = 314;


                    punchOutBtn.style.display = 'none';
                    punchInBtn.style.display = 'none';
                    $('.punch-btn-section').append(

                        `<div class="btn btn-primary punch-btn"> No Schedule</div> `
                    );

                } else if (data.punch_in_time == 'show') {

                    clearInterval(intervalId);
                    punchInTime = null;
                    punchInTimeDisplay.textContent = '';
                    timeCounter.textContent = '0:00 hrs';
                    progressCircle.style.strokeDashoffset = 314;


                    punchOutBtn.style.display = 'none';
                    punchInBtn.style.display = 'block';
                }
                else {
                    punchInTime = new Date(data.punch_in_time);
                    endShift = new Date(data.punch_in_time).setMinutes(0, 0, 0);
                    shiftFinish = new Date(data.shiftEnd);
                    shiftDuration = data.shift_duration || shiftDuration;

                    punchInBtn.style.display = 'none';
                    punchOutBtn.style.display = 'block';
                    punchOutBtn.disabled = true;

                    punchInTimeDisplay.textContent = moment(punchInTime).format('dddd, DD MMM YYYY HH:mm:ss A');


                    startShiftProgress(true);

                }
            });



        // punchInBtn.addEventListener('click', punchIn);
        punchInBtn.addEventListener('click', function () {
            var checkIn = 'checkIn';
            employeeCheck(checkIn);

        });

        function employeeCheck(data) {

            password = $('#password').val('');

            clearValidationStates();
            valdateCancel();

            $('#loginModal').modal('show');
            $('#emp-check').on('submit', function (event) {
                event.preventDefault();

                var password = $('#password').val();

                var formData = new FormData();

                formData.append('password', password);
                var isValid = true;
                clearValidationStates();

                if (!validateField('#password')) isValid = false;
                if (isValid) {
                    showLoader();
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });
                    $.ajax({
                        url: '{{route('check.emp')}}',
                        type: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function (response) {

                            //$('#loginModal').modal('hide');
                            hideLoader();
                            if (response.status == 'success') {
                                $('#loginModal').modal('hide');
                                if (data == "checkIn") { punchIn(); }
                                if (data == "checkOut") { punchOut(); }

                            }
                            else {
                                valdateCancel();
                                clearValidationStates();

                                $('#password').addClass('is-invalid');
                                createToast('error', 'fa-solid fa-circle-exclamation', 'Error', 'Invalid User.');
                            }


                        },
                        error: function (error) {
                            hideLoader();
                            createToast('error', 'fa-solid fa-circle-exclamation', 'Error', 'Invalid User.');
                        }
                    });

                }

            });

        }


        function punchIn() {
            fetch('/punch-in', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                }
            })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        statistics();
                        dataTab();

                        punchInTime = new Date(data.punch_in_time);
                        punchInTimeDisplay.textContent = moment(punchInTime).format('dddd, DD MMM YYYY HH:mm:ss A');

                        punchInBtn.style.display = 'none';
                        punchOutBtn.style.display = 'block';
                        punchOutBtn.disabled = true;
                        startShiftProgress(true);



                    }
                    else {

                        clearInterval(intervalId);
                        punchInTime = null;
                        punchInTimeDisplay.textContent = '';
                        timeCounter.textContent = '0:00 hrs';
                        progressCircle.style.strokeDashoffset = 314;


                        punchOutBtn.style.display = 'none';
                        punchInBtn.style.display = 'block';
                    }
                });
        }



        function startShiftProgress(initial = false) {

            function updateProgress() {
                const now = new Date();
                const elapsedTimeInMinutes = Math.floor((now - punchInTime) / (60 * 1000));
                const elapsedTimeInHours = elapsedTimeInMinutes / 60;

                const hours = Math.floor(elapsedTimeInHours);
                const minutes = Math.floor((elapsedTimeInHours - hours) * 60);
                timeCounter.textContent = `${hours}:${String(minutes).padStart(2, '0')} hrs`;


                const progressPercentage = Math.min((elapsedTimeInMinutes / (shiftDuration * 60)) * 100, 100);
                progressCircle.style.strokeDashoffset = 314 - (314 * progressPercentage) / 100;

                const shiftEndTime = new Date(endShift + shiftDuration * 60 * 60 * 1000);
                const shiftEnding = new Date(shiftFinish + shiftDuration * 60 * 60 * 1000);
                if (now >= shiftEnding) {
                    punchOutBtn.disabled = false;
                    clearInterval(intervalId);
                }
            }


            if (initial) updateProgress();


            intervalId = setInterval(updateProgress, 60000);
        }

        punchOutBtn.addEventListener('click', function () {
            var checkOut = "checkOut";
            employeeCheck(checkOut);

        });

        function punchOut() {
            fetch('/punch-out', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                }
            })
                .then(response => response.json())
                .then(data => {

                    if (data.status == 'success') {
                        statistics();
                        dataTab();
                        clearInterval(intervalId);
                        punchInTime = null;
                        punchInTimeDisplay.textContent = '';
                        timeCounter.textContent = '0:00 hrs';
                        progressCircle.style.strokeDashoffset = 314;


                        punchOutBtn.style.display = 'none';
                        punchInBtn.style.display = 'block';

                    }
                });
        }
    });
    function refreshDate() {
        $('#fromDate').val('');
        $('#toDate').val('');
        statistics();
        dataTab();

    }
    function statistics() {

        fetch('/statistics', {
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            }
        })
            .then(response => response.json())
            .then(data => {
                var inf = data.info[0];

                $('#day').empty();
                $('#absent_fine').empty();
                $('#late_fine').empty();
                $('#total').empty();



                if (data.status == 'success') {

                    $('#day').append(`${inf.day}Days`);
                    $('#absent_fine').append(`${inf.absent_fine}%`);
                    $('#late_fine').append(`${inf.late_fine}%`);
                    $('#total').append(`${inf.total}%`);
                    $('#check_in').append(`${inf.check_in}.`);
                    $('#check_out').append(`${inf.check_out}.`);
                }

            });
    }
    function clearValidationStates() {
        $('.form-control').removeClass('is-invalid is-valid');
        $('.text-danger').remove();

    }
    function valdateCancel() {

        $('#email').removeClass('is-invalid is-valid');
        $('#password').removeClass('is-invalid is-valid');

        $('.text-danger').remove();
    }
    function validateField(selector, fieldName) {
        let value = $(selector).val();
        let parent = $(selector).closest('.input-block');
        parent.find('.text-danger').remove();

        if (!value) {
            $(selector).addClass('is-invalid');
            parent.append(`<span class="text-danger">${fieldName} field cannot be empty.</span>`);
            return false;
        } else {
            $(selector).removeClass('is-invalid').addClass('is-valid');
            return true;
        }
    }
    function validateEmail(selector) {
        let email = $(selector).val();
        let regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        let parent = $(selector).closest('.input-block');
        parent.find('.text-danger').remove();

        if (!email || !regex.test(email)) {
            $(selector).addClass('is-invalid');
            parent.append(`<span class="text-danger">Invalid email address.</span>`);
            return false;
        } else {
            $(selector).removeClass('is-invalid').addClass('is-valid');
            return true;
        }
    }
</script>



</body>

</html>


@endsection