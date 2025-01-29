@extends('layout.mainlayout')
@section('content')
<div id="notification" aria-live="polite" aria-atomic="true"></div>

@php
    $user = auth()->user();
    $permissions = getUserPermissions($user); // Use the helper function to fetch permissions
    // Check if the user has at least one of the required permissions or is a superadmin
    $hasActionPermission = $user->role == 1 ||
        in_array('update_designation', $permissions) ||
        in_array('delete_designation', $permissions);
@endphp

<div class="page-header">
    <div class="row align-items-center">
        <div class="col-md-4">
            <h3 class="page-title">Designation</h3>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a
                        href="{{ auth()->user()->role == '1' ? url('admin-dashboard') : url('attendance-employee') }}">Dashboard</a>
                </li>
                <li class="breadcrumb-item active">Designation</li>
            </ul>
        </div>
        <div class="col-md-8 float-end ms-auto">
            <div class="d-flex title-head">
                @if($user->role == 1 || in_array('create_department', $permissions))
                    <a href="#" class="btn_added" data-bs-toggle="modal" data-bs-target="#add_designation"><i
                            class="la la-plus-circle"></i> Add Designation</a>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal Start -->
<div class="modal fade" id="deleteConfirmationModal" tabindex="-1" aria-labelledby="exampleModalScrollable2"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="modalTitle">Delete Confirmation</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this designation? This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDelete">Delete</button>
            </div>
        </div>
    </div>
</div>
<!-- Delete Confirmation Modal End -->

<div class="row">
    <div class="col-md-12">
        <div class="table-responsive">
            <table class="table table-striped custom-table" id="designation_table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Designation Name</th>
                        <th>Department Name</th>
                        @if($user->role == 1 || in_array('update_designation', $permissions) || in_array('delete_designation', $permissions))
                            <th class="text-center">Action</th>
                        @endif
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add Designation Modal -->
<div id="add_designation" class="modal custom-modal fade" role="dialog">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Designation</h5>
                <button type="button" class="closed_btn" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="designation-form">
                    @csrf
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="input-block mb-3">
                                <label class="col-form-label" for="department_label">Department Name
                                    <span class="text-danger">*</span>
                                </label>
                                <select class="form-select department" name="department_id" id="department_id">
                                    <option disabled="" selected="">SELECT OPTION</option>
                                    @foreach ($departments as $department)
                                        <!-- Use the variable passed from the controller -->
                                        <option value="{{ $department->id }}">{{ $department->name }}</option>
                                    @endforeach
                                </select>
                                <div class="val_error_dp text-danger"></div>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="input-block mb-3">
                                <label class="col-form-label" for="designation_name">Designation Name <span
                                        class="text-danger">*</span></label>
                                <input class="form-control" type="text" name="name" id="designation_name">
                                <div class="val_error text-danger"></div>
                            </div>
                        </div>
                        <div class="submit-section">
                            <button type="submit" class="btn btn-primary submit-btn">Add</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Edit Department Modal -->
<div id="edit_designation" class="modal custom-modal fade" role="dialog">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Designation</h5>
                <button type="button" class="closed_btn" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="edit_designation-form">
                    @csrf
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="input-block mb-3">
                                <label class="col-form-label" for="department_label">Department Name
                                    <span class="text-danger">*</span>
                                </label>
                                <select class="form-select department" name="department" id="department_id">
                                    <option disabled selected>Select Department</option> <!-- Default option -->
                                    @foreach ($departments as $department)
                                        <option value="{{ $department->id }}">
                                            {{ $department->name }}
                                        </option>
                                    @endforeach
                                </select>

                                <div class="val_error_dp text-danger"></div>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="input-block mb-3">
                                <label class="col-form-label" for="edit_designation_name">Edit Designation Name <span
                                        class="text-danger">*</span></label>
                                <input class="form-control" type="text" name="name" id="edit_designation_name">
                                <div class="val_error text-danger"></div>
                            </div>
                        </div>

                        <div class="submit-section">
                            <button type="submit" class="btn btn-primary submit-btn">Update</button>
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
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    let table;

    function initializeDataTable() {
        const hasActionPermission = @json($hasActionPermission); // Pass Blade variable to JavaScript

        const columns = [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'name', title: 'Designation Name', orderable: false, searchable: false },
            { data: 'department_name', title: 'Department Name', orderable: false, searchable: false },
        ];

        // Add "Action" column if user has permission
        if (hasActionPermission) {
            columns.push({
                data: 'action',
                orderable: false,
                searchable: false,
                render: function (data, type, row) {
                    const editButton = `
                <button class="btn btn-primary" onclick="editDesignation(${row.id})"><i class="fa fa-edit"></i></button>`;
                    const deleteButton = `
                <button class="btn btn-danger" onclick="deleteDesignation(${row.id})"><i class="fa fa-trash"></i></button>`;

                    // Dynamically show buttons based on permission
                    let buttons = '';
                    if (hasActionPermission && data.edit) buttons += editButton;
                    if (hasActionPermission && data.delete) buttons += deleteButton;

                    return `<div class="text-center">${buttons}</div>`;
                }
            });
        }

        if (table) {
            table.destroy(); // Destroy previous DataTable instance
        }

        table = $('#designation_table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('designation.index') }}", // Route to fetch data
                type: 'GET'
            },
            columns: columns, // Dynamically defined columns
            order: [[0, 'desc']] // Default order by the first column
        });
    }

    $(document).ready(function () {
        initializeDataTable();

        // Clear department error message when department is selected
        $('#department_id').on('change', function () {
            if ($(this).val()) {
                $('.val_error_dp').text(''); // Remove the error message
            }
        });


        $('#designation-form').on('submit', function (e) {
            e.preventDefault();

            // Clear previous error messages
            $('.val_error').text('');
            $('.val_error_dp').text('');
            var isValid = true;

            // Get the values and ensure they're defined
            const name = $('#designation_name').val() ? $('#designation_name').val().trim() : '';
            const departmentId = $('#department_id').val() ? $('#department_id').val().trim() : '';

            // Validate the designation name and department ID
            if (name === '') {
                $('.val_error').text('Please provide a designation.');
                isValid = false;
                return;
            }
            if (departmentId === '') {
                $('.val_error_dp').text('Please provide a department.');
                isValid = false;
                return;
            }

            if (isValid) {
                $.ajax({
                    url: "{{ route('designation.store') }}",
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        name: name,
                        department_id: departmentId // Include department ID
                    },
                    success: function (response) {
                        hideLoader(); // Hide loader on success
                        if (response.error) {
                            // Show the error message if the designation already exists
                            $('.val_error').text(response.error);
                        } else {
                            $('#add_designation').modal('hide');
                            $('#designation_name').val(''); // Clear the input field
                            $('#department_id').val(''); // Clear the department ID field
                            table.ajax.reload();
                            createToast('info', 'fa-solid fa-circle-check', 'Success',
                                'Designation Added Successfully.');
                        }
                    },
                    error: function () {
                        hideLoader();
                        createToast('error', 'fa-solid fa-circle-exclamation', 'Error', 'Error Adding Designation.');
                    }
                });
            }
        });


        // Handle the edit designation form submission
        $('#edit_designation-form').on('submit', function (e) {
            e.preventDefault();
            var isValid = true;
            // Clear previous error messages
            $('.val_error').text('');

            const name = $('#edit_designation_name').val().trim();

            // Validate the company name
            if (name === '') {
                $('.val_error').text('Please provide a designation name.');
                isValid = false;
                return;
            }
            const id = $(this).data('id'); // Get ID for updating

            if (isValid) {
                showLoader(); // Show loader before AJAX request
                $.ajax({
                    url: '{{ route("designation.update", ":id") }}'.replace(':id', id),
                    type: 'PUT',
                    data: $(this).serialize(),
                    success: function (response) {
                        console.log(response);
                        hideLoader(); // Hide loader on success
                        $('#edit_designation').modal('hide');
                        $('#edit_designation_name').val(''); // Clear the input field
                        table.ajax.reload(); // Reload the DataTable
                        createToast('info', 'fa-solid fa-circle-check', 'Success',
                            'Designation Updated Successfully.');
                    },
                    error: function (xhr) {
                        hideLoader(); // Hide loader on success
                        createToast('error', 'fa-solid fa-circle-exclamation', 'Error', 'Error Updating Designation.');
                    }
                });
            }
        });
    });

    function editDesignation(id) {
        // Clear previous values
        $('#edit_designation_name').val('');
        $('#department_id').val(''); // Clear the dropdown

        $.ajax({
            url: '{{ route("designation.edit", ":id") }}'.replace(':id', id),
            type: 'GET',
            success: function (data) {
                console.log(data);
                // Check if data is valid
                if (data && data.name && data.department_id) {
                    $('#edit_designation_name').val(data.name);
                    $('#department_id').val(data.department_id); // Set the selected department ID

                    // Keep the default option, but set the selected one correctly
                    $('#department_id option').each(function () {
                        if ($(this).val() == data.department_id) {
                            $(this).prop('selected', true);
                        }
                    });

                    $('#edit_designation-form').data('id', id);
                    $('#edit_designation').modal('show');
                } else {
                    createToast('error', 'fa-solid fa-circle-exclamation', 'Error', 'Invalid data received.');
                }
            },
            error: function (xhr) {
                createToast('error', 'fa-solid fa-circle-exclamation', 'Error', 'Error fetching designation data: ' + xhr
                    .responseText);
            }
        });
    }


    let designationIdToDelete;

    function deleteDesignation(id) {
        designationIdToDelete = id;
        $('#deleteConfirmationModal').modal('show');
    }

    $('#confirmDelete').on('click', function () {

        showLoader(); // Show loader before AJAX request

        $.ajax({
            url: "{{ route('designation.destroy', '') }}/" + designationIdToDelete,
            type: 'DELETE',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function (response) {
                hideLoader(); // Hide loader on success
                $('#designation_table').DataTable().ajax.reload();
                $('#deleteConfirmationModal').modal('hide');
                createToast('info', 'fa-solid fa-circle-check', 'Success', 'Designation Deleted Successfully.');
            },
            error: function (xhr) {
                hideLoader(); // Hide loader on success
                $('#deleteConfirmationModal').modal('hide');
                createToast('error', 'fa-solid fa-circle-exclamation', 'Error', 'Error deleting Designation.');
            }
        });
    });
</script>
@endsection