@extends('layout.mainlayout')
@section('css')
<!-- Litepicker CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css" />
<style>
    .select2-search__field {
        display: none;
    }

    .select2-selection__choice__remove {
        border: none;
        background-color: #E4E4E4;
    }

    .select2-selection__choice {
        margin-top: 0;
    }

    .select2-selection--multiple.is-invalid {
        border-color: red !important;
    }

    body {
        background-color: #eaeaea;
        padding: 20px;
    }

    .container {
        max-width: 600px;
        margin: auto;
        background: white;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
    }

    .result {
        margin-top: 20px;
        font-size: 1.1em;
        color: #333;
    }
</style>
@endsection
@section('content')


<div class="col-auto ms-auto mb-3">
    <ul class="split-head">
        <li>
            <a href="#" class="btn add-btn text-white" data-bs-toggle="modal" data-bs-target="#schedule">
                <i class="fa fa-plus"></i> Add Schedule</a>
        </li>
    </ul>
</div>

<div id="notification" aria-live="polite" aria-atomic="true"></div>
<div class="row">
    <div class="col-md-12">
        <div class="table-responsive">
            <table class="table table-striped custom-table" id="scheduletable">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Shift Name</th>
                        <th>Start Date</th>
                        <th>Start Time</th>
                        <th>End Date</th>
                        <th>End Time</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="schedule-list">
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal Start -->
<div class="modal fade" id="deleteSchedule" tabindex="-1" aria-labelledby="exampleModalScrollable2" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="modalTitle">Delete Confirmation</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this Schedule? This action cannot be undone.</p>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDelete">Delete</button>
            </div>
        </div>
    </div>
</div>
<!-- Delete Confirmation Modal End -->


<div id="schedule" class="modal custom-modal fade" role="dialog">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Schedule</h5>
                <button type="button" class="closed_btn" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="schedule" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="row">



                        <div class="col-sm-12 ">
                            <div class="input-block mb-3">
                                <label class="col-form-label" for="shiftname">Shift Name <span
                                        class="text-danger">*</span></label>
                                <input class="form-control shiftname" type="text" name="shift_name" id="shiftname">


                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="input-block mb-3">
                                <label for="datepicker">Select Shift Start Dates:</label>
                                <input type="text" id="date-picker" name="start-date[]" class="form-control" />
                                <input type="hidden" name="startdate[]" id="startdate">

                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="input-block mb-3">
                                <label for="datepicker">Select Shift Start Time:</label>
                                <input type="time" id="time-input" name="start_time" class="form-control" />

                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="input-block mb-3">
                                <label for="datepicker2">Select Shift End Dates:</label>
                                <input type="text" id="date-picker2" name="end-date[]" class="form-control" />
                                <input type="hidden" name="enddate[]" id="enddate">
                                <div id="shift_date_error" style="color: red;"></div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="input-block mb-3">
                                <label for="datepicker2">Select Shift End Time:</label>
                                <input type="time" id="end-time" name="end_time" class="form-control" />
                                <div id="show_error_msg" style="color: red;"></div>
                            </div>
                        </div>


                    </div>

                    <div class="submit-section">
                        <button class="btn btn-primary" id="">Submit</button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>
<!-- Edit Modal -->



<!-- PreLoader -->
<div id="loader" class="loader" style="display: none;">
    <div class="loader-animation"></div>
</div>
@endsection

@section('script-z')  
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
    let table = $('#scheduletable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('schedule.data') }}",
            type: 'GET'
        },
        columns: [{
            data: 'DT_RowIndex',
            name: 'DT_RowIndex',
            orderable: false,
            searchable: false
        },
        {
            data: 'name',
            name: 'name',
            orderable: false,
        },
        {
            data: 'start_to',
            name: 'start_to',
            orderable: false,
        },
        {
            data: 'start_time',
            name: 'start_time',
            orderable: false,
        },
        {
            data: 'end_end',
            name: 'end_end',
            orderable: false,
        },
        {
            data: 'end_time',
            name: 'end_time',
            orderable: false,
        },

        {
            data: 'status',
            name: 'status',
            orderable: false, // Disable sorting

        },
        {
            data: 'action',
            name: 'action',
            orderable: false,
            searchable: false,
            render: function (data, type, row) {
                return `<button class="btn btn-danger" onclick="deleteSchedule(${row.id})"><i class="fa fa-trash fa-1x"></i></button>`;
            }
        }
        ],
        order: [
            []
        ]
    });

    const timeInput = document.getElementById("time-input");
    const endTime = document.getElementById("end-time");

    // Initialize Flatpickr
    const datePicker = flatpickr("#date-picker", {
        mode: "range",
        dateFormat: "Y-m-d",
        onChange: function (selectedDates) {

            const dates = datePicker.selectedDates;

            const timeValue = timeInput.value;

            const [startDate, endDate] = dates;
            const [hours, minutes] = timeValue.split(":").map(Number);
            const ampm = hours < 12 ? "AM" : "PM";
            const formattedHours = hours % 12 || 12;

            let resultArray = [];

            let currentDate = new Date(startDate);

            while (currentDate <= endDate) {
                const dateString = `${currentDate.getFullYear()}-${(
                    currentDate.getMonth() + 1
                )
                    .toString()
                    .padStart(2, "0")}-${currentDate
                        .getDate()
                        .toString()
                    } `;
                resultArray.push(dateString);
                currentDate.setDate(currentDate.getDate() + 1);
            }
            $('#startdate').val(resultArray);


        },
    });
    const datePicker2 = flatpickr("#date-picker2", {
        mode: "range",
        dateFormat: "Y-m-d",
        onChange: function (selectedDates) {



            const enddate = datePicker2.selectedDates;
            const endtime = endTime.value;

            const [startDate1, endDate1] = enddate;
            const [hours, minutes] = endtime.split(":").map(Number);
            const ampm = hours < 12 ? "AM" : "PM";
            const formattedHours = hours % 12 || 12;

            let resultArray2 = [];

            let currentDate = new Date(startDate1);

            while (currentDate <= endDate1) {
                const dateString = `${currentDate.getFullYear()}-${(
                    currentDate.getMonth() + 1
                )
                    .toString()
                    .padStart(2, "0")}-${currentDate
                        .getDate()
                        .toString()
                    } `;
                resultArray2.push(dateString);
                currentDate.setDate(currentDate.getDate() + 1);
            }
            $('#enddate').val(resultArray2);

        },
    });

    $('#schedule').on('submit', function (event) {
        event.preventDefault();

        var formData = new FormData();
        formData.append('shift_name', $('.shiftname').val());
        formData.append('start_date', $('#startdate').val());
        formData.append('start_time', $('#time-input').val());
        formData.append('end_date', $('#enddate').val());
        formData.append('end_time', $('#end-time').val());

        var isValid = true;


        clearValidationStates();

        if (!validateField('#shiftname', 'Shift Name')) isValid = false;
        if (!validateField('#date-picker', 'Shift Start Date')) isValid = false;
        if (!validateField('#time-input', 'Start Time')) isValid = false;
        if (!validateField('#date-picker2', 'Shift End Date')) isValid = false;
        if (!validateField('#end-time', 'End Time')) isValid = false;

        var startDate = $('#startdate').val();
        var endDate = $('#enddate').val();
        var start_time = $("#time-input").val();
        var end_time = $("#end-time").val();

        var startDateTime = new Date("1970-01-01T" + start_time + "Z"); // Using a fixed date (1970-01-01) for time comparison
        var endDateTime = new Date("1970-01-01T" + end_time + "Z");
        if (endDateTime <= startDateTime) {
            endDateTime.setDate(endDateTime.getDate() + 1);
        }
        var timeDiffInSeconds = (endDateTime - startDateTime) / 1000;

        // ********************* SHIFT DATES *********************
        var startDateArray = startDate.split(',');  // Array of start dates
        var endDateArray = endDate.split(',');      // Array of end dates

        // Convert the strings to Date objects for comparison and manipulation
        var startDateObj = new Date(startDateArray[0]); // First shift start date
        var lastStartDateObj = new Date(startDateArray[startDateArray.length - 1]); // Last shift start date
        var endDateObj = new Date(endDateArray[0]); // First shift end date
        var lastEndDateObj = new Date(endDateArray[endDateArray.length - 1]); // Last shift end date

        if (endDateObj < startDateObj) {
            $("#date-picker2").css("border", "1px solid red");
            $("#shift_date_error").html("Invalid shift dates.");
            isValid = false;
        } else if (endDateObj.getTime() === startDateObj.getTime()) {
            $("#date-picker2").css("border", "");
            $("#shift_date_error").html("");
        } else if ((endDateObj - startDateObj) === 86400000) { // 86400000 ms = 1 day
            $("#date-picker2").css("border", "");
            $("#shift_date_error").html("");
        } else {
            console.log("Invalid shift dates.");
            $("#date-picker2").css("border", "1px solid red");
            $("#shift_date_error").html("Invalid shift dates.");
            isValid = false;
        }
        if (lastEndDateObj.getTime() !== lastStartDateObj.getTime() && (lastEndDateObj - lastStartDateObj) !== 86400000) {
            $("#date-picker2").css("border", "1px solid red");
            $("#shift_date_error").html("Invalid shift dates");
            isValid = false;
        }

        // Check if shift exceeds 9 hours
        if (timeDiffInSeconds > 32400) {
            $("#show_error_msg").html("Shift Can Not Exceed 09:00 Hours");
            $("#end-time").css("border", "1px solid red");
            $("#end-time").removeClass("is-valid");
            isValid = false;
        } else {
            $("#show_error_msg").html("");
            $("#end-time").css("border", "");
        }

        if (isValid) {
            showLoader();
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: "{{ route('schedule.store') }}",
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function (response) {
                    hideLoader();
                    $('#schedule').modal('hide');
                    clearValidationStates();
                    $('#scheduletable').DataTable().ajax.reload();
                    createToast('info', 'fa-solid fa-circle-check', 'Success', 'Schedule created successfully.');
                },
                error: function (data) {
                    hideLoader();
                    var errors = data.responseJSON;
                    console.log(errors);
                }
            });
        }
    });

    function clearValidationStates() {
        $('.form-control').removeClass('is-invalid is-valid'); // Remove validation classes
        $('.text-danger').remove();

    }

    // Function to validate a generic field
    function validateField(selector, fieldName) {
        let value = $(selector).val();
        let parent = $(selector).closest('.input-block'); // Locate parent container for appending errors
        parent.find('.text-danger').remove(); // Clear previous error messages

        if (!value) {
            $(selector).addClass('is-invalid');
            parent.append(`<span class="text-danger">${fieldName} field cannot be empty.</span>`);
            return false;
        } else {
            $(selector).removeClass('is-invalid').addClass('is-valid'); // Reset the error if valid
            return true;
        }
    }
    // Handle delete confirmation
    $('#confirmDelete').on('click', function () {

        if (scheduleid) {
            $.ajax({
                url: '/delete-schedule/' + scheduleid,
                type: 'get',
                data: {
                    _token: "{{ csrf_token() }}" // Include CSRF token for security
                },
                success: function (result) {
                    console.log(result)
                    // Reload the DataTable after deletion
                    $('#scheduletable').DataTable().ajax.reload();
                    $('#deleteSchedule').modal('hide'); // Hide the modal
                    // Trigger custom success toaster
                    createToast('info', 'fa-solid fa-circle-check', 'Success', 'Schedule deleted successfully.');
                },
                error: function (err) {
                    createToast('error', 'fa-solid fa-circle-exclamation', 'Error', 'Error deleting .');
                }
            });
        }
    });
    function deleteSchedule(id) {
        scheduleid = id; // Store the ID of the user to delete

        $('#deleteSchedule').modal('show'); // Show the modal
    }

</script>

@endsection