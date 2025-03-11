@extends('layout.mainlayout')
@section('css')
    <link href="{{ asset('assets/css/custom-multi.css') }}" rel="stylesheet">
    <style>
        .MultiDropdown {
            width: 100%;
            position: relative;
        }

        .search-container {
            position: relative;
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            padding: 5px;
            background-color: #f9f9f9;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        .search-box-style {
            width: 100%;
            border: none;
            outline: none;
            background-color: transparent;
            padding: 5px 10px;
            font-size: 16px;
            box-sizing: border-box;
        }

        .selected-tags {
            display: flex;
            flex-wrap: wrap;
            margin-top: 5px;
            max-width: 100%;
        }

        .selected-tag {
            display: inline-flex;
            align-items: center;
            padding: 5px 10px;
            margin: 2px;
            background-color: #007bff;
            color: white;
            border-radius: 20px;
            font-size: 14px;
            cursor: pointer;
        }

        .selected-tag span {
            margin-left: 5px;
            cursor: pointer;
        }

        .options-container {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            border: 1px solid #ccc;
            border-top: none;
            max-height: 150px;
            overflow-y: auto;
            background-color: #fff;
            z-index: 100;
            border-radius: 0 0 4px 4px;
            display: none;
            box-sizing: border-box;
        }

        .option {
            padding: 10px;
            cursor: pointer;
            display: flex;
            align-items: center;
        }

        .option.selected {
            background-color: #007bff;
            color: white;
        }

        .option:hover {
            background-color: #f0f0f0;
        }
    </style>
@endsection
@section('content')
    <div class="page-header">
        <div class="row align-items-center justify-content-between">
            <div class="col-md-4">
                <h3 class="page-title">Unassigned Leave Applications</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{route('dashboard')}}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Unassigned Leave Applications</li>
                </ul>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="table-responsive">
                <table class="table table-striped custom-table" id="unassigned_leave_table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Employee ID</th>
                            <th>Username</th>
                            <th>Title</th>
                            <th>Description</th>
                            <th>Balance Leave</th>
                            <th>Day</th>
                            <th>From</th>
                            <th>To</th>
                            <th>Off Days</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>


    <!-- Unassigned Leave Modal Starts -->
    <div class="modal custom-modal fade" id="assign_leave" tabindex="-1" role="dialog" aria-labelledby="assignLeaveModal"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Assign Leaves</h5>
                    <button type="button" class="closed_btn" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="assignLeaveForm">
                        <div class="custom-select" id="first-assigner-select">
                            <label for="select_box">First Assigner Name:</label>
                            <div class="select-box first_select_box" id="select-box">
                                <input type="hidden" class="tags_input" id="first_assigners_backend_field" name="tags"
                                    hidden>
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

                        <div class="custom-select" id="second-assigner-select">
                            <label for="second-select-box">Second Assigner Name:</label>
                            <div class="select-box second_select_box" id="select-box">
                                <input type="text" class="tags_input" id="second_assigners_backend_field" name="tags"
                                    hidden>
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

                        <!-- Hidden field to store leave approval ID -->
                        <input type="hidden" id="leaveApprovalId" name="leaveApprovalId">

                        <!-- Submit bututt -->
                        <div class="submit-section">
                            <button type="button" class="btn btn-primary submitted-btn"
                                id="unassigned_submit_button">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- Unassigned Leave Modal Ends -->
    <!-- PreLoader -->
    <div id="loader" class="loader" style="display: none;">
        <div class="loader-animation"></div>
    </div>
@endsection
@section('script-z')
    <!-- CUSTOM MULTI JS FILE -->
    <script src="{{asset('assets/js/custom-multi.js')}}"></script>
    <!-- CUSTOM MULTI JS FILE -->

    <script>
        // Set up CSRF token for AJAX requests
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        let table;

        // Function to initialize the DataTable
        function initializeDataTable() {
            if (table) {
                table.destroy(); // Destroy the previous DataTable instance before reinitializing
            }

            table = $('#unassigned_leave_table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('leave_application.unassigned') }}", // Use the correct route URL
                    type: 'GET',
                },
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'employee_id', name: 'employee_id', orderable: false, searchable: true },
                    { data: 'username', name: 'username', orderable: false, searchable: true },
                    {
                        data: 'title',
                        render: function (data) {
                            return (data && data.length > 15) ? data.substring(0, 15) + '...' : data || ''; // Handle null
                        },
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'description',
                        render: function (data) {
                            return (data && data.length > 15) ? data.substring(0, 15) + '...' : data || ''; // Handle null
                        },
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'leave_balance',
                        render: function (data) {
                            // Convert the data to a number (in case it's a string)
                            let balance = parseFloat(data);

                            // Check if it's a valid number
                            if (!isNaN(balance)) {
                                // Format the number and append the 'Days' or 'Day'
                                return (balance >= 2) ? balance + ' Days' : balance + ' Day';
                            }

                            // If it's not a valid number, return 'N/A' or another default value
                            return 'N/A'; // or any appropriate default value
                        },
                        orderable: false,
                        searchable: false
                    },
                    { data: 'day', name: 'day', orderable: false, searchable: false },
                    { data: 'from', name: 'from', orderable: false, searchable: false },
                    { data: 'to', name: 'to', orderable: false, searchable: false },
                    { data: 'off_days', name: 'off_days', orderable: false, searchable: false },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false,
                        render: function (data, type, row) {
                            return `<div class="text-center">
                                                <button class="btn btn-success" 
                                                        id="unassigned_btn"
                                                        data-id="${row.id}">
                                                    Assign
                                                </button>            
                                            </div>
                                        `;
                        }
                    }
                ],
                order: [[0, 'desc']] // Default order by the first column (index)
            });
        }


        // Initialize DataTable when the page loads
        $(document).ready(function () {
            initializeDataTable(); // Initialize DataTable
            // Attach a click event handler to the dynamically created "Assign" button
            $('#unassigned_leave_table').on('click', '#unassigned_btn', function () {
                const leaveApprovalId = $(this).data('id'); // Get the ID from the button's data attribute
                console.log(leaveApprovalId);
                AssignLeaveApprovals(leaveApprovalId); // Call the function with the ID
            });

            // Handle adding/removing tags dynamically for both assigner dropdowns
            setupTagManagement('#first-assigner-select', '#first_assigners_backend_field');
            setupTagManagement('#second-assigner-select', '#second_assigners_backend_field');
        });

        // Function to handle the "Assign" button click and populate the modal
        function AssignLeaveApprovals(leaveApprovalId) {
            showLoader(); // Start loader
            $('#leaveApprovalId').val(leaveApprovalId); // Set leaveApprovalId in the hidden field

            // CLEARING VALIDATIONS
            $("#first_field_error").html("");
            $(".first_select_box").css("border", "");
            $(".first_icon").css("color", "");

            $("#second_field_error").html("");
            $(".second_select_box").css("border", "");
            $(".second_icon").css("color", "");

            $.ajax({
                url: `/unassigned_edit/${leaveApprovalId}`, // Fetch data from backend
                method: "GET",
                success: function (response) {
                    // Populate first assigner dropdown and UI
                    const firstAssignedUsers = {};
                    populateCustomSelect(
                        '#first-assigner-select',
                        '#first_assigners_backend_field',
                        firstAssignedUsers
                    );

                    // Populate second assigner dropdown and UI
                    const secondAssignedUsers = {};
                    populateCustomSelect(
                        '#second-assigner-select',
                        '#second_assigners_backend_field',
                        secondAssignedUsers
                    );

                    $('#assign_leave').modal('show'); // Show the modal
                    hideLoader(); // Stop loader
                },
                error: function () {
                    alert('An error occurred while fetching data. Please try again.');
                    hideLoader();
                }
            });
        }


        // Helper function to populate custom select dropdown and UI
        function populateCustomSelect(selectId, hiddenFieldId, assignedUsers) {
            const selectedOptions = Object.entries(assignedUsers).map(([id, username]) => ({
                id,
                username
            }));

            // Populate hidden field with user IDs
            const userIds = selectedOptions.map(opt => opt.id).join(',');
            $(hiddenFieldId).val(userIds);

            // Populate tags in UI
            const tagsHTML = selectedOptions.map(opt => `
                    <span class="tag" data-value="${opt.id}">
                        ${opt.username}
                        <span class="remove-tag" data-value="${opt.id}">&times;</span>
                    </span>
                `).join('');
            $(`${selectId} .selected-options`).html(tagsHTML);

            // Mark options as active in dropdown
            const optionsContainer = $(`${selectId} .options`);
            optionsContainer.find('.option').each(function () {
                const optionValue = $(this).data('value');
                if (selectedOptions.some(opt => opt.id == optionValue)) {
                    $(this).addClass('active');
                } else {
                    $(this).removeClass('active');
                }
            });
        }

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

            // Define the function to send data
            function storeUnassignersData() {

                const firstAssigner = [];
                const secondAssigner = [];

                // Collect the selected users for both assigners
                $("#first-assigner-select .tag").each(function () {
                    const value = $(this).data("value");
                    firstAssigner.push(value);
                });

                $("#second-assigner-select .tag").each(function () {
                    const value = $(this).data("value");
                    secondAssigner.push(value);
                });

                // Get the leaveApprovalId
                const leaveApprovalId = $("#leaveApprovalId").val();

                // Prepare the data to send
                const data = {
                    team_leader_ids: firstAssigner,
                    manager_ids: secondAssigner,
                    leaveApprovalId: leaveApprovalId,
                };

                // Send the data via AJAX if validation passed
                showLoader();
                $.ajax({
                    url: "{{route('leave.add_unassigned')}}",
                    type: "POST",
                    data: data,
                    success: function (response) {
                        if (response.success) {
                            hideLoader();
                            $("#assign_leave").modal("hide");
                            $("#unassigned_leave_table").DataTable().ajax.reload(null, false);
                            clearCustomSelects();
                        } else {
                            alert("Error saving assigned users.");
                        }
                    },
                    error: function () {
                        hideLoader();
                        alert("An error occurred while saving assigned users.");
                    },
                });

                // Clear custom selects
                function clearCustomSelects() {
                    $("#first-assigner-select .selected-options").html("");
                    $("#first-assigner-select .tags_input").val("");

                    $("#second-assigner-select .selected-options").html("");
                    $("#second-assigner-select .tags_input").val("");

                    selectedOptions = [];

                    $("#first-assigner-select").removeClass("open");
                    $("#second-assigner-select").removeClass("open");
                }
            }


            // Attach click event handler to the submit button
            $("#unassigned_submit_button").click(function () {
                let first_field = $("#first_assigners_backend_field").val();
                let second_field = $("#second_assigners_backend_field").val();
                let valid = true;

                // Validate first assigner field
                if (first_field === "") {
                    $("#first_field_error").html("At least one Assigner is required");
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
                    $("#second_field_error").html("At least one Assigner is required");
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
                    storeUnassignersData(); // Execute the AJAX call to save data
                }
            });

        }
        $(document).ready(function () {
            $('#assign_leave').modal({
                backdrop: 'static',
                keyboard: false
            });

            $(".closed_btn").on("click", function () {
                $("#assign_leave").modal('hide');
            });
        });
</script>@endsection