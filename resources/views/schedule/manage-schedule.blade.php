@extends('layout.mainlayout')
@section('head')
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
        font-family: "Arial", sans-serif;
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
<div class="page-header">
    <div class="row align-items-center">
        <div class="col-md-4">
            <h3 class="page-title">Enable/Delete Schedule</h3>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="admin-dashboard.html">Dashboard</a></li>
                <li class="breadcrumb-item active">Enable/Delete Schedule</li>
            </ul>
        </div>

    </div>
</div>
<div id="notification" aria-live="polite" aria-atomic="true"></div>
<div class="row">
    <div class="col-md-12">
        <div class="table-responsive">
            <table class="table table-striped custom-table" id="manageSchedule">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Shift Name</th>
                        <th>Start Date</th>
                        <th>Start Time</th>
                        <th>End Date</th>
                        <th>End Time</th>
                        <th>Status / Activate </th>

                    </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
        </div>
    </div>
</div>



<div id="assign_schedule" class="modal custom-modal fade" role="dialog">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Assign Employee</h5>
                <button type="button" class="closed_btn" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="assign-employee" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="row">

                        <input type="hidden" name="schedule_id" val="" id="ID">
                        <input type="hidden" name="start_to" val="" id="st">
                        <input type="hidden" name="start_end" val="" id="se">
                        <input type="hidden" name="start_time" val="" id="stime">
                        <input type="hidden" name="end_to" val="" id="et">
                        <input type="hidden" name="end_end" val="" id="en">
                        <input type="hidden" name="end_time" val="" id="etime">

                        <div class="col-sm-12 ">
                            <div class="input-block mb-3">
                                <label class="col-form-label" for="shiftname">Shift Name <span
                                        class="text-danger">*</span></label>
                                <input class="form-control shiftname" type="text" name="shift_name" id="shiftname"
                                    disabled>


                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="input-block mb-3">
                                <label for="datepicker">Select Shift Start Dates:</label>
                                <input type="text" id="date-picker" name="start-date[]" class="form-control" disabled />


                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="input-block mb-3">
                                <label for="datepicker-time">Select Shift Start Time:</label>
                                <input type="time" id="time-input" name="start_time" class="form-control" disabled />

                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="input-block mb-3">
                                <label for="datepicker2">Select Shift End Dates:</label>
                                <input type="text" id="date-picker2" name="end-date[]" class="form-control" disabled />


                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="input-block mb-3">
                                <label for="datepicker2-time">Select Shift End Time:</label>
                                <input type="time" id="end-time" name="end_time" class="form-control" disabled />

                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="input-block mb-3 ">
                                <label class="col-form-label" for="assign_label">Assign Employee <span
                                        class="text-danger">*</span></label>

                                <select class="form-select tagging assign " name="employee_id[]" multiple="multiple"
                                    id="assign_label">

                                </select>
                                <div id="assign"></div>
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



<!-- PreLoader -->
<div id="loader" class="loader" style="display: none;">
    <div class="loader-animation"></div>
</div>
@endsection

@section('script-z')  
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
    $(document).ready(function () {
        $('.tagging').select2({
            tags: true
        });

        let table = $('#manageSchedule').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('manage.scheduledata') }}",
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
                orderable: false,
                render: function (data, type, row) {

                    let checked = (data === '0') ? 'checked' : '';
                    return `
                        <div class="status-toggle-container" style="display:flex;">
                <div class="status-toggle">
                    <input type="checkbox" id="staff_module_${row.id}" class="check" ${checked} onchange="changeStatus(${row.id}, this)">
                    <label for="staff_module_${row.id}" class="checktoggle">checkbox</label>
                </div>
            </div>
                    `;
                }
            },

            ],
            order: [
                []
            ]
        });
    });

    function changeStatus(id, checkbox) {
        const status = checkbox.checked ? 0 : 1; // Assuming 1 is active and 0 is inactive

        $.ajax({
            url: "{{ route('schedule.update') }}", // Update this URL to your status update endpoint
            type: 'POST',
            data: {
                id: id,

                _token: '{{ csrf_token() }}' // Include CSRF token for security
            },
            success: function (response) {
                if (response.id == '0') {
                    createToast('info', 'fa-solid fa-circle-check', 'Success', 'Schedule  is activated!');
                } else if (response.id == '1') {
                    createToast('error', 'fa-solid fa-circle-xmark', 'Success', 'User status is deactivated!');
                } else {
                    // If there's an error, revert the checkbox to its original state
                    checkbox.checked = !checkbox.checked; // Toggle back
                    alert('Error updating status: ' + response.message);
                }
            },
            error: function (xhr, status, error) {
                // Handle AJAX error
                checkbox.checked = !checkbox.checked; // Toggle back
                alert('AJAX Error: ' + error);
            }
        });
    }



</script>

@endsection