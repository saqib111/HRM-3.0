@extends('layout.mainlayout')
@section('css')
<!-- Litepicker CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css" />

<link href="{{ asset('assets/css/custom-multi.css') }}" rel="stylesheet">
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

    .select2-selection--multiple.is-invalid {
        border-color: red !important;
    }

    body {
        font-family: "Arial", sans-serif;
        background-color: #eaeaea;
        padding: 20px;
    }

    .container {
        max-width: 600px;
        margin: auto;
        background: white;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
    }



    .table tr.dayoff {
        --bs-table-bg: #d9f7fffc;
    }

    .result {
        margin-top: 20px;
        font-size: 1.1em;
        color: #333;
    }

    .dt-column-order {
        display: none !important;
    }

    table.dataTable th.dt-type-numeric,
    table.dataTable th.dt-type-date,
    table.dataTable td.dt-type-numeric,
    table.dataTable td.dt-type-date {
        text-align: center !important;
    }
</style>
@endsection
@section('content')
<div class="page-header">
    <div class="row align-items-center">
        <div class="col-md-4">
            <h3 class="page-title">Assign Offday</h3>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="admin-dashboard.html">Dashboard</a></li>
                <li class="breadcrumb-item active">Assign Offday</li>
            </ul>
        </div>

    </div>
</div>
<div id="notification" aria-live="polite" aria-atomic="true"></div>

<div class="col-auto ms-auto mb-3">
    <ul class="split-head">
        <li>
            <button class="btn add-btn text-white" onclick="addHoliday()">
                <i class="fa fa-plus"></i> Add Offday</button>
        </li>
    </ul>
</div>
<!-- Table -->
<div class="row">
    <div class="col-md-12">
        <div class="table-responsive">
            <table class="table  custom-table" id="attendance">

                <tbody id="attendance-list">
                </tbody>
            </table>
        </div>
    </div>
</div>

<!--End-->



<div id="holiday" class="modal custom-modal fade" data-bs-backdrop='static' role="dialog">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Assign Off Day</h5>
                <button type="button" class="closed_btn" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="assign-holiday" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="row">


                        <div class="col-sm-12 ">
                            <div class=" input-block mb-3 ">
                                <div class="custom-select" id="empID">
                                    <label for="select_box">First Assigner Name:</label>
                                    <div class="select-box first_select_box" id="select-box">
                                        <input type="hidden" class="tags_input" id="first_assigners_backend_field"
                                            name="tags" hidden>
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
                            </div>

                        </div>


                        <div class="col-sm-12">
                            <div class="input-block mb-3 ">
                                <label for="datepicker">Select Off Days:</label>
                                <input type="text" id="date-picker" name="holiday[]" class="form-control" />


                            </div>
                        </div>

                        <div class="submit-section">
                            <button class="btn btn-primary" id="">Submit</button>
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
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="{{asset('assets/js/custom-multi.js')}}"></script>
<script>
    $(document).ready(function () {
        $.ajax({
            url: "{{ route('groupname.data') }}",
            type: 'GET',
            success: function (data) {
                console.log(data.groups);


                $('#attendance').DataTable({
                    data: data.groups,

                    columns: [
                        { data: 'id', title: 'User Id' },
                        { data: 'name', title: 'Username' },
                        { data: 'group_name', title: 'Group Name' },
                        { data: 'group_id', title: 'Group ID' },

                    ]
                });
            },
            error: function (xhr, status, error) {
                console.log(xhr.status);
                console.error(error);
            }
        });
    });



    $('#assign-holiday').on('submit', function (event) {
        event.preventDefault();


        var formData = new FormData();
        const employee_id = [];
        formData.append('date', $('#date-picker').val());


        $("#empID .tag").each(function () {
            const value = $(this).data("value");
            employee_id.push(value);
        });

        console.log(employee_id)
        formData.append('selectedEmployee', employee_id);

        var isValid = true;
        clearValidationStates();
        if (!validateEmployee(employee_id)) isValid = false;
        if (!validateField('#date-picker', ' Date')) isValid = false;
        if (isValid) {
            showLoader();
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                url: "{{ route('holiday.submit') }}",
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function (response) {
                    hideLoader();

                    $('#holiday').modal('hide');


                    createToast('info', 'fa-solid fa-circle-check', 'Success', 'Holiday created successfully.');

                },
                error: function (data) {
                    hideLoader();
                    $('#holiday').modal('hide');
                    var errors = data.responseJSON;
                    console.log(errors.message)
                    createToast('error', 'fa-solid fa-circle-exclamation', 'Fail', 'Wrong Month Select.No Data');
                }
            });
        }
    });

    function clearValidationStates() {
        $('.form-control').removeClass('is-invalid is-valid');
        $('.text-danger').remove();

    }
    function valdateCancel() {

        $('#employee').removeClass('is-invalid is-valid');
        $('#groupName').removeClass('is-invalid is-valid');

        $('.text-danger').remove();
    }
    function validateEmployee(selectedEmployee) {
        let parent = $('#employee').closest('.input-block');
        parent.find('.text-danger').remove();

        if (!selectedEmployee || selectedEmployee.length === 0) {
            $('#employee').addClass('is-invalid');
            parent.append('<span class="text-danger">Please select at least one Employee.</span>');
            return false;
        } else {
            $('#employee').removeClass('is-invalid').addClass('is-valid');
            return true;
        }
    }
    function validateField(selector, fieldName) {
        let value = $(selector).val();
        let parent = $(selector).closest('.input-block');
        parent.find('.text-danger').remove();

        if (!value) {
            $(selector).addClass('is-invalid');
            parent.append(`<span class="text-danger">${fieldName} field cannot be empty.</span>`);
            return false;
        } else {
            $(selector).removeClass('is-invalid').addClass('is-valid');
            return true;
        }
    }

    function addHoliday() {

        const datePicker = flatpickr("#date-picker", {
            mode: "multiple",
            dateFormat: "Y-m-d",
            onChange: function (selectedDates) {


            }

        });

        $.ajax({
            url: "{{route('employee.get')}}",
            type: 'get',
            data: {
                _token: "{{ csrf_token() }}" // Include CSRF token for security
            },
            success: function (response) {
                var select = $('#employee');
                select.empty();
                $.each(response, function (key, value) {

                    select.append('<option value="' + value.id + '">' + value.name + '</option>');


                });
                if (!select.data('multiselect-initialized')) {
                    new MultiSelectTag("employee", {
                        rounded: true,
                        shadow: false,
                        placeholder: "Search",
                        tagColor: {
                            textColor: "#327b2c",
                            borderColor: "#92e681",
                            bgColor: "#eaffe6"
                        }
                    });
                    select.data('multiselect-initialized', true);
                }
            },
            error: function (err) {
                createToast('error', 'fa-solid fa-circle-exclamation', 'Error', 'Error loading employees.');
            }
        });

        $('#holiday').modal('show');
    }

    //for select

    $(document).ready(function () {
        let isLoading = false;
        let debounceTimer = null;

        // Function to handle custom select behavior
        function initCustomSelect(customSelectId) {
            let selectedOptions = []; // Store selected options for each select instance

            // Open/close dropdown when the select box is clicked
            $("#" + customSelectId).click(function (e) {
                // Prevent closing if the click is inside the tags or remove button
                if (
                    $(e.target).closest(".remove-tag").length ||
                    $(e.target).closest(".tag").length
                ) {
                    return; // Don't toggle dropdown if clicking on the "x" button or inside the tag
                }

                // Toggle the open class to show/hide the dropdown
                $("#" + customSelectId).toggleClass("open");

                // Stop the event from propagating to avoid closing on click outside
                e.stopPropagation();
            });

            // Handle the scroll event to load more options when scrolling to the bottom
            $("#" + customSelectId + " .options").scroll(function () {
                const optionsContainer = $(this);
                const scrollTop = optionsContainer.scrollTop(); // Get current scroll position
                const scrollHeight = optionsContainer[0].scrollHeight;
                const containerHeight = optionsContainer.outerHeight();
                const threshold = 100;

                if (scrollHeight - (scrollTop + containerHeight) <= threshold && !isLoading) {
                    const customSelect = $("#" + customSelectId);
                    const searchTerm = customSelect.find(".search-tags").val();
                    let currentPage = customSelect.data("current-page") || 1;

                    isLoading = true;
                    currentPage++;
                    customSelect.data("current-page", currentPage);

                    // Append data and maintain scroll position
                    loadOptions(customSelect, searchTerm, currentPage).then(() => {
                        isLoading = false;

                        // Maintain the scroll position by keeping the scrollTop value
                        optionsContainer.scrollTop(scrollTop);
                    });
                }
            });

            // Handle the input event for search and filtering options
            $("#" + customSelectId + " .search-tags").on("input", function () {
                const searchTerm = $(this).val().trim();
                const customSelect = $("#" + customSelectId);

                if (debounceTimer) clearTimeout(debounceTimer);

                debounceTimer = setTimeout(function () {
                    customSelect.data("current-page", 1);
                    const optionsContainer = customSelect.find(".options");
                    optionsContainer.find(".option").remove(); // Clear current options (not selected ones)
                    loadOptions(customSelect, searchTerm, 1);
                }, 300);
            });

            $("#" + customSelectId + " .search-tags").click(function (e) {
                e.stopPropagation();
            });


            function loadOptions(customSelect, searchTerm = "", page = 1) {
                const optionsContainer = customSelect.find(".options");
                const noResultMessage = customSelect.find(".no-result-message");

                optionsContainer.append('<div class="loading">Loading...</div>');

                return $.ajax({
                    url: "{{route('emp.edit')}}", // Replace with your actual endpoint
                    type: "GET",
                    data: { searchTerm, page },
                    success: function (data) {
                        optionsContainer.find(".loading").remove();

                        if (data.data && data.data.length > 0) {
                            data.data.forEach((item) => {
                                const isSelected = customSelect
                                    .find(`.selected-options .tag[data-value="${item.id}"]`)
                                    .length > 0; // Check if the item is already selected

                                const optionElement = $('<div class="option">')
                                    .text(item.username)
                                    .attr("data-value", item.id)
                                    .toggleClass("active", isSelected) // Retain selected state
                                    .click(function (e) {
                                        $(this).toggleClass("active");

                                        const currentValues = customSelect
                                            .find(".tags_input")
                                            .val()
                                            .split(',')
                                            .filter(Boolean);

                                        if ($(this).hasClass("active")) {
                                            // Append to selectedOptions if not already there
                                            if (!currentValues.includes(item.id.toString())) {
                                                currentValues.push(item.id.toString());

                                                // Append the tag to the UI
                                                const tagHTML = `
                                            <span class="tag" data-value="${item.id}">
                                                ${item.username}
                                                <span class="remove-tag" data-value="${item.id}">&times;</span>
                                            </span>`;
                                                customSelect.find(".selected-options").append(tagHTML);
                                            }
                                        } else {
                                            // Remove from selectedOptions
                                            customSelect
                                                .find(`.selected-options .tag[data-value="${item.id}"]`)
                                                .remove();
                                            const index = currentValues.indexOf(item.id.toString());
                                            if (index > -1) currentValues.splice(index, 1);
                                        }

                                        customSelect.find(".tags_input").val(currentValues.join(","));
                                        e.stopPropagation();
                                    });

                                optionsContainer.append(optionElement);
                            });

                            noResultMessage.hide();
                        } else if (page === 1) {
                            noResultMessage.show();
                        }

                        customSelect.addClass("open");
                        customSelect.find(".search-tags").focus();
                    },
                    error: function () {
                        optionsContainer.find(".loading").remove();
                        alert("An error occurred while fetching options.");
                    },
                });
            }


            // Function to update the selected options in the UI
            function updateSelectedOptions(customSelect) {
                let tagsHTML = "";
                selectedOptions.forEach(function (opt, index) {
                    if (index < 4) {
                        // Ensure data-value is set properly here
                        tagsHTML += `<span class="tag" data-value="${opt.id}">${opt.username}<span class="remove-tag" data-value="${opt.id}">&times;</span></span>`;
                    }
                });

                customSelect.find(".selected-options").html(tagsHTML);

                const selectedValues = selectedOptions.map((opt) => opt.id);
                customSelect.find(".tags_input").val(selectedValues.join(", "));
            }

            // Remove selected option when clicking the "x" button
            $(document).on(
                "click",
                "#" + customSelectId + " .remove-tag",
                function (e) {
                    e.stopImmediatePropagation();
                    e.preventDefault();

                    const customSelect = $("#" + customSelectId);
                    const valueToRemove = $(this).data("value");

                    // Update the selected options array to exclude the removed value
                    selectedOptions = selectedOptions.filter(
                        (opt) => opt.id.toString() !== valueToRemove.toString()
                    );

                    // Remove the tag from the UI
                    customSelect
                        .find(`.selected-options .remove-tag[data-value="${valueToRemove}"]`)
                        .parent()
                        .remove();

                    // Uncheck the corresponding option in the dropdown
                    customSelect
                        .find(`.option[data-value="${valueToRemove}"]`)
                        .removeClass("active");

                    // Update the hidden input value with the new selected options
                    const updatedValues = selectedOptions.map((opt) => opt.id);
                    customSelect.find(".tags_input").val(updatedValues.join(","));

                    // Log the updated values for debugging
                    console.log("Updated selected options:", updatedValues);
                }
            );

            // Close dropdown when clicking anywhere outside
            $(document).click(function (e) {
                // Ensure the click is outside of the custom select
                if (!$(e.target).closest(".custom-select").length) {
                    $("#" + customSelectId).removeClass("open");
                }
            });

            $("#" + customSelectId).each(function () {
                loadOptions($(this), "", 1);
            });

            // Clear search box when clicking the clear button
            $("#" + customSelectId + " .clear").on("click", function () {
                const searchInput = $(this)
                    .closest(".custom-select")
                    .find(".search-tags");

                searchInput.val(""); // Clear the search field
                searchInput.trigger("input"); // Trigger the input event to update the options list
            });
        }

        // Initialize both select boxes with unique IDs
        initCustomSelect("empID");


        // FUNCTION TO STORE ASSIGNERS Data.
        // Function to send the data to the server for storage
        $(document).ready(function () {
            // Define the function to send data
            function storeAssignersData() {
                console.log("Store assigners data logic goes here.");
                const firstAssigner = [];
                const secondAssigner = [];

                // Collect the selected users for both assigners
                $("#empID .tag").each(function () {
                    const value = $(this).data("value"); // Ensure you're getting the data-value correctly
                    firstAssigner.push(value);
                });


                $("#empID.tag").each(function () {
                    console.log(
                        "First Assigner Data Value: ",
                        $(this).data("value")
                    );
                });



                // Get the leaveApprovalId
                const leaveApprovalId = $("#leaveApprovalId").val();

                // Prepare the data to send
                const data = {
                    first_assigned_user: firstAssigner,
                    second_assigned_user: secondAssigner,
                    leaveApprovalId: leaveApprovalId,
                };

                console.log("Data being sent:", data); // Log the data to ensure it's correct

                // Send the data via AJAX
                showLoader();
                $.ajax({
                    url: "/assigned-leave-approvals/store", // Adjust the URL to match your route
                    type: "POST",
                    data: data,
                    success: function (response) {
                        if (response.success) {
                            hideLoader();
                            $("#assign_leave").modal("hide");
                            $("#leave_approvals_table")
                                .DataTable()
                                .ajax.reload(null, false);
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
                function clearCustomSelects() {
                    $("#empID .selected-options").html("");
                    $("#empID .tags_input").val("");



                    // Optionally: If you need to reset the selected options array
                    selectedOptions = [];

                    // Reset the state of both select dropdowns
                    $("#empID").removeClass("open");

                }
            }

            // Attach click event handler to the submit button
            $(".submit-btn").click(function () {
                storeAssignersData(); // Call the function when the button is clicked
            });
        });
    });

    //end


</script>
@endsection