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



    .table tr.dayoff {
        --bs-table-bg: #d9f7fffc;
    }

    .result {
        margin-top: 20px;
        font-size: 1.1em;
        color: #333;
    }


    table.dataTable th.dt-type-numeric,
    table.dataTable th.dt-type-date,
    table.dataTable td.dt-type-numeric,
    table.dataTable td.dt-type-date {
        text-align: center !important;
    }
</style>
@endsection
@section('content')
<div class="page-header">
    <div class="row align-items-center">
        <div class="col-md-4">
            <h3 class="page-title">Manage Attendance</h3>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="admin-dashboard.html">Dashboard</a></li>
                <li class="breadcrumb-item active">Manage Attendance</li>
            </ul>
        </div>

    </div>
</div>

<!-- Table -->
<div class="row">
    <div class="col-md-12">
        <div class="table-responsive">
            <table class="table  custom-table" id="attendance">
                <thead>
                    <tr style="text-left">
                        <th>#</th>
                        <th>Employee Name</th>
                        <th>Shift In</th>
                        <th>Shift Out</th>
                        <th>Duty Hours</th>
                        <th>Check In</th>
                        <th>Check Out</th>
                        <th>Off Day</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="attendance-list">
                </tbody>
            </table>
        </div>
    </div>
</div>

<!--End-->


<div id="notification" aria-live="polite" aria-atomic="true"></div>
<div id="holiday" class="modal custom-modal fade" data-bs-backdrop='static' role="dialog">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Assign Off Day</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="assign-holiday" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="row">


                        <div class="col-sm-12 ">
                            <div class=" input-block mb-3 ">
                                <label class="col-form-label" for="assign_label" placeholder="Select Employee">Select
                                    Employee <span class="text-danger">*</span></label>

                                <select class="form-select " name="employee_id[]" multiple id="employee">

                                </select>
                                <div id="assign"></div>
                            </div>
                        </div>
                    </div>




                    <div class="col-sm-12">
                        <div class="input-block mb-3 ">
                            <label for="datepicker">Select Off Days:</label>
                            <input type="text" id="date-picker" name="holiday[]" class="form-control" />


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

        let table = $('#attendance').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('attendance.record') }}",
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
                data: 'shift_in',
                name: 'shift_in',
                orderable: false,
            },
            {
                data: 'shift_out',
                name: 'shift_out',
                orderable: false,
            },
            {
                data: 'duty_hours',
                name: 'duty_hours',
                orderable: false,
            },
            {
                data: 'check_in',
                name: 'chek_in',
                orderable: false,
            },

            {
                data: 'check_out',
                name: 'check_out',
                orderable: false,

            },
            {
                data: 'dayoff',
                name: 'dayoff',
                orderable: false,
            },
            {
                data: 'action',
                name: 'action',
                orderable: false,
                searchable: false,
                render: function (data, type, row) {
                    if (row.dayoff === "Yes") {
                        console.log(row)


                    }
                    return `<button class="btn btn-primary" onclick="deleteSchedule(${row.id})"><i class="fa fa-edit fa-1x"></i></button>`;
                }
            }
            ],
            order: [],
            createdRow: function (row, data, dataIndex) {
                if (data.dayoff === "Yes") {
                    $(row).addClass("dayoff");
                }
            }

        });





        const datePicker = flatpickr("#date-picker", {
            mode: "multiple",
            dateFormat: "Y-m-d",
            onChange: function (selectedDates) {


            }

        });

        $.ajax({
            url: "{{route('employee.get')}}",
            type: 'get',
            data: {
                _token: "{{ csrf_token() }}" // Include CSRF token for security
            },
            success: function (response) {
                var select = $('#employee');
                select.empty();
                $.each(response, function (key, value) {

                    select.append('<option value="' + value.id + '">' + value.name + '</option>');


                });
                new MultiSelectTag("employee", {
                    rounded: true,
                    shadow: false,
                    placeholder: "Search",
                    tagColor: {
                        textColor: "#327b2c",
                        borderColor: "#92e681",
                        bgColor: "#eaffe6"
                    }
                });

            },
            error: function (err) {
                createToast('error', 'fa-solid fa-circle-exclamation', 'Error', 'Error deleting .');
            }
        });

        $('#holiday').modal('show');

    });


    $('#assign-holiday').on('submit', function (event) {
        event.preventDefault();

        $('.select').select2();
        var formData = new FormData();

        formData.append('date', $('#date-picker').val());


        var selectedEmployee = $('#employee').val();

        if (selectedEmployee) {
            selectedEmployee.forEach(function (employee_id) {
                formData.append('employee_id[]', employee_id);
            });
        }

        var isValid = true;
        clearValidationStates();
        if (!validateEmployee(selectedEmployee)) isValid = false;
        if (!validateField('#date-picker', ' Date')) isValid = false;
        if (isValid) {
            showLoader();
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                url: "{{ route('holiday.submit') }}",
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function (response) {
                    hideLoader();

                    $('#holiday').modal('hide');


                    createToast('info', 'fa-solid fa-circle-check', 'Success', 'Holiday created successfully.');

                },
                error: function (data) {
                    hideLoader();
                    $('#holiday').modal('hide');
                    var errors = data.responseJSON;
                    console.log(errors.message)
                    createToast('error', 'fa-solid fa-circle-exclamation', 'Fail', 'Wrong Month Select.No Data');
                }
            });
        }
    });

    function clearValidationStates() {
        $('.form-control').removeClass('is-invalid is-valid');
        $('.text-danger').remove();

    }
    function valdateCancel() {

        $('#employee').removeClass('is-invalid is-valid');
        $('#groupName').removeClass('is-invalid is-valid');

        $('.text-danger').remove();
    }
    function validateEmployee(selectedEmployee) {
        let parent = $('#employee').closest('.input-block');
        parent.find('.text-danger').remove();

        if (!selectedEmployee || selectedEmployee.length === 0) {
            $('#employee').addClass('is-invalid');
            parent.append('<span class="text-danger">Please select at least one Employee.</span>');
            return false;
        } else {
            $('#employee').removeClass('is-invalid').addClass('is-valid');
            return true;
        }
    }
    function validateField(selector, fieldName) {
        let value = $(selector).val();
        let parent = $(selector).closest('.input-block');
        parent.find('.text-danger').remove();

        if (!value) {
            $(selector).addClass('is-invalid');
            parent.append(`<span class="text-danger">${fieldName} field cannot be empty.</span>`);
            return false;
        } else {
            $(selector).removeClass('is-invalid').addClass('is-valid');
            return true;
        }
    }


</script>
@endsection