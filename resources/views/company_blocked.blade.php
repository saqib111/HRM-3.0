@extends('layout.mainlayout')
@section('content')
<div id="notification" aria-live="polite" aria-atomic="true"></div>

<div class="page-header">
    <div class="row align-items-center">
        <div class="col-md-4">
            <h3 class="page-title">Company</h3>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a
                        href="{{ auth()->user()->role == '1' ? url('admin-dashboard') : url('attendance-employee') }}">Dashboard</a>
                </li>
                <li class="breadcrumb-item active">Company</li>
            </ul>
        </div>
        <div class="col-md-8 float-end ms-auto">
            <div class="d-flex title-head">
                <a href="#" class="btn_added" data-bs-toggle="modal" data-bs-target="#add_company"><i
                        class="la la-plus-circle"></i> Add Company</a>
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
                <p>Are you sure you want to delete this company? This action cannot be undone.</p>
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
            <table class="table table-striped custom-table" id="company_table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Company Name</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add Company Modal -->
<div id="add_company" class="modal custom-modal fade" role="dialog">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Company</h5>
                <button type="button" class="closed_btn" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="company-form">
                    @csrf
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="input-block mb-3">
                                <label class="col-form-label" for="company_name">Company Name <span
                                        class="text-danger">*</span></label>
                                <input class="form-control" type="text" name="name" id="company_name">
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

<!-- Edit Company Modal -->
<div id="edit_company" class="modal custom-modal fade" role="dialog">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Company</h5>
                <button type="button" class="closed_btn" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="edit_company-form">
                    @csrf
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="input-block mb-3">
                                <label class="col-form-label" for="edit_company_name">Edit Company Name <span
                                        class="text-danger">*</span></label>
                                <input class="form-control" type="text" name="name" id="edit_company_name">
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
    $(document).ready(function () {
        const table = $('#company_table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('company.index') }}",
                type: 'GET'
            },
            columns: [{
                data: null,
                render: function (data, type, row, meta) {
                    return meta.row + meta.settings._iDisplayStart + 1;
                },
                orderable: false,
                searchable: false
            },
            {
                data: 'name',
                name: 'name'
            },
            {
                data: null,
                render: function (data, type, row) {
                    return `
            <button class="btn btn-primary" onclick="editCompany(${row.id})"><i class="fa fa-edit"></i></button>
            <button class="btn btn-danger" onclick="deleteCompany(${row.id})"><i class="fa fa-trash"></i></button>
          `;
                },
                orderable: false,
                searchable: false
            }
            ],
        });


        $('#company-form').on('submit', function (e) {
            e.preventDefault();
            var isValid = true;

            // Clear previous error messages
            $('.val_error').text('');

            const name = $('#company_name').val().trim();

            // Validate the company name
            if (name === '') {
                $('.val_error').text('Please provide a company name.');
                isValid = false;
                return;
            }

            if (isValid) {
                showLoader();
                $.ajax({
                    url: "{{ route('company.store') }}",
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        name: name
                    },
                    success: function (response) {
                        hideLoader(); // Hide loader on success
                        if (response.error) {
                            // Show the error message if the company already exists
                            $('.val_error').text(response.error);
                        } else {
                            $('#add_company').modal('hide');
                            $('#company_name').val(''); // Clear the input field
                            table.ajax.reload();
                            createToast('info', 'fa-solid fa-circle-check', 'Success', 'Company Added Successfully.');
                        }
                    },
                    error: function () {
                        hideLoader();
                        createToast('error', 'fa-solid fa-circle-exclamation', 'Error', 'Error Adding Company.');
                    }
                });
            }
        });

        // Handle the edit company form submission
        $('#edit_company-form').on('submit', function (e) {
            e.preventDefault();
            var isValid = true;
            // Clear previous error messages
            $('.val_error').text('');

            const name = $('#edit_company_name').val().trim();

            // Validate the company name
            if (name === '') {
                $('.val_error').text('Please provide a company name.');
                isValid = false;
                return;
            }
            const id = $(this).data('id'); // Get ID for updating

            if (isValid) {
                showLoader(); // Show loader before AJAX request
                $.ajax({
                    url: '{{ route("company.update", ":id") }}'.replace(':id', id),
                    type: 'PUT',
                    data: $(this).serialize(),
                    success: function (response) {
                        hideLoader(); // Hide loader on success
                        $('#edit_company').modal('hide');
                        $('#edit_company_name').val(''); // Clear the input field
                        table.ajax.reload(); // Reload the DataTable
                        createToast('info', 'fa-solid fa-circle-check', 'Success', 'Company Updated Successfully.');
                    },
                    error: function (xhr) {
                        hideLoader(); // Hide loader on success
                        createToast('error', 'fa-solid fa-circle-exclamation', 'Error', 'Error Updating Company.');
                    }
                });
            }
        });
    });

    function editCompany(id) {
        $.ajax({
            url: '{{ route("company.edit", ":id") }}'.replace(':id', id),
            type: 'GET',
            success: function (data) {
                $('#edit_company_name').val(data.name);
                $('#edit_company-form').data('id', id); // Store the ID for updating
                $('#edit_company').modal('show'); // Show the modal
            },
            error: function (xhr) {
                alert('Error fetching company data: ' + xhr.responseText);
            }
        });
    }

    let companyIdToDelete;

    function deleteCompany(id) {
        companyIdToDelete = id;
        $('#deleteConfirmationModal').modal('show');
    }

    $('#confirmDelete').on('click', function () {

        showLoader(); // Show loader before AJAX request
        $.ajax({
            url: "{{ route('company.destroy', '') }}/" + companyIdToDelete,
            type: 'DELETE',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function (response) {
                hideLoader(); // Hide loader on success
                $('#company_table').DataTable().ajax.reload();
                $('#deleteConfirmationModal').modal('hide');
                createToast('info', 'fa-solid fa-circle-check', 'Success', 'Company Deleted Successfully.');
            },
            error: function (xhr) {
                hideLoader(); // Hide loader on success
                $('#deleteConfirmationModal').modal('hide');
                createToast('error', 'fa-solid fa-circle-exclamation', 'Error', 'Error deleting Company.');
            }
        });
    });
</script>
@endsection