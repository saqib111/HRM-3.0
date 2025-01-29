@extends('layout.mainlayout')
@section('content')

<!-- ---Header ---- -->
<div class="page-header">
    <div class="row align-items-center">
        <div class="col-md-4">
            <h3 class="page-title">Manage IP Restrictions</h3>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a
                        href="{{ auth()->user()->role == '1' ? url('admin-dashboard') : url('attendance-employee') }}">Dashboard</a>
                </li>
                <li class="breadcrumb-item active">Manage IP Restrictions</li>
            </ul>
        </div>
        <div class="col-md-1 float-end ms-auto">
            <div class="d-flex title-head">
                <select id="status_search" class="form-select form-select-sm  text-center fw-semibold">
                    <option value="" class="text-info">All Users</option>
                    <option value="0" class="text-danger">Restricted</option>
                    <option value="1" class="text-success">Allowed</option>
                </select>
            </div>
        </div>
    </div>
</div>
<!----- Header END ----->

<!-- USERS TABLE STARTS -->
<div class="row">
    <div class="col-md-12">
        <div class="table-responsive">
            <table class="table table-striped custom-table" id="users_data_table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Employee ID</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Action</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
</div>
<!-- USERS TABLE ENDS -->

<!-- Confirmation Modal STARTS -->
<div class="modal fade" id="confirmationModal" tabindex="-1" aria-labelledby="confirmationModalLabel" aria-hidden="true"
    data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header text-center align-items-center justify-content-center">
                <h3 class="modal-title d-flex align-items-center" id="confirmationModalLabel">
                    <i id="statusIcon" class="fa-solid fa-lock fa-lg me-2" style="color: #ff0000;"></i>
                    <span id="statusTitle">Confirm Action</span>
                </h3>
            </div>
            <div class="modal-body text-center">
                <p id="modalMessage" class="fs-6"></p>
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-success" id="confirmButton">Confirm</button>
                <button type="button" class="btn btn-secondary" id="cancelButton"
                    data-bs-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>
<!-- Confirmation Modal ENDS -->

<!-- PreLoader -->
<div id="loader" class="loader" style="display: none;">
    <div class="loader-animation"></div>
</div>
@endsection

@section('script-z')
<script>
    $(document).ready(function () {

        var table = $("#users_data_table").DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{route('getData.manageIPs')}}",
            columns: [
                {
                    data: null,
                    searchable: false,
                    orderable: false,
                    render: function (data, type, row, meta) {
                        return meta.row + 1;
                    }
                },
                {
                    data: "employee_id"
                },
                {
                    data: "username"
                },
                {
                    data: "email",
                    searchable: false,
                },
                {
                    data: "action",
                    orderable: false,
                    searchable: false,
                    render: function (data, type, row) {
                        let checked = (row.status === 1) ? 'checked' : '';
                        return `
                    <div class="status-toggle-container" style="display:flex;">
                        <div class="status-toggle">
                            <input type="checkbox" id="staff_module_${row.id}" class="check" ${checked} onchange="toggleStatus(${row.id}, this)">
                            <label for="staff_module_${row.id}" class="checktoggle">checkbox</label>
                        </div>
                    </div>`;
                    }
                },
                {
                    data: "status",
                    render: function (data, type, row) {
                        return row.status === 0 ? "Restricted" : "Allowed";
                    },

                },
            ],
            order: [1, 'asc'],
            pageLength: 20, // Set the default number of records to show
            lengthMenu: [10, 20, 25, 50, 100] // Options for records per page
        });

        // FILTER STATUS
        $("#status_search").on('change', function () {

            let selectedOption = $(this).find('option:selected');
            let selectedClass = selectedOption.attr('class'); // Get the class of the selected option

            // Apply the color class to the select dropdown
            $(this).removeClass('text-info text-danger text-success').addClass(selectedClass);

            table.columns(5).search(this.value).draw();
        });

    });

    function toggleStatus(id, checkbox) {

        const status = checkbox.checked ? 1 : 0; // Assuming 1 is active and 0 is inactive

        const modalMessage = status === 1 ?
            "Are you sure you want to <strong class='text-success'>Allow</strong> this user? They will have access from all the <strong>Public IPs</strong>." :
            "Are you sure you want to <strong class='text-danger'>Restrict</strong> this user? They will only have access from <strong>Office IPs</strong>.";

        const statusTitle = status === 1 ? "Allow Access" : "Restrict Access";
        const statusIcon = status === 1 ? "fa-check-circle" : "fa-ban";
        const iconColor = status === 1 ? "#28a745" : "#dc3545"; // Green for allow, Red for restrict

        // Set dynamic content
        document.getElementById("modalMessage").innerHTML = modalMessage;
        document.getElementById("statusTitle").textContent = statusTitle;
        document.getElementById("statusIcon").classList.replace("fa-lock", statusIcon);
        document.getElementById("statusIcon").style.color = iconColor;

        const confirmButton = document.getElementById("confirmButton");

        if (status === 1) {
            confirmButton.classList.replace("btn-danger", "btn-success"); // Make button green when allowing
        } else {
            confirmButton.classList.replace("btn-success", "btn-danger"); // Make button red when restricting
        }

        // Set the modal message dynamically based on the current status
        $('#modalMessage').html(modalMessage);

        // Show the confirmation modal
        $('#confirmationModal').modal('show');

        // Store the checkbox element reference in a variable
        const currentCheckbox = checkbox;

        const currentPage = $('#users_data_table').DataTable().page.info().page;

        // Remove any existing event handlers before attaching new ones
        $('#confirmButton').off('click');

        // When the user confirms, send the AJAX request
        $('#confirmButton').on('click', function () {
            $.ajax({
                url: "{{ route('updateStatus.manageIPs') }}", // Update this URL to your status update endpoint
                type: 'POST',
                data: {
                    id: id,
                    status: status,
                    _token: '{{ csrf_token() }}' // Include CSRF token for security
                },
                success: function (response) {
                    createToast('info', 'fa-solid fa-circle-check', 'Success',
                        'User Status Changed Successfully.');
                    // $('#users_data_table').DataTable().ajax.reload();
                    const table = $('#users_data_table').DataTable();
                    table.ajax.reload(null, false); // Reload without resetting the page

                    // Revert to the saved page index
                    table.page(currentPage).draw(false);
                },
                error: function (xhr, status, error) {
                    // Handle AJAX error
                    currentCheckbox.checked = !currentCheckbox.checked; // Toggle back the checkbox state
                    alert('AJAX Error: ' + error);
                }
            });
            $('#confirmationModal').modal('hide');
        });

        // Ensure the cancel button is scoped to the current row
        $('#confirmationModal').off("click", "#cancelButton").on("click", "#cancelButton", function () {
            currentCheckbox.checked = !currentCheckbox.checked; // Revert the checkbox state only for the current row
            $('#confirmationModal').modal('hide');
        });
    }

</script>

@endsection