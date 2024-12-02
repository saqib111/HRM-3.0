@extends('layout.mainlayout')
@section('css')
<link href="{{ asset('assets/css/custom-multi.css') }}" rel="stylesheet">
@endsection
@section('content')
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
                <a href="#" class="btn_added" data-bs-toggle="modal" data-bs-target="#add_team"><i
                        class="la la-plus-circle"></i> Add Team</a>
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
                        <th class="text-center">Action</th>
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
                            <input type="hidden" class="tags_input tags" id="leader_id" name="leader_id">
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
        if (table) {
            table.destroy(); // Destroy the previous DataTable instance before reinitializing
        }

        table = $('#groupTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('data.datatable') }}", // The correct route URL
                type: 'GET',
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'lid', title: 'Leader Id' },
                { data: 'name', title: 'Leader Name' },
                {
                    data: 'action',
                    title: 'Action',
                    orderable: false,
                    searchable: false,
                    render: function (data, type, row) {
                        return `
                            <button class="btn btn-primary" id="editTeamBtn"
                                            data-id="${row.lid}">
                                <i class="fa fa-edit fa-1x"></i>
                            </button>
                            <button class="btn btn-danger" onclick="deleteTeam(${row.lid})">
                                <i class="fa fa-trash fa-1x"></i>
                            </button>`;
                    }
                }
            ],
            order: [[0, 'desc']] // Default order by the first column (Leader Id)
        });
    }

    $(document).ready(function () {
        initializeDataTable();
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
                console.log(response); // Log the full response object

                if (response && response.message) {
                    // Show success toast if the team is created
                    if (response.message === "Team created successfully!") {
                        hideLoader();
                        $("#add_team").modal("hide");
                        $("#groupTable").DataTable().ajax.reload(null, false); // Reload the DataTable
                        clearCustomSelects(); // Clear the selects
                        createToast('info', 'fa-solid fa-circle-check', 'Success', response.message);
                    } else if (response.message === "leaders already exist!") {
                        hideLoader();
                        $("#add_team").modal("hide");
                        clearCustomSelects();
                        createToast('error', 'fa-solid fa-circle-exclamation', 'Error', response.message);
                    } else {
                        // Show other error messages
                        hideLoader();
                        $("#add_team").modal("hide");
                        clearCustomSelects();
                        createToast('error', 'fa-solid fa-circle-exclamation', 'Error', response.message);
                    }
                } else {
                    // Handle unexpected response
                    hideLoader();
                    $("#add_team").modal("hide");
                    clearCustomSelects();
                    createToast('error', 'fa-solid fa-circle-exclamation', 'Error', response.message);

                }
            },

            error: function (xhr, status, error) {
                // Show error toast for AJAX error
                hideLoader();
                $("#add_team").modal("hide");
                clearCustomSelects();
                createToast('error', 'fa-solid fa-circle-exclamation', 'Error', 'An error occurred: ' + xhr.responseText);

            }
        });
    }


    // Function to clear the custom select dropdowns and tags
    function clearCustomSelects() {
        $("#first-leader-select .selected-options").html("");  // Clear leader selection
        $("#first-leader-select .tags_input").val("");  // Clear leader input field

        $("#second-employee-select .selected-options").html("");  // Clear employee selection
        $("#second-employee-select .tags_input").val("");  // Clear employee input field

        selectedOptions = [];  // Clear selected options array

        $("#first-leader-select").removeClass("open");  // Close the leader select box
        $("#second-employee-select").removeClass("open");  // Close the employee select box
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
</script>

@endsection