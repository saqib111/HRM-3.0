@extends('layout.mainlayout')
@section('content')
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
     .select2-selection--multiple.is-invalid
   {
      border-color: red!important;
    }
</style>
<div class="col-auto ms-auto mb-3">
    <ul class="split-head">
        <li>
            <a href="#" class="btn add-btn text-white" data-bs-toggle="modal" data-bs-target="#add_employee">
                <i class="fa fa-plus"></i> Add Employee</a>
        </li>
    </ul>
</div>

<div id="notification" aria-live="polite" aria-atomic="true"></div>
<div class="row">
    <div class="col-md-12">
        <div class="table-responsive">
            <table class="table table-striped custom-table" id="users_table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Employee ID</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Join Date</th>
                        <th>Company</th>
                        <th>Department</th>
                        <th>Designation</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="employee-list">
                </tbody>
            </table>
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
                <p>Are you sure you want to delete this employee? This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDelete">Delete</button>
            </div>
        </div>
    </div>
</div>
<!-- Delete Confirmation Modal End -->


<div id="add_employee" class="modal custom-modal fade" role="dialog">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Employee</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="employee-form" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="row">

                        <div class="col-sm-6">
                            <div class="input-block mb-3">
                                <label class="col-form-label" for="employee_id">Employee ID <span
                                        class="text-danger">*</span></label>
                                <input class="form-control eid " type="text" name="employee_id" id="employee_id">
                                <div id="eid"></div>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="input-block mb-3">
                                <label class="col-form-label" for="username">Username <span
                                        class="text-danger">*</span></label>
                                <input class="form-control username" type="text" name="username" id="username">
                                <div id="username"></div>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="input-block mb-3 ">
                                <label class="col-form-label" for="email">Email <span
                                        class="text-danger">*</span></label>
                                <input class="form-control email " type="email" name="email" id="email">
                                <div id="email"></div>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="input-block mb-3">
                                <label class="col-form-label" for="joining_date_label">Joining Date <span
                                        class="text-danger">*</span></label>
                                <input class="form-control datetimepicker d1" type="text" name="joining_date"
                                    id="joining_date_label">
                                <div id="joining_date"></div>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="input-block mb-3">
                                <label class="col-form-label" for="password">Password</label>
                                <input class="form-control password" type="password" name="password" id="password">
                                <div id="pass"></div>
                            </div>
                        </div>

                    </div>

                    <div class="submit-section">
                        <button class="btn btn-primary submit-btn" id="">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- Edit Modal -->
<div id="edit_employee" class="modal custom-modal fade" role="dialog">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Employee</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="edit-employee-form" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="id" id="id" >
                    <div class="row">

                        <div class="col-sm-6">
                            <div class="input-block mb-3 valid">
                                <label class="col-form-label">Employee ID</label>
                                <input class="form-control " type="text" name="employee_id" id="edit_eid">
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="input-block mb-3 valid">
                                <label class="col-form-label">Username</label>
                                <input class="form-control  " type="text" name="username" id="edit_username">
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="input-block mb-3 valid">
                                <label class="col-form-label">Email <span class="text-danger">*</span></label>
                                <input class="form-control " type="email" name="email" id="edit_email">
                            </div>
                        </div>


                        <div class="col-sm-6">
                            <div class="input-block mb-3 valid">
                                <label class="col-form-label">Joining Date <span class="text-danger">*</span></label>
                                <input class="form-control datetimepicker" type="text" name="joining_date"
                                    id="edit_joiningdate">
                            </div>
                        </div>

                   

                    </div>

                    <div class="submit-section">
                        <button class="btn btn-primary" type="submit" id="onUpdate">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- End Edit Modal -->
<!--nothing -->

<!-- PreLoader -->
<div id="loader" class="loader" style="display: none;">
    <div class="loader-animation"></div>
</div>
@endsection

@section('script-z')


@endsection
