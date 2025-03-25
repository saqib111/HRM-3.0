@extends('layout.mainlayout')
@section('content')

    @php
        $user = auth()->user();
        $permissions = getUserPermissions($user); // Use the helper function to fetch permissions
    @endphp
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

        .bg-light-yellow {
            background-color: rgb(244 225 66);
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

        #images-sections {
            display: flex;
            max-width: 100px;
            cursor: pointer;
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
                <h3 class="page-title"><span>Unlock Leave Category Applications</span>
                </h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{route('dashboard')}}">Dashboard</a></li>
                    <li class="breadcrumb-item active"><span>Unlock Leave Category Applications</span></li>
                </ul>
            </div>
            <div class="col-md8 d-flex justify-content-end me-0">
                <ul class="c_Employee">
                    <li>
                        @if(auth()->user()->role === "1" || in_array('pending_leaves', $permissions))
                                            <div class="d-flex justify-content-end flex-wrap">
                                                <!-- Status Buttons (Pendings, Approved, Rejected) -->
                                                @php
                                                    $statuses = ['pending' => 'Pending', 'approved' => 'Approved', 'rejected' => 'Rejected'];
                                                @endphp

                                                @foreach($statuses as $status => $label)
                                                    <button class="btn btn-outline-primary mx-1 company-btn {{ $loop->first ? 'active' : '' }}"
                                                        style="width:138px; margin-bottom: 10px;" data-status="{{ $status }}"
                                                        onclick="filterByStatus('{{ $status }}')">
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
                            <th><span data-translate="employee_id">Employee ID</span></th>
                            <th><span data-translate="username">Username</span></th>
                            <th><span data-translate="title">Title</span></th>
                            <th><span data-translate="description">Description</span></th>
                            <th><span data-translate="balance">Leave Balance</span></th>
                            <th><span data-translate="day">Day</span></th>
                            <th><span data-translate="from">From</span></th>
                            <th><span data-translate="to">To</span></th>
                            <th><span data-translate="action">Action</span></th>
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
                    <h5 class="modal-title"><span data-translate="leave_application_details">Leave Application
                            Details</span></h5>
                    <button type="button" class="closed_btn" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true"></span>
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
                                            <span class="fw-semibold"><span data-translate="modal_employee_id">Employee
                                                    ID:</span></span>
                                            <span id="modal-employee-id" class="text-dark fs-6 ms-2">AV12345</span>
                                        </div>
                                        <div class="py-2 ms-2 text-center  ">
                                            <span class="fw-semibold"><span
                                                    data-translate="modal_username">Username:</span></span>
                                            <span id="modal-username" class="text-dark fs-6 ms-2">Remy</span>
                                        </div>
                                        <div class="py-2 greenText text-center  ">
                                            <span class="fw-semibold"><span data-translate="al_balance">AL
                                                    Balance:</span></span>
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
                                        <span class="fw-semibold"><span data-translate="modal_description">Description:
                                            </span></span>
                                        <span id="modal-description" class="text-dark fs-6 ms-3">Responsible for designing,
                                            developing, and maintaining software applications.</span>
                                    </div>

                                    <!-- Placeholder for Dynamic Leave Sections -->
                                    <hr class="my-2">
                                    <div class="py-2 d-flex justify-content-between align-items-center">
                                        <span class="fw-semibold"><span data-translate="total_leave_days">Total Leave Days:
                                            </span></span>
                                        <span class="text-dark fs-6" id="total_leave_days">34 days</span>
                                    </div>
                                    <div class="py-2 d-flex justify-content-between align-items-center">
                                        <span class="fw-semibold"><span data-translate="total_al_days">Total AL Days:
                                            </span></span>
                                        <span class="text-dark fs-6" id="total_al_days">28 days</span>
                                    </div>
                                    <div class="py-2 d-flex justify-content-between align-items-center">
                                        <span class="fw-semibold"><span data-translate="total_off_days">Total Off Days:
                                            </span></span>
                                        <span class="text-dark fs-6" id="total_off_days">6 days</span>
                                    </div>
                                    <hr class="my-2">
                                    <div class="leave-sections"></div>

                                    <hr class="my-2">
                                    <div id="images-sections"></div>

                                    <!-- Total Days Information -->
                                    <hr class="my-2">
                                    <div class="leave-sections">
                                        <div class="row">
                                            <div class="col-12">
                                                <div class="d-flex justify-content-between align-items-center py-2">
                                                    <span class="fw-semibold"><span>Status : </span><span id="first_status">
                                                            Approved</span></span>
                                                    <span class="fw-semibold"><span>Approval Name : </span><span
                                                            id="first_approval_name"> Test</span></span>
                                                    <span class="fw-semibold"><span>Date & Time : </span><span
                                                            id="superadmin_created_at"> 12/02/2024 08:00:00</span></span>
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
                    <button class="btn btn-success mb-2 mb-sm-0" id="approval_btn"><span
                            data-translate="approve">Approve</span></button>
                    <button class="btn btn-danger ms-0 ms-sm-2" id="rejection_btn"><span
                            data-translate="decline">Decline</span></button>
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
                    url: "{{ route('data.leaveApplication') }}", // Backend URL
                    data: function (d) {
                        d.status = status;  // Send the selected status as a query parameter
                    }
                },
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'employee_id', name: 'employee_id', orderable: false },
                    { data: 'username', name: 'username', orderable: false },
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
                console.log(status);
            });

            $('#approval_btn').click(function () {
                const id = $(this).data('id');

                showLoader(); // Start loader

                $.ajax({
                    url: "{{route('action.leaveApplication')}}",
                    method: "POST",
                    data: {
                        leave_id: id,
                        leave_action: 'approve_request',
                        _token: $('meta[name="csrf-token"]').attr('content'),

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
                })

            });

            $('#rejection_btn').click(function () {
                const id = $(this).data('id');

                showLoader(); // Start loader

                // Fetch leave details using AJAX
                $.ajax({
                    url: "{{route('action.leaveApplication')}}", // Route for fetching leave application by ID
                    method: "POST",
                    data: {
                        leave_id: id,
                        leave_action: 'reject_request',
                        _token: $('meta[name="csrf-token"]').attr('content'),
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
                var auth_id = {{ auth()->user()->id }};
                showLoader(); // Start loader

                // Fetch leave details using AJAX
                $.ajax({
                    url: `/view-unlock-leave-details/${id}`, // Route for fetching leave application by ID
                    method: 'GET',
                    success: function (data) {
                        // Populate modal fields with dynamic data
                        $('#modal-employee-id').text(data.employee_id);
                        $('#modal-username').text(data.username);
                        $('#modal-title').text(data.title);
                        $('#modal-description').text(data.description);
                        $('#modal-alblance').text(data.leave_balance)
                        $("#images-section").empty();

                        if (data.images && data.images.length > 0) {

                            $('#images-sections').empty();  // This removes any previous images

                            // Loop through and append the images
                            data.images.forEach(function (image) {
                                const imgElement = $('<img>', {
                                    src: image,   // Use the image URL from the server response
                                    alt: 'Leave Image', // Adjust the alt text as needed
                                    class: 'img-fluid', // Optional class for styling
                                    // style: 'max-width: 100%; height: auto; margin-bottom: 10px;' // Optional styling
                                });
                                imgElement.on('click', function () {
                                    window.open(image, '_blank'); // Open the image in a new tab
                                });
                                $('#images-sections').append(imgElement); // Append each image
                            });
                        } else {
                            // If no images are available, show a message or leave the section empty
                            $('#images-sections').empty();  // Clear any previous content
                            $('#images-sections').append('<p>No images available</p>');
                        }


                        // Update 1st Step Information
                        updateApprovalStatus('#first_status', '#first_approval_name', '#superadmin_created_at', data.status, data.superadmin_id, data.superadmin_created_at);

                        // Show or hide action buttons based on status
                        const actionButtons = $('#action_buttons');

                        if (data.status === "pending") {
                            $('#approval_btn, #rejection_btn').prop('disabled', false); // Enable buttons
                            actionButtons.removeClass("hideBlock");
                            $('#approval_btn').data('id', id);
                            $('#rejection_btn').data('id', id);
                        } else {
                            $('#approval_btn, #rejection_btn').prop('disabled', true); // Enable buttons
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
                } else if (status === 'Null') {
                    statusElement.text("Pending").removeClass().addClass("fw-semibold yellowText");
                    nameElement.text(approverName || 'N/A').removeClass().addClass("fw-semibold yellowText");
                    timeElement.text(approvalTime || 'N/A').removeClass().addClass("fw-semibold yellowText");
                } else if (status === '1') { // Check if status is '1' (Revoked)
                    statusElement.text("Revoked").removeClass().addClass("fw-semibold redText");
                    nameElement.text(approverName || 'N/A').removeClass().addClass("fw-semibold redText");
                    timeElement.text(approvalTime || 'N/A').removeClass().addClass("fw-semibold redText");
                } else {
                    statusElement.text("Done").removeClass().addClass("fw-semibold greenText");
                    nameElement.text(approverName || 'N/A').removeClass().addClass("fw-semibold greenText");
                    timeElement.text(approvalTime || 'N/A').removeClass().addClass("fw-semibold greenText");
                }
            }

            function populateLeaveSection(leaveDetails, offDays) {
                const storedLang = localStorage.getItem('language');
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

                    if (storedLang == "vi") {
                        switch (leaveTypeId) {
                            case 1:
                                leaveType = leave.type === 'full_day' ? 'Hàng năm Nghĩ phép (Full Day)' : 'Nửa ngày (Giờ làm việc) (Half Day)';
                                bgColorClass = 'bg-light-blue';
                                break;
                            case 2:
                                leaveType = 'Ngày sinh Nghĩ phép';
                                bgColorClass = 'bg-light-green';
                                break;
                            case 3:
                                leaveType = 'Kết hôn Nghĩ phép';
                                bgColorClass = 'bg-light-pink';
                                break;
                            case 4:
                                leaveType = 'Nghĩ phép không lương';
                                bgColorClass = 'bg-light-red';
                                break;
                            case 5:
                                leaveType = 'Hospitalisation Leave';
                                bgColorClass = 'bg-light-yellow';
                                break;
                            case 6:
                                leaveType = 'Compassionate Leave';
                                bgColorClass = 'bg-light-yellow';
                                break;
                            case 7:
                                leaveType = 'Maternity Leave';
                                bgColorClass = 'bg-light-yellow';
                                break;
                            case 8:
                                leaveType = 'Paternity Leave';
                                bgColorClass = 'bg-light-yellow';
                                break;
                            case 9:
                                leaveType = 'Medical Leave(Malaysian Special)';
                                bgColorClass = 'bg-info';
                                break;
                            default:
                                bgColorClass = 'bg-light-gray';
                                leaveType = 'Other Leave';
                        }
                    }
                    else {
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
                            case 5:
                                leaveType = 'Hospitalisation Leave';
                                bgColorClass = 'bg-light-yellow';
                                break;
                            case 6:
                                leaveType = 'Compassionate Leave';
                                bgColorClass = 'bg-light-yellow';
                                break;
                            case 7:
                                leaveType = 'Maternity Leave';
                                bgColorClass = 'bg-light-yellow';
                                break;
                            case 8:
                                leaveType = 'Paternity Leave';
                                bgColorClass = 'bg-light-yellow';
                                break;
                            case 9:
                                leaveType = 'Medical Leave(Malaysian Special)';
                                bgColorClass = 'bg-info';
                                break;
                            default:
                                bgColorClass = 'bg-light-gray';
                                leaveType = 'Other Leave';
                        }
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

                document.getElementById('total_leave_days').textContent = `${totalLeaveDays} days`;
                document.getElementById('total_al_days').textContent = `${totalAnnualLeaveDays + halfDayCounter} days`;
            }

            // Helper function to format date in a readable way
            function formatDate(date) {
                const options = { year: 'numeric', month: 'long', day: 'numeric' };
                return new Date(date).toLocaleDateString(undefined, options);
            }

            // Hide modal when close button is clicked
            $('.btn-close').on('click', function () {
                $('#leaveDetailsModal').modal('hide');
            });
        });
    </script>
@endsection