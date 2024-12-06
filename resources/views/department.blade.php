@extends('layout.mainlayout')
@section('content')

@php
    $user = auth()->user();
    $permissions = getUserPermissions($user); // Use the helper function to fetch permissions
    // Check if the user has at least one of the required permissions or is a superadmin
    $hasActionPermission = $user->role == 1 ||
        in_array('update_department', $permissions) ||
        in_array('delete_department', $permissions);
@endphp

<div id="notification" aria-live="polite" aria-atomic="true"></div>

<div class="page-header">
    <div class="row align-items-center">
        <div class="col-md-4">
            <h3 class="page-title">Department</h3>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                <li class="breadcrumb-item active">Department</li>
            </ul>
        </div>
        <div class="col-md-8 float-end ms-auto">
            <div class="d-flex title-head">
                @if($user->role == 1 || in_array('create_department', $permissions))
                    <a href="#" class="btn_added" data-bs-toggle="modal" data-bs-target="#add_department"><i
                            class="la la-plus-circle"></i> Add Department</a>
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
                <p>Are you sure you want to delete this department? This action cannot be undone.</p>
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
            <table class="table table-striped custom-table" id="department_table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Department Name</th>
                        @if($user->role == 1 || in_array('update_department', $permissions) || in_array('delete_department', $permissions))
                            <th class="text-center">Action</th>
                        @endif
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add Company Modal -->
<div id="add_department" class="modal custom-modal fade" role="dialog">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Department</h5>
                <button type="button" class="closed_btn" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="department-form">
                    @csrf
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="input-block mb-3">
                                <label class="col-form-label" for="department_name">Department Name <span
                                        class="text-danger">*</span></label>
                                <input class="form-control" type="text" name="name" id="department_name">
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
<div id="edit_department" class="modal custom-modal fade" role="dialog">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Department</h5>
                <button type="button" class="closed_btn" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="edit_department-form">
                    @csrf
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="input-block mb-3">
                                <label class="col-form-label" for="edit_department_name">Edit Department Name <span
                                        class="text-danger">*</span></label>
                                <input class="form-control" type="text" name="name" id="edit_department_name">
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
            { data: 'name', title: 'Department Name', orderable: false, searchable: false },
        ];

        // Add "Action" column if user has permission
        if (hasActionPermission) {
            columns.push({
                data: 'action',
                orderable: false,
                searchable: false,
                render: function (data, type, row) {
                    const editButton = `
                <button class="btn btn-primary" onclick="editDepartment(${row.id})"><i class="fa fa-edit"></i></button>`;
                    const deleteButton = `
                <button class="btn btn-danger" onclick="deleteDepartment(${row.id})"><i class="fa fa-trash"></i></button>`;

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

        table = $('#department_table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('department.index') }}", // Route to fetch data
                type: 'GET'
            },
            columns: columns, // Dynamically defined columns
            order: [[0, 'desc']] // Default order by the first column
        });
    }

    $(document).ready(function () {
        initializeDataTable();

        $('#department-form').on('submit', function (e) {
            e.preventDefault();
            var isValid = true;

            // Clear previous error messages
            $('.val_error').text('');

            const name = $('#department_name').val().trim();

            // Validate the department name
            if (name === '') {
                $('.val_error').text('Please provide a department name.');
                isValid = false;
                return;
            }

            if (isValid) {
                showLoader();
                $.ajax({
                    url: "{{ route('department.store') }}",
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        name: name
                    },
                    success: function (response) {
                        hideLoader(); // Hide loader on success
                        if (response.error) {
                            // Show the error message if the department already exists
                            $('.val_error').text(response.error);
                        } else {
                            $('#add_department').modal('hide');
                            $('#department_name').val(''); // Clear the input field
                            table.ajax.reload();
                            createToast('info', 'fa-solid fa-circle-check', 'Success', 'Department Added Successfully.');
                        }
                    },
                    error: function () {
                        hideLoader();
                        createToast('error', 'fa-solid fa-circle-exclamation', 'Error', 'Error Adding Department.');
                    }
                });
            }
        });

        // Handle the edit department form submission
        $('#edit_department-form').on('submit', function (e) {
            e.preventDefault();
            var isValid = true;
            // Clear previous error messages
            $('.val_error').text('');

            const name = $('#edit_department_name').val().trim();

            // Validate the company name
            if (name === '') {
                $('.val_error').text('Please provide a department name.');
                isValid = false;
                return;
            }
            const id = $(this).data('id'); // Get ID for updating

            if (isValid) {
                showLoader(); // Show loader before AJAX request
                $.ajax({
                    url: '{{ route("department.update", ":id") }}'.replace(':id', id),
                    type: 'PUT',
                    data: $(this).serialize(),
                    success: function (response) {
                        hideLoader(); // Hide loader on success
                        $('#edit_department').modal('hide');
                        $('#edit_department_name').val(''); // Clear the input field
                        table.ajax.reload(); // Reload the DataTable
                        createToast('info', 'fa-solid fa-circle-check', 'Success', 'Department Updated Successfully.');
                    },
                    error: function (xhr) {
                        hideLoader(); // Hide loader on success
                        createToast('error', 'fa-solid fa-circle-exclamation', 'Error', 'Error Updating Department.');
                    }
                });
            }
        });
    });

    function editDepartment(id) {
        $.ajax({
            url: '{{ route("department.edit", ":id") }}'.replace(':id', id),
            type: 'GET',
            success: function (data) {
                $('#edit_department_name').val(data.name);
                $('#edit_department-form').data('id', id); // Store the ID for updating
                $('#edit_department').modal('show'); // Show the modal
            },
            error: function (xhr) {
                alert('Error fetching department data: ' + xhr.responseText);
            }
        });
    }

    let departmentIdToDelete;

    function deleteDepartment(id) {
        departmentIdToDelete = id;
        $('#deleteConfirmationModal').modal('show');
    }

    $('#confirmDelete').on('click', function () {

        showLoader(); // Show loader before AJAX request

        $.ajax({
            url: "{{ route('department.destroy', '') }}/" + departmentIdToDelete,
            type: 'DELETE',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function (response) {
                hideLoader(); // Hide loader on success
                $('#department_table').DataTable().ajax.reload();
                $('#deleteConfirmationModal').modal('hide');
                createToast('info', 'fa-solid fa-circle-check', 'Success', 'Department Deleted Successfully.');
            },
            error: function (xhr) {
                hideLoader(); // Hide loader on success
                $('#deleteConfirmationModal').modal('hide');
                createToast('error', 'fa-solid fa-circle-exclamation', 'Error', 'Error deleting Department.');
            }
        });
    });
</script>
@endsection