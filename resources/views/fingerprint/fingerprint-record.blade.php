@extends('layout.mainlayout')
@section('content')

    @php
        $authRole = auth()->user()->role;
        $user = auth()->user();
        $permissions = getUserPermissions($user); // Use the helper function to fetch permissions
    @endphp

    <div id="notification" aria-live="polite" aria-atomic="true"></div>
    <style>
        .select-all-option {
            font-weight: bold;
            cursor: pointer;
            padding: 10px;
        }

        .select-all-option:hover {
            background-color: #f0f0f0;
        }

        .unselect-all-option {
            font-weight: bold;
        }


        .MultiDropdown {
            width: 100%;
            position: relative;
        }

        .search-container {
            position: relative;
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            padding: 7px 0px 6px;
            background-color: #fff;
            border: 1px solid #ccc;
            border-radius: 4px;
            /* margin-bottom: 15px; */
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

        .selected-tag {
            display: inline-flex;
            align-items: center;
            padding: 5px 10px;
            margin: 2px;
            background-color: #00C5FB;
            color: white;
            border-radius: 6px;
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
            background-color: #b4b4b4d0;
            color: #000;
        }

        .option:hover {
            background-color: #f0f0f0;
        }

        @media only screen and (max-width: 575px) {
            .form-focus .form-control {
                margin-top: 15px !important;
            }
        }
    </style>
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col-md-4">
                <h3 class="page-title"><span data-translate="fingerprint_record">Fingerprint Record</span></h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a
                            href="{{ auth()->user()->role == '1' ? url('admin-dashboard') : url('attendance-employee') }}">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item active"><span data-translate="fingerprint_record">Fingerprint Record</span></li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Search Filter -->
    <div class="row filter-row mb-4">
        <div class="col-sm-6 col-md-3">
            <div class="MultiDropdown">
                <div class="search-container">
                    <input type="text" class="search-box-style" id="searchBox" placeholder="Search and select employee name"
                        autocomplete="off">
                    <div class="selected-tags" id="selectedTags"></div>
                </div>
                <div class="error_msg text-danger"></div>
                <div class="options-container" id="optionsContainer">
                    <!-- Dynamic options will be populated here -->
                </div>
                <input type="hidden" name="employee_name" id="employee_name">

            </div>
        </div>



        <div class="col-sm-6 col-md-3">
            <div class="input-block mb-3 form-focus select-focus">
                <div class="cal-icon">
                    <input type="text" id="daterange" name="daterange" class="form-control" placeholder="Select Date Range">
                </div>
                <label class="focus-label"><span data-translate="from">From</span> - <span data-translate="to">To</span></label>
            </div>
        </div>

        <div class="col-sm-6 col-md-3">
            <div class="d-grid">
                <a href="#" class="btn_employee text-center"><span data-translate="search">Search</span></a>
            </div>
        </div>
    </div>
    <!-- /Search Filter -->



    <div class="row">
        <div class="col-md-12">
            <div class="table-responsive">
                <table class="table table-striped custom-table" id="fingerprint_table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th><span data-translate="employee_id">Employee ID</span></th>
                            <th><span data-translate="employee_name">Employee Name</span></th>
                            <th><span data-translate="type">Type</span></th>
                            <th><span data-translate="date">Date</span></th>
                            <th><span data-translate="time">Time</span></th>
                            @if($user->role == 1 || in_array('update_fingerprint_status', $permissions) || in_array('delete_fingerprint_record', $permissions))
                                <th class="text-center"><span data-translate="action">Action</span></th>
                            @endif
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Edit Fingerprint Modal -->
    <div class="modal custom-modal fade" id="edit_fingerprint" role="dialog">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Fingerprint</h5>
                    <button type="button" class="closed_btn" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="edit_fingerprint-form">
                        @csrf
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="input-block mb-3">
                                    <label class="col-form-label" for="edit_employee_name">Employee Name</label>
                                    <input type="text" class="form-control" id="edit_employee_name" disabled>
                                </div>
                                <div class="input-block mb-3">
                                    <label class="col-form-label" for="edit_fingerprint_type">Fingerprint Type</label>
                                    <select class="form-control" id="edit_fingerprint_type" name="type">
                                        <option value="0">Check IN</option>
                                        <option value="1">Check Out</option>
                                        <option value="2">Break IN</option>
                                        <option value="3">Break Out</option>
                                        <option value="4">Overtime IN</option>
                                        <option value="5">Overtime Out</option>
                                        <option value="9">Rejected</option>
                                    </select>
                                </div>
                                <div class="submit-section">
                                    <button type="submit" class="btn btn-primary submit-btn">Save Changes</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
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

    <!-- PreLoader -->
    <div id="loader" class="loader" style="display: none;">
        <div class="loader-animation"></div>
    </div>

@endsection

@section('script-z')
    <script>
        $(document).ready(function () {
            const canUpdateFingerprint = @json($user->role == 1 || in_array('update_fingerprint_status', $permissions));
            const canDeleteFingerprint = @json($user->role == 1 || in_array('delete_fingerprint_record', $permissions));

            const columns = [
                { data: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'employee_id', name: 'employee_id' },
                { data: 'username', name: 'username' },
                {
                    data: 'type',
                    name: 'type',
                    render: function (data) {
                        switch (data) {
                            case 0: return 'Check IN';
                            case 1: return 'Check Out';
                            case 2: return 'Break IN';
                            case 3: return 'Break Out';
                            case 4: return 'Overtime IN';
                            case 5: return 'Overtime Out';
                            case 9: return 'Rejected';
                            default: return 'Unknown';
                        }
                    }
                },
                {
                    data: 'fingerprint_in',
                    render: function (data) {
                        const date = new Date(data);
                        return date.toLocaleDateString('en-GB', { day: '2-digit', month: 'long', year: 'numeric' });
                    }
                },
                {
                    data: 'fingerprint_in',
                    render: function (data) {
                        const date = new Date(data);
                        return date.toLocaleTimeString('en-GB', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
                    }
                },
            ];

            if (canUpdateFingerprint || canDeleteFingerprint) {
                columns.push({
                    data: null,
                    orderable: false,
                    searchable: false,
                    render: function (data, type, row) {
                        let buttons = '';
                        if (canUpdateFingerprint) {
                            buttons += `
                            <button class="btn btn-primary" onclick="editFingerprint(${row.id})">
                                <i class="fa fa-edit"></i>
                            </button>`;
                        }
                        if (canDeleteFingerprint) {
                            buttons += `
                            <button class="btn btn-danger" onclick="showDeleteConfirmationModal(${row.id})">
                                <i class="fa fa-trash"></i>
                            </button>`;
                        }
                        return buttons;
                    },
                    className: 'text-center'
                });
            }

            $('#fingerprint_table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('fingerprint-record.index') }}",
                    type: 'GET',
                    data: function (d) {
                        const userId = $('#employee_name').val();  // The input field containing user IDs
                        const dateRange = $('#daterange').val();   // The input field containing the date range (e.g., "2025-01-01 to 2025-01-31")

                        if (userId) {
                            // Split the user_id by comma if it's a list of user IDs
                            d.user_id = userId.split(',').map(item => item.trim()).join(','); // Send the user IDs as a comma-separated string

                            if (dateRange) {
                                // Split the date range into start and end dates
                                const [startDate, endDate] = dateRange.split(' to ');

                                // Ensure the dates are in the format YYYY-MM-DD
                                d.start_date = startDate.trim();
                                d.end_date = endDate.trim();
                            } else {
                                // Default to the current month if no date range is provided
                                const currentDate = new Date();
                                const startDate = new Date(currentDate.getFullYear(), currentDate.getMonth(), 1).toLocaleDateString('en-CA');  // First day of the month
                                const endDate = new Date(currentDate.getFullYear(), currentDate.getMonth() + 1, 0).toLocaleDateString('en-CA');  // Last day of the month

                                d.start_date = startDate;
                                d.end_date = endDate;
                            }
                        } else {
                            // If user_id is not provided, stop the request
                            return false;
                        }
                    }
                },
                columns: columns
            });


            // Search button click event
            $('.btn_employee').on('click', function (e) {
                const userId = $('#employee_name').val();
                const dateRange = $('#daterange').val();

                $('.error_msg').text('');  // Clear previous error message

                if (!userId) {
                    $('.error_msg').text('Please select an employee name.');
                    return false;  // Prevent search if user is not selected
                } else {
                    $('#fingerprint_table').DataTable().ajax.reload();  // Reload the table data based on the selected user and date range
                }
            });

            // Edit Fingerprint Form Submission
            $('#edit_fingerprint-form').on('submit', function (e) {
                e.preventDefault();
                const id = $(this).data('id');
                const type = $('#edit_fingerprint_type').val();

                showLoader();  // Show the loader

                $.ajax({
                    url: '{{ route("fingerprint-record.update", ":id") }}'.replace(':id', id),
                    type: 'PUT',
                    data: { _token: '{{ csrf_token() }}', type: type },
                    success: function (data) {
                        if (data.success) {
                            hideLoader();
                            const row = $('#fingerprint_table').DataTable().row($('#edit_fingerprint-form').data('row'));
                            const updatedData = row.data();
                            updatedData.type = data.fingerprint.type;
                            row.invalidate().draw();  // Redraw the row with updated data

                            $('#edit_fingerprint').modal('hide');  // Close the modal
                            createToast('info', 'fa-solid fa-circle-check', 'Success', 'Fingerprint Record Updated Successfully.');
                        } else {
                            hideLoader();
                            createToast('error', 'fa-solid fa-circle-exclamation', 'Error', 'Error updating fingerprint.');
                        }
                    },
                    error: function () {
                        hideLoader();
                        createToast('error', 'fa-solid fa-circle-exclamation', 'Error', 'Error updating fingerprint.');
                    }
                });
            });
        });

        // Show the Delete Confirmation Modal
        let deleteEmployeeId = null;
        function showDeleteConfirmationModal(id) {
            deleteEmployeeId = id;  // Store the employee ID
            $('#deleteConfirmationModal').modal('show');  // Show the modal
        }

        // Handle Delete Confirmation
        $('#confirmDelete').on('click', function () {
            if (deleteEmployeeId !== null) {
                $.ajax({
                    url: "{{ route('fingerprint-record.destroy', ':id') }}".replace(':id', deleteEmployeeId),
                    type: 'DELETE',
                    data: { _token: '{{ csrf_token() }}' },
                    success: function (data) {
                        if (data.success) {
                            $('#fingerprint_table').DataTable().ajax.reload(null, false);  // Reload the table without resetting pagination
                            $('#deleteConfirmationModal').modal('hide');  // Close the modal
                            createToast('error', 'fa-solid fa-circle-check', 'Success', 'Fingerprint Record Deleted Successfully.');
                        } else {
                            $('#deleteConfirmationModal').modal('hide');
                            createToast('error', 'fa-solid fa-circle-exclamation', 'Error', 'Error deleting fingerprint.');
                        }
                    },
                    error: function () {
                        $('#deleteConfirmationModal').modal('hide');
                        createToast('error', 'fa-solid fa-circle-exclamation', 'Error', 'Error deleting fingerprint.');
                    }
                });
            }
        });

    </script>

    <script>
        $(function () {
            $('#daterange').daterangepicker({
                locale: {
                    format: 'YYYY-MM-DD'
                },
                opens: 'center',
                autoUpdateInput: false
            }, function (start, end, label) {
                // Automatically set the selected date range on the input field
                $('#daterange').val(start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD'));
            });
        });

        function editFingerprint(id) {
            // First AJAX request to get fingerprint details

            $.ajax({
                url: '{{ route("fingerprint-record.edit", ":id") }}'.replace(':id', id),  // Replace with actual ID
                type: 'GET',
                data: {
                    _token: '{{ csrf_token() }}', // CSRF token for security
                },
                success: function (data) {
                    // Extract user_id from the fingerprint data
                    const userId = data.fingerprint.user_id;
                    // Make a second AJAX request to fetch user data using user_id
                    $.ajax({
                        url: '{{ route("fingerprint-record.show", ":id") }}'.replace(':id', userId),  // Assuming you have a route to get user data
                        type: 'GET',
                        data: {
                            _token: '{{ csrf_token() }}', // CSRF token for security
                        },
                        success: function (userData) {
                            // Now you have user data, so populate the modal with both fingerprint and user info
                            $('#edit_employee_name').val(userData.username);  // Set employee name (username) from user data
                            $('#edit_fingerprint_type').val(data.fingerprint.type);  // Set fingerprint type

                            // Store the fingerprint ID for later use (e.g., updating the fingerprint record)
                            $('#edit_fingerprint-form').data('id', id);

                            // Show the modal with the filled-in data
                            $('#edit_fingerprint').modal('show');
                        },
                        error: function (xhr) {
                            alert('Error fetching user data: ' + xhr.responseText);  // Handle any errors while fetching user data
                        }
                    });
                },
                error: function (xhr) {
                    alert('Error fetching fingerprint data: ' + xhr.responseText);  // Handle errors in fingerprint data retrieval
                }
            });
        }
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const searchBox = document.getElementById('searchBox');
            const optionsContainer = document.getElementById('optionsContainer');
            const selectedTagsContainer = document.getElementById('selectedTags');
            let selectedValue = [];  // Store multiple user objects
            let currentPage = 1;
            let isLoading = false;
            let totalPages = 1;

            const authRole = @json(auth()->user()->role);
            // Update selected tags in the search box
            function updateSelectedTags() {
                selectedTagsContainer.innerHTML = ''; // Clear the current tags

                if (selectedValue && Array.isArray(selectedValue)) {
                    selectedValue.forEach(user => {
                        const tag = document.createElement('div');
                        tag.classList.add('selected-tag');
                        tag.textContent = user.username;

                        const closeButton = document.createElement('span');
                        closeButton.innerHTML = '&times;';
                        closeButton.addEventListener('click', function () {
                            removeSelectedTag(user.id);
                        });

                        tag.appendChild(closeButton);
                        selectedTagsContainer.appendChild(tag);
                    });

                    if (selectedValue.length > 8) {
                        selectedTagsContainer.style.boxSizing = 'border-box';
                        selectedTagsContainer.style.overflowY = 'auto';
                        selectedTagsContainer.style.height = '9.3em';
                    } else {
                        selectedTagsContainer.style.boxSizing = '';
                        selectedTagsContainer.style.overflowY = '';
                        selectedTagsContainer.style.height = '';
                    }

                }
            }

            // Remove selected tag
            function removeSelectedTag(userId) {
                // Remove the tag by user ID
                selectedValue = selectedValue.filter(user => user.id !== userId);

                // Update the selected tags and input field
                updateSelectedTags();
                const input = document.getElementById('employee_name');
                input.value = selectedValue.map(user => user.id).join(',');
                // Update the dropdown options' selected state
                const options = optionsContainer.querySelectorAll('.option');
                options.forEach(option => {
                    if (option.dataset.userId === userId.toString()) {
                        option.classList.remove('selected');
                    }
                });

                // Hide options container if no tags are selected
                if (selectedValue.length === 0) {
                    optionsContainer.style.display = 'none';
                }
            }


            // Handle option selection (for individual users)
            function handleOptionSelection(userId, userName) {
                const existingUserIndex = selectedValue.findIndex(user => user.id === userId);

                if (existingUserIndex === -1) {
                    // If the user is not already selected, add them
                    selectedValue.push({ id: userId, username: userName });
                } else {
                    // If the user is already selected, remove them
                    selectedValue.splice(existingUserIndex, 1);
                }

                updateSelectedTags(); // Update the selected tags display
                const input = document.getElementById('employee_name');
                input.value = selectedValue.map(user => user.id).join(','); // Update the hidden input field

                // Update the dropdown option's selected state
                const options = optionsContainer.querySelectorAll('.option');
                options.forEach(option => {
                    if (option.textContent === userName) {
                        option.classList.toggle('selected', existingUserIndex === -1);
                    }
                });
            }

            // Fetch options from the backend
            function fetchOptions(query = '', page = 1) {
                if (isLoading) return;
                isLoading = true;

                fetch(`/search-users?search=${query}&page=${page}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.message) {
                            alert(data.message);
                        } else {
                            populateOptions(data.data); // Append new results to the dropdown
                            totalPages = data.last_page;
                            currentPage = data.current_page + 1;
                        }
                    })
                    .catch(error => console.error('Error fetching data:', error))
                    .finally(() => {
                        isLoading = false;
                    });
            }

            // Handle Select All functionality
            function handleSelectAll() {
                fetch(`/search-users?all=true`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.data) {
                            selectedValue = data.data.map(user => ({ id: user.id, username: user.username }));

                            const input = document.getElementById('employee_name');
                            input.value = selectedValue.map(user => user.id).join(',');

                            updateSelectedTags();

                            // Update the dropdown options' selected state
                            const options = optionsContainer.querySelectorAll('.option');
                            options.forEach(option => {
                                if (data.data.some(user => user.username === option.textContent)) {
                                    option.classList.add('selected');
                                }
                            });

                            toggleSelectOptions(true); // Switch to show Unselect All
                        }
                    })
                    .catch(error => console.error('Error fetching all users:', error));
            }


            // Handle Unselect All functionality
            function handleUnselectAll() {
                selectedValue = [];
                const input = document.getElementById('employee_name');
                input.value = '';

                updateSelectedTags();
                // Update the dropdown options' selected state
                const options = optionsContainer.querySelectorAll('.option');
                options.forEach(option => {
                    option.classList.remove('selected');
                });

                toggleSelectOptions(false); // Switch to show Select All
            }

            // Toggle between Select All and Unselect All options
            function toggleSelectOptions(isSelectAll) {
                const selectAllOption = document.querySelector('.select-all-option');
                const unselectAllOption = document.querySelector('.unselect-all-option');

                if (isSelectAll) {
                    selectAllOption.style.display = 'none';
                    unselectAllOption.style.display = 'block';
                } else {
                    selectAllOption.style.display = 'block';
                    unselectAllOption.style.display = 'none';
                }
            }

            // Populate options in the dropdown
            function populateOptions(data) {
                if (data.length === 0) {
                    optionsContainer.style.display = 'none';
                } else {
                    optionsContainer.style.display = 'block';
                }

                // Only show Select All and Unselect All if authRole is not 5
                if (authRole !== "5") {
                    // Add the "Select All" option only if it's the first load
                    if (optionsContainer.children.length === 0) {
                        let selectAllOption = document.createElement('div');
                        selectAllOption.classList.add('option', 'select-all-option');
                        selectAllOption.textContent = 'Select All';
                        selectAllOption.addEventListener('click', handleSelectAll);
                        optionsContainer.appendChild(selectAllOption);

                        let unselectAllOption = document.createElement('div');
                        unselectAllOption.classList.add('option', 'unselect-all-option');
                        unselectAllOption.textContent = 'Unselect All';
                        unselectAllOption.style.display = 'none';
                        unselectAllOption.addEventListener('click', handleUnselectAll);
                        optionsContainer.appendChild(unselectAllOption);
                    }
                }


                // Append the fetched user options without clearing previous ones
                data.forEach(user => {
                    if (![...optionsContainer.children].some(option => option.textContent === user.username)) {
                        const option = document.createElement('div');
                        option.classList.add('option');
                        option.textContent = user.username;
                        option.dataset.userId = user.id;  // Add data attribute for user ID
                        option.addEventListener('click', function () {
                            handleOptionSelection(user.id, user.username);
                        });

                        // Check if the user is already selected and apply the selected class
                        if (selectedValue.some(selected => selected.id === user.id)) {
                            option.classList.add('selected');
                        }

                        optionsContainer.appendChild(option);
                    }
                });
            }


            // Handle scroll event to load more users when reaching bottom
            optionsContainer.addEventListener('scroll', function () {
                if (optionsContainer.scrollTop + optionsContainer.clientHeight >= optionsContainer.scrollHeight - 10) {
                    if (currentPage <= totalPages && !isLoading) {
                        fetchOptions(searchBox.value.trim(), currentPage); // Fetch the next page
                    }
                }
            });

            // Show options when search box is clicked
            searchBox.addEventListener('click', function () {
                optionsContainer.style.display = 'block';
                if (optionsContainer.children.length === 0) {
                    fetchOptions('', 1); // Load first page if no options exist
                }
            });

            // Handle input event (search query change)
            searchBox.addEventListener('input', function () {
                const query = searchBox.value.trim();
                currentPage = 1;
                // Clear existing options only if starting a new search query
                optionsContainer.innerHTML = '';
                fetchOptions(query, 1);
            });

            // Close the options if clicked outside
            document.addEventListener('click', function (event) {
                if (!event.target.closest('.MultiDropdown')) {
                    optionsContainer.style.display = 'none';
                }
            });
        });
    </script>
    <!-- LANGUAGE SCRIPT -->
    <script src="{{ asset('assets/js/switch.language.js') }}"></script>

@endsection