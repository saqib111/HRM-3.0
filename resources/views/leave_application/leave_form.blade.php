@extends('layout.mainlayout')
@section('content')

    @php
        // Decode the JSON string into a PHP array
        $decodedAllowedUL = json_decode($allowedUL, true);
    @endphp

    <div id="notification" aria-live="polite" aria-atomic="true"></div>

    <div class="page-header">
        <div class="row align-items-center">
            <div class="col-md-4">
                <h3 class="page-title"><span data-translate="leave_application">Leave Application</span></h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="admin-dashboard.html">Dashboard</a></li>
                    <li class="breadcrumb-item active"><span data-translate="leave_application">Leave Application</span>
                    </li>
                </ul>
            </div>
            <div class="col-md-8 float-end ms-auto">
                <div class="d-flex title-head">
                </div>
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0 text-center" style="color: #00c5fb;">
                        <i class="fas fa-plane-departure"></i>
                        <span data-translate="leave_application">Leave Application</span>
                    </h4>
                    <h5 class="text-success" style="position:absolute; top: 24px; right: 20px;"><span
                            data-translate="leave_balance">Leave Balance:</span>
                        {{$formattedLeaveBalance}}
                    </h5>
                    <hr />
                </div>
                @if(
                    auth()->user()->confirmation_status === "1" || in_array(4, $decodedAllowedUL) || in_array(5, $decodedAllowedUL)
                    || in_array(6, $decodedAllowedUL) || in_array(7, $decodedAllowedUL) || in_array(8, $decodedAllowedUL)
                )
                                <form id="leave_application_form" action="{{route('leave.form.store')}}" method="POST">
                                    @csrf
                                    <div class="card-body mx-5">
                                        <div class="input-block mb-3 row">
                                            <div class="col-md-12">
                                                <label class="col-form-label" for="leave_title"><span data-translate="leave_title">Leave
                                                        Title</span></label>
                                                <input type="text" name="leave_title" id="leave_title" class="form-control leave-title" />
                                                <div class="leave_error text-danger"></div>
                                            </div>
                                        </div>
                                        <div class="input-block mb-3 row">
                                            <div class="col-md-12">
                                                <label class="col-form-label" for="description"><span
                                                        data-translate="leave_description">Description (optional):</span></label>
                                                <textarea rows="5" cols="5" name="description" id="description"
                                                    class="form-control leave-description" placeholder="Enter text here"></textarea>
                                            </div>
                                        </div>
                                        <div class="input-block mb-3 row">
                                            <div class="col-md-12">
                                                <input type="hidden" value="{{$formattedLeaveBalance}}" name="annual_leave_balance"
                                                    id="annual_leave_balance">
                                            </div>
                                        </div>
                                        <div class="input-block mb-3 row">
                                            <div class="col-md-4" style="position: relative;">
                                                <i class="fa-solid fa-circle-plus add-fields"
                                                    style="position:absolute; top: 0px; left: -25px; font-size: 22px; color:#00c5fb; cursor: pointer;"></i>
                                                <label class="col-form-label" for="full_day_leave"><span
                                                        data-translate="full_day_leave">Full-Day Leave</span></label>
                                                <div class="text-danger" id="at_least_one"></div>
                                            </div>
                                        </div>
                                        <div id="dynamic-fields-container"></div>
                                        <hr>
                                        <div class="input-block mb-3 row">
                                            <div class="col-md-4" style="position: relative;">
                                                <i class="fa-solid fa-circle-plus add-half-fields"
                                                    style="position:absolute; top: 0px; left: -25px; font-size: 22px; color:#00c5fb; cursor: pointer;"></i>
                                                <label class="col-form-label" for="half_day_leave"><span
                                                        data-translate="half_day_leave">Half-Day Leave (Select Duty Hours)</span></label>
                                            </div>
                                        </div>
                                        <div id="dynamic-half-days-container"></div>
                                        <hr>
                                        <div class="input-block mb-3 row">
                                            <div class="col-md-4" style="position: relative;">
                                                <i class="fa-solid fa-circle-plus add-off-fields"
                                                    style="position:absolute; top: 0px; left: -25px; font-size: 22px; color:#00c5fb; cursor: pointer;"></i>
                                                <label class="col-form-label" for="off_day"><span data-translate="off_day">OFF-Day
                                                        (Optional)</span></label>
                                            </div>
                                        </div>
                                        <div id="dynamic-off-days-container"></div>
                                        <div class="input-block mb-3 row text-center">
                                            <div class="col-md-12">
                                                <button class="btn btn-primary btn-submit" type="button"><span
                                                        data-translate="submit_leave">Submit</span></button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                @else
                    <div class="card-body px-5">
                        <div class="notice-box">
                            <h4 class="notice-heading"><span data-translate="notice_heading">Important Notice</span></h4>
                            <p><span data-translate="notice_text">Your leave application is currently restricted as you are
                                    still under probation. Please refer to
                                    your probation terms for further details.</span></p>
                            <p class="text-center mt-3 text-danger"><strong><span data-translate="notice_note">Leave
                                        applications can only be submitted once your employment status is
                                        confirmed.</span></strong></p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
    </div>

    <!-- PreLoader -->
    <div id="loader" class="loader" style="display: none;">
        <div class="loader-animation"></div>
    </div>
    <!-- PreLoader Ends -->
    <!-- Unpaid Leave Confirmation Modal -->
    <div class="modal fade" id="unpaidLeaveModal" tabindex="-1" aria-labelledby="unpaidLeaveModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title mx-auto" id="unpaidLeaveModalLabel"></h4>
                    <!-- <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button> -->
                </div>
                <div class="modal-body">
                    <p id="unpaidLeaveMessage"></p>
                    <p><strong>Note:</strong> The remaining days will be applied as unpaid leave.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmUnpaidLeave">Apply with Unpaid Leave</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Unpaid Leave Confirmation Modal Ends -->
    <input type="hidden" id="allowed_ul" value="{{ $allowedUL }}">
@endsection

@section('script-z')
    <script>
        $(document).ready(function () {
            const storedLang = localStorage.getItem('language');
            let offDayCount = 0;
            let birthdayLeaveCount = 0;
            let marriageLeaveCount = 0;

            // Array of user IDs eligible for "Medical Leave (Malaysian Special)"
            let malayisanSpecial = [1, 3, 4, 22, 26, 48, 50, 56, 58, 60, 73, 80, 88, 144, 163, 225, 277, 315, 351, 381, 672, 1011, 1031, 1080, 1081, 1082, 1104, 1105, 1135, 1136, 1303];
            // Current logged-in user ID
            let userId = {{ auth()->user()->id }}; // Get the authenticated user's ID from the backend
            // Define the leave categories HTML globally
            let confirmation_status_value = {{$confirmation_status}};

            let leaveCategoryHtml = '';

            let allowedUL = {!! $allowedUL !!}; // Convert JSON string into a JavaScript array
            allowedUL = Array.isArray(allowedUL) ? allowedUL.map(Number) : []; // Ensure it's an array of numbers

            // Function to update the leave categories
            function updateLeaveCategories(allowedUL) {
                // Initialize leaveCategoryHtml variable
                leaveCategoryHtml = '';
                if (storedLang == "vi") {
                    if (confirmation_status_value) {
                        leaveCategoryHtml += `<option value="1">Hàng năm Nghĩ phép</option>`;
                        leaveCategoryHtml += `<option value="2">Ngày sinh Nghĩ phép</option>`;
                        leaveCategoryHtml += `<option value="3">Kết hôn Nghĩ phép</option>`;
                    }
                    if (allowedUL.includes(4)) {
                        leaveCategoryHtml += `<option value="4">Nghĩ phép không lương</option>`;
                    }
                } else {
                    if (confirmation_status_value) {
                        leaveCategoryHtml += `<option value="1">Annual Leave</option>`;
                        leaveCategoryHtml += `<option value="2">Birthday Leave</option>`;
                        leaveCategoryHtml += `<option value="3">Marriage Leave</option>`;
                    }
                    if (allowedUL.includes(4)) {
                        leaveCategoryHtml += `<option value="4">Unpaid Leave</option>`;
                    }
                }
                // Add additional leaves based on allowedUL values (e.g., 4, 5, 6, 7, etc.)
                if (allowedUL.includes(5)) {
                    leaveCategoryHtml += `<option value="5">Hospitalisation Leave</option>`;
                }
                if (allowedUL.includes(6)) {
                    leaveCategoryHtml += `<option value="6">Compassionate Leave</option>`;
                }
                if (allowedUL.includes(7)) {
                    leaveCategoryHtml += `<option value="7">Maternity Leave</option>`;
                }
                if (allowedUL.includes(8)) {
                    leaveCategoryHtml += `<option value="8">Paternity Leave</option>`;
                }
                if (malayisanSpecial.includes(userId)) {
                    leaveCategoryHtml += `<option value="9">Medical Leave (Malaysian Special)</option>`;
                }

                // Update the dropdown with the new options
                $('#leaveCategorySelect').html(leaveCategoryHtml);
            }

            // Call the function to set the initial state based on allowedUL value
            updateLeaveCategories(allowedUL);

            // Add dynamic leave category fields when plus icon is clicked
            $('.add-fields').click(function () {
                if (storedLang == "vi") {
                    let dynamicFields = `
                    <div class="input-block mb-3 row dynamic-field">
                        <div class="col-md-4">
                            <label class="col-form-label" for="full_day_leave"><span data-translate="leave_category">Nghĩ phép Kiểu:</span></label>
                            <select name="full_day_leave[]" id="full_day_leave" class="form-control form-select leave-category">
                                <option disabled selected>Select Category</option>
                                    ${leaveCategoryHtml} <!-- This is where the dynamically generated options go -->
                            </select>
                            <div class="leave_error text-danger"></div>
                        </div>
                        <div class="col-md-4">
                            <label class="col-form-label" for="full_leave_from"><span data-translate="from">Từ:</span></label>
                            <input type="date" class="form-control leave-date" name="full_leave_from[]" id="full_leave_from" />
                            <div class="leave_error text-danger"></div>
                        </div>
                        <div class="col-md-4" style="position: relative;">
                            <label class="col-form-label" for="full_leave_to">Đến:</label>
                            <input type="date" class="form-control leave-date" name="full_leave_to[]" id="full_leave_to" />
                            <div class="leave_error text-danger"></div>
                            <i class="fa-solid fa-circle-xmark remove-field"
                                style="position: absolute; top: 36px; right: -25px; font-size: 22px; color: red; cursor: pointer;"></i>
                        </div>
                    </div>`;
                    $('#dynamic-fields-container').append(dynamicFields);
                    $("#at_least_one").text('');
                } else {
                    let dynamicFields = `
                    <div class="input-block mb-3 row dynamic-field">
                        <div class="col-md-4">
                            <label class="col-form-label" for="full_day_leave"><span data-translate="leave_category">Leave Category:</span></label>
                            <select name="full_day_leave[]" id="full_day_leave" class="form-control form-select leave-category">
                                <option disabled selected>Select Category</option>
                                    ${leaveCategoryHtml} <!-- This is where the dynamically generated options go -->
                            </select>
                            <div class="leave_error text-danger"></div>
                        </div>
                        <div class="col-md-4">
                            <label class="col-form-label" for="full_leave_from"><span data-translate="from">From:</span></label>
                            <input type="date" class="form-control leave-date" name="full_leave_from[]" id="full_leave_from" />
                            <div class="leave_error text-danger"></div>
                        </div>
                        <div class="col-md-4" style="position: relative;">
                            <label class="col-form-label" for="full_leave_to">To:</label>
                            <input type="date" class="form-control leave-date" name="full_leave_to[]" id="full_leave_to" />
                            <div class="leave_error text-danger"></div>
                            <i class="fa-solid fa-circle-xmark remove-field"
                                style="position: absolute; top: 36px; right: -25px; font-size: 22px; color: red; cursor: pointer;"></i>
                        </div>
                    </div>`;
                    $('#dynamic-fields-container').append(dynamicFields);
                    $("#at_least_one").text('');
                }
            });

            // Remove dynamic fields when cross icon is clicked
            $(document).on('click', '.remove-field', function () {
                $(this).closest('.dynamic-field').remove();
            });

            // Add dynamic off-days fields when plus icon is clicked
            $('.add-off-fields').click(function () {
                let offDayField = `
                <div class="col-md-4 mb-3" style="position: relative;">
                    <input type="date" class="form-control off-day-date" name="off_days[]" />
                    <div class="leave_error text-danger"></div>
                    <i class="fa-solid fa-circle-xmark remove-off-day"
                        style="position: absolute; top: 10px; right: -11px; font-size: 22px; color: red; cursor: pointer;"></i>
                </div>`;
                if (offDayCount % 3 === 0) {
                    $('#dynamic-off-days-container').append('<div class="row off-day-row"></div>');
                }
                $('#dynamic-off-days-container .off-day-row:last-child').append(offDayField);
                offDayCount++;
            });

            // Remove dynamic off-days fields when cross icon is clicked
            $(document).on('click', '.remove-off-day', function () {
                $(this).closest('.col-md-4').remove();
                offDayCount--;
            });

            // Add dynamic half-day fields when plus icon is clicked
            $('.add-half-fields').click(function () {
                if (storedLang == "vi") {
                    let halfDayField = `
                    <div class="input-block mb-3 row dynamic-half-day">
                        <div class="col-md-4">
                            <label class="col-form-label">Ngày:</label>
                            <input type="date" class="form-control half-day-date" name="half_day_date[]" id="half_day_date" />
                            <div class="leave_error text-danger"></div>
                        </div>
                        <div class="col-md-4">
                            <label class="col-form-label" for="half_day_start_time">Thời gian bắt đầu:</label>
                            <input type="time" class="form-control half-day-start-time" name="half_day_start_time[]" id="half_day_start_time" />
                            <div class="leave_error text-danger"></div>
                        </div>
                        <div class="col-md-4" style="position: relative;">
                            <label class="col-form-label" for="half_day_end_time">Thời gian kết thúc:</label>
                            <input type="time" class="form-control half-day-end-time" name="half_day_end_time[]" id="half_day_end_time" />
                            <div class="leave_error text-danger"></div>
                            <i class="fa-solid fa-circle-xmark remove-half-day"
                                style="position: absolute; top: 36px; right: -25px; font-size: 22px; color: red; cursor: pointer;"></i>
                        </div>
                    </div>`;
                    $('#dynamic-half-days-container').append(halfDayField);
                    $("#at_least_one").text('');
                } else {
                    let halfDayField = `
                    <div class="input-block mb-3 row dynamic-half-day">
                        <div class="col-md-4">
                            <label class="col-form-label">Date:</label>
                            <input type="date" class="form-control half-day-date" name="half_day_date[]" id="half_day_date" />
                            <div class="leave_error text-danger"></div>
                        </div>
                        <div class="col-md-4">
                            <label class="col-form-label" for="half_day_start_time">Start Time:</label>
                            <input type="time" class="form-control half-day-start-time" name="half_day_start_time[]" id="half_day_start_time" />
                            <div class="leave_error text-danger"></div>
                        </div>
                        <div class="col-md-4" style="position: relative;">
                            <label class="col-form-label" for="half_day_end_time">End Time:</label>
                            <input type="time" class="form-control half-day-end-time" name="half_day_end_time[]" id="half_day_end_time" />
                            <div class="leave_error text-danger"></div>
                            <i class="fa-solid fa-circle-xmark remove-half-day"
                                style="position: absolute; top: 36px; right: -25px; font-size: 22px; color: red; cursor: pointer;"></i>
                        </div>
                    </div>`;
                    $('#dynamic-half-days-container').append(halfDayField);
                    $("#at_least_one").text('');
                }
            });

            // Remove dynamic half-day fields when cross icon is clicked
            $(document).on('click', '.remove-half-day', function () {
                $(this).closest('.dynamic-half-day').remove();
            });

            // Submit button click event
            $('.btn-submit').click(function (event) {
                event.preventDefault();
                let isValid = true;
                birthdayLeaveCount = 0; // Reset the count on each submission
                marriageLeaveCount = 0; // Reset the count on each submission

                // Calculate Annual Leave days and validate against balance
                let annualLeaveDaysRequested = 0;
                let annualLeaveBalance = parseFloat($('#annual_leave_balance').val());
                let unpaidLeaveDays = 0;

                // Clear previous error messages
                $('.leave_error').text('');

                // Validate Leave Title
                let leaveTitle = $('.leave-title').val().trim();
                if (leaveTitle === '') {
                    $('.leave-title').next('.leave_error').text('Please enter a leave title.');
                    isValid = false;
                }

                // Check if at least one full-day or half-day leave is selected
                let hasFullDayLeave = $('.dynamic-field').length > 0;
                let hasHalfDayLeave = $('.dynamic-half-day').length > 0;

                if (!hasFullDayLeave && !hasHalfDayLeave) {
                    $("#at_least_one").text('You must select at least one full-day or half-day leave.');
                    isValid = false;
                }

                // Validate initial and dynamic Leave Category sets
                $('.dynamic-field').each(function () {
                    let category = $(this).find('.leave-category').val();
                    let fromDate = $(this).find('.leave-date').eq(0).val();
                    let toDate = $(this).find('.leave-date').eq(1).val();

                    if (!category) {
                        $(this).find('.leave-category').next('.leave_error').text('Please select a leave category.');
                        isValid = false;
                    }
                    if (!fromDate) {
                        $(this).find('.leave-date').eq(0).next('.leave_error').text('Please select a start date.');
                        isValid = false;
                    }
                    if (!toDate) {
                        $(this).find('.leave-date').eq(1).next('.leave_error').text('Please select an end date.');
                        isValid = false;
                    } else if (toDate < fromDate) {
                        $(this).find('.leave-date').eq(1).next('.leave_error').text('"To date" cannot be before "From date".');
                        isValid = false;
                    }

                    // Validate "Birthday Leave" can only be selected once and for a single day
                    if (category === 'Birthday Leave' || category === '2') {
                        birthdayLeaveCount++;

                        if (birthdayLeaveCount > 1) {
                            $(this).find('.leave-category').next('.leave_error').text('Birthday Leave can only be selected once.');
                            isValid = false;
                        }

                        if (fromDate !== toDate) {
                            $(this).find('.leave-date').eq(1).next('.leave_error').text('Birthday Leave must be for one day only.');
                            isValid = false;
                        }
                    }

                    // Validation "Marriage Leave" can only be selected once and for maximum of 3 days 
                    if (category === 'Marriage Leave' || category === '3') {
                        marriageLeaveCount++;

                        if (marriageLeaveCount > 1) {
                            $(this).find('.leave-category').next('.leave_error').text('Marriage Leave can only be selected once.');
                            isValid = false;
                        }

                        if (fromDate && toDate) {
                            let startDate = new Date(fromDate);
                            let endDate = new Date(toDate);
                            let dayDiff = (endDate - startDate) / (1000 * 3600 * 24) + 1;

                            if (dayDiff > 3) {
                                $(this).find('.leave-date').eq(1).next('.leave_error').text('Marriage Leave cannot exceed 3 days.');
                                isValid = false;
                            }
                        }
                    }

                    if (category === "1") {  // 1 stands for Annual Leave
                        let startDate = new Date(fromDate);
                        let endDate = new Date(toDate);
                        let dayDiff = (endDate - startDate) / (1000 * 3600 * 24) + 1;

                        for (let i = 0; i < dayDiff; i++) {
                            let currentDate = new Date(startDate);
                            currentDate.setDate(startDate.getDate() + i);
                            let currentDateString = currentDate.toISOString().split('T')[0];

                            let isOffDay = false;
                            $('.off-day-date').each(function () {
                                if ($(this).val() === currentDateString) {
                                    isOffDay = true;
                                    return false;
                                }
                            });

                            if (!isOffDay) {
                                annualLeaveDaysRequested++;
                            }
                        }
                    }
                });

                $('.off-day-date').each(function () {
                    if ($(this).val() === '') {
                        $(this).next('.leave_error').text('Please select an off-day date.');
                        isValid = false;
                    }
                });

                $('.dynamic-half-day').each(function () {
                    let halfDayDate = $(this).find('.half-day-date').val();
                    let startTime = $(this).find('.half-day-start-time').val();
                    let endTime = $(this).find('.half-day-end-time').val();

                    if (!halfDayDate) {
                        $(this).find('.half-day-date').next('.leave_error').text('Please select a date.');
                        isValid = false;
                    }
                    if (!startTime) {
                        $(this).find('.half-day-start-time').next('.leave_error').text('Please select a start time.');
                        isValid = false;
                    }
                    if (!endTime) {
                        $(this).find('.half-day-end-time').next('.leave_error').text('Please select an end time.');
                        isValid = false;
                    }

                    if (startTime && endTime) {
                        if (startTime >= endTime) {
                            $(this).find('.half-day-end-time').next('.leave_error').text('End time must be after start time.');
                            isValid = false;
                        } else {
                            let start = new Date('1970-01-01T' + startTime + 'Z');
                            let end = new Date('1970-01-01T' + endTime + 'Z');
                            let diffInHours = (end - start) / (1000 * 60 * 60);

                            if (diffInHours < 4) {
                                $(this).find('.half-day-end-time').next('.leave_error').text('The duration between start and end time must be at least 4 hours.');
                                isValid = false;
                            }
                        }
                    }

                    if (halfDayDate) {
                        annualLeaveDaysRequested += 0.5;
                    }
                });

                if (isValid) {
                    if (annualLeaveDaysRequested > annualLeaveBalance) {
                        // unpaidLeaveDays = annualLeaveDaysRequested - annualLeaveBalance;
                        // $('#unpaidLeaveMessage').html(`You are applying for <strong>${annualLeaveDaysRequested}</strong> Annual Leave days, but your balance is only <strong>${annualLeaveBalance}</strong>. The remaining <strong>${unpaidLeaveDays}</strong> days will be unpaid.`);
                        // $('#unpaidLeaveModal').modal('show');

                        unpaidLeaveDays = annualLeaveDaysRequested - Math.floor(annualLeaveBalance);
                        let remainingALBalance = annualLeaveBalance - Math.floor(annualLeaveBalance);

                        if (remainingALBalance > 0) {
                            $('#unpaidLeaveMessage').html(
                                `You are applying for <strong>${annualLeaveDaysRequested}</strong> Annual Leave days, but your balance is only <strong class="text-success">${annualLeaveBalance} Days</strong>. To fully utilize your balance, you need to define a "<strong class="text-warning">half-day</strong>" leave. Otherwise, only <strong class="text-success">${Math.floor(annualLeaveBalance)} Days</strong> will be counted, and the remaining <strong class="text-danger">${unpaidLeaveDays} Days</strong> will be <strong class="text-danger">unpaid</strong>. Your remaining balance will be <strong class="text-success">0.5</strong> Annual Leave.`
                            );
                            $("#unpaidLeaveModalLabel").html(`
                        Confirmation For <strong class="badge text-bg-danger text-wrap fs-5">${unpaidLeaveDays} Days</strong> of Unpaid Leaves
                        `)
                        } else {
                            $('#unpaidLeaveMessage').html(
                                `You are applying for <strong>${annualLeaveDaysRequested}</strong> Annual Leave days, but your balance is only <strong class="text-success">${annualLeaveBalance} Days</strong>.<br class="mt-2">The remaining <strong class="text-danger">${unpaidLeaveDays} Days</strong> will be <strong class="text-danger">unpaid</strong>.`
                            );
                            $("#unpaidLeaveModalLabel").html(`
                        Confirmation For <strong class="badge text-bg-danger text-wrap fs-5">${unpaidLeaveDays} Days</strong> of <strong class="text-danger">Unpaid</strong> Leaves
                        `)
                        }

                        // Show the modal
                        $('#unpaidLeaveModal').modal('show');
                    } else {
                        sendAjaxRequest();
                    }
                }
            });

            $('#confirmUnpaidLeave').click(function () {
                $('#unpaidLeaveModal').modal('hide');
                sendAjaxRequest();
            });

            function sendAjaxRequest() {
                let formData = $('#leave_application_form').serialize();
                showLoader();
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                $.ajax({
                    url: $('#leave_application_form').attr('action'),
                    type: 'POST',
                    data: formData,
                    success: function (response) {
                        console.log(response);
                        hideLoader();
                        $('#notification').html('<div class="alert alert-success">' + response.message + '</div>');
                        $('#leave_application_form')[0].reset();
                        $('#dynamic-fields-container').empty();
                        $('#dynamic-half-days-container').empty();
                        $('#dynamic-off-days-container').empty();
                    },
                    error: function (xhr) {
                        hideLoader();
                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;
                            $('.leave_error').text(''); // Clear previous errors

                            // Display specific errors
                            if (errors.already_applied_leave) {
                                $('#notification').html(`<div class="alert alert-danger">${errors.already_applied_leave}</div>`);
                            }

                            if (errors.birthday_leave) {
                                $('#notification').html(`<div class="alert alert-danger">${errors.birthday_leave}</div>`);
                            }

                            if (errors.marriage_leave) {
                                $('#notification').html(`<div class="alert alert-danger">${errors.marriage_leave}</div>`);
                            }

                            // Display other validation errors if any
                            $.each(errors, function (key, value) {
                                let field = $('[name="' + key + '"]');
                                field.next('.leave_error').text(value[0]);
                            });
                        } else {
                            $('#notification').html('<div class="alert alert-danger">An error occurred. Please try again later.</div>');
                        }
                    }
                });
            }
        });
    </script>
@endsection