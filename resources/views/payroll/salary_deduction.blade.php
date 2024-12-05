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
</style>
@endsection
@section('content')
<div class="page-header">
    <div class="row align-items-center">
        <div class="col-md-4">
            <h3 class="page-title">Salary Deduction</h3>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="admin-dashboard.html">Dashboard</a></li>
                <li class="breadcrumb-item active">Salary Deduction</li>
            </ul>
        </div>

    </div>
</div>

<form id="customizeSearch">
    <div class="col-auto my-3 d-flex justify-content-start">
        <div class="col-md-2 text-dark">
            <div class="input-block mb-3 text-dark">
                <label for="datepicker">Date Range:</label>
                <input type="text" id="date-picker" name="start-date[]" class="form-control"
                    placeholder="Select Date Range" />
                <input type="hidden" name="startdate" id="startdate">
            </div>
        </div>
        <div class="col-md-2 text-dark ms-md-3">
            <div class="input-block mb-3 text-dark">
                <label for="nationality">Nationality:</label>
                <select class="form-select" aria-label="Default select example" id="nationality" name="nationality">
                    <option value="ALL">All Nationalities</option>
                    <option value="Pakistan">Pakistani</option>
                    <option value="India">Indian</option>
                    <option value="Bangladesh">Bangladeshi</option>
                    <option value="Malaysia">Malaysian</option>
                    <option value="Singapore">Singaporean</option>
                    <option value="Vietnam">Vietnamese</option>
                    <option value="Cambodia">Cambodian</option>
                    <option value="Philippines">Filipino</option>
                    <option value="Indonesia">Indonesian</option>
                    <option value="Brazil">Brazilian</option>
                    <option value="Nepal">Nepalese</option>
                    <option value="Korea">Korean</option>
                    <option value="Thailand">Thai</option>
                </select>
            </div>
        </div>
        <div class="col-md-2 text-dark ms-md-3">
            <div class="input-block mb-3 text-dark">
                <label for="office">Office:</label>
                <select class="form-select" id="office" name="office">
                    <option value="AllOffices">All Offices</option>
                    <option value="Sihanoukville" selected>Sihanoukville</option>
                    <option value="Bataan">Bataan</option>
                    <option value="Bavet">Bavet</option>
                    <option value="Malaysia">Malaysia</option>
                </select>
            </div>
        </div>
        <div class="col-md-1 text-dark ms-md-3">
            <div class="input-block mb-3 text-dark d-flex flex-column">
                <button class="btn btn-primary mt-4" id="filter_btn" style="padding: 8px;">Filter</button>
            </div>
        </div>
        <div class="col-md-2 text-dark ms-md-3">
            <div class="input-block mb-3 text-dark d-flex flex-column">
                <button id="export-btn" class="btn btn-success mt-4" style="padding: 8px;">Download in
                    Excel</button>
            </div>
        </div>
    </div>
</form>


<div id="notification" aria-live="polite" aria-atomic="true"></div>
<div class="row">
    <div class="col-md-12">
        <div class="table-responsive">
            <table class="table table-striped custom-table" id="scheduletable">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Username</th>
                        <th>Employee ID</th>
                        <th>No of OFF-Days</th>
                        <th>No of Leaves</th>
                        <th>No of Absentees</th>
                        <th>Absentee Fine</th>
                        <th>Late Fine</th>
                        <th>Total Fine</th>
                    </tr>
                </thead>
                <tbody id="schedule-list">
                </tbody>
            </table>
        </div>
    </div>
</div>


<!-- PreLoader -->
<div id="loader" class="loader" style="display: none;">
    <div class="loader-animation"></div>
</div>

@endsection

@section('script-z')
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
    // Initialize DataTable without data
    let table = $('#scheduletable').DataTable({
        processing: true,
        serverSide: true,
        searching: true, // Disable searching
        paging: true,     // Enable pagination
        info: true,       // Show table information
        ajax: {
            url: "{{ route('payroll.dynamic_data') }}",
            type: 'GET',
            data: function (d) {
                // Append form data to the DataTable AJAX request
                d.nationality = $('#nationality').val();
                d.start_date = $('#startdate').val(); // Date range
                d.office = $('#office').val(); // Date range
            },
            dataSrc: function (json) {
                // If no data, prevent DataTable from showing rows
                if (!json.data || json.data.length === 0) {
                    return [];
                }
                return json.data;
            },
        },
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'username', name: 'username' },
            { data: 'employee_id', name: 'employee_id' },
            { data: 'dayoff', name: 'dayoff' },
            { data: 'leave_count', name: 'leave_count' },
            { data: 'total_absentees', name: 'total_absentees' },
            { data: 'absentee_fine', name: 'absentee_fine' },
            { data: 'late_fine', name: 'late_fine' },
            { data: 'total_deduction', name: 'total_deduction' },
        ],
        order: [
            [0, 'desc'] // Default order by the first column (index)
        ],
        // Handle pagination
        pageLength: 20, // Set the default number of records to show
        lengthMenu: [10, 20, 25, 50, 100], // Options for records per page
        deferLoading: 0, // Do not load data on initialization
    });

    // Handle Form Submission
    $('#customizeSearch').on('submit', function (e) {
        e.preventDefault(); // Prevent default form submission
        table.ajax.reload(); // Reload the DataTable with filter data
    });

</script>

<script>
    $(document).ready(function () {
        // Initially, disable the Filter button and hide the Export button
        $('#filter_btn').prop('disabled', true);  // Filter button initially disabled
        $('#export-btn').hide();  // Hide Export button initially

        const timeInput = document.getElementById("time-input");
        const endTime = document.getElementById("end-time");
        // Initialize the flatpickr for the date range
        const datePicker = flatpickr("#date-picker", {
            mode: "range",
            dateFormat: "Y-m-d",
            onChange: function (selectedDates) {
                // When dates are selected, update the hidden input field with the formatted date range
                if (selectedDates.length === 2) {
                    const startDate = flatpickr.formatDate(selectedDates[0], "Y-m-d");
                    const endDate = flatpickr.formatDate(selectedDates[1], "Y-m-d");
                    $('#startdate').val(`${startDate},${endDate}`);
                } else {
                    $('#startdate').val('');  // If no valid date is selected, reset the hidden input
                }
                checkFilters();  // Check filters when date range changes
            }
        });

        // Listen for changes in other filter inputs and check the filters
        $('#nationality, #office').on('change', function () {
            checkFilters();
        });

        // Check if all filters are selected
        function checkFilters() {
            const nationality = $('#nationality').val();
            const startDate = $('#startdate').val();  // Get the value of the hidden input field
            const office = $('#office').val();

            // Enable the Filter button only if all fields are filled
            if (nationality && startDate && office) {
                $('#filter_btn').prop('disabled', false);  // Enable Filter button
            } else {
                $('#filter_btn').prop('disabled', true);  // Disable Filter button if any field is empty
            }
        }

        // Handle the filter form submission
        $('#customizeSearch').on('submit', function (e) {
            e.preventDefault();  // Prevent the default form submission

            // Reload the DataTable with the current filter data
            table.ajax.reload(function (json) {
                // Check if any data is returned
                if (json.data && json.data.length > 0) {
                    // Data exists, enable and show the Export button
                    $('#export-btn').prop('disabled', false);  // Enable Export button
                    $('#export-btn').show();  // Show Export button
                } else {
                    // No data, disable and hide the Export button
                    $('#export-btn').prop('disabled', true);  // Disable Export button
                    $('#export-btn').hide();  // Hide Export button
                }
            });
        });

        // Handle Export button click
        $('#export-btn').on('click', function () {
            // Get the filter values
            const nationality = $('#nationality').val();
            const startDate = $('#startdate').val();
            const office = $('#office').val();

            // Proceed with export if all filters are selected
            if (nationality && startDate && office) {
                const filters = {
                    nationality: nationality,
                    start_date: startDate,
                    office: office
                };

                // Construct the query string with the filters
                let queryString = $.param(filters);

                // Redirect to the export route with the query parameters
                window.location.href = "{{ route('payroll.export') }}?" + queryString;
            } else {
                alert("Please fill in all filter fields before exporting.");
            }
        });

        // Ensure initial state when the page is loaded
        checkFilters();  // Call it on document load to check the initial state
    });
</script>



@endsection