@extends('layout.mainlayout')
@section('css')
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

<!-- -- Unassigned Leave Model ---- -->
<!-- Unassigned Leave Modal -->
<div class="modal custom-modal fade" id="Unassign_leave" tabindex="-1" role="dialog"
    aria-labelledby="UnassignLeaveModal" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Unassigned Leaves</h5>
                <button type="button" class="closed_btn" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="assignLeaveForm">

                    <div class="MultiDropdown">
                        <label class="col-form-label" for="team_leader_ids">First Assign Name <span
                                class="text-danger">*</span></label>
                        <div class="search-container">
                            <input type="text" class="search-box-style" id="searchBox"
                                placeholder="Search and select..." autocomplete="off">
                            <div class="selected-tags" id="selectedTags"></div>
                        </div>
                        <div class="options-container" id="optionsContainer">
                            <!-- Dynamic options will be populated here -->
                        </div>
                        <input type="hidden" name="team_leader_ids[]" id="team_leader_ids">
                    </div>
                    <div class="error_message text-danger" id="teamLeaderErrorMessage" style="display:none;"></div>


                    <div class="MultiDropdown">
                        <label class="col-form-label" for="manager_ids">Second Assign Name <span
                                class="text-danger">*</span></label>
                        <div class="search-container">
                            <input type="text" class="search-box-style" id="searchBoxManager"
                                placeholder="Search and select..." autocomplete="off">
                            <div class="selected-tags" id="selectedTagsManager"></div>
                        </div>
                        <div class="options-container" id="optionsContainerManager">
                            <!-- Dynamic options will be populated here -->
                        </div>
                        <input type="hidden" name="manager_ids[]" id="manager_ids">
                    </div>
                    <div class="error_message text-danger" id="managerErrorMessage" style="display:none;"></div>

                    <!-- Hidden field to store leave approval ID -->
                    <input type="hidden" id="leaveApprovalId" name="leaveApprovalId">

                    <!-- Submit button -->
                    <div class="submit-section">
                        <button type="submit" class="btn btn-primary submit-btn">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@section('script-z')
<script>
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
                { data: 'employee_id', name: 'employee_id', orderable: false, searchable: false },
                { data: 'username', name: 'username', orderable: false, searchable: false },
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
                    data: 'action', // Action column with Assign button
                    name: 'action',
                    orderable: false,
                    searchable: false,
                    render: function (data, type, row) {
                        return `<div class="text-center">
                      <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#Unassign_leave" 
                            onclick="UnassignLeaveApprovals(${row.id})">Assign</button>            
                    </div>`;
                    }
                }
            ],
            order: [[0, 'desc']] // Default order by the first column (index)
        });
    }


    // Function to handle the "Assign" button click and populate the modal
    function UnassignLeaveApprovals(leaveApprovalId) {
        // Set the leaveApprovalId in the hidden input field
        $('#leaveApprovalId').val(leaveApprovalId);

        // Open the modal
        $('#Unassign_leave').modal('show'); // Use the correct ID here
    }

    // Initialize the DataTable when the page loads
    $(document).ready(function () {
        initializeDataTable();
    });


</script>



<script>
    $('#assignLeaveForm').submit(function (event) {
        event.preventDefault(); // Prevent the form from submitting normally


        // Clear any existing error messages
        $('#teamLeaderErrorMessage').hide();
        $('#managerErrorMessage').hide();

        // Get the values of team_leader_ids and manager_ids
        var teamLeaderUsernames = $('#team_leader_ids').val();  // This will be a comma-separated string of usernames
        var managerUsernames = $('#manager_ids').val();        // This will be a comma-separated string of usernames

        // Validate the team leader field
        var valid = true;


        if (!teamLeaderUsernames) {
            // Show error message for team leader
            $('#teamLeaderErrorMessage').text('Please select at least one "First Assign Name"').show();
            valid = false; // Set validation flag to false
        }

        // Validate the manager field
        if (!managerUsernames) {
            // Show error message for manager
            $('#managerErrorMessage').text('Please select at least one "Second Assign Name"').show();
            valid = false; // Set validation flag to false
        }

        // If any of the fields are empty, prevent form submission
        if (!valid) {
            return; // Stop further processing
        }

        // Convert comma-separated usernames into arrays
        var teamLeaderUsernamesArray = teamLeaderUsernames.split(',');
        var managerUsernamesArray = managerUsernames.split(',');

        // Now, you can make an AJAX request to send the data to the backend

        $.ajax({
            url: '{{ route("leave.add_unassigned") }}',
            type: 'POST',
            data: {
                team_leader_ids: teamLeaderUsernamesArray,
                manager_ids: managerUsernamesArray,
                leaveApprovalId: $('#leaveApprovalId').val(),
                _token: '{{ csrf_token() }}'
            },
            success: function (response) {
                if (response.success) {
                    $('#Unassign_leave').modal('hide'); // Close the modal
                    // Clear the input fields and selected tags
                    $('#team_leader_ids').val('');
                    $('#manager_ids').val('');
                    $('#searchBox').val('');
                    $('#searchBoxManager').val('');
                    $('#selectedTags').empty();
                    $('#selectedTagsManager').empty();
                    $('#unassigned_leave_table').DataTable().ajax.reload();

                } else {
                    alert('Error assigning leave. Please try again.');
                }
            },
            error: function (xhr, status, error) {
                console.log(xhr.responseText); // Log the error details for debugging
                alert('Something went wrong. Please try again later.');
            }
        });
    });

</script>


<script>
    document.addEventListener('DOMContentLoaded', function () {
        const searchBox = document.getElementById('searchBox');
        const optionsContainer = document.getElementById('optionsContainer');
        const selectedTagsContainer = document.getElementById('selectedTags');
        let selectedValues = [];
        const userIdsMap = {};
        let currentPage = 1;  // Keep track of the current page
        let isLoading = false;  // Flag to prevent multiple simultaneous requests
        let totalPages = 1;  // Track the total number of pages

        // Function to update selected tags inside the search box
        function updateSelectedTags() {
            selectedTagsContainer.innerHTML = '';
            selectedValues.forEach(userId => {
                const tag = document.createElement('div');
                tag.classList.add('selected-tag');
                tag.textContent = userIdsMap[userId];

                const closeButton = document.createElement('span');
                closeButton.innerHTML = '&times;';
                closeButton.addEventListener('click', function () {
                    removeSelectedTag(userId);
                });

                tag.appendChild(closeButton);
                selectedTagsContainer.appendChild(tag);
            });

            searchBox.value = '';
            const input = document.getElementById('team_leader_ids');
            input.value = selectedValues.join(',');
        }

        // Function to remove a selected tag
        function removeSelectedTag(userId) {
            selectedValues = selectedValues.filter(item => item !== userId);
            updateSelectedTags();
        }

        // Toggle selection state for an option
        function toggleSelectItem(userId) {
            if (selectedValues.includes(userId)) {
                selectedValues = selectedValues.filter(item => item !== userId);
            } else {
                selectedValues.push(userId);
            }
            updateSelectedTags();
        }

        // Handle option selection (click event)
        function handleOptionSelection(userId, username) {
            toggleSelectItem(userId);
            const option = document.getElementById(userId);
            option.classList.toggle('selected');
            const input = document.getElementById('team_leader_ids');
            const selectedIds = selectedValues.join(',');
            input.value = selectedIds;
        }

        // Fetch options based on the search query or for initial load
        function fetchOptions(query = '', page = 1) {
            if (isLoading) return;  // Prevent multiple fetches at once

            isLoading = true;  // Set loading flag
            fetch("{{ route('multiselect') }}?search=" + query + "&page=" + page)
                .then(response => response.json())
                .then(data => {
                    if (data.data) {
                        populateOptions(data.data);
                        totalPages = data.last_page;  // Update total pages
                        currentPage++;  // Increment the page counter after each fetch
                    }
                })
                .catch(error => console.error('Error fetching data:', error))
                .finally(() => {
                    isLoading = false;  // Reset loading flag
                });
        }

        // Function to populate the dropdown with the fetched options
        function populateOptions(data) {
            data.forEach(user => {
                const option = document.createElement('div');
                option.classList.add('option');
                option.textContent = user.username;
                option.id = user.id;

                userIdsMap[user.id] = user.username;

                if (selectedValues.includes(user.id)) {
                    option.classList.add('selected');
                }

                option.addEventListener('click', function () {
                    handleOptionSelection(user.id, user.username);
                });

                optionsContainer.appendChild(option);
            });
        }

        // Scroll event for lazy loading more data when reaching the bottom of the dropdown
        optionsContainer.addEventListener('scroll', function () {
            if (optionsContainer.scrollTop + optionsContainer.clientHeight >= optionsContainer.scrollHeight) {
                if (currentPage <= totalPages) {
                    fetchOptions(searchBox.value.trim(), currentPage);  // Fetch next page if not at the last page
                }
            }
        });

        // Handle the search box click event to load the first set of records
        searchBox.addEventListener('click', function () {
            optionsContainer.style.display = 'block';

            // If the options container is empty, load the first 10 records (search or no search)
            if (optionsContainer.children.length === 0) {
                fetchOptions('', 1);  // Fetch the first 10 records, no search query
            }
        });

        // Event listener for the search box input to filter the options based on the search query
        searchBox.addEventListener('input', function () {
            const query = searchBox.value.trim();
            if (query) {
                currentPage = 1;  // Reset to page 1 for new search query
                optionsContainer.innerHTML = '';  // Clear current options
                fetchOptions(query);  // Fetch new options based on search query
            } else {
                currentPage = 1;  // Reset to page 1 when the search box is cleared
                optionsContainer.innerHTML = '';  // Clear current options
                fetchOptions();  // Fetch all options without search query
            }
        });

        // Close the options list when clicking outside of the dropdown
        document.addEventListener('click', function (event) {
            if (!event.target.closest('.MultiDropdown')) {
                optionsContainer.style.display = 'none';
            }
        });

        // Initial call to populate options when the page loads (first 10 records without search)
        fetchOptions();
    });


</script>





<script>
    document.addEventListener('DOMContentLoaded', function () {
        const searchBoxManager = document.getElementById('searchBoxManager');
        const optionsContainerManager = document.getElementById('optionsContainerManager');
        const selectedTagsContainerManager = document.getElementById('selectedTagsManager');
        let selectedValuesManager = [];  // Store user IDs as integers
        const userIdsMapManager = {};  // Map to store username -> user ID for display purposes
        let currentPage = 1;  // Keep track of the current page
        let isLoading = false;  // Flag to prevent multiple simultaneous requests
        let totalPages = 1;  // Track the total number of pages

        // Function to update selected tags inside the search box
        function updateSelectedTagsManager() {
            selectedTagsContainerManager.innerHTML = '';  // Clear current tags
            selectedValuesManager.forEach(userId => {
                const tag = document.createElement('div');
                tag.classList.add('selected-tag');
                tag.textContent = userIdsMapManager[userId]; // Display username

                // Add a close icon to each tag to remove it
                const closeButton = document.createElement('span');
                closeButton.innerHTML = '&times;';
                closeButton.addEventListener('click', function () {
                    removeSelectedTagManager(userId);
                });

                tag.appendChild(closeButton);
                selectedTagsContainerManager.appendChild(tag);
            });

            // Clear the search box (only tags will be shown, not the placeholder)
            searchBoxManager.value = '';

            // Update the hidden input with the selected user IDs
            const input = document.getElementById('manager_ids');
            input.value = selectedValuesManager.join(',');  // Update the hidden field with the selected user IDs (comma-separated)
        }

        // Function to remove a selected tag
        function removeSelectedTagManager(userId) {
            selectedValuesManager = selectedValuesManager.filter(item => item !== userId);  // Remove by userId
            updateSelectedTagsManager();
            updateOptionsManager();  // Refresh options after removing the tag
        }

        // Function to toggle selection of an option (using user ID)
        function toggleSelectItemManager(userId) {
            if (selectedValuesManager.includes(userId)) {
                selectedValuesManager = selectedValuesManager.filter(item => item !== userId); // Deselect
            } else {
                selectedValuesManager.push(userId); // Select
            }
            updateSelectedTagsManager();
        }

        // Event listener for search box input (filter options)
        searchBoxManager.addEventListener('input', function () {
            const query = searchBoxManager.value.trim();
            currentPage = 1;  // Reset to page 1 when search input changes
            optionsContainerManager.innerHTML = '';  // Clear the options before fetching new results
            fetchOptionsManager(query); // Fetch options dynamically based on query
        });

        // Display options when the user clicks the search box
        searchBoxManager.addEventListener('click', function () {
            optionsContainerManager.style.display = 'block';

            // If the options container is empty, load the first 10 records (search or no search)
            if (optionsContainerManager.children.length === 0) {
                fetchOptionsManager('');  // Fetch the first 10 records, no search query
            }
        });

        // Close the options list when clicking outside of the dropdown
        document.addEventListener('click', function (event) {
            if (!event.target.closest('.MultiDropdown')) {
                optionsContainerManager.style.display = 'none';
            }
        });

        // Handle option selection (store user ID instead of username)
        function handleOptionSelectionManager(userId, username) {
            toggleSelectItemManager(userId);
            const option = document.getElementById(`user-manager-${userId}`);
            option.classList.toggle('selected');

            // Update the hidden input with the selected user IDs (send integer IDs)
            const input = document.getElementById('manager_ids');
            const selectedIds = selectedValuesManager.join(',');  // Comma-separated list of integer IDs
            input.value = selectedIds;  // This will store the selected user IDs as integers
        }

        // Fetch data dynamically from the server (supports search or no search)
        function fetchOptionsManager(query) {
            if (isLoading) return;  // Prevent multiple simultaneous requests

            isLoading = true;  // Set loading flag
            fetch("{{ route('multiselect') }}?search=" + query + "&page=" + currentPage)
                .then(response => response.json())
                .then(data => {
                    if (data.data) {
                        populateOptionsManager(data.data);
                        totalPages = data.last_page;  // Update total pages
                        currentPage++;  // Increment the page counter after each fetch
                    }
                })
                .catch(error => console.error('Error fetching data:', error))
                .finally(() => {
                    isLoading = false;  // Reset loading flag
                });
        }

        // Function to populate options in the dropdown
        function populateOptionsManager(data) {
            data.forEach(user => {
                const option = document.createElement('div');
                option.classList.add('option');
                option.textContent = user.username;  // Display username
                option.id = `user-manager-${user.id}`;  // Use user ID to uniquely identify the option

                // Store the user ID and username mapping for later display
                userIdsMapManager[user.id] = user.username;  // Map user ID to username

                // Add click event listener for selection
                option.addEventListener('click', function () {
                    handleOptionSelectionManager(user.id, user.username);  // Pass user ID to the handler
                });

                optionsContainerManager.appendChild(option);
            });
        }

        // Scroll event for lazy loading more data when reaching the bottom of the dropdown
        optionsContainerManager.addEventListener('scroll', function () {
            if (optionsContainerManager.scrollTop + optionsContainerManager.clientHeight >= optionsContainerManager.scrollHeight) {
                // If not on the last page, fetch more data
                if (currentPage <= totalPages) {
                    fetchOptionsManager(searchBoxManager.value.trim());  // Fetch next page based on search query
                }
            }
        });

        // New function to refresh the options after removing a tag
        function updateOptionsManager() {
            // Refresh the options based on the remaining selected values
            const query = searchBoxManager.value.trim();
            fetchOptionsManager(query);  // Re-fetch options to update the list
        }

        // Initial call to populate options when the page loads (first 10 records without search)
        fetchOptionsManager('');
    });

</script>

@endsection