@extends('layout.mainlayout')
@section('css')
<link href="{{ asset('assets/css/custom-multi.css') }}" rel="stylesheet">
@endsection
@section('content')

@php
    $user = auth()->user();
    $permissions = getUserPermissions($user); // Use the helper function to fetch permissions
    // Check if the user has at least one of the required permissions or is a superadmin
    $hasActionPermission = $user->role == 1 ||
        in_array('update_team', $permissions) ||
        in_array('delete_team', $permissions);
@endphp

<div id="notification" aria-live="polite" aria-atomic="true"></div>
<div class="page-header">
    <div class="row align-items-center justify-content-between">
        <div class="col-md-4">
            <h3 class="page-title">Create Team</h3>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{route('dashboard')}}">Dashboard</a></li>
                <li class="breadcrumb-item active">Create Team</li>
            </ul>
        </div>
        <div class="col-md-8 float-end ms-auto">
            <div class="d-flex title-head">
                @if($user->role == 1 || in_array('create_team', $permissions))
                    <a href="#" class="btn_added" data-bs-toggle="modal" data-bs-target="#add_team"><i
                            class="la la-plus-circle"></i> Add Team</a>
                @endif
            </div>
        </div>
    </div>

</div>
<div class="row">
    <div class="col-md-12">
        <div class="table-responsive">
            <table class="table table-striped custom-table" id="groupTable">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Leader Name</th>
                        <th>Employee Name</th>
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


<!-- Add Team Modal Starts -->
<div class="modal custom-modal fade" id="add_team" tabindex="-1" role="dialog" aria-labelledby="addTeamModal"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Create Team</h5>
                <button type="button" class="closed_btn" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="AddTeamForm" method="POST">
                    @csrf
                    <div class="custom-select" id="first-leader-select">
                        <label for="select_box">Leader Name:</label>
                        <div class="select-box first_select_box" id="select-box">
                            <input type="hidden" class="tags_input tags singleTag" id="leader_id" name="leader_id">
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

                    <div class="custom-select" id="second-employee-select">
                        <label for="second-select-box">Employees Name:</label>
                        <div class="select-box second_select_box" id="select-box">
                            <input type="hidden" class="tags_input tags" id="employee_id" name="employee_id">
                            <div class="selected-options"></div>
                            <div class="arrow">
                                <i class="fa fa-angle-down second_icon"></i>
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
                        <span class="text-danger" id="second_field_error"></span>
                    </div>

                    <div class="submit-section">
                        <button type="button" class="btn btn-primary submitted-btn" id="team_submit_btn">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- Add Team Modal Ends -->



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
                <p>Are you sure you want to delete this team? This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDelete">Delete</button>
            </div>
        </div>
    </div>
</div>
<!-- Delete Confirmation Modal End -->


<!-- Edit Team Modal Starts -->
<div class="modal custom-modal fade" id="edit_team" tabindex="-1" role="dialog" aria-labelledby="editTeamModal"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Team</h5>
                <button type="button" class="closed_btn" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="EditTeamForm" method="POST">
                    @csrf
                    <div class="custom-select" id="leader-select">
                        <label for="select_box">Edit Leader Name:</label>
                        <div class="select-box edit_first_select_box" id="select-box">
                            <input type="hidden" class="tags_input tags" id="edit_leader_id" name="leader_id">
                            <div class="selected-options"></div>
                            <div class="arrow">
                                <i class="fa fa-angle-down edit_first_icon"></i>
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
                        <span class="text-danger" id="edit_first_field_error"></span>
                    </div>

                    <div class="custom-select" id="employee-select">
                        <label for="second-select-box">Edit Employees Name:</label>
                        <div class="select-box edit_second_select_box" id="select-box">
                            <input type="hidden" class="tags_input tags" id="edit_employee_id" name="employee_id">
                            <div class="selected-options"></div>
                            <div class="arrow">
                                <i class="fa fa-angle-down edit_second_icon"></i>
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
                        <span class="text-danger" id="edit_second_field_error"></span>
                    </div>

                    <input type="hidden" id="currentTeamId" name="currentTeamId">

                    <div class="submit-section">
                        <button type="button" class="btn btn-primary submitted-btn"
                            id="edit_team_submit_btn">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- Edit Team Modal Ends -->





<!-- PreLoader -->
<div id="loader" class="loader" style="display: none;">
    <div class="loader-animation"></div>
</div>
@endsection
@section('script-z')
<!-- CUSTOM MULTI JS FILE -->
<script src="{{asset('assets/js/manage_team_custom_multi.js')}}"></script>
<!-- CUSTOM MULTI JS FILE -->

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
            { data: 'name', title: 'Leader Name', orderable: false, searchable: false },
            {
                data: 'employee_names',
                title: 'Employee Name',
                orderable: false,
                searchable: false,
                render: function (data) {
                    return data.length > 50 ? data.substring(0, 50) + '...' : data; // Truncate long text
                }
            }
        ];

        // Add "Action" column if user has permission
        if (hasActionPermission) {
            columns.push({
                data: 'action',
                orderable: false,
                searchable: false,
                render: function (data, type, row) {
                    const editButton = `
                    <button class="btn btn-primary" id="editTeamBtn" data-id="${row.lid}">
                        <i class="fa fa-edit fa-1x"></i>
                    </button>`;
                    const deleteButton = `
                    <button class="btn btn-danger" id="deleteTeamBtn" data-id="${row.lid}">
                        <i class="fa fa-trash fa-1x"></i>
                    </button>`;

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

        table = $('#groupTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('data.datatable') }}", // Route to fetch data
                type: 'GET'
            },
            columns: columns, // Dynamically defined columns
            order: [[0, 'desc']] // Default order by the first column
        });
    }

    $(document).ready(function () {
        initializeDataTable();
    });


    // Handle delete button click
    $(document).on('click', '#deleteTeamBtn', function () {
        const teamId = $(this).data('id');  // Get the team ID from the button data-id
        $('#confirmDelete').data('id', teamId);  // Store the team ID in the confirm delete button

        // Open the modal to confirm deletion
        $('#deleteConfirmationModal').modal('show');
    });

    // Handle confirmation of delete
    $('#confirmDelete').click(function () {
        const teamId = $(this).data('id');  // Get the team ID stored in the confirm button

        showLoader();
        $.ajax({
            url: "{{ route('delete.team', '') }}/" + teamId, // Ensure the correct route URL
            type: 'GET',  // Using GET since you're calling the route defined as 'Route::get'
            success: function (response) {
                hideLoader();
                $('#deleteConfirmationModal').modal('hide');
                table.ajax.reload();
                createToast('error', 'fa-solid fa-circle-check', 'Success', response.message);
            },
            error: function () {
                // alert('Error deleting team.');
                createToast('error', 'fa-solid fa-circle-check', 'Error', 'Error deleting team.');
            }
        });
    });


    function storeTeamData() {
        const firstAssigner = []; // Store the selected leader
        const secondAssigner = []; // Store the selected employees

        // Collect the selected leader (only one leader can be selected)
        $("#first-leader-select .tag").each(function () {
            const value = $(this).data("value");
            firstAssigner.push(value);  // Add selected leader to the array
        });

        // Collect selected employees for the second assigner
        $("#second-employee-select .tag").each(function () {
            const value = $(this).data("value");
            secondAssigner.push(value);  // Add selected employees to the array
        });

        // Prepare the data to send
        const data = {
            leader_id: firstAssigner.join(','),  // Leader ID (comma-separated)
            employee_id: secondAssigner.join(',') // Employee IDs (comma-separated)
        };

        // Send the data via AJAX to store it in the database
        showLoader();
        $.ajax({
            url: "{{ route('team.store') }}",  // Adjust with your route
            type: "POST",
            data: data,
            success: function (response) {
                hideLoader();
                $("#add_team").modal("hide");
                if (response && response.message) {
                    // Show success toast if the team is created
                    if (response.message === "Team created successfully!") {
                        $("#groupTable").DataTable().ajax.reload(null, false); // Reload the DataTable
                        createToast('info', 'fa-solid fa-circle-check', 'Success', response.message);
                    } else if (response.message === "Team Leader already exists!") {
                        // Show error toast if the team leader already exists
                        createToast('error', 'fa-solid fa-circle-exclamation', 'Error', response.message);
                    } else {
                        // Handle any other message
                        createToast('error', 'fa-solid fa-circle-exclamation', 'Error', 'Unexpected error occurred.');
                    }
                }
            },
            error: function (xhr) {
                hideLoader();
                $("#add_team").modal("hide");
                clearCustomSelects();
                let errorMessage = 'An error occurred. Please try again.';
                if (xhr.responseText && xhr.responseText.includes("Team Leader already exists")) {
                    errorMessage = "Team Leader already exists!";
                }
                createToast('error', 'fa-solid fa-circle-exclamation', 'Error', errorMessage);
            }
        });
    }


    // Function to clear the custom select dropdowns and tags
    function clearCustomSelects() {
        // Clear the selected tags
        $("#first-leader-select .selected-options").html("");  // Clear leader selection
        $("#first-leader-select .tags_input").val("");  // Clear leader input field

        $("#second-employee-select .selected-options").html("");  // Clear employee selection
        $("#second-employee-select .tags_input").val("");  // Clear employee input field

        // Clear the active class from the dropdown options
        $("#first-leader-select .options .option").removeClass("active");
        $("#second-employee-select .options .option").removeClass("active");

        // Optionally reset any internal states like current page for pagination
        $("#first-leader-select").data("current-page", 1);
        $("#second-employee-select").data("current-page", 1);

        // Close the dropdowns
        $("#first-leader-select").removeClass("open");
        $("#second-employee-select").removeClass("open");

        // Reset the selected options array if you have it
        selectedOptions = [];
    }

    // Handle adding/removing tags dynamically for both assigner dropdowns
    setupTagManagement('#first-leader-select', '#leader_id');
    setupTagManagement('#second-employee-select', '#employee_id');

    // Function to set up tag management for adding and removing options
    function setupTagManagement(selectId, hiddenFieldId) {
        // Handle adding new options
        $(`${selectId} .options`).on('click', '.option', function () {
            const optionId = $(this).data('value');
            const optionText = $(this).text().trim();

            // Toggle active state
            $(this).toggleClass('active');

            // Update tags and hidden field
            const currentValues = $(`${hiddenFieldId}`).val().split(',').filter(Boolean);
            if ($(this).hasClass('active')) {
                if (!currentValues.includes(optionId.toString())) {
                    currentValues.push(optionId);
                    const tagHTML = ` 
                    <span class="tag" data-value="${optionId}">
                        ${optionText}
                        <span class="remove-tag" data-value="${optionId}">&times;</span>
                    </span>`;
                    $(`${selectId} .selected-options`).append(tagHTML);
                }
            } else {
                // Remove from selected options
                $(`${selectId} .selected-options .tag[data-value="${optionId}"]`).remove();
                const index = currentValues.indexOf(optionId.toString());
                if (index > -1) currentValues.splice(index, 1);
            }

            $(`${hiddenFieldId}`).val(currentValues.join(','));
        });

        // Handle removing selected tags
        $(`${selectId}`).on('click', '.remove-tag', function (e) {
            e.stopPropagation();
            const valueToRemove = $(this).data('value');

            // Remove tag from UI
            $(this).parent().remove();

            // Uncheck option in dropdown
            $(`${selectId} .options .option[data-value="${valueToRemove}"]`).removeClass('active');

            // Update hidden field
            const currentValues = $(`${hiddenFieldId}`).val().split(',').filter(Boolean);
            const index = currentValues.indexOf(valueToRemove.toString());
            if (index > -1) currentValues.splice(index, 1);
            $(`${hiddenFieldId}`).val(currentValues.join(','));
        });
    }

    // Submit the form when the submit button is clicked
    $("#team_submit_btn").click(function () {
        let first_field = $("#leader_id").val();
        let second_field = $("#employee_id").val();
        let valid = true;

        // Reset previous validation error messages
        $("#first_field_error").html(""); // Clear previous error messages
        $("#second_field_error").html(""); // Clear previous error messages

        $(".first_select_box").css("border", ""); // Reset the border color
        $(".first_icon").css("color", ""); // Reset the icon color

        $(".second_select_box").css("border", ""); // Reset the border color
        $(".second_icon").css("color", ""); // Reset the icon color

        // Validate first assigner field
        if (first_field === "") {
            $("#first_field_error").html("Leader Name is required");
            $(".first_select_box").css("border", "1px solid red");
            $(".first_icon").css("color", "red");
            valid = false;
        } else {
            $("#first_field_error").html("");
            $(".first_select_box").css("border", "");
            $(".first_icon").css("color", "");
        }

        // Validate second assigner field
        if (second_field === "") {
            $("#second_field_error").html("At least one employee is required");
            $(".second_select_box").css("border", "1px solid red");
            $(".second_icon").css("color", "red");
            valid = false;
        } else {
            $("#second_field_error").html("");
            $(".second_select_box").css("border", "");
            $(".second_icon").css("color", "");
        }

        // If valid, proceed with submission
        if (valid) {
            storeTeamData(); // Execute the AJAX call to save data
        }
    });

    // EDIT AND UPDATE CODE 

    $(document).on('click', '#editTeamBtn', function () {
        const teamId = $(this).data('id');  // Get the team ID from the button data-id

        showLoader();
        $.ajax({
            url: "{{ route('edit.team', '') }}/" + teamId,  // Call the 'teamEdit' route
            type: 'GET',
            success: function (response) {
                console.log(response);
                hideLoader();

                // Populate leader data
                $('#edit_leader_id').val(response.leader[0].id);  // Set leader ID in the hidden field
                $("#leader-select .selected-options").html(`
                <span class="tag" data-value="${response.leader[0].id}">
                    ${response.leader[0].username}
                    <span class="remove-tag" data-value="${response.leader[0].id}">&times;</span>
                </span>
            `);

                // Populate employee data
                $('#edit_employee_id').val(response.info.map(item => item.eid).join(','));
                $("#employee-select .selected-options").html('');
                response.info.forEach(emp => {
                    $("#employee-select .selected-options").append(`
                    <span class="tag" data-value="${emp.eid}">
                        ${emp.name}
                        <span class="remove-tag" data-value="${emp.eid}">&times;</span>
                    </span>
                `);
                });

                // Add the active class to selected leader and employee options
                setActiveSelection(response.leader[0].id, response.info.map(item => item.eid));

                // Open the edit modal
                $('#edit_team').modal('show');

            },
            error: function () {
                hideLoader();
                createToast('error', 'fa-solid fa-circle-check', 'Error', 'Error fetching team data.');
            }
        });
    });


    $("#edit_team_submit_btn").click(function () {
        const leader_id = $("#edit_leader_id").val();
        const employee_id = $("#edit_employee_id").val();

        let valid = true;

        // Reset error messages and styles
        $("#edit_first_field_error").html(""); // Clear previous error messages
        $("#edit_second_field_error").html(""); // Clear previous error messages

        $(".edit_first_select_box").css("border", ""); // Reset border
        $(".edit_first_icon").css("color", ""); // Reset icon color

        $(".edit_second_select_box").css("border", ""); // Reset border
        $(".edit_second_icon").css("color", ""); // Reset icon color

        // Validate leader field
        if (!leader_id || leader_id === "") {
            // If no leader is selected
            $("#edit_first_field_error").html("Leader Name is required");
            $(".edit_first_select_box").css("border", "1px solid red");
            $(".edit_first_icon").css("color", "red");
            valid = false;
        }

        // Validate employee field
        if (!employee_id || employee_id === "") {
            // If no employee is selected
            $("#edit_second_field_error").html("At least one employee is required");
            $(".edit_second_select_box").css("border", "1px solid red");
            $(".edit_second_icon").css("color", "red");
            valid = false;
        }

        // If valid, proceed with the AJAX request
        if (valid) {
            showLoader();
            $.ajax({
                url: "{{ route('update.team') }}",
                type: 'POST',
                data: {
                    leader_id: leader_id,
                    employee_id: employee_id,
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) {
                    hideLoader();
                    $('#edit_team').modal('hide');
                    table.ajax.reload();
                    createToast('info', 'fa-solid fa-circle-check', 'Success', response.message);
                    clearEditSelects();
                },
                error: function () {
                    hideLoader();
                    createToast('error', 'fa-solid fa-circle-check', 'Error', 'Error updating team.');
                }
            });
        }
    });


    // Function to clear the selected leader and employee fields after successful submission
    function clearEditSelects() {
        // Reset the leader and employee selection (could be done with your selection code)
        $("#edit_leader_id").val("");
        $("#edit_employee_id").val("");

        // Clear selected options display
        $('#leader-select .selected-options').html('');
        $('#employee-select .selected-options').html('');

        // Reset active class and input fields
        $("#leader-select .options .option").removeClass('active');
        $("#employee-select .options .option").removeClass('active');
    }


    // Handle tag removal
    $(document).on('click', '.remove-tag', function (e) {
        e.stopImmediatePropagation();

        const tag = $(this).closest('.tag');
        const valueToRemove = tag.data('value');

        // Remove the tag from the DOM
        tag.remove();

        // Update the leader and employee hidden inputs
        updateLeaderAndEmployeeValues();

        // Remove the active class from the dropdown option
        removeActiveClass(valueToRemove);
    });

    // Function to remove active class from leader and employee options
    function removeActiveClass(valueToRemove) {
        // Remove active class from leader option
        $("#leader-select .options .option").each(function () {
            const optionLeaderId = $(this).data('value');
            if (optionLeaderId == valueToRemove) {
                $(this).removeClass('active'); // Remove active class from leader option
            }
        });

        // Remove active class from employee options
        $("#employee-select .options .option").each(function () {
            const optionEmployeeId = $(this).data('value');
            if (optionEmployeeId == valueToRemove) {
                $(this).removeClass('active'); // Remove active class from employee option
            }
        });
    }

    // Function to update the leader and employee hidden inputs
    function updateLeaderAndEmployeeValues() {
        const leaderId = $("#leader-select .selected-options .tag").data('value');
        const employeeIds = [];

        $("#employee-select .selected-options .tag").each(function () {
            employeeIds.push($(this).data('value'));
        });

        $('#edit_leader_id').val(leaderId);
        $('#edit_employee_id').val(employeeIds.join(','));

        // Optionally: call function to set active selections based on updated values
        setActiveSelection(leaderId, employeeIds);
    }

    // Function to add the active class to the selected leader and employee options
    function setActiveSelection(leaderId, employeeIds) {
        // Add active class to the selected leader option
        $("#leader-select .options .option").each(function () {
            const optionLeaderId = $(this).data('value');
            if (optionLeaderId == leaderId) {
                $(this).addClass('active'); // Mark the leader as active
            } else {
                $(this).removeClass('active'); // Remove the active class from non-selected options
            }
        });

        // Add active class to the selected employee options
        $("#employee-select .options .option").each(function () {
            const optionEmployeeId = $(this).data('value');
            if (employeeIds.includes(optionEmployeeId)) {
                $(this).addClass('active'); // Mark the employee as active
            } else {
                $(this).removeClass('active'); // Remove the active class from non-selected options
            }
        });
    }


</script>

@endsection