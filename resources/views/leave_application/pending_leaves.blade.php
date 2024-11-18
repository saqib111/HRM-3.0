@extends('layout.mainlayout')
@section('content')
<style>
    .date5,
    .date4,
    .date3,
    .date {
        padding: 2px;
        cursor: pointer;
        text-align: center;
        border: 1px solid #ddd;
        transition: background-color 0.3s;
        border-radius: 50%;
        margin: 5px;
        width: 30px;
        height: 30px;
        line-height: 30px;
        font-size: 12px;
        display: flex;
        justify-content: center;
        align-items: center;
        background-color: rgb(194, 191, 191);
        color: #FFF;
    }

    .bg-light-blue {
        background-color: rgb(0, 197, 251);
    }

    .bg-light-green {
        background-color: rgb(147, 231, 122);
    }

    .bg-light-pink {
        background-color: rgb(245, 143, 186);
    }

    .bg-light-red {
        background-color: rgb(243, 112, 112);
    }

    .customFullWidth {
        width: 100%;
    }


    .modal-body {
        max-height: 80vh;
        /* Max height for the modal body */
        overflow-y: auto;
        /* Allow scrolling for the body */
    }

    .modal-content {
        max-height: 90vh;
        /* Set a max height for the modal itself */
        margin: auto;
        /* Center the modal */
    }

    .hideBlock {
        display: none !important;
    }

    @media (max-width: 768px) {
        .modal-body {
            max-height: 70vh;
            /* Adjust height for smaller screens */
        }
    }

    @media (max-width: 576px) {
        .modal-body {
            max-height: 60vh;
            /* Further adjustment for extra small screens */
        }
    }
</style>
<div class="page-header">
    <div class="row align-items-center justify-content-between">
        <div class="col-md-4">
            <h3 class="page-title">Pending Leave Applications</h3>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{route('dashboard')}}">Dashboard</a></li>
                <li class="breadcrumb-item active">Pending Leave Applications</li>
            </ul>
        </div>
        <div class="col-md-4 d-flex justify-content-end">
            <ul class="c_Employee">
                <li>
                    @if(auth()->user()->role === "1" || auth()->user()->role === "4")
                                        <div class="d-flex justify-content-end">
                                            <!-- Status Buttons (Pendings, Approved, Rejected) -->
                                            @php
                                                $statuses = ['pending' => 'Pending', 'approved' => 'Approved', 'rejected' => 'Rejected'];
                                            @endphp

                                            @foreach($statuses as $status => $label)
                                                <button class="btn btn-outline-primary mx-1 company-btn {{ $loop->first ? 'active' : '' }}"
                                                    data-status="{{ $status }}" onclick="filterByStatus('{{ $status }}')">
                                                    {{ $label }}
                                                </button>
                                            @endforeach
                                        </div>
                    @endif
                </li>
            </ul>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="table-responsive">
            <table class="table table-striped custom-table" id="leave_table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Employee ID</th>
                        <th>Username</th>
                        <th>Title</th>
                        <th>Description</th>
                        <th>Balance Leave</th>
                        <th>Day</th>
                        <th>From</th>
                        <th>To</th>
                        <th>Off Days</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>

<!-- Leave Detail Modal Start -->
<div class="modal custom-modal fade mt-4" role="dialog" id="leaveDetailsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header p-2 align-items-center shadow-sm">
                <h5 class="modal-title">Leave Application Details</h5>
                <button type="button" class="closed_btn" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">x</span>
                </button>
            </div>
            <div class="modal-body p-2">
                <div class="card-body p-4">
                    <div class="row">
                        <div class="col-12 ml-2">
                            <div class="personal-info">
                                <!-- Employee Information -->
                                <div
                                    class="d-flex flex-column align-items-start align-md-items-center flex-md-row justify-content-between flex-wrap">
                                    <div class="py-2  ">
                                        <span class="fw-semibold">Employee ID:</span>
                                        <span id="modal-employee-id" class="text-dark fs-6 ms-2">AV12345</span>
                                    </div>
                                    <div class="py-2 ms-2 text-center  ">
                                        <span class="fw-semibold">Username:</span>
                                        <span id="modal-username" class="text-dark fs-6 ms-2">Remy</span>
                                    </div>
                                    <div class="py-2 greenText text-center  ">
                                        <span class="fw-semibold">AL Balance:</span>
                                        <span id="modal-alblance" class="fs-6 ms-2">31</span>
                                    </div>
                                </div>

                                <!-- Title and Description -->
                                <hr class="my-2">
                                <div class="py-2 d-flex justify-content-center align-items-center">
                                    <span id="modal-title" class="text-dark fs-6">Senior Developer</span>
                                </div>
                                <hr class="my-2">
                                <div
                                    class="py-2 d-flex flex-column flex-md-row justify-content-between align-items-start p-0 p-md-1">
                                    <span class="fw-semibold">Description:</span>
                                    <span id="modal-description" class="text-dark fs-6 ms-3">Responsible for designing,
                                        developing, and maintaining software applications.</span>
                                </div>

                                <!-- Placeholder for Dynamic Leave Sections -->
                                <hr class="my-2">
                                <div class="py-2 d-flex justify-content-between align-items-center">
                                    <span class="fw-semibold">Total Leave Days:</span>
                                    <span class="text-dark fs-6" id="total_leave_days">34 days</span>
                                </div>
                                <div class="py-2 d-flex justify-content-between align-items-center">
                                    <span class="fw-semibold">Total AL Days:</span>
                                    <span class="text-dark fs-6" id="total_al_days">28 days</span>
                                </div>
                                <div class="py-2 d-flex justify-content-between align-items-center">
                                    <span class="fw-semibold">Total Off Days:</span>
                                    <span class="text-dark fs-6" id="total_off_days">6 days</span>
                                </div>
                                <hr class="my-2">
                                <div class="leave-sections"></div>

                                <!-- Total Days Information -->
                                <hr class="my-2">
                                <div class="leave-sections">
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="d-flex justify-content-between align-items-center py-2">
                                                <span class="fw-semibold">First Status: <span
                                                        id="first_status">Approved</span></span>
                                                <span class="fw-semibold">Approval Name: <span
                                                        id="first_approval_name">Test</span></span>
                                                <span class="fw-semibold">Date & Time: <span
                                                        id="first_created_time">12/02/2024
                                                        08:00:00</span></span>
                                            </div>
                                            <div class="d-flex justify-content-between align-items-center py-2">
                                                <span class="fw-semibold">Second Status: <span
                                                        id="second_status">Approved</span></span>
                                                <span class="fw-semibold">Approval Name: <span
                                                        id="second_approval_name">Test</span></span>
                                                <span class="fw-semibold">Date & Time: <span
                                                        id="second_created_time">12/02/2024
                                                        08:00:00</span></span>
                                            </div>
                                            <div class="d-flex justify-content-between align-items-center py-2">
                                                <span class="fw-semibold">HR Status: <span>Pending</span></span>
                                                <span class="fw-semibold">Approval Name: <span>Test</span></span>
                                                <span class="fw-semibold">Date & Time: <span>12/02/2024
                                                        08:00:00</span></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Action Buttons -->
            <div class="d-flex flex-column flex-sm-row justify-content-center mt-4 mb-4 p-2" id="action_buttons">
                <button class="btn btn-success mb-2 mb-sm-0" id="approval_btn">Approved</button>
                <button class="btn btn-danger ms-0 ms-sm-2" id="rejection_btn">Decline</button>
            </div>
        </div>
    </div>
</div>
<!-- Modal Ends -->

<!-- Dynamic Modal Ends -->

<!-- PreLoader -->
<div id="loader" class="loader" style="display: none;">
    <div class="loader-animation"></div>
</div>
@endsection

@section('script-z')
<script>
    let table;

    // Function to initialize the DataTable
    function initializeDataTable(status) {
        table = $('#leave_table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('leave_application.data') }}", // Backend URL
                data: function (d) {
                    d.status = status;  // Send the selected status as a query parameter
                }
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'employee_id', name: 'employee_id', orderable: false, searchable: false },
                { data: 'username', name: 'username', orderable: false, searchable: false },
                {
                    data: 'title',
                    render: function (data) {
                        return (data && data.length > 15) ? data.substring(0, 15) + '...' : data || ''; // Handle null
                    },
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'description',
                    render: function (data) {
                        return (data && data.length > 15) ? data.substring(0, 15) + '...' : data || ''; // Handle null
                    },
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'leave_balance',
                    render: function (data) {
                        // Convert the data to a number (in case it's a string)
                        let balance = parseFloat(data);

                        // Check if it's a valid number
                        if (!isNaN(balance)) {
                            // Format the number and append the 'Days' or 'Day'
                            return (balance >= 2) ? balance + ' Days' : balance + ' Day';
                        }

                        // If it's not a valid number, return 'N/A' or another default value
                        return 'N/A'; // or any appropriate default value
                    },
                    orderable: false,
                    searchable: false
                },
                { data: 'day', name: 'day', orderable: false, searchable: false },
                { data: 'from', name: 'from', orderable: false, searchable: false },
                { data: 'to', name: 'to', orderable: false, searchable: false },
                { data: 'off_days', name: 'off_days', orderable: false, searchable: false },
                {
                    data: 'id',
                    render: function (data) {
                        return '<div class="ms-3 toggle-modal" data-id="' + data +
                            '" style="cursor: pointer;">' +
                            '<i class="fas fa-eye"></i>' +
                            '</div>';
                    },
                    orderable: false,
                    searchable: false
                }
            ],
            order: [[0, 'desc']]
        });
    }

    // Function to filter data by company
    function filterByStatus(status) {
        // Clear the active class from all buttons
        $('.company-btn').removeClass('active');

        // Add active class to clicked button using the 'data-status' attribute
        $(`button[data-status='${status}']`).addClass('active');

        // Destroy the previous DataTable instance and reinitialize it with the new status
        if (table) {
            table.destroy();
        }

        // Reinitialize DataTable with the selected status
        initializeDataTable(status);
    }

    $(document).ready(function () {

        /// Initialize DataTable with the default status (Pending)
        const defaultStatus = 'pending';  // You can adjust this based on which status you want to load by default
        initializeDataTable(defaultStatus);

        // Handle the status buttons click event to filter data
        $('.company-btn').on('click', function () {
            const status = $(this).data('status');  // Get the status from the button's data-status attribute
            filterByStatus(status);
        });

        $('#approval_btn').click(function () {
            const id = $(this).data('id');
            const step = $(this).data('action');
            showLoader(); // Start loader

            // Fetch leave details using AJAX
            $.ajax({
                url: `/leave_action`, // Route for fetching leave application by ID
                method: 'POST',
                data: {
                    leave_id: id,
                    leave_action: 'approval_request',
                    leave_step: step,
                    _token: $('meta[name="csrf-token"]').attr('content'), // CSRF token for security
                },
                success: function (response) {
                    console.log(response);
                    hideLoader(); // Hide loader
                    $('#leaveDetailsModal').modal('hide');
                    $('#leave_table').DataTable().ajax.reload();
                },
                error: function (xhr) {
                    console.error('Error fetching leave application:', xhr);
                    hideLoader(); // Hide loader even if there's an error
                }
            });
        });

        $('#rejection_btn').click(function () {
            const id = $(this).data('id');
            const step = $(this).data('action');
            showLoader(); // Start loader

            // Fetch leave details using AJAX
            $.ajax({
                url: `/leave_action`, // Route for fetching leave application by ID
                method: 'POST',
                data: {
                    leave_id: id,
                    leave_action: 'reject_request',
                    leave_step: step,
                    _token: $('meta[name="csrf-token"]').attr('content'), // CSRF token for security
                },
                success: function (response) {
                    console.log(response);
                    hideLoader(); // Hide loader
                    $('#leaveDetailsModal').modal('hide');
                    $('#leave_table').DataTable().ajax.reload();
                },
                error: function (xhr) {
                    console.error('Error fetching leave application:', xhr);
                    hideLoader(); // Hide loader even if there's an error
                }
            });
        });

        // Fetch leave details and open the modal
        $(document).on('click', '.toggle-modal', function () {
            const id = $(this).data('id');
            const modal = $('#leaveDetailsModal');
            showLoader(); // Start loader

            // Fetch leave details using AJAX
            $.ajax({
                url: `/leave_application/${id}`, // Route for fetching leave application by ID
                method: 'GET',
                success: function (data) {
                    // Populate modal fields with dynamic data
                    $('#modal-employee-id').text(data.employee_id);
                    $('#modal-username').text(data.username);
                    $('#modal-title').text(data.title);
                    $('#modal-description').text(data.description);
                    $('#modal-alblance').text(data.leave_balance)

                    // Update 1st Step Information
                    updateApprovalStatus('#first_status', '#first_approval_name', '#first_created_time', data.status_1, data.first_approval_id, data.first_approval_created_time);
                    // Update 2nd Step Information
                    updateApprovalStatus('#second_status', '#second_approval_name', '#second_created_time', data.status_2, data.second_approval_id, data.second_approval_created_time);

                    // Show or hide action buttons based on status
                    const actionButtons = $('#action_buttons');

                    // Check if 2nd step needs action
                    if (data.status_1 === "approved" && data.status_2 === "pending") {
                        $('#approval_btn, #rejection_btn').prop('disabled', false); // Enable buttons
                        actionButtons.removeClass("hideBlock");
                        $('#approval_btn').data('id', id);
                        $('#rejection_btn').data('id', id);
                        $('#approval_btn').data('action', 'second_status');
                        $('#rejection_btn').data('action', 'second_status');
                    }
                    // Check if 1st step needs action
                    else if (data.status_1 === "pending") {
                        $('#approval_btn, #rejection_btn').prop('disabled', false); // Enable buttons
                        actionButtons.removeClass("hideBlock");
                        $('#approval_btn').data('id', id);
                        $('#rejection_btn').data('id', id);
                        $('#approval_btn').data('action', 'first_status');
                        $('#rejection_btn').data('action', 'first_status');
                    }
                    // If both steps are completed, hide buttons
                    else {
                        $('#approval_btn, #rejection_btn').prop('disabled', true); // Disable buttons
                        actionButtons.addClass("hideBlock");
                    }

                    // Separate off-days from leave details
                    const offDays = data.leave_details.filter(leave => leave.type === 'off_day').map(leave => leave.date);
                    const leaveDetails = data.leave_details.filter(leave => leave.type !== 'off_day');

                    // Populate leave sections dynamically
                    populateLeaveSection(leaveDetails, offDays);

                    // Show the modal
                    modal.modal('show');
                    hideLoader(); // Hide loader
                },
                error: function (xhr) {
                    console.error('Error fetching leave application:', xhr);
                    hideLoader(); // Hide loader even if there's an error
                }
            });
        });

        // Helper function to update approval statuses in the modal
        function updateApprovalStatus(statusSelector, nameSelector, timeSelector, status, approverName, approvalTime) {
            const statusElement = $(statusSelector);
            const nameElement = $(nameSelector);
            const timeElement = $(timeSelector);

            if (status === 'pending') {
                statusElement.text("Pending").removeClass().addClass("fw-semibold yellowText");
                nameElement.text(approverName || 'N/A').removeClass().addClass("fw-semibold yellowText");
                timeElement.text(approvalTime || 'N/A').removeClass().addClass("fw-semibold yellowText");
            } else if (status === 'approved') {
                statusElement.text("Approved").removeClass().addClass("fw-semibold greenText");
                nameElement.text(approverName || 'N/A').removeClass().addClass("fw-semibold greenText");
                timeElement.text(approvalTime || 'N/A').removeClass().addClass("fw-semibold greenText");
            } else if (status === 'rejected') {
                statusElement.text("Rejected").removeClass().addClass("fw-semibold redText");
                nameElement.text(approverName || 'N/A').removeClass().addClass("fw-semibold redText");
                timeElement.text(approvalTime || 'N/A').removeClass().addClass("fw-semibold redText");
            }
        }

        function populateLeaveSection(leaveDetails, offDays) {
            const leaveSections = document.querySelector('.leave-sections');
            leaveSections.innerHTML = ''; // Clear previous content

            let totalLeaveDays = 0;
            let totalAnnualLeaveDays = 0;
            let totalOffDaysInAnnualLeave = 0;
            let totalOffDays = offDays.length;
            let halfDayCounter = 0;


            // Group leaves by type and month, excluding off-days from annual and unpaid leaves
            const groupedLeaves = {};

            leaveDetails.forEach(leave => {
                const leaveTypeId = parseInt(leave.leave_type_id, 10);

                // Define leave type and corresponding color
                let leaveType = '';
                let bgColorClass = '';

                switch (leaveTypeId) {
                    case 1:
                        leaveType = leave.type === 'full_day' ? 'Annual Leave (Full Day)' : 'Annual Leave (Half Day)';
                        bgColorClass = 'bg-light-blue';
                        break;
                    case 2:
                        leaveType = 'Birthday Leave';
                        bgColorClass = 'bg-light-green';
                        break;
                    case 3:
                        leaveType = 'Marriage Leave';
                        bgColorClass = 'bg-light-pink';
                        break;
                    case 4:
                        leaveType = 'Unpaid Leave';
                        bgColorClass = 'bg-light-red';
                        break;
                    default:
                        bgColorClass = 'bg-light-gray';
                        leaveType = 'Other Leave';
                }

                const startDate = new Date(leave.start_date);
                const endDate = new Date(leave.end_date);
                let leaveDaysCount = (endDate - startDate) / (1000 * 3600 * 24) + 1;

                if (leave.type === 'half_day') {
                    leaveDaysCount = 0.5;
                    halfDayCounter += 0.5;
                }

                totalLeaveDays += leaveDaysCount;

                if (leaveTypeId === 1) { // Only for annual leave
                    let currentAnnualLeaveDays = 0;
                    let currentDate = new Date(startDate);

                    while (currentDate <= endDate) {
                        const formattedDate = currentDate.toISOString().split('T')[0];

                        if (!offDays.includes(formattedDate)) {
                            currentAnnualLeaveDays++;
                        } else {
                            totalOffDaysInAnnualLeave++;
                        }
                        currentDate.setDate(currentDate.getDate() + 1);
                    }
                    totalAnnualLeaveDays += currentAnnualLeaveDays;
                }

                let currentDate = new Date(startDate);

                while (currentDate <= endDate) {
                    const formattedDate = currentDate.toISOString().split('T')[0];
                    const monthName = currentDate.toLocaleString('default', { month: 'long' });

                    if (!offDays.includes(formattedDate)) { // Exclude off-days here
                        if (!groupedLeaves[leaveType]) {
                            groupedLeaves[leaveType] = {};
                        }

                        if (!groupedLeaves[leaveType][monthName]) {
                            groupedLeaves[leaveType][monthName] = [];
                        }

                        groupedLeaves[leaveType][monthName].push({
                            date: currentDate.getDate(),
                            fullDate: formattedDate,
                            bgColorClass: bgColorClass,
                            leaveType: leaveType,
                        });
                    }

                    currentDate.setDate(currentDate.getDate() + 1);
                }
            });

            // Render leave details without off-days
            for (let leaveType in groupedLeaves) {
                for (let month in groupedLeaves[leaveType]) {
                    const section = document.createElement('div');
                    section.classList.add('personal-info', 'px-1', 'py-2', 'd-flex', 'justify-content-between', 'align-items-center', 'flex-wrap');

                    const firstLeaveDay = groupedLeaves[leaveType][month][0].fullDate;
                    const lastLeaveDay = groupedLeaves[leaveType][month][groupedLeaves[leaveType][month].length - 1].fullDate;

                    section.innerHTML = `
                <span class="fw-semibold">${groupedLeaves[leaveType][month][0].leaveType} (${month}):</span>
                <div>
                    <span class="text-muted">From:</span>
                    <span class="text-dark fs-6 ms-1">${formatDate(firstLeaveDay)}</span>
                </div>
                <div>
                    <span class="text-muted">To:</span>
                    <span class="text-dark fs-6 ms-1">${formatDate(lastLeaveDay)}</span>
                </div>
                <div class="d-flex flex-wrap ms-2 customFullWidth"></div>
            `;

                    const dateRow = section.querySelector('.d-flex.flex-wrap');

                    groupedLeaves[leaveType][month].forEach(leave => {
                        const dateCircle = document.createElement('div');
                        dateCircle.classList.add('date', 'col-3', 'col-sm-1', 'col-md-1', 'p-0', 'p-md-1');
                        dateCircle.classList.add(leave.bgColorClass);
                        dateCircle.innerText = leave.date;
                        dateRow.appendChild(dateCircle);
                    });

                    leaveSections.appendChild(section);
                }
            }

            // Display off-days in a separate section with month info
            displayOffDays(offDays);
            // Handle half-day leaves separately after full-day processing
            leaveDetails.forEach(leave => {
                if (leave.type === 'half_day') {
                    displayHalfDayLeave(leave);
                }
            });

            document.getElementById('total_leave_days').textContent = `${totalLeaveDays} days`;
            document.getElementById('total_al_days').textContent = `${totalAnnualLeaveDays + halfDayCounter} days`;
            document.getElementById('total_off_days').textContent = `${totalOffDays} days`;
        }

        function displayOffDays(offDays) {
            const leaveSections = document.querySelector('.leave-sections');

            if (offDays.length > 0) {
                // Group off-days by month
                const offDaysByMonth = {};

                offDays.forEach(date => {
                    const monthName = new Date(date).toLocaleString('default', { month: 'long' });
                    if (!offDaysByMonth[monthName]) {
                        offDaysByMonth[monthName] = [];
                    }
                    offDaysByMonth[monthName].push(new Date(date).getDate());
                });

                // Loop through each month and display off-days in a structured format
                for (let month in offDaysByMonth) {
                    const section = document.createElement('div');
                    section.classList.add('personal-info', 'px-1', 'py-2', 'd-flex', 'justify-content-between', 'align-items-center', 'flex-wrap');

                    // Structure similar to other leave types
                    section.innerHTML = `
                <span class="fw-semibold">Off Days (${month}):</span>
                <div class="d-flex flex-wrap ms-2 customFullWidth"></div>
            `;

                    const dateRow = section.querySelector('.d-flex.flex-wrap');

                    // Add each off-day as a date circle with a gray background
                    offDaysByMonth[month].forEach(day => {
                        const dateCircle = document.createElement('div');
                        dateCircle.classList.add('date', 'col-3', 'col-sm-1', 'col-md-1', 'p-0', 'p-md-1', 'bg-light-gray');
                        dateCircle.innerText = day;
                        dateRow.appendChild(dateCircle);
                    });

                    // Append the section to the leave sections container
                    leaveSections.appendChild(section);
                }
            }
        }

        // Separate function for handling half-day leave display
        function displayHalfDayLeave(leave) {
            const leaveSections = document.querySelector('.leave-sections');

            // Add the <hr> before half-day leave section
            const hrElement = document.createElement('hr');
            hrElement.classList.add('my-2');
            leaveSections.appendChild(hrElement);

            const halfDaySection = document.createElement('div');
            halfDaySection.classList.add('personal-info', 'px-1', 'py-2', 'd-flex', 'justify-content-between', 'flex-wrap');

            halfDaySection.innerHTML = `
        <span class="fw-semibold">Annual Leave (Half Day):</span>
        <div class="d-flex gap-3 ms-2">
            <div class="d-flex justify-content-between">
                <span class="text-muted">Date:</span>
                <span class="text-dark fs-6 ms-2">${formatDate(leave.date)}</span>
            </div>
            <div class="d-flex justify-content-between">
                <span class="text-muted">Start Time:</span>
                <span class="text-dark fs-6 ms-2">${leave.start_time || 'N/A'}</span>
            </div>
            <div class="d-flex justify-content-between">
                <span class="text-muted">End Time:</span>
                <span class="text-dark fs-6 ms-2">${leave.end_time || 'N/A'}</span>
            </div>
        </div>`;

            // Append the half-day leave section
            leaveSections.appendChild(halfDaySection);

            // Display the half-day leave date as a circle
            const halfDayCircle = document.createElement('div');
            halfDayCircle.classList.add('date', 'col-3', 'col-sm-1', 'col-md-1', 'p-0', 'p-md-1', 'ms-3', 'bg-light-blue');
            halfDayCircle.innerText = new Date(leave.date).getDate();
            leaveSections.appendChild(halfDayCircle);
        }


        // Helper function to format date in a readable way
        function formatDate(date) {
            const options = { year: 'numeric', month: 'long', day: 'numeric' };
            return new Date(date).toLocaleDateString(undefined, options);
        }

        // Helper function to check if a date is an off-day
        function isOffDay(date, offDays) {
            return offDays.includes(date);
        }

        // Hide modal when close button is clicked
        $('.btn-close').on('click', function () {
            $('#leaveDetailsModal').modal('hide');
        });
    });
</script>
@endsection






























<!-- 

function populateLeaveSection(leaveDetails, offDays) {
            const leaveSections = document.querySelector('.leave-sections');
            leaveSections.innerHTML = ''; // Clear previous content

            let totalLeaveDays = 0;
            let totalAnnualLeaveDays = 0;
            let totalOffDaysInAnnualLeave = 0;
            let totalOffDays = offDays.length; // Already filtered off days

            // Group leaves by type and month
            const groupedLeaves = {};

            leaveDetails.forEach(leave => {
                const leaveTypeId = parseInt(leave.leave_type_id, 10);

                // Define leave type and corresponding color
                let leaveType = '';
                let bgColorClass = '';

                switch (leaveTypeId) {
                    case 1:
                        leaveType = leave.type === 'full_day' ? 'Annual Leave (Full Day)' : 'Annual Leave (Half Day)';
                        bgColorClass = 'bg-light-blue';
                        break;
                    case 2:
                        leaveType = 'Birthday Leave';
                        bgColorClass = 'bg-light-green';
                        break;
                    case 3:
                        leaveType = 'Marriage Leave';
                        bgColorClass = 'bg-light-pink';
                        break;
                    case 4:
                        leaveType = 'Unpaid Leave';
                        bgColorClass = 'bg-light-red';
                        break;
                    default:
                        bgColorClass = 'bg-light-gray'; // Fallback color
                        leaveType = 'Other Leave'; // Default type
                }

                // Process leaves by grouping them by type and then by month
                const startDate = new Date(leave.start_date);
                const endDate = new Date(leave.end_date);
                let leaveDaysCount = (endDate - startDate) / (1000 * 3600 * 24) + 1; // Days count for full-day leaves

                // If it's a half-day leave, count as 0.5 days
                if (leave.type === 'half_day') {
                    leaveDaysCount = 0.5;
                }

                // Add leave days to total leave days
                totalLeaveDays += leaveDaysCount;

                // Add only annual leave days to total annual leave days
                if (leaveTypeId === 1) {
                    let currentAnnualLeaveDays = 0;
                    let currentDate = new Date(startDate);

                    while (currentDate <= endDate) {
                        const formattedDate = currentDate.toISOString().split('T')[0];

                        // Check if the current date is an off day
                        if (!offDays.includes(formattedDate)) {
                            currentAnnualLeaveDays++;
                        } else {
                            totalOffDaysInAnnualLeave++; // Count only off days within annual leave
                        }

                        currentDate.setDate(currentDate.getDate() + 1);
                    }

                    totalAnnualLeaveDays += currentAnnualLeaveDays;
                }


                let currentDate = new Date(startDate);

                while (currentDate <= endDate) {
                    const formattedDate = currentDate.toISOString().split('T')[0];
                    const monthName = currentDate.toLocaleString('default', { month: 'long' });

                    // Initialize groupings if they don't exist
                    if (!groupedLeaves[leaveType]) {
                        groupedLeaves[leaveType] = {};
                    }

                    if (!groupedLeaves[leaveType][monthName]) {
                        groupedLeaves[leaveType][monthName] = [];
                    }

                    // Add date to the corresponding type and month
                    groupedLeaves[leaveType][monthName].push({
                        date: currentDate.getDate(),
                        fullDate: formattedDate,
                        bgColorClass: isOffDay(formattedDate, offDays) ? 'bg-light-gray' : bgColorClass,
                        leaveType: leaveType, // Pass the leaveType into the grouping
                    });

                    currentDate.setDate(currentDate.getDate() + 1);
                }
            });

            // Now render each type and its respective months
            for (let leaveType in groupedLeaves) {
                for (let month in groupedLeaves[leaveType]) {
                    const section = document.createElement('div');
                    section.classList.add('personal-info', 'px-1', 'py-2', 'd-flex', 'justify-content-between', 'align-items-center', 'flex-wrap');

                    const firstLeaveDay = groupedLeaves[leaveType][month][0].fullDate;
                    const lastLeaveDay = groupedLeaves[leaveType][month][groupedLeaves[leaveType][month].length - 1].fullDate;

                    // Display the date range for the leave type
                    section.innerHTML = `
                <span class="fw-semibold">${groupedLeaves[leaveType][month][0].leaveType} (${month}):</span>
                <div>
                    <span class="text-muted">From:</span>
                    <span class="text-dark fs-6 ms-1">${formatDate(firstLeaveDay)}</span>
                </div>
                <div>
                    <span class="text-muted">To:</span>
                    <span class="text-dark fs-6 ms-1">${formatDate(lastLeaveDay)}</span>
                </div>
                <div class="d-flex flex-wrap ms-2 customFullWidth"></div>
            `;

                    const dateRow = section.querySelector('.d-flex.flex-wrap');

                    // Add the dates for this month
                    groupedLeaves[leaveType][month].forEach(leave => {
                        const dateCircle = document.createElement('div');
                        dateCircle.classList.add('date', 'col-3', 'col-sm-1', 'col-md-1', 'p-0', 'p-md-1');

                        if (leave.bgColorClass && leave.bgColorClass.trim() !== '') {
                            dateCircle.classList.add(leave.bgColorClass);
                        } else {
                            console.error(`Error: Empty bgColorClass for leave on ${leave.fullDate}`);
                        }

                        dateCircle.innerText = leave.date;
                        dateRow.appendChild(dateCircle);
                    });

                    // Append each month row to the section
                    leaveSections.appendChild(section);
                }
            }

            // Handle half-day leaves separately after full-day processing
            leaveDetails.forEach(leave => {
                if (leave.type === 'half_day') {
                    displayHalfDayLeave(leave);
                }
            });

            // Update totals in the modal
            document.getElementById('total_leave_days').textContent = `${totalLeaveDays} days`;
            document.getElementById('total_al_days').textContent = `${totalAnnualLeaveDays} days`;
            document.getElementById('total_off_days').textContent = `${totalOffDays} days`;
        } -->