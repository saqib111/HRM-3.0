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
    }

    tbody {
        background-color: red !important;
    }

    .row-absent {
        background-color: #870501 !important;
        /* Dark Red */
    }

    .row-other {
        background-color: #A90500 !important;
        /* Light Red */
    }
</style>
@endsection
@section('content')
<div id="notification" aria-live="polite" aria-atomic="true"></div>
<div class="page-header">
    <div class="row">
        <div class="col-sm-12">
            <div class="main-employee-title">
                <h3 class="page-title">Late Employee Attendance </h3>
                <div class="employee-title">( {{auth()->user()->employee_id}} | {{auth()->user()->username}} )</div>
            </div>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a
                        href="{{ auth()->user()->role == '1' ? url('admin-dashboard') : url('attendance-employee') }}">Dashboard</a>
                </li>
                <li class="breadcrumb-item active">Attendance </li>
            </ul>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="table-responsive">
            <table class="table  custom-table mb-0" id="late-employees-table">
                <thead>
                    <tr class="table_row">
                        <th class="text-center">Verify</th>
                        <th>#</th>
                        <th>Employee Name</th>
                        <th>Shift In</th>
                        <th>Shift Out</th>
                        <th>Check In</th>
                        <th>Check Out</th>
                        <th>CheckIn Status</th>
                        <th>CheckOut Status</th>
                        <th>Duty Hours</th>
                        <th class="text-center">Verify</th>
                    </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
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
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>


<script>

    $(document).ready(function () {
        $("#late-employees-table").DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('late.employee.record') }}", // Make sure the route is correct
                type: "GET",
                dataSrc: "data", // Data will be available under 'data' key
            },
            columns: [
                {
                    data: 'fingerprint_checkin_status',
                    name: 'fingerprint_checkin_status',
                    render: function (data) {
                        let content = '';

                        if (data === "yes_record_found") {
                            content = '<i class="fas fa-check-circle text-success"></i>'; // Green check icon
                        } else if (data === "no_late_record_found") {
                            content = '<i class="fa fa-times-circle text-danger"></i>'; // Red cross icon
                        } else if (data === "no_data_fingerprint") {
                            content = 'No Record'; // Text for no record
                        }

                        // Wrap the content in a div with class "text-center" to center the content
                        return '<div class="text-center">' + content + '</div>';
                    }
                },
                { data: 'DT_RowIndex', name: 'DT_RowIndex' }, // For index column
                { data: 'username', name: 'username' },
                { data: 'shift_in', name: 'shift_in' },
                { data: 'shift_out', name: 'shift_out' },
                { data: 'check_in', name: 'check_in' },
                { data: 'check_out', name: 'check_out' },
                { data: 'check_in_status', name: 'check_in_status' }, // Custom column for Check In status
                { data: 'check_out_status', name: 'check_out_status' }, // Custom column for Check Out status
                { data: 'duty_hours', name: 'duty_hours' },
                {
                    data: 'fingerprint_checkout_status',
                    name: 'fingerprint_checkout_status',
                    render: function (data) {
                        let content = '';

                        if (data === "yes_record_found") {
                            content = '<i class="fas fa-check-circle text-success"></i>'; // Green check icon
                        } else if (data === "no_late_record_found") {
                            content = '<i class="fa fa-times-circle text-danger"></i>'; // Red cross icon
                        } else if (data === "no_data_fingerprint") {
                            content = 'No Record'; // Text for no record
                        }

                        // Wrap the content in a div with class "text-center" to center the content
                        return '<div class="text-center">' + content + '</div>';
                    }
                }
            ],
            pageLength: 50, // Set the default number of records to show
            lengthMenu: [10, 25, 50, 100], // Options for records per page
            "order": [[1, 'asc']], // Example: order by first column (employee id)
            createdRow: function (row, data, dataIndex) {
                // Define columns to apply the colors
                const LateAttendanceColumns = [1, 2, 3, 4, 5, 6, 7, 8, 9]; // Target columns 2-10

                // Define statuses for row coloring
                const checkInStatus = data.check_in_status;
                const checkOutStatus = data.check_out_status;

                // Apply conditions for row coloring
                let rowColor = "";
                let textColor = ""; // Variable to store text color

                if (
                    (checkInStatus === "Late" && checkOutStatus === "On Time") ||
                    (checkInStatus === "Late" && checkOutStatus === "Early") ||
                    (checkInStatus === "On Time" && checkOutStatus === "Early") ||
                    (checkInStatus === "Late" && checkOutStatus === "Not Checked Out") ||
                    (checkInStatus === "On Time" && checkOutStatus === "Extended") ||
                    (checkInStatus === "Late" && checkOutStatus === "Extended")
                ) {
                    rowColor = '#A90500'; // Light Red
                    textColor = 'white';
                } else if (
                    checkInStatus === "Absent" && checkOutStatus === "Not Checked Out"
                ) {
                    rowColor = '#870501'; // Dark Red
                    textColor = 'white';
                }

                // Apply the color to specified columns in the row
                if (rowColor) {
                    LateAttendanceColumns.forEach((columnIndex) => {
                        $('td', row).eq(columnIndex).css({
                            'background-color': rowColor,
                            'color': textColor
                        });
                    });
                }
            }

        });

    });
</script>
</body>

</html>


@endsection