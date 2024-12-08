@extends('layout.mainlayout')
@section('content')

@php
    $user = auth()->user();
    $permissions = getUserPermissions($user); // Use the helper function to fetch permissions
    // Check if the user has at least one of the required permissions or is a superadmin
    $hasActionPermission = $user->role == 1 ||
        in_array('update_user', $permissions) ||
        in_array('change_password', $permissions) ||
        in_array('delete_user', $permissions) ||
        in_array('manage_permissions', $permissions);
@endphp

<div class="col-auto ms-auto mb-3">
    <ul class="c_Employee">
        <li>
            {{-- Show "Add Employee" if the user has the "create_user" permission or is Superadmin --}}
            @if($user->role == 1 || in_array('create_user', $permissions))
                <a href="#" class="btn_employee" data-bs-toggle="modal" data-bs-target="#add_employee">
                    <i class="fa fa-plus"></i> Add Employee
                </a>
            @endif
        </li>


        <li>
            @if(auth()->user()->role === "1" || !empty($company))
                <div class="d-flex justify-content-end">
                    @foreach($company as $index => $item)
                        <button class="btn btn-outline-primary mx-1 company-btn {{ $index === 0 ? 'active' : '' }}"
                            onclick="filterByCompany('{{ $item['name'] }}')">
                            {{ $item['name'] }}
                        </button>
                    @endforeach
                </div>
            @endif
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
                        {{-- Show "Status" column if the user has "update_status" permission or is Superadmin --}}
                        @if($user->role == 1 || in_array('update_status', $permissions))
                            <th>Status</th>
                        @endif
                        <th>Image</th>
                        {{-- Show "Action" column if the user has the necessary permission or is Superadmin --}}
                        @if($hasActionPermission)
                            <th class="text-center">Action</th>
                        @endif
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
                <button type="button" class="closed_btn" data-bs-dismiss="modal" aria-label="Close">
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

                        <div class="col-sm-6">
                            <div class="input-block mb-3">
                                <label class="col-form-label" for="confirm_password_label">Confirm Password</label>
                                <input class="form-control cpassword" type="password" name="confirm_password"
                                    id="confirm_password_label">
                                <div id="cpassword"></div>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="input-block mb-3">
                                <label class="col-form-label" for="company_label">Company</label>
                                <select class="form-select company" name="company" id="company_label">
                                    <option disabled selected>SELECT OPTION</option>
                                    @foreach($company as $com)
                                        <option value="{{$com->id}}">{{$com->name}}</option>

                                    @endforeach
                                </select>
                                <div id="company"></div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="input-block mb-3">
                                <label class="col-form-label" for="department_label">Department <span
                                        class="text-danger">*</span></label>
                                <select class="form-select department" name="department" id="department_label">
                                    <option disabled selected>SELECT OPTION</option>
                                    @foreach($department as $dep)
                                        <option value="{{$dep->id}}">{{$dep->name}}</option>
                                    @endforeach
                                </select>
                                <div id="department"></div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="input-block mb-3">
                                <label class="col-form-label" for="designation_label">Designation <span
                                        class="text-danger">*</span></label>

                                <select class="form-select designation " name="designation" id="designation_label">


                                    <option value=""></option>

                                </select>
                                <div id="designation"></div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="input-block mb-3">
                                <label class="col-form-label" for="brand_label">Brand <span
                                        class="text-danger">*</span></label>

                                <select class="form-select tagging brand " name="brand[]" multiple="multiple"
                                    id="brand_label">
                                    @foreach($brand as $bran)
                                        <option value="{{$bran->id}}">{{$bran->name}}</option>
                                    @endforeach
                                </select>
                                <div id="brand"></div>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="input-block mb-3">
                                <label class="col-form-label" for="profile_image_label">Profile Image <span
                                        class="text-danger">*</span></label>
                                <input class="form-control img" type="file" name="image" value=""
                                    id="profile_image_label">
                                <div id="image"></div>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="input-block mb-3">
                                <label class="col-form-label" for="leave_type">Annual Leaves</label>
                                <input type="number" class="form-control leave_type" name="leave_type" id="leave_type">
                                <div id="leaves"></div>
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
                <button type="button" class="closed_btn" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="edit-employee-form" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="id" id="id">
                    <div class="row">

                        <div class="col-sm-6">
                            <div class="input-block mb-3 valid">
                                <label class="col-form-label" for="edit_eid">Employee ID</label>
                                <input class="form-control" type="text" name="employee_id" id="edit_eid">
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="input-block mb-3 valid">
                                <label class="col-form-label" for="edit_username">Username</label>
                                <input class="form-control" type="text" name="username" id="edit_username">
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="input-block mb-3 valid">
                                <label class="col-form-label" for="edit_email">Email <span
                                        class="text-danger">*</span></label>
                                <input class="form-control" type="email" name="email" id="edit_email">
                            </div>
                        </div>


                        <div class="col-sm-6">
                            <div class="input-block mb-3 valid">
                                <label class="col-form-label" for="edit_joiningdate">Joining Date <span
                                        class="text-danger">*</span></label>
                                <input class="form-control datetimepicker" type="text" name="joining_date"
                                    id="edit_joiningdate">
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="input-block mb-3">
                                <label class="col-form-label" for="edit_company">Company</label>
                                <select class="form-select company" name="company" id="edit_company">

                                    @foreach($company as $com)

                                        <option value="{{ $com->id }}">{{ $com->name }}</option>

                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="input-block mb-3">
                                <label class="col-form-label" for="edit_department">Department <span
                                        class="text-danger">*</span></label>
                                <select class="form-select department" name="department" id="edit_department">
                                    @foreach($department as $dep)
                                        <option value="{{$dep->id}}">{{$dep->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="input-block mb-3 valid">
                                <label class="col-form-label" for="edit_designation">Designation <span
                                        class="text-danger">*</span></label>
                                <select class="form-select " name="designation" id="edit_designation">
                                    <option value=""></option>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="input-block mb-3 valid">
                                <label class="col-form-label" for="edit_brand">Brand <span
                                        class="text-danger">*</span></label>

                                <select class="form-select tagging brand " name="brand[]" multiple="multiple"
                                    id="edit_brand">
                                    @foreach($brand as $bran)
                                        <option value="{{$bran->id}}">{{$bran->name}}</option>
                                    @endforeach

                                </select>
                            </div>
                        </div>
                        <div id="old">


                        </div>

                        <div class="col-sm-6">
                            <div class="input-block mb-3">
                                <label class="col-form-label" for="edit_image">Profile Image <span
                                        class="text-danger">*</span></label>
                                <input class="form-control old_img" type="hidden" name="old_image" id="edit_oldimage">
                                <input class="form-control img" type="file" name="image" id="edit_image">
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

<!-- Edit Password Modal -->
<div id="edit_password_modal" class="modal custom-modal fade" role="dialog">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Password</h5>
                <button type="button" class="closed_btn" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="edit_password_form">
                    @csrf
                    <div class="row">
                        <input type="hidden" id="password_employee_id" name="employee_id">

                        <div class="col-sm-12">
                            <div class="input-block mb-3">
                                <label class="col-form-label" for="edit_password">Password <span
                                        class="text-danger">*</span></label>
                                <input class="form-control password-control" type="password" name="password"
                                    id="edit_password">
                                <div class="val_error text-danger"></div>
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="input-block mb-3">
                                <label class="col-form-label" for="edit_confirm_password">Confirm Password <span
                                        class="text-danger">*</span></label>
                                <input class="form-control password-control" type="password" name="confirm_password"
                                    id="edit_confirm_password">
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
<!-- End Password Modal -->

<!-- Edit Permission Modal -->
<div id="edit_permisson_modal" class="modal custom-modal fade" role="dialog">
    <div class="modal-dialog modal-dialog-centered modal-xl modal-dialog-scrollable" style="min-width: 70%;">
        <div class="modal-content column-container">
            <div class="modal-header">
                <h4 class="modal-title text-decoration-underline ml-5" style="font-size: 27px; margin-left: 40%;"
                    id="permission_modal_heading">Assign & Edit
                    Permission
                </h4>
                <button type="button" class="closed_btn" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="permission_form">
                <!-- Add a hidden field for userId -->
                <input type="hidden" id="user_id" name="user_id" value="">

                <div class="modal-body" style="max-height: 70vh; overflow-y: auto;">
                    <div id="permissions-container" class="main-container">
                        <!-- Permissions will be dynamically loaded here -->
                    </div>
                </div>
                <div class="modal-footer justify-content-center">
                    <!-- <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button> -->
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- End Permission Modal -->


<!-- PreLoader -->
<div id="loader" class="loader" style="display: none;">
    <div class="loader-animation"></div>
</div>
@endsection

@section('script-z')
<script>

    let table;

    function initializeDataTable(companyName) {
        const hasActionPermission = @json($hasActionPermission);
        const canUpdateStatus = @json($user->role == 1 || in_array('update_status', $permissions));

        const columns = [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'employee_id', name: 'employee_id', orderable: false },
            { data: 'username', name: 'username', orderable: false },
            { data: 'email', name: 'email', orderable: false },
            { data: 'joining_date', name: 'joining_date', orderable: false },
            { data: 'company_name', name: 'company_name', orderable: false },
            { data: 'department_name', name: 'department_name', orderable: false },
            { data: 'designation_name', name: 'designation_name', orderable: false },
        ];

        // Add the "Status" column if the user has permission
        if (canUpdateStatus) {
            columns.push({
                data: 'status',
                name: 'status',
                orderable: false,
                render: function (data, type, row) {
                    let checked = (data === '1') ? 'checked' : '';
                    return `
                    <div class="status-toggle-container" style="display:flex;">
                        <div class="status-toggle">
                            <input type="checkbox" id="staff_module_${row.id}" class="check" ${checked} onchange="toggleStatus(${row.id}, this)">
                            <label for="staff_module_${row.id}" class="checktoggle">checkbox</label>
                        </div>
                    </div>`;
                }
            });
        }

        columns.push({
            data: 'image',
            name: 'image',
            orderable: false,
            render: function (data) {
                return `<img width="50px" height="50px" src="uploads/${data}" onclick="showProfileImageModal('${data}')" alt="Profile Picture">`;
            }
        });

        // Add the "Action" column if the user has permission
        if (hasActionPermission) {
            columns.push({
                data: 'action',
                name: 'action',
                orderable: false,
                searchable: false,
                render: function (data, type, row) {
                    return data ? data : ''; // Return data or empty string
                }
            });
        }

        table = $('#users_table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('list.employee') }}",
                type: 'GET',
                data: { company: companyName } // Pass the selected company name
            },
            columns: columns, // Use the dynamically defined columns
            order: [],
            pageLength: 14, // Set the default number of records to show
            lengthMenu: [10, 14, 25, 50, 100] // Options for records per page
        });
    }



    // Function to filter data by company
    function filterByCompany(companyName) {
        // Clear existing data and destroy the DataTable
        if (table) {
            table.destroy();
        }

        // Re-initialize the DataTable with the selected company
        initializeDataTable(companyName);

        // Remove active class from all buttons and add to the clicked button
        $('.company-btn').removeClass('active');
        $('.company-btn:contains("' + companyName + '")').addClass('active');
    }

    $(document).ready(function () {
        $('.tagging').select2({
            tags: true
        });

        // Get the first company's name
        const defaultCompanyName = $('.company-btn:first').text().trim();
        initializeDataTable(defaultCompanyName);

        $('.department').change(function () {
            var id = $(this).val();
            $.ajax({
                type: "GET",
                url: '/check-designation/' + id,
                success: function (res) {
                    if (res) {
                        $(".designation").empty();
                        $(".designation").append('<option disabled selected>Select Designation</option>');
                        $.each(res, function (key, value) {
                            $(".designation").append('<option value="' + res[key]['id'] + '">' + res[key]['name'] +
                                '</option>');
                        });

                    }
                }
            });

        });


        $('#employee-form').on('submit', function (event) {
            event.preventDefault();

            var formData = new FormData();
            formData.append('id', $('#id').val());
            formData.append('employee_id', $('.eid').val());
            formData.append('username', $('.username').val());
            formData.append('email', $('.email').val());
            formData.append('password', $('.password').val());
            formData.append('confirmed_password', $('.cpassword').val());
            formData.append('joining_date', $('.d1').val());
            formData.append('company', $('.company').val());
            formData.append('department', $('.department').val());
            formData.append('designation', $('.designation').val());

            // Handle brand array
            var selectedBrands = $('.brand').val(); // This should return an array
            if (selectedBrands) {
                selectedBrands.forEach(function (brand) {
                    formData.append('brand[]', brand);
                });
            }

            formData.append('image', $('.img')[0].files[0]);

            formData.append('leave_type', $('#leave_type').val());
            var isValid = true;

            // Reset validation state before running new validation
            clearValidationStates();

            // Perform field validation
            if (!validateField('.eid', 'Employee ID')) isValid = false;
            if (!validateField('.username', 'Username')) isValid = false;
            if (!validateEmail('.email')) isValid = false;
            if (!validatePassword('.password', '.cpassword')) isValid = false;
            if (!validateField('.d1', 'Joining Date')) isValid = false;
            if (!validateField('.company', 'Company')) isValid = false;
            if (!validateField('.department', 'Department')) isValid = false;
            if (!validateField('.designation', 'Designation')) isValid = false;
            if (!validateBrand(selectedBrands)) isValid = false;

            // Validate leave_type
            if (!validateField('#leave_type', 'Leave Type')) isValid = false;

            // If all fields are valid, proceed with form submission
            if (isValid) {
                showLoader();
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                $.ajax({
                    url: '/add-employee',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function (response) {
                        hideLoader();
                        $('#add_employee').modal('hide');
                        $('#users_table').DataTable().ajax.reload(null, false);
                        createToast('info', 'fa-solid fa-circle-check', 'Success', 'Employee added successfully.');
                    },
                    error: function (data) {
                        hideLoader();
                        var errors = data.responseJSON;
                        console.log(errors);
                    }
                });
            }
        });

        // Function to clear validation states and error messages
        function clearValidationStates() {
            $('.form-control').removeClass('is-invalid is-valid'); // Remove validation classes
            $('.text-danger').remove();

        }

        // Function to validate a generic field
        function validateField(selector, fieldName) {
            let value = $(selector).val();
            let parent = $(selector).closest('.input-block'); // Locate parent container for appending errors
            parent.find('.text-danger').remove(); // Clear previous error messages

            if (!value) {
                $(selector).addClass('is-invalid');
                parent.append(`<span class="text-danger">${fieldName} field cannot be empty.</span>`);
                return false;
            } else {
                $(selector).removeClass('is-invalid').addClass('is-valid'); // Reset the error if valid
                return true;
            }
        }

        // Function to validate email with regex
        function validateEmail(selector) {
            let email = $(selector).val();
            let regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            let parent = $(selector).closest('.input-block');
            parent.find('.text-danger').remove();

            if (!email || !regex.test(email)) {
                $(selector).addClass('is-invalid');
                parent.append(`<span class="text-danger">Invalid email address.</span>`);
                return false;
            } else {
                $(selector).removeClass('is-invalid').addClass('is-valid');
                return true;
            }
        }

        // Function to validate password and confirm password
        function validatePassword(passwordSelector, confirmPasswordSelector) {
            let password = $(passwordSelector).val();
            let confirmPassword = $(confirmPasswordSelector).val();
            let parentPass = $(passwordSelector).closest('.input-block');
            let parentConfirm = $(confirmPasswordSelector).closest('.input-block');
            parentPass.find('.text-danger').remove();
            parentConfirm.find('.text-danger').remove();

            if (!password) {
                $(passwordSelector).addClass('is-invalid');
                parentPass.append(`<span class="text-danger">Password field cannot be empty.</span>`);
                return false;
            } else if (password !== confirmPassword) {
                $(confirmPasswordSelector).addClass('is-invalid');
                parentConfirm.append(`<span class="text-danger">Passwords do not match.</span>`);
                return false;
            } else {
                $(passwordSelector).removeClass('is-invalid').addClass('is-valid');
                $(confirmPasswordSelector).removeClass('is-invalid').addClass('is-valid');
                return true;
            }
        }

        // Function to validate brand selection
        function validateBrand(selectedBrands) {
            let parent = $('.brand').closest('.input-block');
            parent.find('.text-danger').remove(); // Clear previous messages

            if (!selectedBrands || selectedBrands.length === 0) {
                $('.brand').addClass('is-invalid');
                parent.append('<span class="text-danger">Please select at least one brand.</span>');
                return false;
            } else {
                $('.brand').removeClass('is-invalid').addClass('is-valid');
                return true;
            }
        }
    });

    // Function to open the password edit modal
    function PasswordEditModal(id) {
        clearPasswordValidationStates();
        $.ajax({
            url: "{{ route('get.employee.id', '') }}" + '/' + id, // Ensure this is correct
            type: 'GET',
            success: function (response) {
                // console.log('Response from employee details:', response);
                $('#password_employee_id').val(response.employee_id); // Set employee ID
                $('#edit_password_modal').modal('show'); // Show the modal
            },
            error: function (error) {
                alert('Error fetching employee details: ' + error.responseJSON.message);
            }
        });
    }


    // Function to open the permission edit modal
    function PermissonEditModal(userId) {
        // Set the user ID in the hidden field
        $('#user_id').val(userId);

        // Helper function to capitalize the first letter of each word
        function capitalizeFirstLetter(str) {
            return str.replace(/_/g, ' ') // Replace underscores with spaces
                .replace(/\b\w/g, (char) => char.toUpperCase()); // Capitalize each word's first letter
        }

        // Fetch and display permissions
        $.ajax({
            url: `/user-permissions/${userId}`,
            type: 'GET',
            success: function (response) {
                const permissions = response.permissions;

                const permissionGroups = {
                    'Manage Employee': ['create_user', 'show_users', 'update_user', 'delete_user',
                        'change_password', 'manage_permissions', 'update_status'
                    ],
                    'Manage Brand': ['show_brands', 'create_brand', 'update_brand', 'delete_brand'],
                    'Manage Department': ['show_departments', 'create_department', 'update_department', 'delete_department'],
                    'Manage Designation': ['show_designations', 'create_designation', 'update_designation', 'delete_designation'],
                    'Manage Team': ['create_team', 'show_teams', 'update_team', 'delete_team'],
                    'Manage Shift': ['view_manage_shift', 'view_attendance', 'update_attendance_schedule', 'delete_attendance_schedule', 'bulk_delete_attendance_schedule'],
                    'Annual Leave Balance': ['show_al_balance', 'update_al_balance'],
                    'Fingerprint Record': ['show_fingerprint_record', 'update_fingerprint_status', 'delete_fingerprint_record'],
                    'Manage Leaves': ['unassigned_leaves', 'pending_leaves', 'hr_work', 'revoked_leaves', 'leave_approvals'],
                    'Employee Info': ['show_all_employee_info', 'update_employee_info'],
                    'Visa Documents': ['expired_visa'],
                };

                let html = '';
                for (const [group, perms] of Object.entries(permissionGroups)) {
                    html +=
                        `<div class="card card-role py-4 px-4"><h3 class="mb-3" style= color:#009dc8>${group}</h3><div class="roles-container">`;

                    perms.forEach((perm) => {
                        const checked = permissions.includes(perm) ? 'checked' : '';
                        html += `
                        <div class="check-role">
                            <div class="checkbox-role">
                                <label class="custom_check">
                                    <input type="checkbox" name="permissions[]" value="${perm}" ${checked}>
                                    <span class="checkmark"></span>
                                </label>
                            </div>
                            <div class="permissions-one">
                            <h5>${capitalizeFirstLetter(perm)}</h5>
                            </div>
                            </div>`;
                    });
                    html += `</div></div>`;
                }

                $('#permissions-container').html(html);
                $('#edit_permisson_modal').modal('show');
            },
            error: function () {
                alert('Error fetching permissions.');
            },
        });
    }


    // Handle Permission submission form
    $('#permission_form').on('submit', function (e) {
        e.preventDefault();
        showLoader();

        const userId = $('#user_id').val(); // Get user ID from hidden field
        const permissions = [];
        $('input[name="permissions[]"]:checked').each(function () {
            permissions.push($(this).val());
        });

        $.ajax({
            url: `/user-permissions/${userId}`,
            type: 'POST',
            data: {
                permissions: permissions,
                _token: "{{ csrf_token() }}",
            },
            success: function (response) {
                hideLoader();
                createToast('info', 'fa-solid fa-circle-check', 'Success', response.message);
                $('#edit_permisson_modal').modal('hide');
                $('#users_table').DataTable().ajax.reload(null, false);
            },
            error: function () {
                hideLoader();
                alert('Error saving permissions.');
            },
        });
    });




    // Handle password form submission with AJAX
    $('#edit_password_form').on('submit', function (event) {
        event.preventDefault();

        var formData = new FormData();
        var employeeId = $('#password_employee_id').val(); // Get the employee ID from the input
        formData.append('employee_id', employeeId); // Use the actual employee ID

        formData.append('password', $('#edit_password').val());
        formData.append('password_confirmation', $('#edit_confirm_password').val()); // Use the correct key

        formData.append('_token', $('meta[name="csrf-token"]').attr('content'));

        // Validate password fields
        var isValid = validatePassword('#edit_password', '#edit_confirm_password');

        if (isValid) {
            showLoader();
            $.ajax({
                url: "{{ route('update.employee.password') }}",
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function (response) {
                    hideLoader();
                    // console.log('Response from server:', response);
                    $('#edit_password_modal').modal('hide'); // Hide the modal after success
                    createToast('info', 'fa-solid fa-circle-check', 'Success', response.message);
                },
                error: function (data) {
                    hideLoader();
                    console.log(data.responseJSON.errors); // This will give you details on the errors
                    if (data.responseJSON.errors.employee_id) {
                        createToast('error', 'fa-solid fa-circle-exclamation', 'Error', data.responseJSON.errors
                            .employee_id[0]);
                    } else {
                        createToast('error', 'fa-solid fa-circle-exclamation', 'Error', 'Error updating password.');
                    }
                }
            });
        }
    });


    // Function to clear validation states for password fields
    function clearPasswordValidationStates() {
        $('.password-control').removeClass('is-invalid is-valid');
        $('.text-danger').remove();
    }

    // Function to validate password and confirm password
    function validatePassword(passwordSelector, confirmPasswordSelector) {
        let password = $(passwordSelector).val();
        let confirmPassword = $(confirmPasswordSelector).val();
        let parentPass = $(passwordSelector).closest('.input-block');
        let parentConfirm = $(confirmPasswordSelector).closest('.input-block');

        // Clear previous error messages
        parentPass.find('.text-danger').remove();
        parentConfirm.find('.text-danger').remove();

        // Check if password is empty
        if (!password) {
            $(passwordSelector).addClass('is-invalid');
            parentPass.append(`<span class="text-danger">Password field cannot be empty.</span>`);
        }

        // Check if confirm password is empty
        if (!confirmPassword) {
            $(confirmPasswordSelector).addClass('is-invalid');
            parentConfirm.append(`<span class="text-danger">Confirm Password field cannot be empty.</span>`);
        }

        // If password is empty, no need to check matching
        if (!password) {
            return false; // Prevent further checks if password is empty
        }

        // If confirm password is empty, do not check for match
        if (!confirmPassword) {
            return false; // Prevent further checks if confirm password is empty
        }

        // Check if password is less than 8 characters
        if (password.length < 8) {
            $(passwordSelector).addClass('is-invalid');
            parentPass.append(`<span class="text-danger">Password must be at least 8 characters long.</span>`);
            return false;
        }

        // Check if passwords match
        if (password !== confirmPassword) {
            $(confirmPasswordSelector).addClass('is-invalid');
            parentConfirm.append(`<span class="text-danger">Passwords do not match.</span>`);
            return false;
        }

        // If all checks pass, mark as valid
        $(passwordSelector).removeClass('is-invalid').addClass('is-valid');
        $(confirmPasswordSelector).removeClass('is-invalid').addClass('is-valid');
        return true;
    }
</script>
<script>

    // Show the modal when the delete button is clicked
    function showDeleteModal(id) {
        userIdToDelete = id; // Store the ID of the user to delete
        $('#deleteConfirmationModal').modal('show'); // Show the modal
    }

    function showProfileImageModal(data) {
        console.log(data);
    }

    function toggleStatus(id, checkbox) {
        const status = checkbox.checked ? 1 : 0; // Assuming 1 is active and 0 is inactive

        $.ajax({
            url: "{{ route('update.employee.status') }}", // Update this URL to your status update endpoint
            type: 'POST',
            data: {
                id: id,
                status: status,
                _token: '{{ csrf_token() }}' // Include CSRF token for security
            },
            success: function (response) {
                if (response.status === '1') {
                    createToast('info', 'fa-solid fa-circle-check', 'Success', 'User status is activated!');
                } else if (response.status === '0') {
                    createToast('error', 'fa-solid fa-circle-xmark', 'Success', 'User status is deactivated!');
                } else {
                    // If there's an error, revert the checkbox to its original state
                    checkbox.checked = !checkbox.checked; // Toggle back
                    alert('Error updating status: ' + response.message);
                }
            },
            error: function (xhr, status, error) {
                // Handle AJAX error
                checkbox.checked = !checkbox.checked; // Toggle back
                alert('AJAX Error: ' + error);
            }
        });
    }

    // Handle delete confirmation
    $('#confirmDelete').on('click', function () {
        if (userIdToDelete) {
            $.ajax({
                url: '/delete-employee/' + userIdToDelete,
                type: 'DELETE',
                data: {
                    _token: "{{ csrf_token() }}" // Include CSRF token for security
                },
                success: function (result) {
                    // Reload the DataTable after deletion
                    $('#users_table').DataTable().ajax.reload(null, false);
                    $('#deleteConfirmationModal').modal('hide'); // Hide the modal
                    // Trigger custom success toaster
                    createToast('info', 'fa-solid fa-circle-check', 'Success', 'Employee deleted successfully.');
                },
                error: function (err) {
                    createToast('error', 'fa-solid fa-circle-exclamation', 'Error', 'Error deleting employee.');
                }
            });
        }
    });

    function EmployeeEditModal(id) {

        valdateCancel();

        $.ajax({
            url: '/get-employee/' + id,
            type: 'GET',
            success: function (response) {

                var department_id = response[0].department_id;
                var designation_id = response[0].designation_id;
                $('#edit_eid').val(response[0].employee_id);
                $('#edit_username').val(response[0].username);
                $('#edit_password').val(response[0].password);
                $('#edit_cpassword').val(response[0].password);
                $('#edit_email').val(response[0].email);
                $('#edit_joiningdate').val(response[0].joining_date);
                $('#edit_company').val(response[0].company_id).trigger('change');

                $('#edit_department').val(response[0].department_id).trigger('change');
                $('#edit_designation').val(response[0].designation_id).trigger('change');
                $('#edit_brand').val(response[1]).trigger('change');
                $('#edit_oldimage').val(response[0].image);
                $('#id').val(response[0].id);

                $("#old").empty().append('<img loading="lazy" class="relative" src="' + response[2] + '"  alt="davestewar avatar" width="78" height="78" style="border-radius: 9px;">');

                $.ajax({
                    type: "GET",
                    url: '/check-designation/' + department_id,
                    success: function (res) {
                        if (res) {
                            $("#edit_designation").empty();
                            $("#edit_designation").append('<option disabled selected>Select Designation</option>');
                            $.each(res, function (key, value) {
                                if (res[key]['id'] == designation_id) {
                                    $("#edit_designation").append('<option value="' + res[key]['id'] + '" selected>' + res[key]['name'] +
                                        '</option>');
                                }
                                else {
                                    $("#edit_designation").append('<option value="' + res[key]['id'] + '" >' + res[key]['name'] +
                                        '</option>');
                                }

                            });

                        }
                    }
                });




                $('#edit_employee').modal('show');
            },
            error: function (error) {
                alert('Error fetching employee details.');
            }
        });
    }

    $('#edit-employee-form').on('submit', function (event) {
        event.preventDefault();

        var formData = new FormData($('#edit-employee-form')[0]);
        var isValid = true;

        var EditBrand = $('#edit_brand').val(); // This should return an array
        if (EditBrand) {
            EditBrand.forEach(function (brand) {
                formData.append('brand[]', brand);
            });
        }
        var id = $('#id').val();
        var employee_id = $('#edit_eid').val();
        var username = $('#edit_username').val();
        var email = $('#edit_email').val();
        var joining_date = $('#edit_joiningdate').val();
        var company = $('#edit_company').val();
        var department = $('#edit_department').val();
        var designation = $('#edit_designation').val();
        var brand = $('#edit_brand').val();
        var old_image = $('#edit_oldimage').val();
        var image = $('#edit_image')[0].files[0];

        valdateCancel();

        if (!validateField('#edit_eid', 'Employee ID')) isValid = false;
        if (!validateField('#edit_username', 'Username')) isValid = false;
        if (!validateEmail('#edit_email')) isValid = false;
        if (!validateField('#edit_joiningdate', 'Joining Date')) isValid = false;
        if (!validateField('#edit_designation', 'Designation')) isValid = false;
        if (!validateBrand(EditBrand)) isValid = false;

        if (isValid) {
            showLoader();
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: '/update-employee',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function (response) {

                    $('#edit_employee').modal('hide');
                    hideLoader();
                    createToast('info', 'fa-solid fa-circle-check', 'Success', 'Employee Updated successfully.');
                    $('#users_table').DataTable().ajax.reload(null, false);
                },
                error: function (error) {
                    hideLoader();
                    createToast('error', 'fa-solid fa-circle-exclamation', 'Error', 'Error Updating Employee.');
                }
            });

        }



    });

    function valdateCancel() {
        $('.form-control').removeClass('is-invalid is-valid');
        $('#edit_company').removeClass('is-invalid is-valid');
        $('#edit_department').removeClass('is-invalid is-valid');
        $('#edit_designation').removeClass('is-invalid is-valid');
        $('.text-danger').remove();
    }
    $('#edit_department').change(function () {
        var id = $(this).val();
        $.ajax({
            type: "GET",
            url: '/check-designation/' + id,
            success: function (res) {
                if (res) {
                    $("#edit_designation").empty();
                    $("#edit_designation").append('<option disabled selected>Select Designation</option>');
                    $.each(res, function (key, value) {
                        $("#edit_designation").append('<option value="' + res[key]['id'] + '">' + res[key]['name'] +
                            '</option>');
                    });

                }
            }
        });

    });
    function validateField(selector, fieldName) {
        let value = $(selector).val();
        let parent = $(selector).closest('.valid'); // Locate parent container for appending errors
        parent.find('.text-danger').remove(); // Clear previous error messages

        if (!value) {
            $(selector).addClass('is-invalid');
            parent.append(`<span class="text-danger">${fieldName} field cannot be empty.</span>`);
            return false;
        } else {
            $(selector).removeClass('is-invalid').addClass('is-valid'); // Reset the error if valid
            return true;
        }
    }
    function validateEmail(selector) {
        let email = $(selector).val();
        let regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        let parent = $(selector).closest('.input-block');
        parent.find('.text-danger').remove();

        if (!email || !regex.test(email)) {
            $(selector).addClass('is-invalid');
            parent.append(`<span class="text-danger">Invalid email address.</span>`);
            return false;
        } else {
            $(selector).removeClass('is-invalid').addClass('is-valid');
            return true;
        }
    }

    function validateBrand(selectedBrands) {
        let parent = $('#edit_brand').closest('.valid');
        parent.find('.text-danger').remove(); // Clear previous messages

        if (!selectedBrands || selectedBrands.length === 0) {
            $('#edit_brand').addClass('is-invalid');
            parent.append('<span class="text-danger">Please select at least one brand.</span>');
            return false;
        } else {
            $('#edit_brand').removeClass('is-invalid').addClass('is-valid');
            return true;
        }
    }

</script>

@endsection