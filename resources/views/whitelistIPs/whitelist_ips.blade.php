@extends('layout.mainlayout')
@section('content')


<div id="notification" aria-live="polite" aria-atomic="true"></div>

<div class="page-header">
    <div class="row align-items-center">
        <div class="col-md-4">
            <h3 class="page-title">WhiteList IPs</h3>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a
                        href="{{ auth()->user()->role == '1' ? url('admin-dashboard') : url('attendance-employee') }}">Dashboard</a>
                </li>
                <li class="breadcrumb-item active">WhiteList IP Addresses</li>
            </ul>
        </div>
        <div class="col-md-8 float-end ms-auto">
            <div class="d-flex title-head">
                <a href="#" class="btn_added" data-bs-toggle="modal" data-bs-target="#add_ip"><i
                        class="la la-plus-circle"></i> Add ISP</a>
            </div>
        </div>
    </div>
</div>

<!-- DATATABLE STARTS -->
<div class="row">
    <div class="col-md-12">
        <div class="table-responsive">
            <table class="table table-striped custom-table" id="ip_table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>ISP Name</th>
                        <th>IP Address</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- DYNAMIC DATA -->
                </tbody>
            </table>
        </div>
    </div>
</div>
<!-- DATATABLE ENDS -->

<!-- Add IP Modal STARTS-->
<div id="add_ip" class="modal custom-modal fade" role="dialog">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add IP Address</h5>
                <button type="button" class="closed_btn" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="ip_form">
                    @csrf
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="input-block mb-3">
                                <label class="col-form-label" for="ip_name">ISP Name <span
                                        class="text-danger">*</span></label>
                                <input class="form-control" type="text" name="ip_name" id="ip_name">
                                <div class="text-danger" id="name_error"></div>
                            </div>
                            <div class="input-block mb-3">
                                <label class="col-form-label" for="ip_address">IP Address <span
                                        class="text-danger">*</span></label>
                                <input class="form-control" type="text" name="ip_address" id="ip_address">
                                <div class="text-danger" id="address_error"></div>
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
<!-- Add IP Modal ENDS-->

<!-- Delete Confirmation Modal Start -->
<div class="modal fade" id="deleteConfirmationModal" tabindex="-1" aria-labelledby="deleteConfirmationModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header text-center">
                <h5 class="modal-title w-100" id="deleteConfirmationModalLabel">Delete IP Address</h5>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this <strong>IP Address</strong>? This action cannot be undone.</p>
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDelete">Delete</button>
            </div>
        </div>
    </div>
</div>
<!-- Delete Confirmation Modal End -->

<!-- Edit IP Modal STARTS-->
<div id="edit_ip" class="modal custom-modal fade" role="dialog">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit IP Address</h5>
                <button type="button" class="closed_btn" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="update_ip_form">
                    @csrf
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="input-block mb-3">
                                <label class="col-form-label" for="edit_ip_name">Edit ISP Name <span
                                        class="text-danger">*</span></label>
                                <input class="form-control" type="text" name="edit_ip_name" id="edit_ip_name">
                                <input class="form-control" type="hidden" name="ip_id" id="ip_id">
                                <div class="text-danger" id="edit_name_error"></div>
                            </div>
                            <div class="input-block mb-3">
                                <label class="col-form-label" for="edit_ip_address">Edit IP Address <span
                                        class="text-danger">*</span></label>
                                <input class="form-control" type="text" name="edit_ip_address" id="edit_ip_address">
                                <div class="text-danger" id="edit_address_error"></div>
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
<!-- Edit IP Modal STARTS-->


<!-- PreLoader -->
<div id="loader" class="loader" style="display: none;">
    <div class="loader-animation"></div>
</div>

@endsection

@section('script-z')
<script>
    $(document).ready(function () {

        // CREATE IP 
        $("#ip_form").on("submit", function (e) {
            e.preventDefault();
            var ip_name = $("#ip_name").val();
            var ip_address = $("#ip_address").val();
            var valid = true;

            // VALIDATIONS
            if (ip_name === "") {
                $("#name_error").html("ISP name is required!");
                $("#ip_name").css("border", "1px solid red");
                valid = false;
            } else {
                $("#ip_name").css("border", "");
                $("#name_error").html("");
                valid = true;
            }
            if (ip_address === "") {
                $("#address_error").html("IP address is required!");
                $("#ip_address").css("border", "1px solid red");
                valid = false;
            } else {
                $("#ip_address").css("border", "");
                $("#address_error").html("");
                valid = true;
            }
            if (valid) {
                showLoader();
                $.ajax({
                    url: "{{route('addIPs')}}",
                    type: "POST",
                    data: {
                        ip_name: ip_name,
                        ip_address: ip_address,
                        _token: "{{ csrf_token() }}"
                    },
                    success: function (response) {
                        hideLoader();
                        $("#ip_name").val("");
                        $("#ip_address").val("");
                        $("#add_ip").modal("hide");
                        $('#ip_table').DataTable().ajax.reload();
                        createToast('info', 'fa-solid fa-circle-check', 'Success',
                            'IP Address Added Successfully.');

                    },
                    error: function (xhr, status, error) {
                        hideLoader();

                        // Handle validation errors from the response
                        if (xhr.status === 422) { // Unprocessable Entity (Validation Error)
                            var errors = xhr.responseJSON.errors;

                            // Show the IP address error
                            if (errors.ip_address) {
                                $("#address_error").html(errors.ip_address[0]);
                                $("#ip_address").css("border", "1px solid red");
                            }

                            // Show the name error
                            if (errors.ip_name) {
                                $("#name_error").html(errors.ip_name[0]);
                                $("#ip_name").css("border", "1px solid red");
                            }
                        } else {
                            alert("An error occurred while adding the IP address.");
                        }
                    }
                });
            }

        });

        // LOADING IPS DATATABLE
        $("#ip_table").DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('getIPs') }}",
                type: "GET",
                dataType: "json",
                data: function (d) {
                    // You can modify the request data here if needed
                },
            },
            columns: [{
                data: 'index'
            },
            {
                data: 'name'
            },
            {
                data: 'ip'
            },
            {
                data: 'action',
                orderable: false,
                searchable: false
            }
            ],
            pageLength: 10, // Number of rows per page
            stateSave: true, // Retain table state (search, pagination)
            drawCallback: function (settings) {
                // Optional: perform any actions after the table is drawn, if needed
            },
        });

        // DELETE CONFIRMATION 
        $(document).on("click", ".delete-btn", function (e) {
            var ipId = $(this).data("id");

            $("#confirmDelete").data("id", ipId);
            $("#deleteConfirmationModal").modal("show");

        });

        // DELETE FUNCTION
        $("#confirmDelete").on("click", function () {

            var ipId = $(this).data("id");

            if (ipId) {
                // Proceed with the AJAX delete request
                $.ajax({
                    url: "{{ route('deleteIPs') }}", // Send request to the deleteIPs route
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}", // CSRF token for security
                        ip_id: ipId // Pass the IP ID to be deleted
                    },
                    success: function (response) {
                        $("#deleteConfirmationModal").modal("hide");
                        createToast('info', 'fa-solid fa-circle-check', 'Deleted',
                            'IP Address Deleted Successfully.');
                        $('#ip_table').DataTable().ajax.reload();
                    },
                    error: function (response) {
                        alert("Error deleting IP address.");
                    }
                });
            } else {
                console.log("IP ID is missing!");
            }

        })

        // EDIT IP MODAL POPULATION
        $(document).on("click", ".edit-btn", function (e) {

            var ipID = $(this).data("id");

            $.ajax({
                url: "{{route('editIPs')}}",
                type: "GET",
                data: {
                    id: ipID,
                    _token: "{{csrf_token()}}",
                },
                success: function (response) {
                    if (response.success) {
                        $("#edit_ip_name").val(response.data.name);
                        $("#edit_ip_address").val(response.data.ip_address);
                        $("#ip_id").val(response.data.id);
                        $("#edit_ip").modal("show");
                    }
                },
                error: function () {
                    alert("Error!");
                }
            });
        });

        // UPDATE IP 
        $("#update_ip_form").on("submit", function (e) {
            e.preventDefault();

            var updateName = $("#edit_ip_name").val();
            var updateAddress = $("#edit_ip_address").val();
            var ipID = $("#ip_id").val();
            var valid = true;

            if (updateName === "") {
                $("#edit_name_error").html("ISP name is required!");
                $("#edit_ip_name").css("border", "1px solid red");
                valid = false;
            } else {
                $("#edit_ip_name").css("border", "");
                $("#edit_name_error").html("");
                valid = true;
            }
            if (updateAddress === "") {
                $("#edit_address_error").html("IP address is required!");
                $("#edit_ip_address").css("border", "1px solid red");
                valid = false;
            } else {
                $("#edit_ip_address").css("border", "");
                $("#edit_address_error").html("");
                valid = true;
            }
            if (valid) {
                $.ajax({
                    url: "{{route('updateIPs')}}",
                    type: "POST",
                    data: {
                        _token: "{{csrf_token()}}",
                        id: ipID,
                        name: updateName,
                        ip_address: updateAddress,
                    },
                    success: function (response) {
                        hideLoader();
                        if (response.success) {
                            $("#edit_ip").modal("hide");
                            $("#ip_table").DataTable().ajax.reload();
                            createToast('info', 'fa-solid fa-circle-check', 'Success',
                                'IP Address Updated Successfully.');
                        } else {
                            alert(response.message);
                        }
                    },
                    error: function () {
                        createToast('error', 'fa-solid fa-circle-check', 'Success',
                            'Error Updating The IP Address.');
                    }
                });
            }
        });



    });
</script>
@endsection