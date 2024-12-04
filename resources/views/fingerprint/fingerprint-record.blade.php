@extends('layout.mainlayout')
@section('content')

@php
    $user = auth()->user();
    $permissions = getUserPermissions($user); // Use the helper function to fetch permissions
@endphp

<div id="notification" aria-live="polite" aria-atomic="true"></div>
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

    .selected-tags {
        display: flex;
        flex-wrap: wrap;
        max-width: 100%;
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
        background-color: #007bff;
        color: white;
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
            <h3 class="page-title">Fingerprint Record</h3>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="admin-dashboard.html">Dashboard</a></li>
                <li class="breadcrumb-item active">Fingerprint Record</li>
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
            <label class="focus-label">From - To</label>
        </div>
    </div>

    <div class="col-sm-6 col-md-3">
        <div class="d-grid">
            <a href="#" class="btn_employee text-center">Search</a>
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
                        <th>Employee ID</th>
                        <th>Employee Name</th>
                        <th>Type</th>
                        <th>Date</th>
                        <th>Time</th>
                        @if($user->role == 1 || in_array('update_fingerprint_status', $permissions) || in_array('delete_fingerprint_record', $permissions))
                            <th class="text-center">Action</th>
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
                    const userId = $('#employee_name').val();
                    const dateRange = $('#daterange').val();

                    if (userId) {
                        if (dateRange) {
                            const [startDate, endDate] = dateRange.split(' to ');
                            d.user_id = userId;
                            d.start_date = startDate;
                            d.end_date = endDate;
                        } else {
                            const currentDate = new Date();
                            const startDate = new Date(currentDate.getFullYear(), currentDate.getMonth(), 1).toLocaleDateString('en-CA');
                            const endDate = new Date(currentDate.getFullYear(), currentDate.getMonth() + 1, 0).toLocaleDateString('en-CA');

                            d.user_id = userId;
                            d.start_date = startDate;
                            d.end_date = endDate;
                        }
                    } else {
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
        let selectedValue = null;  // Store only the user ID for backend
        let currentPage = 1;  // Keep track of the current page
        let isLoading = false;  // Flag to prevent multiple simultaneous requests
        let totalPages = 1;  // Track the total number of pages

        // Function to update selected tags inside the search box
        function updateSelectedTags() {
            selectedTagsContainer.innerHTML = '';  // Clear current tags

            if (selectedValue) {
                const tag = document.createElement('div');
                tag.classList.add('selected-tag');
                tag.textContent = selectedValue.username;  // Display the username (for frontend)
                // Add a close icon to the tag to remove it
                const closeButton = document.createElement('span');
                closeButton.innerHTML = '&times;';
                closeButton.addEventListener('click', function () {
                    removeSelectedTag();
                });

                tag.appendChild(closeButton);
                selectedTagsContainer.appendChild(tag);
            }
        }

        // Function to remove the selected tag
        function removeSelectedTag() {
            selectedValue = null;  // Clear the selected value
            updateSelectedTags();
            optionsContainer.style.display = 'none';  // Hide options when tag is removed
        }

        // Handle option selection
        function handleOptionSelection(userId, userName) {
            selectedValue = { id: userId, username: userName };  // Store the user ID and username

            updateSelectedTags();

            // Update the hidden input with the selected user's ID (for backend)
            const input = document.getElementById('employee_name');
            input.value = selectedValue.id;  // Store the selected user's ID
        }

        // Fetch options based on the search query or for initial load
        function fetchOptions(query = '', page = 1) {
            if (isLoading) return;  // Prevent multiple fetches at once

            isLoading = true;  // Set loading flag
            fetch("{{ route('search.users') }}?search=" + query + "&page=" + page)
                .then(response => response.json())
                .then(data => {
                    if (data.message) {
                        // Show the 'No users found' message
                        alert(data.message);  // You can use any method to display this message, like a modal or div
                        populateOptions([]);  // Clear previous options if no users are found
                    } else if (data.data) {
                        // Handle the case when data is present
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


        // Function to populate options in the dropdown
        function populateOptions(data) {
            if (data.length === 0) {
                optionsContainer.style.display = 'none';  // Hide options if no data is returned
            } else {
                optionsContainer.style.display = 'block';  // Show options if data is available
            }

            data.forEach(user => {
                const option = document.createElement('div');
                option.classList.add('option');
                option.textContent = user.username;  // Display username in the dropdown
                option.id = `option-${user.id}`;

                // Add click event listener for selection
                option.addEventListener('click', function () {
                    handleOptionSelection(user.id, user.username);  // Pass user.id and user.username
                });

                optionsContainer.appendChild(option);
            });
        }

        // Scroll event for lazy loading more data when reaching the bottom of the dropdown
        optionsContainer.addEventListener('scroll', function () {
            if (optionsContainer.scrollTop + optionsContainer.clientHeight >= optionsContainer.scrollHeight) {
                if (currentPage <= totalPages && !isLoading) {
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
                fetchOptions(query, 1);  // Fetch new options based on search query
            } else {
                currentPage = 1;  // Reset to page 1 when the search box is cleared
                optionsContainer.innerHTML = '';  // Clear current options
                fetchOptions('', 1);  // Fetch first 10 records without any search query
            }
        });

        // Close the options list when clicking outside of the dropdown
        document.addEventListener('click', function (event) {
            if (!event.target.closest('.MultiDropdown')) {
                optionsContainer.style.display = 'none';
            }
        });
    });
</script>

@endsection