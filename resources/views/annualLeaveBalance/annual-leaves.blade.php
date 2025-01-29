@extends('layout.mainlayout')
@section('content')

@php
    $user = auth()->user();
    $permissions = getUserPermissions($user); // Use the helper function to fetch permissions
    // Check if the user has at least one of the required permissions or is a superadmin
    $hasActionPermission = $user->role == 1 ||
        in_array('update_al_balance', $permissions);
@endphp

<div id="notification" aria-live="polite" aria-atomic="true"></div>
<!-- --Page Header --- -->
<div class="page-header">
    <div class="row align-items-center">
        <div class="col-md-4">
            <h3 class="page-title">Leave Balance</h3>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a
                        href="{{ auth()->user()->role == '1' ? url('admin-dashboard') : url('attendance-employee') }}">Dashboard</a>
                </li>
                <li class="breadcrumb-item active">Leave Balance</li>
            </ul>
        </div>
    </div>
</div>
<!-- --Page Header END --- -->
<div class="row">
    <div class="col-md-12">
        <div class="table-responsive">
            <table class="table table-striped custom-table" id="leave_balance_table">
                <thead>
                    <tr>
                        <th class="text-center">#</th>
                        <th>Name</th>
                        <th class="text-center">Leave Type</th>
                        <th class="text-center">Leave Balance</th>
                        <th class="text-center">Last Year Balance</th>
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


<!-- ---EDIT LEAVE BALANCE MODEL ------>
<div id="editLeaveBalance" class="modal custom-modal fade" role="dialog">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Leave Balance</h5>
                <button type="button" class="closed_btn" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="edit_leave_balance_form">
                    @csrf
                    <div class="row">

                        <div class="col-sm-12">
                            <div class="input-block mb-3">
                                <label class="col-form-label" for="leave_balance">Edit Leave Balance <span
                                        class="text-danger">*</span></label>
                                <input class="form-control" type="text" name="leave_balance" id="leave_balance">
                                <div class="leave_error text-danger"></div>
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
<!-- ---EDIT LEAVE BALANCE MODEL END ------>
<!-- PreLoader -->
<div id="loader" class="loader" style="display: none;">
    <div class="loader-animation"></div>
</div>

@endsection

@section('script-z')

<script>
    function initializeLeaveBalanceTable() {
        const table = $('#leave_balance_table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('annual-leaves.index') }}",
                type: 'GET'
            },
            columns: [
                {
                    data: null,
                    render: function (data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    },
                    orderable: false,
                    searchable: false,
                    className: 'text-center'
                },
                { data: 'username', name: 'username', orderable: false, searchable: true },
                { data: 'leave_type', name: 'leave_type', className: 'text-center', orderable: false, searchable: false },
                { data: 'leave_balance', name: 'leave_balance', className: 'text-center', orderable: false, searchable: false },
                { data: 'last_year_balance', name: 'last_year_balance', className: 'text-center', orderable: false, searchable: false },
                {
                    data: 'can_update',
                    orderable: false,
                    searchable: false,
                    render: function (data, type, row) {
                        if (data) {
                            return `
                        <button class="btn btn-primary" onclick="editLeaveBalance(${row.id})">
                            <i class="fa fa-edit"></i>
                        </button>
                    `;
                        }
                        return ''; // Return empty string if no permission
                    },
                    className: 'text-center'
                }
            ],
            columnDefs: [
                {
                    targets: -1, // Target the last column (Action column)
                    visible: function (data, type, row) {
                        return row.can_update; // Show column only if `can_update` is true
                    }
                }
            ],
            pageLength: 15, // Set the default number of records to show
            lengthMenu: [10, 15, 25, 50, 100] // Options for records per page
        });
    }
    $(document).ready(function () {

        initializeLeaveBalanceTable();
        // Handle form submission
        $('#edit_leave_balance_form').on('submit', function (e) {
            e.preventDefault(); // Prevent the form from submitting the traditional way

            var isValid = true;
            // Clear previous error messages
            $('.leave_error').text('');

            const id = $('#leave_balance').data('id'); // Get the ID for the update
            const leaveBalance = parseInt($('#leave_balance').val(), 10); // Parse the leave balance

            // Validate the leave balance
            if (isNaN(leaveBalance)) {
                $('.leave_error').text('Please Enter Valid Leave Balance.');
                isValid = false;
            }

            const formData = $(this).serialize();

            if (isValid) {
                showLoader();
                $.ajax({
                    url: '{{ route("annual-leaves.update", ":id") }}'.replace(':id', id),
                    type: 'PUT',
                    data: formData,
                    success: function (response) {
                        hideLoader();
                        $('#editLeaveBalance').modal('hide');
                        $('#leave_balance_table').DataTable().ajax.reload(null, false); // Reload DataTable
                        createToast('info', 'fa-solid fa-circle-check', 'Success',
                            'Leave balance updated successfully!');
                    },
                    error: function (xhr) {
                        hideLoader();
                        createToast('error', 'fa-solid fa-circle-exclamation', 'Error',
                            'An error occurred while updating leave balance.');
                    }
                });
            }
        });
    });

    function editLeaveBalance(id) {
        $.ajax({
            url: '{{ route("annual-leaves.edit", ":id") }}'.replace(':id', id),
            type: 'GET',
            success: function (data) {
                // Assuming `data` contains the leave balance details
                $('#leave_balance').val(data.leave_balance).data('id', data.id); // Set the ID as a data attribute

                // Show the modal
                $('#editLeaveBalance').modal('show');
            },
            error: function (xhr) {
                // Handle error
                console.error('Error fetching leave balance:', xhr);
                alert('An error occurred while fetching leave balance details.');
            }
        });
    }
</script>

@endsection