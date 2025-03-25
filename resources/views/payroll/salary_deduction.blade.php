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
    @php
        $user = auth()->user();
        $permissions = getUserPermissions($user);
        $companies = DB::table('companies')->where('status', '1')->pluck('id', 'name')->toArray();

        $allowed_offices = ['Sihanoukville', 'Malaysia', 'Bavet', 'Poipet', 'TWFM'];
        $matching_offices = array_intersect($allowed_offices, $permissions);

        if (!empty($matching_offices)) {
            $office_based_permission = array_values($matching_offices);
        } else {
            $office_based_permission = [];
        }
    @endphp

    <div class="page-header">
        <div class="row align-items-center">
            <div class="col-md-4">
                <h3 class="page-title"><span data-translate="salary_deduction">Salary Deduction</span></h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="admin-dashboard.html">Dashboard</a></li>
                    <li class="breadcrumb-item active"><span data-translate="salary_deduction">Salary Deduction</span></li>
                </ul>
            </div>

        </div>
    </div>

    <form id="customizeSearch">
        <div class="col-auto my-3 d-flex justify-content-start">
            <div class="col-md-2 text-dark">
                <div class="input-block mb-3 text-dark">
                    <span data-translate="date_range"><label for="datepicker">Date range:</label></span>
                    <input type="text" id="date-picker" name="start-date[]" class="form-control"
                        placeholder="Select Date Range" />
                    <input type="hidden" name="startdate" id="startdate">
                </div>
            </div>
            @if(auth()->user()->role == 1 || auth()->user()->role == 3)
                <div class="col-md-2 text-dark ms-md-3">
                    <div class="input-block mb-3 text-dark">
                        <label for="nationality">Nationality:</label>
                        <select class="form-select" aria-label="Default select example" id="nationality" name="nationality"
                            @if(auth()->user()->role == 5 || auth()->user()->role == 4 || auth()->user()->role == 2) disabled @endif>
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
                            @if(auth()->user()->role == 5 || auth()->user()->role == 4 || auth()->user()->role == 2)
                                <option value="{{ $user_nationality->nationality }}" selected>{{ $user_nationality->nationality }}
                                </option>
                            @endif
                        </select>
                    </div>
                </div>
            @endif
            @if(auth()->user()->role == 1 || auth()->user()->role == 3)
                <div class="col-md-2 text-dark ms-md-3">
                    <div class="input-block mb-3 text-dark">
                        <label for="office">Office:</label>
                        <select class="form-select" id="office" name="office" @if(auth()->user()->role == 5 || auth()->user()->role == 4 || auth()->user()->role == 2) disabled @endif>

                            @if(auth()->user()->role == 1)
                                <option value="AllOffices">All Offices</option>
                            @endif

                            @if(auth()->user()->role == 1 || (auth()->user()->role == 3 && in_array('Sihanoukville', $office_based_permission)))
                                <option value="Sihanoukville">Sihanoukville</option>
                            @endif
                            @if(auth()->user()->role == 1 || (auth()->user()->role == 3 && in_array('Malaysia', $office_based_permission)))
                                <option value="Malaysia">Malaysia</option>
                            @endif

                            @if(auth()->user()->role == 1 || (auth()->user()->role == 3 && in_array('Bavet', $office_based_permission)))
                                <option value="Bavet">Bavet</option>
                            @endif

                            @if(auth()->user()->role == 1 || (auth()->user()->role == 3 && in_array('TWFM', $office_based_permission)))
                                <option value="TWFM">TWFM</option>
                            @endif
                            @if(auth()->user()->role == 1 || (auth()->user()->role == 3 && in_array('Poipet', $office_based_permission)))
                                <option value="Poipet">Poipet</option>
                            @endif

                            @if(auth()->user()->role == 5 || auth()->user()->role == 4 || auth()->user()->role == 2)
                                <option value="{{ $user_office->office }}" selected>{{ $user_office->office }}</option>
                            @endif
                        </select>
                    </div>
                </div>
            @endif
            @if(auth()->user()->role == 1 || auth()->user()->role == 3)
                <div class="col-md-2 text-dark ms-md-3">
                    <div class="input-block mb-3 text-dark">
                        <label for="group">Group:</label>
                        <select class="form-select" id="group" name="group">
                            @foreach($companies as $name => $id)
                                <option value="{{ $name }}">{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            @endif
            <div class="col-md-1 text-dark ms-md-3">
                <div class="input-block mb-3 text-dark d-flex flex-column">
                    <button class="btn btn-primary mt-4" id="filter_btn" style="padding: 8px;"><span
                            data-translate="filter">Filter</span></button>
                </div>
            </div>
            <div class="col-md-2 text-dark ms-md-3">
                <div class="input-block mb-3 text-dark d-flex flex-column">
                    <button id="export-btn" class="btn btn-success mt-4" style="padding: 8px;"><span
                            data-translate="download_excel">Download in Excel</span></button>
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
                            <th></th>
                            <th></th>
                            <th>No of OFF-Days</th>
                            <th>No of Leaves</th>
                            <th>No of Absentees</th>
                            <th>Absentee Fine</th>
                            <th>Late Fine</th>
                            <th>Total Fine</th>
                            <th>Total UL</th>
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
            searching: true,    // Disable searching
            paging: true,       // Enable pagination
            info: true,         // Show table information
            ajax: {
                url: "{{ route('payroll.dynamic_data') }}",
                type: 'GET',
                data: function (d) {
                    // Append form data to the DataTable AJAX request
                    d.nationality = $('#nationality').val();
                    d.start_date = $('#startdate').val(); // Date range
                    d.office = $('#office').val(); // Date range
                    d.group = $('#group').val(); // Date range
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
                { data: 'employee_id', name: 'employee_id', title: '<span data-translate="employee_id">Employee ID</span>', orderable: false, searchable: true },
                { data: 'username', name: 'username', title: '<span data-translate="username">Username</span>', orderable: false, searchable: true },
                { data: 'dayoff', name: 'dayoff', title: '<span data-translate="no_off_day">No of OFF-Days</span>', orderable: false, searchable: false },
                { data: 'leave_count', name: 'leave_count', title: '<span data-translate="no_of_leaves">No of Leaves</span>', orderable: false, searchable: false },
                { data: 'total_absentees', name: 'total_absentees', title: '<span data-translate="no_of_absentees">No of Absentees</span>', orderable: false, searchable: false },
                { data: 'absentee_fine', name: 'absentee_fine', title: '<span data-translate="absent_fine">Absentee Fine</span>', orderable: false, searchable: false },
                { data: 'late_fine', name: 'late_fine', title: '<span data-translate="late_fine">Late Fine</span>', orderable: false, searchable: false },
                { data: 'total_deduction', name: 'total_deduction', title: '<span data-translate="total_deduction">Total Fine</span>', orderable: false, searchable: false },
                { data: 'unpaid_leave_count', name: 'unpaid_leave_count', title: '<span data-translate="total_ul">Total UL</span>', orderable: false, searchable: false },
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
            $('#filter_btn').prop('disabled', true);
            $('#export-btn').hide();

            const datePicker = flatpickr("#date-picker", {
                mode: "range",
                dateFormat: "Y-m-d",
                onChange: function (selectedDates) {
                    if (selectedDates.length === 2) {
                        const startDate = flatpickr.formatDate(selectedDates[0], "Y-m-d");
                        const endDate = flatpickr.formatDate(selectedDates[1], "Y-m-d");
                        $('#startdate').val(`${startDate},${endDate}`);
                    } else {
                        $('#startdate').val('');
                    }
                    checkFilters();
                }
            });

            $('#nationality, #office').on('change', function () {
                checkFilters();
            });

            function checkFilters() {
                const nationality = $('#nationality').val();
                const startDate = $('#startdate').val();
                const office = $('#office').val();
                const group = $('#group').val();

                if (startDate) {
                    $('#filter_btn').prop('disabled', false);
                } else {
                    $('#filter_btn').prop('disabled', true);
                }
            }

            let filterChanged = false;

            $('#customizeSearch').on('submit', function (e) {
                e.preventDefault();
                filterChanged = true;
                const nationality = $('#nationality').val();
                const office = $('#office').val();
                const group = $('#group').val();

                // Reload DataTable with current filter values
                table.ajax.reload(function (json) {
                    if (json.data && json.data.length > 0) {
                        $('#export-btn').prop('disabled', false).show();
                    } else {
                        $('#export-btn').prop('disabled', true).hide();
                    }
                });
            });

            $('#export-btn').on('click', function () {
                const nationality = $('#nationality').val();
                const startDate = $('#startdate').val();
                const office = $('#office').val();

                if (startDate) {
                    const filters = {
                        nationality: nationality,
                        start_date: startDate,
                        office: office
                    };

                    let queryString = $.param(filters);
                    window.location.href = "{{ route('payroll.export') }}?" + queryString;
                } else {
                    alert("Please fill in all filter fields before exporting.");
                }
            });

            checkFilters();  // Ensure filters are checked when page loads
        });
    </script>

@endsection