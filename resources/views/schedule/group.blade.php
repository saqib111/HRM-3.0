@extends('layout.mainlayout')
@section('css')
<!-- Litepicker CSS -->

<link href="{{ asset('assets/css/custom-multi.css') }}" rel="stylesheet">
<style>
    .MultiDropdown {
        width: 100%;
        position: relative;
    }

    .search-container {
        position: relative;
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        padding: 5px;
        background-color: #f9f9f9;
        border: 1px solid #ccc;
        border-radius: 4px;
    }

    .search-box-style {
        width: 100%;
        border: none;
        outline: none;
        background-color: transparent;
        padding: 5px 10px;
        font-size: 16px;
        box-sizing: border-box;
    }

    .selected-tags {
        display: flex;
        flex-wrap: wrap;
        margin-top: 5px;
        max-width: 100%;
    }

    .selected-tag {
        display: inline-flex;
        align-items: center;
        padding: 5px 10px;
        margin: 2px;
        background-color: #007bff;
        color: white;
        border-radius: 20px;
        font-size: 14px;
        cursor: pointer;
    }

    .selected-tag span {
        margin-left: 5px;
        cursor: pointer;
    }

    .options-container {
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        border: 1px solid #ccc;
        border-top: none;
        max-height: 150px;
        overflow-y: auto;
        background-color: #fff;
        z-index: 100;
        border-radius: 0 0 4px 4px;
        display: none;
        box-sizing: border-box;
    }

    .option {
        padding: 10px;
        cursor: pointer;
        display: flex;
        align-items: center;
    }

    .option.selected {
        background-color: #007bff;
        color: white;
    }

    .option:hover {
        background-color: #f0f0f0;
    }
</style>
@endsection

@section('content')
<div class="page-header">
    <div class="row align-items-center">
        <div class="col-md-4">
            <h3 class="page-title">Create Group</h3>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a
                        href="{{ auth()->user()->role == '1' ? url('admin-dashboard') : url('attendance-employee') }}">Dashboard</a>
                </li>
                <li class="breadcrumb-item active">Create Group</li>
            </ul>
        </div>

    </div>
</div>
<div class="col-auto ms-auto mb-3">
    <ul class="split-head">
        <li>
            <button class="btn add-btn text-white" onclick="groupAdd()">
                <i class="fa fa-plus"></i> Add Group</button>
        </li>
    </ul>
</div>


<div id="notification" aria-live="polite" aria-atomic="true"></div>
<div class="row">
    <div class="col-md-12">
        <div class="table-responsive">
            <table class="table table-striped custom-table" id="groupTable">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Group Name</th>

                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="group-list">
                </tbody>
            </table>
        </div>
    </div>
</div>
<div id="create-group" class="modal custom-modal fade" role="dialog">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Create Group</h5>
                <button type="button" class="closed_btn" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form method="POST" enctype="multipart/form-data" id="group-submit">
                    @csrf
                    <div class="row">
                        <div class="input-block mb-3">

                            <div class="input-block mb-3">
                                <label for="groupName">Group Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="group_name" id="groupName">
                            </div>

                        </div>
                    </div>


                    <div class="input-block mb-3">

                        <div class="custom-select" id="first-assigner-select">
                            <label for="select_box">First Assigner Name:</label>
                            <div class="select-box first_select_box" id="select-box">
                                <input type="hidden" class="tags_input" id="first_assigners_backend_field" name="tags"
                                    hidden>
                                <div class="selected-options"></div>
                                <div class="arrow">
                                    <i class="fa fa-angle-down first_icon"></i>
                                </div>
                            </div>
                            <div class="options">
                                <div class="option-search-tags">
                                    <input type="text" class="search-tags" placeholder="Search Tags ..">
                                    <button type="button" class="clear"><i class="fa fa-close"></i></button>
                                </div>
                                <div class="op-disabled" selected disabled>Select Users</div>
                                <div class="no-result-message" style="display:none;">No Result Match</div>
                            </div>
                            <span class="text-danger" id="first_field_error"></span>
                        </div>
                    </div>

                    <div class="submit-section">
                        <button class="btn btn-primary">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


<!--Delete Modal -->
<div class="modal  fade" id="deleteGroup" tabindex="-1" aria-labelledby="exampleModalScrollable2" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="modalTitle">Delete Confirmation</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this Group? This action cannot be undone.</p>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDelete">Delete</button>
            </div>
        </div>
    </div>
</div>

<!--dDelete Modal End -->


<!-- The Modal -->
<div class="modal fade" id="employeeList" tabindex="-1" aria-labelledby="employeeListLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">

            <!-- Modal Header -->

            <div class="modal-header ">

                <h4 class="modal-title">Group Details </h4>
                <button type="button" class="closed_btn" data-bs-dismiss="modal"
                    style="margin-left:300px;">&times;</button>
            </div>
            <div class="row mt-2" style="margin-left:10px;" id="groupInfo">

            </div>

            <!-- Modal body -->
            <div class="modal-body">
                <table class="table table-striped">
                    <thead>
                        <tr class="text-center">
                            <th>Employee Name</th>
                            <th>Group Name</th>

                        </tr>
                    </thead>
                    <tbody id="employeeData">


                    </tbody>
                </table>
            </div>


            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Close</button>
            </div>

        </div>
    </div>
</div>






<div id="loader" class="loader" style="display: none;">
    <div class="loader-animation"></div>
</div>
@endsection
@section('script-z')  
<!-- <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script> -->
<script src="{{asset('assets/js/teamMembers.js')}}"></script>

<script>

    $(document).ready(function () {



        let table = $('#groupTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('group.data') }}",
                type: 'GET'
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'name', name: 'name', orderable: false },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false,
                    render: function (data, type, row) {
                        return `
                             <button class="btn btn-primary" onclick="showGroup(${row.id})">
                                <i class="fa fa-eye fa-1x"></i>
                            </button>
                            <button class="btn btn-danger" onclick="deleteGroup(${row.id})">
                                <i class="fa fa-trash fa-1x"></i>
                            </button>`;
                    }
                }
            ],
            order: []
        });


    });



    function groupAdd() {
        $('#groupName').val('');
        clearValidationStates();
        // loadEmployees();

        $('#create-group').modal('show').on('shown.bs.modal', function () {
            $('#first-assigner-select').val('');
        });
    }

    $('#create-group').on('hidden.bs.modal', function () {
        $('#selectEmployee').val(null).trigger('change');
    });

    $('#group-submit').on('submit', function (event) {
        event.preventDefault();
        const employee_id = [];

        var formData = new FormData();
        formData.append('group_name', $('#groupName').val());

        $("#first-assigner-select .tag").each(function () {
            const value = $(this).data("value");
            employee_id.push(value);
        });


        formData.append('selectedEmployee', employee_id);

        // if (selectedEmployee) {
        //     selectedEmployee.forEach(function (employee_id) {
        //         formData.append('employee_id[]', employee_id);
        //     });
        // }
        var isValid = true;
        clearValidationStates();
        if (!validateEmployee(employee_id)) isValid = false;
        if (!validateField('#groupName', 'Group Name')) isValid = false;
        if (isValid) {
            showLoader();
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                url: "{{ route('group.store') }}",
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function (response) {
                    hideLoader();
                    $('#create-group').modal('hide');

                    $('#groupTable').DataTable().ajax.reload();
                    createToast('info', 'fa-solid fa-circle-check', 'info', 'Group created successfully.');

                },
                error: function (data) {


                    var errors = data.responseJSON;
                    createToast('info', 'fa-solid fa-circle-uncheck', 'Fail', data.responseJSON.employee_name + ' ' + 'Schedule already exist.');
                }
            });
        }
    });

    function clearValidationStates() {
        $('.form-control').removeClass('is-invalid is-valid');
        $('.text-danger').remove();

    }
    function valdateCancel() {

        $('#first-assigner-select').removeClass('is-invalid is-valid');
        $('#groupName').removeClass('is-invalid is-valid');

        $('.text-danger').remove();
    }
    function validateEmployee(selectedEmployee) {

        let parent = $('#first-assigner-select').closest('.input-block');
        parent.find('.text-danger').remove();

        if (!selectedEmployee || selectedEmployee.length === 0) {


            parent.append('<span class="text-danger">Please select at least one Employee.</span>');
            return false;
        } else {
            $('#assign_label').removeClass('is-invalid').addClass('is-valid');
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

    $('#confirmDelete').on('click', function () {

        if (groupId) {
            $.ajax({
                url: '/group/' + groupId,
                type: 'DELETE',
                data: {
                    _token: "{{ csrf_token() }}"
                },
                success: function (result) {


                    $('#groupTable').DataTable().ajax.reload();
                    $('#deleteGroup').modal('hide');

                    createToast('info', 'fa-solid fa-circle-check', 'info', 'Group deleted successfully.');
                },
                error: function (err) {
                    createToast('error', 'fa-solid fa-circle-exclamation', 'Error', 'Error deleting .');
                }
            });
        }
    });
    function deleteGroup(id) {
        groupId = id;

        $('#deleteGroup').modal('show');
    }

    function showGroup(id) {
        group_id = id;

        if (group_id) {

            $.ajax({
                url: "{{ route('group.member', '') }}" + "/" + group_id,
                type: 'get',
                data: {
                    _token: "{{ csrf_token() }}"
                },
                success: function (response) {
                    console.log(response)
                    $('#groupInfo').empty().append(

                        `<div class="col-md-12"><h5 class="modal-title">Group name : ${response.group_name} </h5></div>
        <div class="col-md-12"><h5 class="modal-title">Total employee :  ${response.total}  </h5></div>`

                    )
                    $('#employeeData').empty().append();
                    $.each(response.name, function (key, value) {
                        $('#employeeData').append(
                            `
                  <tr class="text-center">
                    <td>${value[0]}</td>
                    <td>${response.group_name}</td>
       
                 </tr>
                `
                        )
                    });

                    $('#employeeList').modal('show');

                },
                error: function (err) {
                    createToast('error', 'fa-solid fa-circle-exclamation', 'Error', 'Fetching deleting .');
                }
            });
        }


    }









</script>


@endsection