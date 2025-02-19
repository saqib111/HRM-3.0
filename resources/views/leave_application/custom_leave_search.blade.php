@extends('layout.mainlayout')
@section('css')
    <!-- Litepicker CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css" />


    <style>
        <style>body {
            font-family: "Arial", sans-serif;
            background-color: #eaeaea;
            padding: 20px;
        }

        .container {
            max-width: 600px;
            height: 100px;
            margin: auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            border-color: green;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
        }

        .selection {
            display: none;
        }

        .form-control:disabled,
        .form-control[readonly] {
            background-color: #FFFF;
            opacity: 1;
        }

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
    </style>

@endsection
@section('content')

    <div class="page-header">
        <div class="row align-items-center">
            <div class="col-md-4">
                <h3 class="page-title"><span data-translate="custom_search_leaves">Custom Search Leaves</span></h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="admin-dashboard.html">Dashboard</a></li>
                    <li class="breadcrumb-item active"><span data-translate="custom_search_leaves">Custom Search
                            Leaves</span></li>
                </ul>
            </div>
        </div>
    </div>

    <form id="custom_search_leave">
        <div class="col-auto my-3 d-flex justify-content-start">
            <div class="col-md-2 text-dark">
                <div class="input-block mb-3 text-dark">
                    <label for="datepicker"><span data-translate="date_range">Date Range:</span></label>
                    <input type="text" id="date-picker" name="start-date[]" class="form-control"
                        placeholder="Select Date Range" />
                </div>
            </div>
            <div class="col-md-2 text-dark ms-md-3">
                <div class="input-block mb-3 text-dark">
                    <label for="leave_status"><span data-translate="leave_status">Leave Status:</span></label>
                    <select class="form-select" aria-label="Default select example" id="leave_status" name="leave_status">
                        <option value="ALL">All Leaves</option>
                        <option value="pending">Pending Leaves</option>
                        <option value="approved">Approved Leaves</option>
                        <option value="rejected">Rejected Leaves</option>
                        <option value="revoked">Revoked Leaves</option>
                    </select>
                </div>
            </div>
            <div class="col-md-2 text-dark ms-md-3">
                <div class="input-block mb-3 text-dark">
                    <label for="leave_type"><span data-translate="leave_type">Leave Type:</span></label>
                    <select class="form-select" aria-label="Default select example" id="leave_type" name="leave_type">
                        <option value="0">All Leaves</option>
                        <option value="1">Annual Leaves</option>
                        <option value="2">Birthday Leaves</option>
                        <option value="3">Marriage Leaves</option>
                        <option value="4">Unpaid Leave</option>
                        <option value="5">Hospitalisation Leaves</option>
                        <option value="6">Compassionate Leaves</option>
                        <option value="7">Maternity Leaves</option>
                        <option value="8">Paternity Leaves</option>
                        <option value="9">Half Day Leaves</option>
                    </select>
                </div>
            </div>
            <div class="col-md-2 text-dark ms-md-3">
                <div class="input-block mb-3 text-dark">
                    <label for="username"><span data-translate="employee_name">Employee Name:</span></label>
                    <input type="text" class="form-control" id="username">
                </div>
            </div>
            <div class="col-md-2 text-dark ms-md-3">
                <div class="input-block mb-3 text-dark">
                    <label for="nationality"><span data-translate="nationality">Nationality</span></label>
                    <select class="form-select" aria-label="Default select example" id="nationality" name="leave_type">
                        <option value="all">All Nationalities</option>
                        <option value="Pakistan">Pakistani</option>
                        <option value="Malaysia">Malaysian</option>
                        <option value="Vietnam">Vietnamese</option>
                        <option value="India">Indian</option>
                        <option value="Philippines">Philippines</option>
                        <option value="Cambodia">Cambodian</option>
                        <option value="Thailand">Thailand</option>
                        <option value="Korea">Korea</option>
                        <option value="Indonesia">Indonesia</option>
                        <option value="Bangladesh">Bangladeshi</option>
                    </select>
                </div>
            </div>
            <div class="col-md-1 text-dark ms-md-3">
                <div class="input-block mb-3 text-dark d-flex flex-column">
                    <button class="btn btn-primary mt-4" id="filter_btn" style="padding: 8px;"><span
                            data-translate="filter">Filter</span></button>
                </div>
            </div>
        </div>
    </form>


    <!-- LEAVES TABLE -->
    <div id="notification" aria-live="polite" aria-atomic="true"></div>
    <div class="row">
        <div class="col-md-12">
            <div class="table-responsive">
                <table class="table table-striped custom-table" id="leavesTable">
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
                            <th><span data-translate="off_days">OFF Days</span></th>
                            <th><span data-translate="action">Action</span></th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
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

                                    <!-- Total Days Information -->
                                    <hr class="my-2">
                                    <div class="leave-sections">
                                        <div class="row">
                                            <div class="col-12">
                                                <div class="d-flex justify-content-between align-items-center py-2">
                                                    <span class="fw-semibold"><span data-translate="first_status">First
                                                            Status: </span><span id="first_status">Approved</span></span>
                                                    <span class="fw-semibold"><span data-translate="approval_name">Approval
                                                            Name: </span><span id="first_approval_name">Test</span></span>
                                                    <span class="fw-semibold"><span data-translate="date_and_time">Date &
                                                            Time: </span><span id="first_created_time">12/02/2024
                                                            08:00:00</span></span>
                                                </div>
                                                <div class="d-flex justify-content-between align-items-center py-2">
                                                    <span class="fw-semibold"><span data-translate="second_status">Second
                                                            Status: </span><span id="second_status">Approved</span></span>
                                                    <span class="fw-semibold"><span data-translate="approval_name">Approval
                                                            Name: </span><span id="second_approval_name">Test</span></span>
                                                    <span class="fw-semibold"><span data-translate="date_and_time">Date &
                                                            Time: </span><span id="second_created_time">12/02/2024
                                                            08:00:00</span></span>
                                                </div>
                                                <div class="d-flex justify-content-between align-items-center py-2">
                                                    <span class="fw-semibold"><span data-translate="hr_status">HR Status:
                                                        </span><span id="hr_status">Pending</span></span>
                                                    <span class="fw-semibold"><span data-translate="approval_name">Approval
                                                            Name: </span><span id="hr_approval_name">Test</span></span>
                                                    <span class="fw-semibold"><span data-translate="date_and_time">Date &
                                                            Time: </span><span id="hr_created_time">12/02/2024
                                                            08:00:00</span></span>
                                                </div>
                                                <div class="d-flex justify-content-between align-items-center py-2"
                                                    id="revoked_container">
                                                    <span class="fw-semibold">Status: <span
                                                            id="revoked_status">Revoked</span></span>
                                                    <span class="fw-semibold">Approval Name: <span
                                                            id="revoked_approval_name">Test</span></span>
                                                    <span class="fw-semibold">Date & Time: <span
                                                            id="revoked_created_time">12/02/2024
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
            </div>
        </div>
    </div>
    <!-- Modal Ends -->

    <!-- PreLoader -->
    <div id="loader" class="loader" style="display: none;">
        <div class="loader-animation"></div>
    </div>

@endsection

@section('script-z')
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <!-- LANGUAGE SCRIPT -->
    <script src="{{ asset('assets/js/switch.language.js') }}"></script>
    <script>
        var leavesTable = null;
        function populateTable() {
            if (leavesTable) {
                leavesTable.destroy();
            }

            leavesTable = $('#leavesTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{route('search.searchleaves')}}",
                    type: "POST",
                    data: function (d) {
                        d.leave_dates = $("#date-picker").val();
                        d.leave_status = $("#leave_status").val();
                        d.leave_type = $("#leave_type").val();
                        d.username = $("#username").val();
                        d.nationality = $("#nationality").val();
                        d._token = '{{ csrf_token() }}';
                    },
                    dataSrc: function (json) {
                        return json.data; // Return the fetched data to populate the table
                        leavesTable.clear();

                    }
                },
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false },
                    { data: 'employee_id', name: 'employee_id', orderable: false },
                    { data: 'username', name: 'username', orderable: false },
                    {
                        data: 'title',
                        render: function (data) {
                            return (data && data.length > 15) ? data.substring(0, 15) + '...' : data || ''; // Handle null
                        },
                        name: 'title',
                        orderable: false
                    },
                    {
                        data: 'description',
                        render: function (data) {
                            return (data && data.length > 15) ? data.substring(0, 15) + '...' : data || ''; // Handle null
                        },
                        name: 'description',
                        orderable: false
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
                    { data: 'day', name: 'day', orderable: false },
                    { data: 'from', name: 'from', orderable: false },
                    { data: 'to', name: 'to', orderable: false },
                    { data: 'off_days', name: 'off_days', orderable: false },
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
                order: [[0, 'desc']],
                pageLength: 10,
                lengthMenu: [10, 20, 25, 50, 100],
                deferLoading: 0
            });
        }

        populateTable();

        // SEND FORM TO GET DATA (FILTER)
        $("#filter_btn").click(function (e) {
            e.preventDefault();
            var leave_dates = $("#date-picker").val();
            var leave_status = $("#leave_status").val();
            var leave_type = $("#leave_type").val();
            var username = $("#username").val();
            var nationality = $("#nationality").val();

            leavesTable.ajax.reload(); // Reload DataTable with the current filters
        });

        // FILTER FUNCTION
        $(document).ready(function () {
            // Initially, disable the Filter button and hide the Export button
            $('#filter_btn').prop('disabled', true);
            $('#export-btn').hide();

            const datePicker = flatpickr("#date-picker", {
                mode: "range",
                dateFormat: "Y-m-d",
                onChange: function (selectedDates) {
                    if (selectedDates.length === 2) {
                        const startDate = flatpickr.formatDate(selectedDates[0], "Y-m-d");
                        const endDate = flatpickr.formatDate(selectedDates[1], "Y-m-d");
                        $('#date-picker').val(`${startDate},${endDate}`);
                    } else {
                        $('#date-picker').val('');
                    }
                    checkFilters();
                }
            });

            function checkFilters() {
                const startDate = $('#date-picker').val();

                if (startDate) {
                    $('#filter_btn').prop('disabled', false);
                } else {
                    $('#filter_btn').prop('disabled', true);
                }
            }

            checkFilters();  // Ensure filters are checked when page loads
        });

        // LEAVE MODAL 
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
                    // Update HR Step Information
                    updateApprovalStatus('#hr_status', '#hr_approval_name', '#hr_created_time', data.hr_approval_id, data.hr_approval_id, data.hr_approval_created_time);

                    updateApprovalStatus('#revoked_status', '#revoked_approval_name', '#revoked_created_time', data.revoked, data.revoked_by, data.revoked_created_time);


                    // Show or hide action buttons based on status
                    const actionButtons = $('#action_buttons');

                    // Check if 2nd step needs action
                    if (data.status_1 === "approved" && data.status_2 === "approved" && (data.hr_approval_id === "" || data.hr_approval_id === null || data.hr_approval_id === "Null")) {
                        $('#hr_task_done').prop('disabled', false); // Enable buttons
                        actionButtons.removeClass("hideBlock");
                        $('#hr_task_done').data('id', id);
                    }
                    // If both steps are completed, hide buttons
                    else {
                        $('#hr_task_done').prop('disabled', true); // Disable buttons
                        actionButtons.addClass("hideBlock");
                    }

                    const revokedContainer = $('#revoked_container');
                    if (data.revoked === "0") {
                        revokedContainer.remove(); // Completely remove the revoked container
                    } else if (data.revoked === "1") {
                        if (!revokedContainer.length) {
                            // If the revoked container is not already in the DOM, append it
                            $('#leaveDetailsModal .modal-body').append(revokedContainer);
                        }
                        revokedContainer.removeClass('hideBlock'); // Make sure it's visible
                        actionButtons.addClass("hideBlock"); // Hide action buttons
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
            } else if (status === '1') {
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

    </script>
@endsection