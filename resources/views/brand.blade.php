@extends('layout.mainlayout')
@section('content')

@php
    $user = auth()->user();
    $permissions = getUserPermissions($user); // Use the helper function to fetch permissions
    // Check if the user has at least one of the required permissions or is a superadmin
    $hasActionPermission = $user->role == 1 ||
        in_array('update_brand', $permissions) ||
        in_array('delete_brand', $permissions);
@endphp

<div id="notification" aria-live="polite" aria-atomic="true"></div>

<div class="page-header">
    <div class="row align-items-center">
        <div class="col-md-4">
            <h3 class="page-title">Brand</h3>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a
                        href="{{ auth()->user()->role == '1' ? url('admin-dashboard') : url('attendance-employee') }}">Dashboard</a>
                </li>
                <li class="breadcrumb-item active">Brand</li>
            </ul>
        </div>
        <div class="col-md-8 float-end ms-auto">
            <div class="d-flex title-head">
                @if($user->role == 1 || in_array('create_brand', $permissions))
                    <a href="#" class="btn_added" data-bs-toggle="modal" data-bs-target="#add_brand"><i
                            class="la la-plus-circle"></i> Add Brand</a>
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
                <p>Are you sure you want to delete this brand? This action cannot be undone.</p>
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
            <table class="table table-striped custom-table" id="brand_table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Brand Name</th>
                        @if($hasActionPermission)
                            <th class="text-center">Action</th>
                        @endif
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add Brand Modal -->
<div id="add_brand" class="modal custom-modal fade" role="dialog">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Brand</h5>
                <button type="button" class="closed_btn" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="brand-form">
                    @csrf
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="input-block mb-3">
                                <label class="col-form-label" for="brand_name">Brand Name <span
                                        class="text-danger">*</span></label>
                                <input class="form-control" type="text" name="name" id="brand_name">
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

<!-- Edit Brand Modal -->
<div id="edit_brand" class="modal custom-modal fade" role="dialog">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Brand</h5>
                <button type="button" class="closed_btn" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="edit_brand-form">
                    @csrf
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="input-block mb-3">
                                <label class="col-form-label" for="edit_brand_name">Edit Brand Name <span
                                        class="text-danger">*</span></label>
                                <input class="form-control" type="text" name="name" id="edit_brand_name">
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
    // Set up CSRF token for AJAX requests
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
            { data: 'name', title: 'Brand Name', orderable: false, searchable: false },
        ];

        // Add "Action" column if user has permission
        if (hasActionPermission) {
            columns.push({
                data: 'action',
                orderable: false,
                searchable: false,
                render: function (data, type, row) {
                    const editButton = `
                    <button class="btn btn-primary" onclick="editBrand(${row.id})"><i class="fa fa-edit"></i></button>`;
                    const deleteButton = `
                    <button class="btn btn-danger" onclick="deleteBrand(${row.id})"><i class="fa fa-trash"></i></button>`;

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

        table = $('#brand_table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('brand.index') }}", // Route to fetch data
                type: 'GET'
            },
            columns: columns, // Dynamically defined columns
            order: [[0, 'desc']] // Default order by the first column
        });
    }

    $(document).ready(function () {
        initializeDataTable();

        $('#brand-form').on('submit', function (e) {
            e.preventDefault();
            var isValid = true;

            // Clear previous error messages
            $('.val_error').text('');

            const name = $('#brand_name').val().trim();

            // Validate the brand name
            if (name === '') {
                $('.val_error').text('Please provide a brand name.');
                var isValid = false;
                return;
            }

            if (isValid) {
                $.ajax({
                    url: "{{ route('brand.store') }}",
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        name: name
                    },
                    success: function (response) {
                        hideLoader(); // Hide loader on success
                        if (response.error) {
                            // Show the error message if the brand already exists
                            $('.val_error').text(response.error);
                        } else {
                            $('#add_brand').modal('hide');
                            $('#brand_name').val(''); // Clear the input field
                            table.ajax.reload();
                            createToast('info', 'fa-solid fa-circle-check', 'Success', 'Brand Added Successfully.');
                        }
                    },
                    error: function () {
                        hideLoader();
                        createToast('error', 'fa-solid fa-circle-exclamation', 'Error', 'Error Adding Brand.');
                    }
                });
            }
        });

        // Handle the edit brand form submission
        $('#edit_brand-form').on('submit', function (e) {
            e.preventDefault();
            var isValid = true;
            // Clear previous error messages
            $('.val_error').text('');

            const name = $('#edit_brand_name').val().trim();

            // Validate the company name
            if (name === '') {
                $('.val_error').text('Please provide a brand name.');
                isValid = false;
                return;
            }
            const id = $(this).data('id'); // Get ID for updating

            if (isValid) {
                showLoader(); // Show loader before AJAX request
                $.ajax({
                    url: '{{ route("brand.update", ":id") }}'.replace(':id', id),
                    type: 'PUT',
                    data: $(this).serialize(),
                    success: function (response) {
                        hideLoader(); // Hide loader on success
                        $('#edit_brand').modal('hide');
                        $('#edit_brand_name').val(''); // Clear the input field
                        table.ajax.reload(); // Reload the DataTable
                        createToast('info', 'fa-solid fa-circle-check', 'Success', 'Brand Updated Successfully.');
                    },
                    error: function (xhr) {
                        hideLoader(); // Hide loader on success
                        createToast('error', 'fa-solid fa-circle-exclamation', 'Error', 'Error Updating Brand.');
                    }
                });
            }
        });
    });

    function editBrand(id) {
        $.ajax({
            url: '{{ route("brand.edit", ":id") }}'.replace(':id', id),
            type: 'GET',
            success: function (data) {
                $('#edit_brand_name').val(data.name);
                $('#edit_brand-form').data('id', id); // Store the ID for updating
                $('#edit_brand').modal('show'); // Show the modal
            },
            error: function (xhr) {
                alert('Error fetching brand data: ' + xhr.responseText);
            }
        });
    }

    let brandIdToDelete;

    function deleteBrand(id) {
        brandIdToDelete = id;
        $('#deleteConfirmationModal').modal('show');
    }

    $('#confirmDelete').on('click', function () {

        showLoader(); // Show loader before AJAX request
        $.ajax({
            url: "{{ route('brand.destroy', '') }}/" + brandIdToDelete,
            type: 'DELETE',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function (response) {
                hideLoader(); // Hide loader on success
                $('#brand_table').DataTable().ajax.reload();
                $('#deleteConfirmationModal').modal('hide');
                createToast('info', 'fa-solid fa-circle-check', 'Success', 'Brand Deleted Successfully.');
            },
            error: function (xhr) {
                hideLoader(); // Hide loader on success
                $('#deleteConfirmationModal').modal('hide');
                createToast('error', 'fa-solid fa-circle-exclamation', 'Error', 'Error deleting Brand.');
            }
        });
    });
</script>
@endsection