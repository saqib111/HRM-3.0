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
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Leave Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">x</span>
                </button>
            </div>
            <div class="card shadow-lg rounded border-0" style="max-width: 800px; margin: auto;">
                <div class="card-body p-4">
                    <div class="row">
                        <div class="col-12 ml-2">
                            <div class="personal-info">
                                <!-- Employee Information -->
                                <div class="px-2 d-flex flex-column">
                                    <div class="py-2 d-flex justify-content-between align-items-center">
                                        <span class="fw-semibold">Employee ID:</span>
                                        <span id="modal-employee-id" class="text-dark fs-6 ms-2">EMP12345</span>
                                    </div>
                                    <div class="py-2 d-flex justify-content-between align-items-center">
                                        <span class="fw-semibold">Username:</span>
                                        <span id="modal-username" class="text-dark fs-6">Rohan1</span>
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
                                <div class="leave-sections"></div>

                                <!-- Total Days Information -->
                                <div class="py-2 d-flex justify-content-between align-items-center">
                                    <span class="fw-semibold">Total Leave Days:</span>
                                    <span class="text-dark fs-6">34 days</span>
                                </div>
                                <div class="py-2 d-flex justify-content-between align-items-center">
                                    <span class="fw-semibold">Annual Off Days:</span>
                                    <span class="text-dark fs-6">28 days</span>
                                </div>
                                <div class="py-2 d-flex justify-content-between align-items-center">
                                    <span class="fw-semibold">Off Days:</span>
                                    <span class="text-dark fs-6">6 days</span>
                                </div>

                                <hr class="my-2">

                                <!-- Action Buttons -->
                                <div class="d-flex flex-column flex-sm-row justify-content-center mt-4">
                                    <button class="btn btn-success mb-2 mb-sm-0">Approved</button>
                                    <button class="btn btn-danger ms-0 ms-sm-2">Decline</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
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
        // Initialize DataTables
        $('#leave_table').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('leave_application.data') }}",
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'employee_id', name: 'employee_id' },
                { data: 'username', name: 'username' },
                { data: 'title', name: 'title' },
                { data: 'description', name: 'description' },
                { data: 'day', name: 'day', orderable: false, searchable: false },
                { data: 'from', name: 'from', orderable: false, searchable: false },
                { data: 'to', name: 'to', orderable: false, searchable: false },
                { data: 'off_days', name: 'off_days', orderable: false, searchable: false },
                {
                    data: 'id',
                    render: function (data) {
                        return '<button class="btn btn-info toggle-modal" data-id="' + data + '">👁️</button>';
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

        // Function to populate leave sections dynamically based on leave types and off-days
        function populateLeaveSection(leaveDetails, offDays) {
            const leaveSections = document.querySelector('.leave-sections');
            leaveSections.innerHTML = ''; // Clear previous content

            leaveDetails.forEach(leave => {
                // Parse leave_type_id as an integer to avoid type mismatch
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

                // Create section for this leave type
                const section = document.createElement('div');
                section.classList.add('personal-info', 'py-2', 'd-flex', 'justify-content-between', 'align-items-center', 'flex-wrap');

                // Section header with date range or half-day time
                let sectionContent = '';

                if (leave.type === 'full_day') {
                    sectionContent = `
                    <span class="fw-semibold">${leaveType}:</span>
                    <div class="d-flex flex-column flex-sm-row ms-2">
                        <div class="me-3">
                            <span class="text-muted">From:</span>
                            <span class="text-dark fs-6 ms-2">${formatDate(leave.start_date)}</span>
                        </div>
                        <div>
                            <span class="text-muted">To:</span>
                            <span class="text-dark fs-6 ms-2">${formatDate(leave.end_date || leave.date)}</span>
                        </div>
                    </div>
                `;
                } else if (leave.type === 'half_day') {
                    sectionContent = `
                    <span class="fw-semibold">${leaveType}:</span>
                    <div class="d-flex flex-column flex-sm-row ms-2">
                        <div class="me-3">
                            <span class="text-muted">Date:</span>
                            <span class="text-dark fs-6 ms-2">${formatDate(leave.date)}</span>
                        </div>
                        <div class="me-3">
                            <span class="text-muted">Start Time:</span>
                            <span class="text-dark fs-6 ms-2">${leave.start_time}</span>
                        </div>
                        <div>
                            <span class="text-muted">End Time:</span>
                            <span class="text-dark fs-6 ms-2">${leave.end_time}</span>
                        </div>
                    </div>
                `;
                }

                section.innerHTML = sectionContent;

                // Create a row for date circles for full-day or half-day leave
                const dateRow = document.createElement('div');
                dateRow.classList.add('d-flex', 'flex-wrap', 'customFullWidth');

                if (leave.type === 'full_day') {
                    // Loop through the date range and create date circles
                    const startDate = new Date(leave.start_date);
                    const endDate = leave.end_date ? new Date(leave.end_date) : new Date(leave.date); // Handle half-day leave as well

                    let currentDate = new Date(startDate);
                    while (currentDate <= endDate) {
                        const formattedDate = currentDate.toISOString().split('T')[0];

                        // Create the date circle
                        const dateCircle = document.createElement('div');
                        dateCircle.classList.add('date', 'col-3', 'col-sm-1', 'col-md-1', 'p-0', 'p-md-1');

                        // If it's an off-day, make it gray
                        if (isOffDay(formattedDate, offDays)) {
                            dateCircle.classList.add('bg-light-gray');
                        } else {
                            dateCircle.classList.add(bgColorClass); // Use the color based on the leave type
                        }

                        // Set the inner text of the circle to the day of the month
                        dateCircle.innerText = currentDate.getDate();

                        // Append the date circle to the row
                        dateRow.appendChild(dateCircle);

                        // Move to the next day
                        currentDate.setDate(currentDate.getDate() + 1);
                    }
                } else if (leave.type === 'half_day') {
                    // For half-day leave, show the single date with time
                    const halfDayDate = new Date(leave.date);

                    const dateCircle = document.createElement('div');
                    dateCircle.classList.add('date', 'col-3', 'col-sm-1', 'col-md-1', 'p-0', 'p-md-1');
                    dateCircle.classList.add(bgColorClass); // Use the leave type color

                    // Set the inner text of the circle to the day of the month
                    dateCircle.innerText = halfDayDate.getDate();

                    // Append the date circle to the row
                    dateRow.appendChild(dateCircle);

                    // Add a time label below the date
                    const timeLabel = document.createElement('div');
                    timeLabel.classList.add('ms-2', 'text-muted');
                    timeLabel.innerHTML = `<small>${leave.start_time} - ${leave.end_time}</small>`;

                    // Append the time label to the row
                    dateRow.appendChild(timeLabel);
                }

                // Append the date row to the section
                section.appendChild(dateRow);

                // Add the section to the leaveSections container
                leaveSections.appendChild(section);
            });
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