@extends('layout.mainlayout')
@section('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css" />
    <style>
        .container {
            padding: 2rem 0rem;
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

        .btnDanger {
            margin-left: 10px;
            border-radius: 7px;
            padding: 12px 11px 12px 11px;
            color: #ffff;
            font-size: medium;
            font-weight: 400;
            background: #dd0028;
            border: none;
        }

        .btnDanger:hover {
            background-color: #7a1919;
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

        @media only screen and (max-width:1124px) {
            .row-wd {
                width: 100%;
            }

        }
    </style>
@endsection
@section('content')


    @php
        $user = auth()->user();
        $permissions = getUserPermissions($user); // Use the helper function to fetch permissions
        // Check if the user has at least one of the required permissions or is a superadmin
        $hasActionPermission = $user->role == 1 ||
            in_array('update_attendance_schedule', $permissions) ||
            in_array('delete_attendance_schedule', $permissions);

        $hasActionPermissionBulkDelete = $user->role == 1 ||
            in_array('bulk_delete_attendance_schedule', $permissions);
    @endphp

    <div id="notification" aria-live="polite" aria-atomic="true"></div>
    <div class="page-header">
        <div class="row">
            <div class="col-sm-12">
                <div class="main-employee-title">
                    <h3 class="page-title"><span data-translate="attendance">Attendance</span></h3>
                    <div class="employee-title">
                        ( {{ getUserEmpId($id) }} || {{ getUserName($id) }} )
                    </div>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a
                            href="{{ auth()->user()->role == '1' ? url('admin-dashboard') : url('attendance-employee') }}">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item active"><span data-translate="attendance">Attendance</span></li>
                </ul>
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
                    <h5 class="card-title"><span data-translate="timesheet">Timesheet</span> <small class="text-muted"
                            id="punchDate">{{date('dS M Y')}}</small>
                    </h5>
                    <div class="punch-det">
                        <h6><span data-translate="check_in_at">Check In at</span></h6>
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
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card att-statistics">
                <div class="card-body">
                    <h5 class="card-title"><span data-translate="salary_deduction">Salary Deduction</span></h5>
                    <div class="stats-list">
                        <div class="stats-info">
                            <p><span data-translate="late">Late</span> <strong><span id="day"></span> </strong></p>

                        </div>


                        <div class="stats-info">
                            <p><span data-translate="absent_fine">Absent Fine</span><strong> <small><span id="absent_fine">
                                        </span></small></strong></p>

                        </div>

                        <div class="stats-info">
                            <p><span data-translate="late_fine">Late Fine</span><strong> <small><span id="late_fine">
                                        </span></small></strong></p>
                        </div>

                        <div class="stats-info">
                            <p><span data-translate="total_deduction">Total Deduction</span> <strong> <small><span
                                            id="total"> </span></small></strong></p>
                        </div>
                        <div class="deductions text-center p-0 mt-3 mb-0">
                            <button class="btn btn-danger btn-sm" id="deductions_btn" data-id="{{$id}}"> <span
                                    data-translate="view_deduction_details">View Deduction Details</span></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card recent-activity">
                <div class="card-body">
                    <h5 class="card-title"><span data-translate="today_activity">Today Activity</span></h5>
                    <ul class="res-activity-list">
                        <li>
                            <p class="mb-0"><span data-translate="check_in_at">Check In at</span></p>
                            <p class="res-activity-time">
                                <i class="fa-regular fa-clock"></i>
                                <span id="check_in"></span>
                            </p>
                        </li>
                        <li>
                            <p class="mb-0"><span data-translate="check_out_at">Check Out at</span></p>
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
        <div class="row filter filter-row mt-5">
            <div class="col-sm-12 col-md-12 col-lg-4 row-wd">
                <label for=""> <span data-translate="from_date">From date:</span> </label>
                <div class="input-block mb-3 form-focus">
                    <div class="cal-icon">
                        <input type="text" class="form-control floating datetimepicker" id="fromDate" name="fromDate">
                    </div>
                    <label class="focus-label"><span data-translate="date">Date</span></label>
                </div>
            </div>
            <div class="col-sm-12 col-md-12 col-lg-4 row-wd">
                <label for=""> <span data-translate="to_date">To date:</span> </label>
                <div class="input-block mb-3 form-focus">
                    <div class="cal-icon">
                        <input type="text" class="form-control floating datetimepicker" id="toDate" name="toDate">
                    </div>
                    <label class="focus-label"><span data-translate="date">Date</span></label>
                </div>
            </div>

            <div class="col-sm-12 col-md-12 col-lg-4 row-wd  d-flex align-items-center justify-content-between">
                <div class="first">
                    <button type="button" class="btn btn-primary me-2" id="refresh" onclick="refreshDate()">
                        <i class="fa fa-refresh" aria-hidden="true"></i>
                    </button>
                    <button class="btn btn-primary" type="submit" id="submitButton"> <span
                            data-translate="search">Search</span> </button>
                </div>
                @if($hasActionPermissionBulkDelete)
                    <button id="deleteSelected" class="btnDanger"><span data-translate="bulk_delete">Bulk Delete</span></button>
                @endif
            </div>
        </div>
    </form>

    <!-- Search Filter End -->

    <div class="row">
        <div class="col-lg-12">
            <div class="table-responsive">
                <table class="table custom-table mb-0" id="attendance-employee">
                    <thead>
                        <tr>
                            <th class="text-center"><span data-translate="verify">Verify</span></th>
                            <th><span data-translate="start_date">Start Date</span></th>
                            <th><span data-translate="shift_in">Shift In</span></th>
                            <th><span data-translate="end_date">End Date</span></th>
                            <th><span data-translate="shift_out">Shift Out</span></th>
                            <th><span data-translate="check_in_date">CheckIn Date</span></th>
                            <th><span data-translate="check_in">Check In</span></th>
                            <th><span data-translate="check_out_date">CheckOut Date</span></th>
                            <th><span data-translate="check_out">Check Out</span></th>
                            <th><span data-translate="duty_hours">Duty Hours</span></th>
                            <th class="text-center"><span data-translate="verify">Verify</span></th>
                            @if($hasActionPermission)
                                <th class="text-center"><span data-translate="action">Action</span></th>
                            @endif
                            <th><input type="checkbox" id="select-all"> </th> <!-- Checkbox Column at the End -->
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <!--Delete Modal -->
    <div class="modal fade" id="deleteGroup" tabindex="-1" aria-labelledby="exampleModalScrollable2" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="modalTitle">Delete Confirmation</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete this Group? This action cannot be undone.</p>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmDelete">Delete</button>

                </div>
            </div>
        </div>
    </div>

    <!--dDelete Modal End -->
    <!--Edit schedule-->
    <div id="schedule" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><span data-translate="edit_schedule">Edit Schedule</span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="schedule" method="post" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <!-- Shift Start Date -->
                            <div class="col-sm-6">
                                <div class="input-block mb-3">
                                    <label for="startdate[]"><span data-translate="select_shift_start_dates">Select Shift
                                            Start Date:</span></label>
                                    <input type="datetime-local" id="date-picker" name="startdate[]" class="form-control" />
                                </div>
                            </div>

                            <!-- Shift End Date -->
                            <div class="col-sm-6">
                                <div class="input-block mb-3">
                                    <label for="date-picker2"><span data-translate="select_shift_end_dates">Select Shift End
                                            Date:</span></label>
                                    <input type="datetime-local" id="date-picker2" name="enddate[]" class="form-control" />
                                </div>
                            </div>
                            @if($user->role == 1 || in_array('edit_checkin_checkout', $permissions))
                                <!-- Check-In Date and Time -->
                                <div class="col-sm-6">
                                    <div class="input-block mb-3">
                                        <label for="checkInDatetime"><span data-translate="select_check_in_date_and_time">Select
                                                Check-In Date and Time:</span></label>
                                        <input type="datetime-local" id="checkInDatetime" name="check_in_datetime"
                                            class="form-control" />
                                    </div>
                                </div>
                                <!-- Check-Out Date and Time -->
                                <div class="col-sm-6">
                                    <div class="input-block mb-3">
                                        <label for="checkOutDatetime"><span
                                                data-translate="select_check_out_date_and_time">Select Check-Out Date and
                                                Time:</span></label>
                                        <input type="datetime-local" id="checkOutDatetime" name="check_out_datetime"
                                            class="form-control" />
                                    </div>
                                </div>
                            @endif
                            <!-- Manage Day -->
                            <div class="col-sm-6">
                                <div class="input-block mb-3">
                                    <label for="manage_day"><span data-translate="manage_day">Manage Day:</span></label>
                                    <select class="form-select" id="manage_day" name="manage_day">
                                        <option value="Yes">OFF</option>
                                        <option value="No">Working Day</option>
                                        @if($user->role === "1")
                                            <option value="BT">Business Trip</option>
                                            <option value="PH">Public Holiday</option>
                                        @endif
                                    </select>
                                </div>
                            </div>
                            <input type="hidden" id="row_id" name="row_id">
                        </div>
                        <div class="submit-section">
                            <button class="btn btn-primary" type="submit"><span
                                    data-translate="submit">Submit</span></button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- DEDUCTION DETAILS MODAL STARTS -->
    <div class="modal fade mt-4" id="deduction_modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl deduction">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5 text-center" id="exampleModalLabel"><span
                            data-translate="salary_deduction_details">Salary Deduction Details</span></h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">X</button>
                </div>
                <div class="modal-body pt-0">
                    <div class="table-responsive">
                        <table class="table mt-0" id="deduction_table">
                            <thead class="sticky-top" style="background-color: #f8f9fa;">
                                <tr>
                                    <th><span data-translate="start_date">Start Date</span></th>
                                    <th><span data-translate="shift_in">Shift In</span></th>
                                    <th><span data-translate="end_date">End Date</span></th>
                                    <th><span data-translate="shift_out">Shift Out</span></th>
                                    <th><span data-translate="check_in_date">CheckIn Date</span></th>
                                    <th><span data-translate="check_in">Check In</span></th>
                                    <th><span data-translate="check_out_date">CheckOut Date</span></th>
                                    <th><span data-translate="check_out">Check Out</span></th>
                                    <th><span data-translate="duty_hours">Duty Hours</span></th>
                                    <th><span data-translate="status">Status</span></th>
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

    </div>

    <!--End -->

    <!-- PreLoader -->
    <div id="loader" class="loader" style="display: none;">
        <div class="loader-animation"></div>
    </div>

@endsection
@section('script-z')
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        let uid = 0;
        $(document).ready(function () {
            $("#fromDate").empty();
            $("#toDate").empty();
            uid = {{ $id }};
            statistics(uid);
            dataTab(uid);
        });

        function dataTab(id) {
            var date = moment();
            var format = date.format("D MMMM Y");
            var serverDate = moment("{{ now()->toDateString() }}", "YYYY-MM-DD"); // Get the server's current date

            $.ajax({
                url: "{{route('edit.attendance', '')}}" + "/" + id,
                type: 'GET',
                success: function (response) {
                    let isSuperadmin = {{ auth()->user()->role == 1 ? 'true' : 'false' }};
                    let canEdit = isSuperadmin || {{ in_array('update_attendance_schedule', $permissions) ? 'true' : 'false' }};
                    let canDelete = isSuperadmin || {{ in_array('delete_attendance_schedule', $permissions) ? 'true' : 'false' }};
                    let canBulkDelete = isSuperadmin || {{ in_array('bulk_delete_attendance_schedule', $permissions) ? 'true' : 'false' }};

                    $('#attendance-employee').DataTable({
                        destroy: true,
                        pageLength: 31,
                        data: response.data,

                        columns: [
                            {
                                data: 'verify1',
                                title: '<span data-translate="verify">Verify</span>',
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

                            { data: 'start_date', title: '<span data-translate="start_date">Start Date</span>' },
                            { data: 'shift_in', title: '<span data-translate="shift_in">Shift In</span>' },
                            { data: 'end_date', title: '<span data-translate="end_date">End Date</span>' },
                            { data: 'shift_out', title: '<span data-translate="shift_out">Shift Out</span>' },
                            { data: 'in_date', title: '<span data-translate="check_in_date">CheckIn Date</span>' },
                            {
                                data: 'check_in',
                                title: '<span data-translate="check_in">Check In</span>',
                                render: function (data, type, row) {
                                    return row.check_in ? data : ' ';
                                }
                            },
                            { data: 'out_date', title: '<span data-translate="check_out_date">CheckOut Date</span>' },
                            {
                                data: 'check_out',
                                title: '<span data-translate="check_out">Check Out</span>',
                                render: function (data, type, row) {
                                    return row.check_out ? data : ' ';
                                }
                            },
                            {
                                data: 'duty_hours',
                                title: '<span data-translate="duty_hours">Duty Hours</span>',
                                render: function (data, type, row) {
                                    if (row.dayoff === "Yes") return "OFF";
                                    if (row.dayoff === "PH") return "Public Holiday";
                                    if (row.dayoff === "BT") return "Bussiness Trip";
                                    if (row.color === '1') return "AL";
                                    if (row.color === '2') return "BL";
                                    if (row.color === '3') return "ML";
                                    if (row.color === '4') return "UL";
                                    if (row.color === '5') return "HL";
                                    if (row.color === '6') return "CL";
                                    if (row.color === '7') return "MTL";
                                    if (row.color === '8') return "PL";
                                    if (row.absent === "Yes") return "Absent";
                                    return data;
                                }
                            },
                            {
                                data: 'verify2',
                                title: '<span data-translate="verify_two">Verify</span>',
                                render: function (data, type, row) {
                                    let result = '';

                                    if (row.verify2 === "yes") {
                                        result = `<i class="fas fa-check-circle"></i>`;
                                    } else if (row.verify2 === "cross") {
                                        result = `<i class="fa fa-times-circle"></i>`;
                                    } else {
                                        result = "No Record";
                                    }

                                    return `<div class="text-center">${result}</div>`;
                                }
                            },
                            {
                                data: null,
                                title: '<span data-translate="action">Action</span>',
                                render: function (data, type, row) {
                                    let buttons = '';

                                    // Parse the start_date to a Moment.js object
                                    let recordDate = moment(row.start_date, "DD MMMM YYYY");
                                    let currentMonthStart = moment().startOf('month'); // Start of the current month

                                    // Check if emergency checkout is used, then show restricted
                                    if (row.emergency_checkout == 1 && !isSuperadmin) {
                                        buttons = '<span style="color: red;">Emergency Checkout</span>';
                                    }

                                    // Check if the recordDate is within the current or future months
                                    else if (isSuperadmin || recordDate.isSameOrAfter(currentMonthStart, 'month')) {
                                        // Allow actions for Superadmin or records in the current month
                                        if (canEdit) {
                                            buttons += `<i class="fas fa-edit edit-btn" data-id="${row.id}" style="cursor: pointer; color:black!important; margin-right: 10px;"></i>`;
                                        }
                                        if (canDelete) {
                                            buttons += `<i class="fas fa-trash delete-btn" data-id="${row.id}" style="cursor: pointer;color:#dd0028!important;"></i>`;
                                        }
                                    } else {
                                        // Restrict actions for past records (non-Superadmin)
                                        buttons = '<span style="color: red;">Restricted</span>';
                                    }

                                    return buttons || ' '; // Return buttons if any exist, otherwise return empty string
                                },
                                orderable: false,
                                className: 'text-center'
                            },
                            {
                                data: null,
                                title: canBulkDelete ? '<input type="checkbox" id="select-all" />' : '',
                                render: function (data, type, row) {

                                    let recordDate = moment(row.start_date, "DD MMMM YYYY");
                                    let currentMonthStart = moment().startOf('month'); // Start of the current month

                                    // BULK DELETE
                                    if (row.emergency_checkout == 1 && !isSuperadmin) {
                                        return '';
                                    }

                                    // Allow bulk delete for Superadmin or records in the current or future months
                                    return canBulkDelete && (isSuperadmin || recordDate.isSameOrAfter(currentMonthStart, 'month'))
                                        ? `<input type="checkbox" class="row-checkbox" data-id="${row.id}" />`
                                        : '';
                                },
                                orderable: false,
                                className: 'text-center',
                                visible: canBulkDelete // Show/hide column based on permission
                            }
                        ],
                        order: [],
                        pageLength: 31, // Set the default number of records to show
                        lengthMenu: [10, 25, 31, 50, 100], // Options for records per page
                        createdRow: function (row, data, dataIndex) {
                            // Target columns from "Start Date" to "Duty Hours" (indexes 1-9)
                            const leaveColumns = [1, 2, 3, 4, 5, 6, 7, 8, 9];

                            // If the row is marked as a day off
                            if (data.dayoff === "Yes" || data.dayoff === "PH" || data.dayoff === "BT") {
                                leaveColumns.forEach(function (colIndex) {
                                    $(row).find('td').eq(colIndex).css({
                                        'background-color': '#767D83',  // Light Gray for OFF days
                                        'color': 'white'                // White text color
                                    });
                                });
                            }

                            // If the row has AL (Annual Leave)
                            else if (data.color === '1') {
                                leaveColumns.forEach(function (colIndex) {
                                    $(row).find('td').eq(colIndex).css({
                                        'background-color': '#1B7EC1',  // Blue for AL
                                        'color': 'white'                // White text color
                                    });
                                });
                            }

                            // If the row has BL (Balance Leave)
                            else if (data.color === '2') {
                                leaveColumns.forEach(function (colIndex) {
                                    $(row).find('td').eq(colIndex).css({
                                        'background-color': '#138744',  // Emerald Green for BL
                                        'color': 'white'                // White text color
                                    });
                                });
                            }

                            // If the row has ML (Marriage Leave)
                            else if (data.color === '3') {
                                leaveColumns.forEach(function (colIndex) {
                                    $(row).find('td').eq(colIndex).css({
                                        'background-color': '#E3005F',  // Pink for ML
                                        'color': 'white'                // White text color
                                    });
                                });
                            }

                            // If the row has UL (Unpaid Leave)
                            else if (data.color === '4') {
                                leaveColumns.forEach(function (colIndex) {
                                    $(row).find('td').eq(colIndex).css({
                                        'background-color': '#FF002E',  // REd for UL
                                        'color': 'white'                // White text color
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

                            // If the row is marked as Absent
                            else if (data.absent === "Yes") {
                                leaveColumns.forEach(function (colIndex) {
                                    $(row).find('td').eq(colIndex).css({
                                        'background-color': '#870501',  // Red for Absent
                                        'color': 'white'                // White text color
                                    });
                                });
                                $(row).addClass("absent");
                            }
                            // If the row is for today's date and has attendance information
                            else if (Date.parse(data.start_date) == Date.parse(format) && data.dayoff !== "Yes") {
                                // If the employee is late (shift_in or shift_out is outside allowed times)
                                if (data.shift_in < data.check_in || data.shift_out > data.check_out) {
                                    leaveColumns.forEach(function (colIndex) {
                                        $(row).find('td').eq(colIndex).css({
                                            'background-color': '#A90500',  // Tomato Red for Late
                                            'color': 'white'                // White text color
                                        });
                                    });
                                    $(row).addClass("late");
                                }
                                // If the employee is on time and it’s today
                                else {
                                    $(row).addClass("today");
                                }
                            }
                            // If the row is not today but the employee is late
                            else if (data.shift_in < data.check_in || data.shift_out > data.check_out) {
                                leaveColumns.forEach(function (colIndex) {
                                    $(row).find('td').eq(colIndex).css({
                                        'background-color': '#A90500',  // Tomato Red for Late
                                        'color': 'white'                // White text color
                                    });
                                });
                                $(row).addClass("late");
                            }
                        },

                    });


                    $('#select-all-header').on('click', function () {
                        var isChecked = $(this).prop('checked');
                        $('.select-row').prop('checked', isChecked);
                    });


                    $('#attendance-employee').on('click', '.edit-btn', function () {
                        var id = $(this).data('id');


                    });


                    $('#attendance-employee').on('click', '.delete-btn', function () {
                        var id = $(this).data('id');


                    });

                },
            });
        }

        $(document).on('change', '#select-all', function () {
            const isChecked = $(this).is(':checked');
            $('.row-checkbox').prop('checked', isChecked);
        });


        $(document).on('click', '#deleteSelected', function () {
            const selectedIds = [];
            $('.row-checkbox:checked').each(function () {
                selectedIds.push($(this).data('id'));
            });

            if (selectedIds.length === 0) {
                alert('Please select at least one record to delete.');
                return;
            }

            if (!confirm('Are you sure you want to delete the selected records?')) {
                return;
            }


            $.ajax({
                url: "{{ route('attendance.delete') }}",
                type: "POST",
                data: {
                    ids: selectedIds,
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) {
                    alert('Selected records deleted successfully.');

                    dataTab(uid);
                },
                error: function (err) {
                    console.error(err);
                    alert('An error occurred while deleting.');
                }
            });
        });


        $(document).on('click', '.delete-btn', function () {
            const id = $(this).data('id');
            deleteGroup(id);

        });

        function deleteGroup(id) {
            groupId = id;

            $('#deleteGroup').modal('show');
        }
        $('#confirmDelete').on('click', function () {

            if (groupId) {
                $.ajax({
                    url: "{{ route('attendance.delete.single') }}",
                    type: "POST",
                    data: {
                        id: groupId,
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (response) {
                        $('#deleteGroup').modal('hide');
                        createToast('info', 'fa-solid fa-circle-check', 'info', 'Record deleted successfully.');

                        dataTab(uid);
                    },

                });
            }
        });


        $('#searchData').on('submit', function (event) {
            event.preventDefault();
            var date = moment();
            var format = date.format("D MMMM Y");
            var fromDate = $('#fromDate').val();
            var toDate = $('#toDate').val();
            var serverDate = moment("{{ now()->toDateString() }}", "YYYY-MM-DD"); // Get the server's current date

            if ((fromDate != "") && (toDate != "")) {
                var formData = new FormData();
                formData.append('from_date', fromDate);
                formData.append('to_date', toDate);
                formData.append('id', uid);
                showLoader();
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                $.ajax({
                    url: "{{ route('search.admin') }}",
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function (response) {
                        let isSuperadmin = {{ auth()->user()->role == 1 ? 'true' : 'false' }};
                        let canEdit = isSuperadmin || {{ in_array('update_attendance_schedule', $permissions) ? 'true' : 'false' }};
                        let canDelete = isSuperadmin || {{ in_array('delete_attendance_schedule', $permissions) ? 'true' : 'false' }};
                        let canBulkDelete = isSuperadmin || {{ in_array('bulk_delete_attendance_schedule', $permissions) ? 'true' : 'false' }};

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




                                { data: 'start_date', title: 'Start Date' },
                                { data: 'shift_in', title: 'Shift In' },
                                { data: 'end_date', title: 'End Date' },
                                { data: 'shift_out', title: 'Shift Out' },
                                { data: 'in_date', title: 'Checkin Date' },
                                {
                                    data: 'check_in',
                                    title: 'Check In',
                                    render: function (data, type, row) {
                                        return row.check_in ? data : ' ';
                                    }
                                },
                                { data: 'out_date', title: 'Checkout Date' },
                                {
                                    data: 'check_out',
                                    title: 'Check Out',
                                    render: function (data, type, row) {
                                        return row.check_out ? data : ' ';
                                    }
                                },
                                {
                                    data: 'duty_hours',
                                    title: 'Duty Hours',
                                    render: function (data, type, row) {
                                        if (row.dayoff === "Yes") return "OFF";
                                        if (row.dayoff === "PH") return "Public Holiday";  // Show "PH" if dayoff is PH
                                        if (row.dayoff === "BT") return "Bussiness Trip";  // Show "BT" if dayoff is BT
                                        if (row.color === '1') return "AL";
                                        if (row.color === '2') return "BL";
                                        if (row.color === '3') return "ML";
                                        if (row.color === '4') return "UL";
                                        if (row.color === '5') return "HL";
                                        if (row.color === '6') return "CL";
                                        if (row.color === '7') return "MTL";
                                        if (row.color === '8') return "PL";
                                        if (row.absent === "Yes") return "Absent";
                                        return data;
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
                                            result = `<i class="fa fa-times-circle"></i>`;
                                        } else {
                                            result = "No Record";
                                        }

                                        return `<div class="text-center">${result}</div>`;
                                    }
                                },
                                {
                                    data: null,
                                    title: 'Action',
                                    render: function (data, type, row) {
                                        let buttons = '';

                                        // Parse the start_date to a Moment.js object
                                        let recordDate = moment(row.start_date, "DD MMMM YYYY");
                                        let currentMonthStart = moment().startOf('month'); // Start of the current month

                                        // Check if the recordDate is within the current or future months
                                        if (isSuperadmin || recordDate.isSameOrAfter(currentMonthStart, 'month')) {
                                            // Allow actions for Superadmin or records in the current month
                                            if (canEdit) {
                                                buttons += `<i class="fas fa-edit edit-btn" data-id="${row.id}" style="cursor: pointer; color:black!important; margin-right: 10px;"></i>`;
                                            }
                                            if (canDelete) {
                                                buttons += `<i class="fas fa-trash delete-btn" data-id="${row.id}" style="cursor: pointer;color:#dd0028!important;"></i>`;
                                            }
                                        } else {
                                            // Restrict actions for past records (non-Superadmin)
                                            buttons = '<span style="color: red;">Restricted</span>';
                                        }

                                        return buttons || ' '; // Return buttons if any exist, otherwise return empty string
                                    },
                                    orderable: false,
                                    className: 'text-center'
                                },
                                {
                                    data: null,
                                    title: canBulkDelete ? '<input type="checkbox" id="select-all" />' : '',
                                    render: function (data, type, row) {

                                        let recordDate = moment(row.start_date, "DD MMMM YYYY");
                                        let currentMonthStart = moment().startOf('month'); // Start of the current month

                                        // Allow bulk delete for Superadmin or records in the current or future months
                                        return canBulkDelete && (isSuperadmin || recordDate.isSameOrAfter(currentMonthStart, 'month'))
                                            ? `<input type="checkbox" class="row-checkbox" data-id="${row.id}" />`
                                            : '';
                                    },
                                    orderable: false,
                                    className: 'text-center',
                                    visible: canBulkDelete // Show/hide column based on permission
                                }
                            ],
                            order: [],
                            createdRow: function (row, data, dataIndex) {
                                // Target columns from "Start Date" to "Duty Hours" (indexes 1-9)
                                const leaveColumns = [1, 2, 3, 4, 5, 6, 7, 8, 9];

                                // If the row is marked as a day off
                                if (data.dayoff === "Yes" || data.dayoff === "PH" || data.dayoff === "BT") {
                                    leaveColumns.forEach(function (colIndex) {
                                        $(row).find('td').eq(colIndex).css({
                                            'background-color': '#767D83',  // Light Gray for OFF days
                                            'color': 'white'                // White text color
                                        });
                                    });
                                }

                                // If the row has AL (Annual Leave)
                                else if (data.color === '1') {
                                    leaveColumns.forEach(function (colIndex) {
                                        $(row).find('td').eq(colIndex).css({
                                            'background-color': '#1B7EC1',  // Blue for AL
                                            'color': 'white'                // White text color
                                        });
                                    });
                                }

                                // If the row has BL (Balance Leave)
                                else if (data.color === '2') {
                                    leaveColumns.forEach(function (colIndex) {
                                        $(row).find('td').eq(colIndex).css({
                                            'background-color': '#138744',  // Emerald Green for BL
                                            'color': 'white'                // White text color
                                        });
                                    });
                                }

                                // If the row has ML (Marriage Leave)
                                else if (data.color === '3') {
                                    leaveColumns.forEach(function (colIndex) {
                                        $(row).find('td').eq(colIndex).css({
                                            'background-color': '#E3005F',  // Pink for ML
                                            'color': 'white'                // White text color
                                        });
                                    });
                                }

                                // If the row has UL (Unpaid Leave)
                                else if (data.color === '4') {
                                    leaveColumns.forEach(function (colIndex) {
                                        $(row).find('td').eq(colIndex).css({
                                            'background-color': '#FF002E',  // REd for UL
                                            'color': 'white'                // White text color
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

                                // If the row is marked as Absent
                                else if (data.absent === "Yes") {
                                    leaveColumns.forEach(function (colIndex) {
                                        $(row).find('td').eq(colIndex).css({
                                            'background-color': '#870501',  // Red for Absent
                                            'color': 'white'                // White text color
                                        });
                                    });
                                    $(row).addClass("absent");
                                }
                                // If the row is for today's date and has attendance information
                                else if (Date.parse(data.start_date) == Date.parse(format) && data.dayoff !== "Yes") {
                                    // If the employee is late (shift_in or shift_out is outside allowed times)
                                    if (data.shift_in < data.check_in || data.shift_out > data.check_out) {
                                        leaveColumns.forEach(function (colIndex) {
                                            $(row).find('td').eq(colIndex).css({
                                                'background-color': '#A90500',  // Tomato Red for Late
                                                'color': 'white'                // White text color
                                            });
                                        });
                                        $(row).addClass("late");
                                    }
                                    // If the employee is on time and it’s today
                                    else {
                                        $(row).addClass("today");
                                    }
                                }
                                // If the row is not today but the employee is late
                                else if (data.shift_in < data.check_in || data.shift_out > data.check_out) {
                                    leaveColumns.forEach(function (colIndex) {
                                        $(row).find('td').eq(colIndex).css({
                                            'background-color': '#A90500',  // Tomato Red for Late
                                            'color': 'white'                // White text color
                                        });
                                    });
                                    $(row).addClass("late");
                                }
                            },
                        });


                        $('#select-all-header').on('click', function () {
                            var isChecked = $(this).prop('checked');
                            $('.select-row').prop('checked', isChecked);
                        });


                        $('#attendance-employee').on('click', '.edit-btn', function () {
                            var id = $(this).data('id');


                        });


                        $('#attendance-employee').on('click', '.delete-btn', function () {
                            var id = $(this).data('id');


                        });

                    },
                    error: function (err) {
                        hideLoader();
                        createToast('error', 'fa-solid fa-circle-exclamation', 'Error', 'Wrong Date selected. Last 3 months data Only');
                    }
                });
            }
        });
        $(document).on('click', '.edit-btn', function () {
            const rowId = $(this).data('id');

            // Make AJAX call to fetch the schedule details
            $.ajax({
                url: `/get-schedule/${rowId}`,
                type: 'GET',
                success: function (response) {
                    if (response) {
                        // Populate the modal form fields with the fetched data
                        $('#date-picker').val(response.start_date); // Set start date
                        $('#date-picker2').val(response.end_date); // Set end date
                        $('#checkInDatetime').val(response.check_in); // Set check-in date and time
                        $('#checkOutDatetime').val(response.check_out); // Set check-out date and time
                        $('#manage_day').val(response.dayoff); // Set day-off status
                        $('#row_id').val(rowId); // Set hidden row ID

                        // Show the modal for editing
                        $('#schedule').modal('show');
                    } else {
                        alert('No data found for this record.');
                    }
                },
                error: function (error) {
                    console.error('Error fetching data:', error);
                    alert('Failed to fetch data for editing.');
                },
            });
        });

        $(document).on('submit', '#schedule', function (e) {
            e.preventDefault();
            const formData = $(this).serialize();
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: '{{ route('scheduleAttendance.update') }}',
                type: 'POST',
                data: formData,
                success: function (response) {
                    if (response.success) {
                        $('#schedule').modal('hide');
                        dataTab(uid);
                        createToast('info', 'fa-solid fa-circle-check', 'info',
                            'Schedule Updated successfully.');
                    } else {
                        createToast('error', 'fa-solid fa-circle-exclamation', 'Error', response
                            .message);
                    }
                },
                error: function (xhr) {
                    const errors = xhr.responseJSON.errors;
                    for (const key in errors) {
                        createToast('error', 'fa-solid fa-circle-exclamation', 'Error', errors[key][0]);
                    }
                }
            });
        });

        // DEDUCTIONS MODAL
        $("#deductions_btn").click(function (e) {
            e.preventDefault();
            var id = $(this).data('id');
            showLoader();
            $.ajax({
                url: "{{route('deduction.details')}}",
                type: "GET",
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

        function refreshDate() {
            $('#fromDate').val('');
            $('#toDate').val('');
            statistics(uid);
            dataTab(uid);

        }
        function statistics(uid) {

            fetch(`/statistics-admin/${uid}`, {
                method: 'GET',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                }
            })
                .then(response => response.json())
                .then(data => {

                    $('#day').empty();
                    $('#absent_fine').empty();
                    $('#late_fine').empty();
                    $('#total').empty();
                    if (data.status == 'success') {

                        $('#day').append(`${data.info[0].day}  Days `);
                        $('#absent_fine').append(`${data.info[0].absent_fine}%`);
                        $('#late_fine').append(`${data.info[0].late_fine}%`);
                        $('#total').append(`${data.info[0].total}%`);

                        $('#check_in').append(`${data.info[0].check_in} . `);
                        $('#check_out').append(`${data.info[0].check_out} . `);
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
    <!-- LANGUAGE SCRIPT -->
    <script src="{{ asset('assets/js/switch.language.js') }}"></script>
@endsection