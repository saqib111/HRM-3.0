@extends('layout.mainlayout')
@section('content')
<div id="notification" aria-live="polite" aria-atomic="true"></div>

<div class="page-header">
    <div class="row align-items-center">
        <div class="col-md-4">
            <h3 class="page-title">Leave Application</h3>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="admin-dashboard.html">Dashboard</a></li>
                <li class="breadcrumb-item active">Leave Apply</li>
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
                    Leave Application
                </h4>
                <h5 class="text-success" style="position:absolute; top: 24px; right: 20px;">Leave Balance: 28</h5>
                <hr />
            </div>
            <form action="#">
                <div class="card-body mx-5">
                    <div class="input-block mb-3 row">
                        <div class="col-md-12">
                            <label class="col-form-label">Leave Title</label>
                            <input type="text" class="form-control leave-title" />
                            <div class="leave_error text-danger"></div>
                        </div>
                    </div>
                    <div class="input-block mb-3 row">
                        <div class="col-md-12">
                            <label class="col-form-label">Description (optional):</label>
                            <textarea rows="5" cols="5" class="form-control leave-description"
                                placeholder="Enter text here"></textarea>
                        </div>
                    </div>
                    <div class="input-block mb-3 row">
                        <div class="col-md-4" style="position: relative;">
                            <i class="fa-solid fa-circle-plus add-fields"
                                style="position:absolute; top: 0px; left: -25px; font-size: 22px; color:#00c5fb; cursor: pointer;"></i>
                            <label class="col-form-label">Full-Day Leave</label>
                            <div class="text-danger" id="at_least_one"></div>
                        </div>
                    </div>
                    <div id="dynamic-fields-container"></div>
                    <hr>
                    <div class="input-block mb-3 row">
                        <div class="col-md-4" style="position: relative;">
                            <i class="fa-solid fa-circle-plus add-half-fields"
                                style="position:absolute; top: 0px; left: -25px; font-size: 22px; color:#00c5fb; cursor: pointer;"></i>
                            <label class="col-form-label">Half-Day Leave</label>
                        </div>
                    </div>
                    <div id="dynamic-half-days-container"></div>
                    <hr>
                    <div class="input-block mb-3 row">
                        <div class="col-md-4" style="position: relative;">
                            <i class="fa-solid fa-circle-plus add-off-fields"
                                style="position:absolute; top: 0px; left: -25px; font-size: 22px; color:#00c5fb; cursor: pointer;"></i>
                            <label class="col-form-label">OFF-Day (Optional)</label>
                        </div>
                    </div>
                    <div id="dynamic-off-days-container"></div>
                    <div class="input-block mb-3 mb-0 row text-center">
                        <div class="col-md-12">
                            <button class="btn btn-primary btn-submit" type="button">Submit</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
</div>

<!-- PreLoader -->
<div id="loader" class="loader" style="display: none;">
    <div class="loader-animation"></div>
</div>

@endsection

@section('script-z')
<script>
    $(document).ready(function () {
        let offDayCount = 0;
        let birthdayLeaveCount = 0;
        let marriageLeaveCount = 0;

        // Add dynamic leave category fields when plus icon is clicked
        $('.add-fields').click(function () {
            let dynamicFields = `
            <div class="input-block mb-3 row dynamic-field">
                <div class="col-md-4">
                    <label class="col-form-label">Leave Category:</label>
                    <select class="form-control form-select leave-category">
                        <option disabled selected>Select Category</option>
                        <option>Annual Leave</option>
                        <option>Birthday Leave</option>
                        <option>Marriage Leave</option>
                        <option>Unpaid Leave</option>
                    </select>
                    <div class="leave_error text-danger"></div>
                </div>
                <div class="col-md-4">
                    <label class="col-form-label">From</label>
                    <input type="date" class="form-control leave-date" />
                    <div class="leave_error text-danger"></div>
                </div>
                <div class="col-md-4" style="position: relative;">
                    <label class="col-form-label">To</label>
                    <input type="date" class="form-control leave-date" />
                    <div class="leave_error text-danger"></div>
                    <i class="fa-solid fa-circle-xmark remove-field"
                        style="position: absolute; top: 36px; right: -25px; font-size: 22px; color: red; cursor: pointer;"></i>
                </div>
            </div>`;
            $('#dynamic-fields-container').append(dynamicFields);
            $("#at_least_one").text('');
        });

        // Remove dynamic fields when cross icon is clicked
        $(document).on('click', '.remove-field', function () {
            $(this).closest('.dynamic-field').remove();
        });

        // Add dynamic off-days fields when plus icon is clicked
        $('.add-off-fields').click(function () {
            let offDayField = `
            <div class="col-md-4 mb-3" style="position: relative;">
                <input type="date" class="form-control off-day-date" />
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
            let halfDayField = `
            <div class="input-block mb-3 row dynamic-half-day">
                <div class="col-md-4">
                    <label class="col-form-label">Date</label>
                    <input type="date" class="form-control half-day-date" />
                    <div class="leave_error text-danger"></div>
                </div>
                <div class="col-md-4">
                    <label class="col-form-label">Start Time</label>
                    <input type="time" class="form-control half-day-start-time" />
                    <div class="leave_error text-danger"></div>
                </div>
                <div class="col-md-4" style="position: relative;">
                    <label class="col-form-label">End Time</label>
                    <input type="time" class="form-control half-day-end-time" />
                    <div class="leave_error text-danger"></div>
                    <i class="fa-solid fa-circle-xmark remove-half-day"
                        style="position: absolute; top: 36px; right: -25px; font-size: 22px; color: red; cursor: pointer;"></i>
                </div>
            </div>`;
            $('#dynamic-half-days-container').append(halfDayField);
            $("#at_least_one").text('');
        });

        // Remove dynamic half-day fields when cross icon is clicked
        $(document).on('click', '.remove-half-day', function () {
            $(this).closest('.dynamic-half-day').remove();
        });

        // Submit button click event
        $('.btn-submit').click(function () {
            let isValid = true;
            birthdayLeaveCount = 0; // Reset the count on each submission
            marriageLeaveCount = 0; // Reset the count on each submission

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
                if (category === 'Birthday Leave') {
                    birthdayLeaveCount++;

                    // Check if it's already been selected more than once
                    if (birthdayLeaveCount > 1) {
                        $(this).find('.leave-category').next('.leave_error').text('Birthday Leave can only be selected once.');
                        isValid = false;
                    }

                    // Check if the "From" and "To" dates are the same (i.e., one day only)
                    if (fromDate !== toDate) {
                        $(this).find('.leave-date').eq(1).next('.leave_error').text('Birthday Leave must be for one day only.');
                        isValid = false;
                    }
                }

                // Validation "Marriage Leave" can only be selected once and for maximum of 3 days 
                if (category === 'Marriage Leave') {
                    marriageLeaveCount++;

                    // Check if it's already been selected more than once
                    if (marriageLeaveCount > 1) {
                        $(this).find('.leave-category').next('.leave_error').text('Marriage Leave can only be selected once.');
                        isValid = false;
                    }

                    // Check if the duration does not exceed 3 days
                    if (fromDate && toDate) {
                        let startDate = new Date(fromDate);
                        let endDate = new Date(toDate);
                        let timeDiff = endDate - startDate;
                        let dayDiff = timeDiff / (1000 * 3600 * 24) + 1; // Calculate number of days (inclusive)

                        if (dayDiff > 3) {
                            $(this).find('.leave-date').eq(1).next('.leave_error').text('Marriage Leave cannot exceed 3 days.');
                            isValid = false;
                        }
                    }
                }

            });

            // Validate Off Days
            $('.off-day-date').each(function () {
                if ($(this).val() === '') {
                    $(this).next('.leave_error').text('Please select an off-day date.');
                    isValid = false;
                }
            });

            // Validate Half-Day Fields
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
                // if (startTime && endTime && startTime >= endTime) {
                //     $(this).find('.half-day-end-time').next('.leave_error').text('End time must be after start time.');
                //     isValid = false;
                // }
                if (startTime && endTime) {
                    // Check if end time is after start time
                    if (startTime >= endTime) {
                        $(this).find('.half-day-end-time').next('.leave_error').text('End time must be after start time.');
                        isValid = false;
                    } else {
                        // Calculate the time difference in hours
                        let start = new Date('1970-01-01T' + startTime + 'Z');
                        let end = new Date('1970-01-01T' + endTime + 'Z');
                        let diffInHours = (end - start) / (1000 * 60 * 60);

                        // Check if the duration is at least 4 hours
                        if (diffInHours < 4) {
                            $(this).find('.half-day-end-time').next('.leave_error').text('The duration between start and end time must be at least 4 hours.');
                            isValid = false;
                        }
                    }
                }
            });

            // If the form is valid, proceed; otherwise, show errors
            if (isValid) {
                alert('Form is valid. You can now submit it.');
                // Submit form logic here (e.g., AJAX or standard form submission)
            } else {
                console.log('Please correct the errors in the form.');
            }
        });
    });

</script>
@endsection