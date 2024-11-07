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
    <div class="row align-items-center">
        <div class="col-md-4">
            <h3 class="page-title">Pending Leave Applications</h3>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="admin-dashboard.html">Dashboard</a></li>
                <li class="breadcrumb-item active">Pending Leave Applications</li>
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

<!-- Dynamic Modal Start -->
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
    $(document).ready(function () {
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

        // Initialize DataTables
        $('#leave_table').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('leave_application.data') }}",
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'employee_id', name: 'employee_id', orderable: false, searchable: false },
                { data: 'username', name: 'username', orderable: false, searchable: false },
                // { data: 'title', name: 'title' },
                // { data: 'description', name: 'description' },
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
                // { data: 'leave_balance', name: 'leave_balance', orderable: false, searchable: false },
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

                    // 1st Step Information
                    if (data.status_1 === 'pending') {
                        $('#first_status').text("Pending");
                        $('#first_status').addClass("yellowText");
                        $('#first_approval_name').text(data.first_approval_id);
                        $('#first_approval_name').addClass("yellowText");
                        $('#first_created_time').text(data.first_approval_created_time);
                        $('#first_created_time').addClass("yellowText");

                    } else if (data.status_1 === 'approved') {
                        $('#first_status').text("Approved");
                        $('#first_status').addClass("greenText");
                        $('#first_approval_name').text(data.first_approval_id);
                        $('#first_approval_name').addClass("greenText");
                        $('#first_created_time').text(data.first_approval_created_time);
                        $('#first_created_time').addClass("greenText");

                    } else if (data.status_1 === 'rejected') {
                        $('#first_status').text("Rejected");
                        $('#first_status').addClass("redText");
                        $('#first_approval_name').text(data.first_approval_id);
                        $('#first_approval_name').addClass("redText");
                        $('#first_created_time').text(data.first_approval_created_time);
                        $('#first_created_time').addClass("redText");
                    }

                    // 2nd Step Information
                    if (data.status_2 === 'pending') {
                        $('#second_status').text("Pending");
                        $('#second_status').addClass("yellowText");
                        $('#second_approval_name').text(data.second_approval_id);
                        $('#second_approval_name').addClass("yellowText");
                        $('#second_created_time').text(data.second_approval_created_time);
                        $('#second_created_time').addClass("yellowText");

                    } else if (data.status_2 === 'approved') {
                        $('#second_status').text("Approved");
                        $('#second_status').addClass("greenText");
                        $('#second_approval_name').text(data.second_approval_id);
                        $('#second_approval_name').addClass("greenText");
                        $('#second_created_time').text(data.second_approval_created_time);
                        $('#second_created_time').addClass("greenText");

                    } else if (data.status_2 === 'rejected') {
                        $('#second_status').text("Rejected");
                        $('#second_status').addClass("redText");
                        $('#second_approval_name').text(data.second_approval_id);
                        $('#second_approval_name').addClass("redText");
                        $('#second_created_time').text(data.second_approval_created_time);
                        $('#second_created_time').addClass("redText");
                    }

                    // Set the data-id & data-action attribute on buttons
                    $('#approval_btn').data('id', id);
                    $('#rejection_btn').data('id', id);
                    if (data.status_1 === "pending") {
                        $('#approval_btn').data('action', 'first_status');
                        $('#rejection_btn').data('action', 'first_status');
                    } else if (data.status_1 === "approved") {
                        $('#approval_btn').data('action', 'second_status');
                        $('#rejection_btn').data('action', 'second_status');
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

        function populateLeaveSection(leaveDetails, offDays) {
            const leaveSections = document.querySelector('.leave-sections');
            leaveSections.innerHTML = ''; // Clear previous content

            let totalLeaveDays = 0;
            let totalAnnualLeaveDays = 0;
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
                    totalAnnualLeaveDays += leaveDaysCount;
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
            document.getElementById('total_al_days').textContent = `${totalAnnualLeaveDays - totalOffDays} days`;
            document.getElementById('total_off_days').textContent = `${totalOffDays} days`;
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