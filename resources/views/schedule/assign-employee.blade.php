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
            <h3 class="page-title">Assign Group</h3>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="admin-dashboard.html">Dashboard</a></li>
                <li class="breadcrumb-item active">Assign Group</li>
            </ul>
        </div>

    </div>
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



<div id="assign_schedule" class="modal custom-modal fade" role="dialog">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Assign Group</h5>
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
                                <label class="col-form-label" for="assign_label">Assign Group<span
                                        class="text-danger">*</span></label>

                                <select class="form-control" name="group" id="assign_label">


                                </select>

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
                    return `  <button class="btn btn-primary" onclick="assignEmployee(${row.id})"><i class="fa fa-user fa-1x"></i></button>
                              
                                `;
                }
            }
            ],
            order: [
                []
            ]
        });
    });
    function assignEmployee(id) {

        $('#assign_label').val(null).trigger('change');
        valdateCancel();

        $.ajax({
            url: '{{route('schedule.get', "")}}/' + id,
            type: 'GET',
            success: function (response) {
                $('#assign_label').val(null).trigger('change');
                $('#ID').val(response.schedule[0].id);
                $('#st').val(response.schedule[0].start_to);
                $('#se').val(response.schedule[0].start_end);
                $('#stime').val(response.schedule[0].start_time);
                $('#et').val(response.schedule[0].end_to);
                $('#en').val(response.schedule[0].end_end);
                $('#etime').val(response.schedule[0].end_time);
                $('#shiftname').val(response.schedule[0].name);
                $('#date-picker').val(response.schedule[0].start_to + ' ' + '-' + ' ' + response.schedule[0].start_end);
                $('#time-input').val(response.schedule[0].start_time);
                $('#date-picker2').val(response.schedule[0].end_to + ' ' + '-' + ' ' + response.schedule[0].end_end);
                $('#end-time').val(response.schedule[0].end_time);

                $("#assign_label")
                    .find('option')
                    .remove()
                    .end()
                $("#assign_label").append('<option disabled selected>Select Group</option>');
                $.each(response.group, function (key, value) {

                    $("#assign_label")

                        .append('<option value="' + value.id + '" >' + value.name +
                            '</option>');



                });

                $('#assign_schedule').modal('show');
            },
            error: function (error) {
                alert('Error fetching employee details.');
            }
        });

    }


    $('#assign-employee').on('submit', function (event) {
        event.preventDefault();



        var formData = new FormData();
        formData.append('schedule_id', $('#ID').val());
        formData.append('shift_name', $('#shiftname').val());
        formData.append('start_to', $('#st').val());
        formData.append('start_end', $('#se').val());
        formData.append('start_time', $('#stime').val());
        formData.append('end_to', $('#et').val());
        formData.append('end_end', $('#en').val());
        formData.append('end_time', $('#etime').val());
        formData.append('group_id', $('#assign_label').val());

        var selectedEmployee = $('#assign_label').val();


        var isValid = true;


        clearValidationStates();


        if (!validateEmployee(selectedEmployee)) isValid = false;



        if (isValid) {
            showLoader();
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                url: "{{ route('attendancerecord.store') }}",
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function (response) {
                    hideLoader();
                    $('#assign_schedule').modal('hide');

                    $('#scheduletable').DataTable().ajax.reload();
                    createToast('info', 'fa-solid fa-circle-check', 'info', 'Schedule created successfully.');

                },
                error: function (data) {
                    hideLoader();

                    var errors = data.responseJSON;
                    console.log(errors)
                    createToast('info', 'fa-solid fa-circle-uncheck', 'Fail', data.responseJSON.employee_name + ' ' + 'Schedule already exist.');
                }
            });
        }
    });

    function clearValidationStates() {
        $('.form-control').removeClass('is-invalid is-valid'); // Remove validation classes
        $('.text-danger').remove();

    }

    function validateEmployee(selectedEmployee) {
        let parent = $('#assign_label').closest('.input-block');
        parent.find('.text-danger').remove(); // Clear previous messages

        if (!selectedEmployee || selectedEmployee.length === 0) {
            $('#assign_label').addClass('is-invalid');
            parent.append('<span class="text-danger">Please select Group.</span>');
            return false;
        } else {
            $('#assign_label').removeClass('is-invalid').addClass('is-valid');
            return true;
        }
    }
    function valdateCancel() {

        $('#assign_label').removeClass('is-invalid is-valid');

        $('.text-danger').remove();
    }



</script>

@endsection