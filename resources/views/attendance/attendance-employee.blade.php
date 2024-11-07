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

        padding: 20% !important;
        margin: 0% !important;
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

    .table tr.dayoff {
        --bs-table-bg: #d9f7fffc;
    }

    .table tr.today {
        --bs-table-bg: #D3D3D3;
    }

    .table tr.late {
        --bs-table-bg: #FF574A;
    }

    table.dataTable th.dt-type-numeric,
    table.dataTable th.dt-type-date,
    table.dataTable td.dt-type-numeric,
    table.dataTable td.dt-type-date {
        text-align: center !important;
    }

    .fa-solid,
    .fas {
        color: #00c5fb !important;
        font-size: 20px;
    }

    .fa-times-circle lt {
        font-size: 20px !important;
        color: #f0f0f0 !important;
    }

    .fa-times-circle {
        font-size: 20px !important;
        color: #c85757 !important;
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
                <div class="punch-btn-section">
                    <button id="punchInBtn" class="btn btn-primary punch-btn">Punch In</button>
                    <button id="punchOutBtn" class="btn btn-primary punch-btn" disabled>Punch Out</button>

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
                        <p>Total Hours <strong> <small><span id="hour"> </span></small></strong></p>

                    </div>

                    <div class="stats-info">
                        <p>Total Deduction <strong> <small><span id="deduction"> </span></small></strong></p>

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

                    <input type="text" class="form-control floating datetimepicker" id="fromDate">
                </div>
                <label class="focus-label">Date</label>
            </div>
        </div>
        <div class="col-sm-5">
            <label for=""> To date: </label>
            <div class="input-block mb-3 form-focus">
                <div class="cal-icon">

                    <input type="text" class="form-control floating datetimepicker" id="toDate">
                </div>
                <label class="focus-label">Date</label>
            </div>
        </div>

        <div class="col-sm-2 mt-4 ">
            <div class="row">
                <div class=" col-sm-4">
                    <button class="btn btn-primary" id="refresh" onclick="refreshDate()"><i class="fa fa-refresh"
                            aria-hidden="true"></i></button>

                </div>
                <div class=" col-sm-8">
                    <button class="btn btn-primary " type="submit"> Search </button>

                </div>
            </div>
        </div>
    </div>
    <!-- /Search Filter -->
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
                    <h4>Check Employee</h4>
                </div>
                <div class="d-flex flex-column text-center ">
                    <form id="emp-check" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group mb-3">
                            <input type="email" class="form-control" id="email" placeholder="Email address..."
                                autocomplete="one-time-code">
                        </div>
                        <div class="form-group mb-4">
                            <input type="password" class="form-control" id="password" placeholder="Password..."
                                autocomplete="one-time-code">
                        </div>
                        <button
                            class="btn btn-info btn-block btn-round ">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Login&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</button>
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

                                if (row.verify1 == "yes") { return ` <i class="fas fa-check-circle" ></i> `; }
                                else if (row.verify1 == "cross") { return ` <i class="fa fa-times-circle wt"></i> `; }

                                else {
                                    return "No Record";
                                }
                            }
                        },

                        { data: 'no', title: '#' },
                        { data: 'start_date', title: 'Start Date' },
                        { data: 'shift_in', title: 'Shift In' },
                        { data: 'end_date', title: 'End Date' },
                        { data: 'shift_out', title: 'Shift Out' },

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
                                    return data;
                                }
                            }
                        },

                        {
                            data: 'verify2',
                            title: 'Verify',
                            render: function (data, type, row) {

                                if (row.verify2 == "yes") { return ` <i class="fas fa-check-circle" ></i> `; }
                                else if (row.verify2 == "cross") { return ` <i class="fa fa-times-circle wt"></i> `; }

                                else {
                                    return "No Record";
                                }
                            }
                        },

                    ], order: [],
                    createdRow: function (row, data, dataIndex) {

                        if (data.dayoff === "Yes") {
                            $(row).addClass("dayoff");

                        }

                        else if (Date.parse(data.start_date) == Date.parse(format) && data.dayoff != "Yes") {

                            if (data.shift_in < data.check_in || data.shift_out > data.check_out) {

                                $(row).addClass("late");
                            }
                            else {
                                $(row).addClass("today");
                            }
                        }
                        else if (data.shift_in < data.check_in || data.shift_out > data.check_out) {

                            $(row).addClass("late");
                        }

                        else {

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


                    $('#attendance-employee').DataTable({
                        destroy: true,
                        pageLength: 31,
                        data: response.data,

                        columns: [

                            {
                                data: 'verify1',
                                title: 'Verify',
                                render: function (data, type, row) {

                                    if (row.verify1 == "yes") { return ` <i class="fas fa-check-circle" ></i> `; }
                                    else if (row.verify1 == "cross") { return ` <i class="fa fa-times-circle"></i> `; }

                                    else {
                                        return "No Record";
                                    }
                                }
                            },

                            { data: 'no', title: '#' },
                            { data: 'start_date', title: 'Start Date' },
                            { data: 'shift_in', title: 'Shift In' },
                            { data: 'end_date', title: 'End Date' },
                            { data: 'shift_out', title: 'Shift Out' },

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
                                        return data;
                                    }
                                }
                            },

                            {
                                data: 'verify2',
                                title: 'Verify',
                                render: function (data, type, row) {
                                    if (row.verify2 == "yes") {
                                        if (row.shift_in < row.check_in) { return ` <i class="fas fa-check-circle" ></i> `; }
                                        else if (row.verify2 == "cross") { return ` <i class="fa fa-times-circle"></i> `; }
                                        else { return ` <i class="fas fa-check-circle" ></i> `; }
                                    } else {
                                        return "No Record";
                                    }
                                }
                            },

                        ], order: [],
                        createdRow: function (row, data, dataIndex) {

                            if (data.dayoff === "Yes") {
                                $(row).addClass("dayoff");

                            }

                            else if (Date.parse(data.start_date) == Date.parse(format) && data.dayoff != "Yes") {
                                if (data.shift_in < data.check_in || data.shift_out > data.check_out) {

                                    $(row).addClass("late");
                                }
                                else {
                                    $(row).addClass("today");
                                }


                            }
                            else if (data.shift_in < data.check_in || data.shift_out > data.check_out) {

                                $(row).addClass("late");
                            }

                            else {

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

        fetch('/get-punch-time')
            .then(response => response.json())
            .then(data => {

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
            email = $('#email').val('');
            password = $('#password').val('');

            clearValidationStates();
            valdateCancel();

            $('#loginModal').modal('show');
            $('#emp-check').on('submit', function (event) {
                event.preventDefault();
                var email = $('#email').val();
                var password = $('#password').val();

                var formData = new FormData();
                formData.append('email', email);
                formData.append('password', password);
                var isValid = true;
                clearValidationStates();
                if (!validateEmail('#email')) isValid = false;
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
                                $('#email').addClass('is-invalid');
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
                        punchInTime = new Date(data.punch_in_time);
                        punchInTimeDisplay.textContent = moment(punchInTime).format('dddd, DD MMM YYYY HH:mm:ss A');

                        punchInBtn.style.display = 'none';
                        punchOutBtn.style.display = 'block';
                        punchOutBtn.disabled = false;

                        startShiftProgress(true);

                        dataTab();
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

                if (now >= shiftEndTime) {
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
                $('#day').val(' ');
                $('#hour').val('');
                $('#deduction').val(' ');
                $('#check_in').val(' ');
                $('#check_out').val(' ');
                if (data.status == 'success') {

                    $('#day').append(`${data.info[0].days}  Days `);
                    $('#hour').append(`${data.info[0].hours} hr ${data.info[0].minute} mins `);
                    $('#deduction').append(`${data.info[0].deduction} % `);
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



</body>

</html>


@endsection