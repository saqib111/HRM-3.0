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

    .main-employee-title {
        display: flex;
        gap: 20px
    }

    .employee-title {
        font-size: 20px;
        font-weight: 600;
        display: flex;
        align-items: center;
        color: rgb(56, 53, 53);
        font-family: sans-serif;
    }

    .deduction {
        max-width: 70%;
    }

    .modal-open .modal-backdrop {
        backdrop-filter: blur(4px);
        background-color: rgba(0, 0, 0, 0.5);
        opacity: 1 !important;
    }
</style>
@endsection
@section('content')
<div id="notification" aria-live="polite" aria-atomic="true"></div>
<div class="page-header">
    <div class="row">
        <div class="col-sm-12">
            <div class="main-employee-title">
                <h3 class="page-title">Attendance </h3>
                <div class="employee-title">( {{auth()->user()->employee_id}} | {{auth()->user()->username}} )</div>
            </div>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="admin-dashboard.html">Dashboard</a></li>
                <li class="breadcrumb-item active">Attendance </li>
            </ul>
        </div>
    </div>
</div>
<!-- /Page Header -->
<input type="hidden" name="user_id" value="{{auth()->user()->id}}" id="userID">
<input type="hidden" name="id" value="" id="id">
<div class="row">
    <div class="col-md-4">
        <div id="timesheetSection">
            @include('partials.timesheet')
        </div>
    </div>

    <div class="col-md-4">
        <div class="card att-statistics">
            <div class="card-body">
                <h5 class="card-title">Salary Deduction</h5>
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
                    <div class="deductions text-center p-0 mt-3 mb-0">
                        <button class="btn btn-danger btn-sm" id="deductions_btn"
                            style="background: #a90500 !important,">View Deduction Details</button>
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

<!-- TABLE STARTS -->
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
<!-- TABLE END -->

<!--Login Modal start-->
<div class="modal fade " id="PunchInModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content ">
            <div class="modal-header border-bottom-0">

            </div>
            <div class="modal-body ">
                <div class="form-title text-center">
                    <h4 id="verify_heading">Verify Employee</h4>
                    <p class="text-muted mt-3" id="emergency_text"></p>
                </div>
                <div class="d-flex flex-column text-center ">
                    <form id="verify_user" method="POST">
                        <div class="form-group mb-4">
                            <input type="password" class="form-control" id="password" placeholder="Password...">
                            <div id="display_error" class="text-danger mt-2" style="display: none; text-align: left;">
                                Please enter password
                            </div>
                        </div>
                        <button type="submit"
                            class="btn btn-info btn-block btn-round ">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Verify&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
</div>
<!--End -->


<!-- DEDUCTION DETAILS MODAL STARTS -->
<div class="modal fade mt-4" id="deduction_modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl deduction">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5 text-center" id="exampleModalLabel">Salary Deduction Details</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">X</button>
            </div>
            <div class="modal-body pt-0">
                <div class="table-responsive">
                    <table class="table mt-0" id="deduction_table">
                        <thead class="sticky-top" style="background-color: #f8f9fa;">
                            <tr>
                                <th>Start Date</th>
                                <th>Shift In</th>
                                <th>End Date</th>
                                <th>Shift Out</th>
                                <th>CheckIn Date</th>
                                <th>Check In</th>
                                <th>CheckOut Date</th>
                                <th>Check Out</th>
                                <th>Duty Hours</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- <tr>
                       DYNAMIC DATA FROM THE AJAX
                    </tr> -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- DEDUCTION DETAILS MODAL ENDS -->

<!-- PreLoader -->
<div id="loader" class="loader" style="display: none;">
    <div class="loader-animation"></div>
</div>

@endsection
@section('script-z')  

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
                                    else if (row.color == '7') { return "MTRL"; }
                                    else if (row.color == '8') { return "PTRL"; }
                                    else if (row.dayoff === "PH") { return "Public Holiday"; }
                                    else if (row.dayoff === "BT") { return "Business Trip"; }
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
                    pageLength: 31, // Set the default number of records to show
                    lengthMenu: [10, 25, 31, 50, 100], // Options for records per page
                    createdRow: function (row, data, dataIndex) {

                        const leaveColumns = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10];

                        if (data.dayoff === "Yes" || data.dayoff === "PH" || data.dayoff === "BT") {
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
                                        else if (row.color == '7') { return "MTRL"; }
                                        else if (row.color == '8') { return "PTRL"; }
                                        else if (row.dayoff === "PH") { return "Public Holiday"; }
                                        else if (row.dayoff === "BT") { return "Business Trip"; }
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

                            if (data.dayoff === "Yes" || data.dayoff === "PH" || data.dayoff === "BT") {
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
</script>
<script>
    $(document).ready(function () {
        attachEventListeners(); // Ensure initial event binding


        let isRequestInProgress = false; // Prevent multiple requests
        let punchAction = ''; // Track whether it's "Punch IN" or "Punch OUT"

        function reloadTimesheet() {
            $.ajax({
                url: '{{ route('reload.timesheet') }}',
                type: 'GET',
                success: function (response) {
                    $('#timesheetSection').html(response); // Replace timesheet content
                },
                error: function () {
                    console.error("Failed to reload timesheet.");
                }
            });
        }

        // 📌 Function to Attach Event Listeners After Content Reload
        function attachEventListeners() {
            console.log("🔄 Attaching event listeners after reload...");

            // ✅ Use event delegation to bind event handlers to dynamically loaded elements
            $(document).off('click', '#punchInBtn').on('click', '#punchInBtn', function () {
                openVerifyModal("Punch IN");
            });

            $(document).off('click', '#punchOutBtn').on('click', '#punchOutBtn', function () {
                openVerifyModal("Punch OUT");
            });

            $(document).off('click', '#emergencyCheckOutBtn').on('click', '#emergencyCheckOutBtn', function () {
                openVerifyModal("Emergency Punch OUT");
            });
        }


        // 📌 Ensure `openVerifyModal` is globally accessible
        window.openVerifyModal = function (action) {
            punchAction = action;
            $('#PunchInModal').modal('show');

            if (punchAction === "Emergency Punch OUT") {
                $('#verify_heading').html('<i class="fa-solid fa-triangle-exclamation" style="color: #f50a0a;"></i><span style="color: #f50a0a;" class="ms-2 fs-5">Warning!</span>');
                $('#emergency_text').html("<span style='color: #f50a0a;'>Emergency checkout</span> may have a salary deduction according to company policies!");
            } else {
                $('#verify_heading').html("Verify Employee");
                $('#emergency_text').html("");
            }
        };

        const punchInBtn = document.getElementById('punchInBtn');
        const punchOutBtn = document.getElementById('punchOutBtn');
        const punchInTimeDisplay = document.getElementById('punchInTime');
        const timeCounter = document.getElementById('timeCounter');
        const progressCircle = document.querySelector('.progress-bar');
        let punchInTime = null;
        let shiftDuration = 9;
        let intervalId = null;
        let endShift = null;
        let shiftFinish = null;
        // Fetch Punch IN/OUT status on page load
        fetch('/get-punch-time')
            .then(response => response.json())
            .then(data => {
                if (data.punch_in_time == "nothing") {
                    clearInterval(intervalId);
                    punchInTime = null;
                    punchInTimeDisplay.textContent = '';
                    timeCounter.textContent = '0:00 hrs';
                    progressCircle.style.strokeDashoffset = 314;

                    punchInBtn.style.display = 'none';
                    $('.punch-btn-section').append(
                        `<div class="btn btn-primary punch-btn"> No Schedule</div>`
                    );
                } else if (data.punch_in_time == 'show') {
                    clearInterval(intervalId);
                    punchInTime = null;
                    punchInTimeDisplay.textContent = '';
                    timeCounter.textContent = '0:00 hrs';
                    progressCircle.style.strokeDashoffset = 314;
                } else {
                    punchInTime = new Date(data.punch_in_time);
                    shiftFinish = new Date(data.shiftEnd);
                    shiftDuration = data.shift_duration || shiftDuration;
                    punchInTimeDisplay.textContent = moment(punchInTime).format('dddd, DD MMM YYYY HH:mm:ss A');
                    startShiftProgress(true);
                }
            });

        // 📌 Handle Password Verification and AJAX requests for both Punch IN & Punch OUT
        $('#verify_user').submit(function (e) {
            e.preventDefault();

            if (isRequestInProgress) return; // Prevent multiple submissions

            let password = $('#password').val();
            let isValid = password !== "";

            // Show validation error if password is empty
            if (!isValid) {
                $('#display_error').show();
                $('#password').css("border", "1px solid red");
                return;
            } else {
                $('#display_error').hide();
                $('#password').css("border", "");

            }

            isRequestInProgress = true; // Block further requests
            showLoader();
            $('#verify_user button[type="submit"]').prop('disabled', true); // Disable submit button

            // 🔹 First AJAX: Password Verification
            $.ajax({
                url: '{{ route('check.emp') }}',
                type: 'POST',
                data: {
                    password: password,
                    _token: $('meta[name="csrf-token"]').attr('content') // CSRF Token
                },
                success: function (response) {
                    if (response.status === 'success') {
                        $('#PunchInModal').modal('hide'); // Close modal on success

                        var url = "";
                        if (punchAction === "Punch IN") {
                            url = "{{route('punchInAttendance')}}";
                        } else if (punchAction === "Punch OUT") {
                            url = "{{route('punchOutAttendance')}}";
                        } else if (punchAction === "Emergency Punch OUT") {
                            url = "{{route('emergencyPunchOutAttendance')}}";
                        }
                        // 🔹 Second AJAX: Punch IN or Punch OUT
                        $.ajax({
                            url: url,
                            type: 'POST',
                            data: {
                                status: punchAction,
                                _token: $('meta[name="csrf-token"]').attr('content') // CSRF Token
                            },
                            success: function (result) {
                                // 🚀 Reload the timesheet section after Punch IN
                                reloadTimesheet();

                                console.log(result);
                                hideLoader();
                                statistics();
                                dataTab();
                                isRequestInProgress = false; // Reset flag
                                $('#verify_user button[type="submit"]').prop('disabled', false);
                                $("#password").val("");


                                if (result.status === "error") {
                                    createToast('error', 'fa-solid fa-check-circle', 'Error', 'Your Shift is Over!');
                                } else {
                                    createToast('info', 'fa-solid fa-check-circle', 'Success', `${punchAction} successful.`);
                                    if (punchAction === "Punch IN") {
                                        punchInTime = new Date(result.punch_in_time);
                                        punchInTimeDisplay.textContent = moment(punchInTime).format('dddd, DD MMM YYYY HH:mm:ss A');
                                        startShiftProgress(true);
                                        console.log("golo is working");
                                    } else {
                                        punchInTime = null;
                                        punchInTimeDisplay.textContent = '';
                                        timeCounter.textContent = '0:00 hrs';
                                        progressCircle.style.strokeDashoffset = 314;
                                        console.log("golo is Ending");
                                    }
                                }

                            },
                            error: function () {
                                hideLoader();
                                isRequestInProgress = false; // Reset flag
                                $('#verify_user button[type="submit"]').prop('disabled', false);
                                createToast('error', 'fa-solid fa-circle-exclamation', 'Error', `${punchAction} failed.`);
                            }
                        });
                    } else {
                        hideLoader();
                        isRequestInProgress = false; // Reset flag
                        $('#verify_user button[type="submit"]').prop('disabled', false);
                        $('#display_error').html("Password is incorrect!");
                        $('#display_error').show();
                        $("#password").css("border", "1px solid red")
                    }
                },
                error: function () {
                    hideLoader();
                    isRequestInProgress = false; // Reset flag
                    $('#verify_user button[type="submit"]').prop('disabled', false);
                    $("#password").val("");
                    createToast('error', 'fa-solid fa-circle-exclamation', 'Error', 'Invalid User.');
                }
            });
        });

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
                    // punchOutBtn.disabled = false;
                    clearInterval(intervalId);
                }
            }


            if (initial) updateProgress();


            intervalId = setInterval(updateProgress, 60000);
        }
    });
</script>
<script>
    $(document).ready(function () {
        $("#deductions_btn").click(function (e) {
            e.preventDefault();
            var id = {{auth()->user()->id}};
            showLoader();
            $.ajax({
                url: "{{ route('deduction.details') }}",
                type: 'GET',
                data: { id: id },
                success: function (data) {
                    // Empty the table body before populating new rows
                    $("#deduction_table tbody").empty();

                    // Loop through the returned data and add rows to the table
                    data.forEach(function (record) {
                        // Format the dates and times
                        let startDate = moment(record.shift_in).format('DD MMMM YYYY');
                        let shiftIn = moment(record.shift_in).format('HH:mm:ss');
                        let endDate = moment(record.shift_out).format('DD MMMM YYYY');
                        let shiftOut = moment(record.shift_out).format('HH:mm:ss');
                        let checkInDate = record.check_in ? moment(record.check_in).format('DD MMMM YYYY') : '';
                        let checkInTime = record.check_in ? moment(record.check_in).format('HH:mm:ss') : '';
                        let checkOutDate = record.check_out ? moment(record.check_out).format('DD MMMM YYYY') : '';
                        let checkOutTime = record.check_out ? moment(record.check_out).format('HH:mm:ss') : '';

                        let row = `
                        <tr>
                            <td>${startDate}</td>
                            <td>${shiftIn}</td>
                            <td>${endDate}</td>
                            <td>${shiftOut}</td>
                            <td>${checkInDate}</td>
                            <td>${checkInTime}</td>
                            <td>${checkOutDate}</td>
                            <td>${checkOutTime}</td>
                            <td>${record.duty_hours}</td> <!-- Correct placement of duty hours -->
                            <td>${record.status}</td> <!-- Correct placement of status -->
                        </tr>
                        `;
                        if (record.status === "Absent") {
                            row = `
                                <tr style="background-color: #870501; color: white;">
                                <td style="background-color: #870501; color: white;">${startDate}</td>
                                <td style="background-color: #870501; color: white;">${shiftIn}</td>
                                <td style="background-color: #870501; color: white;">${endDate}</td>
                                <td style="background-color: #870501; color: white;">${shiftOut}</td>
                                <td style="background-color: #870501; color: white;">${checkInDate}</td>
                                <td style="background-color: #870501; color: white;">${checkInTime}</td>
                                <td style="background-color: #870501; color: white;">${checkOutDate}</td>
                                <td style="background-color: #870501; color: white;">${checkOutTime}</td>
                                <td style="background-color: #870501; color: white;">${record.duty_hours}</td> <!-- duty_hours for Absent -->
                                <td style="background-color: #870501; color: white;">${record.status}</td> <!-- status for Absent -->
                            </tr>
                            `;
                        } else {
                            row = `
                                <tr style="background-color: #A90500; color: white;">
                                <td style="background-color: #A90500; color: white;">${startDate}</td>
                                <td style="background-color: #A90500; color: white;">${shiftIn}</td>
                                <td style="background-color: #A90500; color: white;">${endDate}</td>
                                <td style="background-color: #A90500; color: white;">${shiftOut}</td>
                                <td style="background-color: #A90500; color: white;">${checkInDate}</td>
                                <td style="background-color: #A90500; color: white;">${checkInTime}</td>
                                <td style="background-color: #A90500; color: white;">${checkOutDate}</td>
                                <td style="background-color: #A90500; color: white;">${checkOutTime}</td>
                                <td style="background-color: #A90500; color: white;">${record.duty_hours}</td> <!-- duty_hours in proper column -->
                                <td style="background-color: #A90500; color: white;">${record.status}</td> <!-- status in proper column -->
                            </tr>
                            `;
                        }

                        // Append the row to the table
                        $("#deduction_table tbody").append(row);
                    });

                    // Show the modal
                    $("#deduction_modal").modal("show");
                    hideLoader();
                },
                error: function (xhr, status, error) {
                    console.error('Error fetching data:', error);
                    hideLoader();
                }
            });
        });
    });
</script>
</body>

</html>
@endsection