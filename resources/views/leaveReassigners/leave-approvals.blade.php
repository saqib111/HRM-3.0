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
<!-- Page Header -->
<div class="page-header">
  <div class="row">
    <div class="col-sm-12">
      <h3 class="page-title">Leave Approvals</h3>
      <ul class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item active">Leave Approvals</li>
      </ul>
    </div>
  </div>
</div>
<!-- /Page Header -->
<div id="notification" aria-live="polite" aria-atomic="true"></div>
<div class="row">
  <div class="col-md-12">
    <div class="table-responsive">
      <table class="table table-striped custom-table" id="leave_approvals_table">
        <thead>
          <tr>
            <th>#</th>
            <th>Username</th>
            <th>First Assigned</th>
            <th>Second Assigned</th>
            <th class="text-center">Action</th>
          </tr>
        </thead>
        <tbody id="leave-approvals-list">
        </tbody>
      </table>
    </div>
  </div>
</div>


<!-- Assigned Leave Modal -->
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
          <div class="MultiDropdown">
            <label class="col-form-label" for="first_assigned_user">First Assign Name <span
                class="text-danger">*</span></label>
            <div class="search-container">
              <input type="text" class="search-box-style" id="searchBox" placeholder="Search and select..."
                autocomplete="off">
              <div class="selected-tags" id="selectedTags"></div>
            </div>
            <div class="options-container" id="optionsContainer">
              <!-- Dynamic options will be populated here -->
            </div>
            <input type="hidden" name="first_assigned_user[]" id="first_assigned_user">
          </div>
          <div class="error_message text-danger" id="firstassignedusermsg" style="display:none;"></div>


          <div class="MultiDropdown">
            <label class="col-form-label" for="second_assigned_user">Second Assign Name <span
                class="text-danger">*</span></label>
            <div class="search-container">
              <input type="text" class="search-box-style" id="searchBoxManager" placeholder="Search and select..."
                autocomplete="off">
              <div class="selected-tags" id="selectedTagsManager"></div>
            </div>
            <div class="options-container" id="optionsContainerManager">
              <!-- Dynamic options will be populated here -->
            </div>
            <input type="hidden" name="second_assigned_user[]" id="second_assigned_user">
          </div>
          <div class="error_message text-danger" id="secondassignedusermsg" style="display:none;"></div>



          <!-- Hidden field to store leave approval ID -->
          <input type="hidden" id="leaveApprovalId" name="leaveApprovalId">

          <!-- Submit bututt -->
          <div class="submit-section">
            <button type="submit" class="btn btn-primary submit-btn">Submit</button>
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
<script>
  // Set up CSRF token for AJAX requests
  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });

  let table;

  // Initialize DataTable with server-side processing
  function initializeDataTable() {
    if (table) {
      table.destroy();
    }

    table = $('#leave_approvals_table').DataTable({
      processing: true, // Show processing indicator
      serverSide: true, // Enable server-side processing
      ajax: {
        url: "{{ route('leave-approvals.index') }}", // URL for AJAX request
        type: 'GET',
        data: function (d) {
          // You can add any additional data to the request here
          return d;
        }
      },
      columns: [{
        data: null, // Use `null` here because we're not fetching data for this column
        name: 'row_index',
        orderable: false,
        searchable: false,
        render: function (data, type, row, meta) {
          // Calculate the row index based on current page and page length
          return meta.settings._iDisplayStart + meta.row + 1;
        }
      },
      {
        data: 'user_name',
        name: 'user_name',
        orderable: false,
        searchable: false
      },
      {
        data: 'first_assigned_user_name',
        name: 'first_assigned_user_name',
        orderable: false,
        searchable: false,
        render: function (data) {
          return data ? data.join(", ") : ''; // Join usernames in case of multiple
        }
      },
      {
        data: 'second_assigned_user_name',
        name: 'second_assigned_user_name',
        orderable: false,
        searchable: false,
        render: function (data) {
          return data ? data.join(", ") : ''; // Join usernames in case of multiple
        }
      },
      {
        data: 'action',
        name: 'action',
        orderable: false,
        searchable: false,
        render: function (data, type, row) {
          return `<div class="text-center">
                          <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#assign_leave" 
                          onclick="AssignLeaveApprovals(${row.id})">Assign</button>            
                      </div>`;
        }
      }
      ],
      order: [
        [0, 'desc'] // Default order by the first column (index)
      ],
      // Handle pagination
      pageLength: 10,
    });
  }


  // Function to handle the "Assign" button click and populate the modal
  function AssignLeaveApprovals(leaveApprovalId) {
    $('#leaveApprovalId').val(leaveApprovalId); // Set leave approval ID in hidden input
    $('#assign_leave').modal('show'); // Show the modal
  }

  // Initialize DataTable when the page loads
  $(document).ready(function () {
    initializeDataTable(); // Initialize DataTable
  });
</script>


<script>
  // Global mappings for user IDs to usernames
  var userIdsMap = {}; // For first assigned users
  var userIdsMapManager = {}; // For second assigned users

  // Function to handle the click event on "Edit" button and pre-fill the modal
  function AssignLeaveApprovals(id) {
    $.ajax({
      url: "{{ route('leave-approvals.edit', ':id') }}".replace(':id', id), // Dynamically inject the ID into the route
      type: 'GET',
      success: function (response) {
        if (response.error) {
          alert('Error: ' + response.error);
          return;
        }

        // Set the Leave Approval ID in the hidden input
        $('#leaveApprovalId').val(response.leaveApprovalId);

        // Populate userIdsMap with the first assigned users
        userIdsMap = {}; // Reset the map
        Object.keys(response.first_assigned_user).forEach(function (userId) {
          userIdsMap[userId] = response.first_assigned_user[userId];
        });

        // Populate userIdsMapManager with the second assigned users
        userIdsMapManager = {}; // Reset the map
        Object.keys(response.second_assigned_user).forEach(function (userId) {
          userIdsMapManager[userId] = response.second_assigned_user[userId];
        });

        // Set the hidden input values for assigned users
        $('#first_assigned_user').val(Object.keys(response.first_assigned_user).join(','));
        $('#second_assigned_user').val(Object.keys(response.second_assigned_user).join(','));

        // Update the selected tags in the UI
        updateSelectedTags('#selectedTags', response.first_assigned_user);
        updateSelectedTagsManager('#selectedTagsManager', response.second_assigned_user);

        // Show the modal
        $('#assign_leave').modal('show');

        // Add 'selected' class to dropdown options based on selected users
        const firstAssignedUserIds = Object.keys(response.first_assigned_user);
        $('#optionsContainer .option').each(function () {
          const option = $(this);
          const userId = option.attr('id'); // Assuming the ID of the option is the user ID

          // If the option's user ID is in the list of selected users, add the 'selected' class
          if (firstAssignedUserIds.includes(userId)) {
            option.addClass('selected');
          } else {
            option.removeClass('selected');
          }
        });

        oldValueFirstAssigners = $('#first_assigned_user').val();

        console.log(oldValueFirstAssigners);
      },
      error: function (xhr, status, error) {
        alert('Error loading leave approval data.');
      }
    });
  }

  // Helper function to update the selected tags in the modal (displaying the selected users)
  function updateSelectedTags(container, users) {
    $(container).empty();  // Clear existing tags

    if (typeof users === 'object') {
      Object.keys(users).forEach(function (userId) {
        const username = users[userId];

        // Create and append each username as a tag
        const tag = $('<span class="selected-tag"></span>').text(username);

        // Create and append the remove button (×) for each tag
        const closeButton = $('<span class="remove-tag">&times;</span>');
        closeButton.on('click', function () {
          removeSelectedTag(userId, container); // Pass userId for removal
        });
        tag.append(closeButton);
        $(container).append(tag);
      });
    }
  }

  // Helper function to update the selected tags for the second assigned users (manager)
  function updateSelectedTagsManager(container, users) {
    $(container).empty();  // Clear current tags

    if (typeof users === 'object') {
      Object.keys(users).forEach(function (userId) {
        const username = users[userId];

        // Create and append each username as a tag
        const tag = $('<span class="selected-tag"></span>').text(username);

        // Create and append the remove button (×) for each tag
        const closeButton = $('<span class="remove-tag">&times;</span>');
        closeButton.on('click', function () {
          removeSelectedTagManager(userId, container); // Pass userId for removal
        });

        tag.append(closeButton);
        $(container).append(tag);
      });
    }
  }

  // Function to remove selected tag (first assigned user)
  function removeSelectedTag(userId, container) {
    // Find and remove the tag for the correct userId
    $(container).find('.selected-tag').each(function () {
      const username = $(this).text().replace('×', '').trim(); // Get the username text
      if (username === userIdsMap[userId]) {
        $(this).remove(); // Remove the tag
      }
    });

    // Update the hidden input field with the new list of selected user IDs
    const selectedValues = [];
    $(container).find('.selected-tag').each(function () {
      const username = $(this).text().replace('×', '').trim(); // Get the username
      const userIdToRemove = Object.keys(userIdsMap).find(key => userIdsMap[key] === username);
      if (userIdToRemove) {
        selectedValues.push(userIdToRemove); // Store the userId
      }
    });

    const input = $(container).closest('.MultiDropdown').find('input[type="hidden"]');
    input.val(selectedValues.join(',')); // Update the hidden field with the user IDs

    // Update the oldValueFirstAssigners when a user is removed
    oldValueFirstAssigners = selectedValues.join(','); // Update the old value
    console.log('Updated oldValueFirstAssigners:', oldValueFirstAssigners);
  }


  // Function to remove selected tag for second assigned user (manager)
  function removeSelectedTagManager(userId, container) {
    // Find and remove the tag for the correct userId
    $(container).find('.selected-tag').each(function () {
      const username = $(this).text().replace('×', '').trim(); // Get the username text
      if (username === userIdsMapManager[userId]) {
        $(this).remove(); // Remove the tag
      }
    });

    // Update the hidden input field with the new list of selected user IDs
    const selectedValues = [];
    $(container).find('.selected-tag').each(function () {
      const username = $(this).text().replace('×', '').trim(); // Get the username
      const userIdToRemove = Object.keys(userIdsMapManager).find(key => userIdsMapManager[key] === username);
      if (userIdToRemove) {
        selectedValues.push(userIdToRemove); // Store the userId
      }
    });

    const input = $(container).closest('.MultiDropdown').find('input[type="hidden"]');
    input.val(selectedValues.join(',')); // Update the hidden field with the user IDs
  }

  // Handle form submission to update the leave approval
  $('#assignLeaveForm').submit(function (event) {
    event.preventDefault();

    // Hide previous error messages
    $('#firstassignedusermsg').hide();
    $('#secondassignedusermsg').hide();

    // Get the selected users' values
    var firstAssignedUser = $('#first_assigned_user').val();
    var secondAssignedUser = $('#second_assigned_user').val();

    var valid = true;

    // Validate if users are selected
    if (!firstAssignedUser) {
      $('#firstassignedusermsg').text('Please select at least one "First Assign Name"').show();
      valid = false;
    }

    if (!secondAssignedUser) {
      $('#secondassignedusermsg').text('Please select at least one "Second Assign Name"').show();
      valid = false;
    }

    if (!valid) {
      return;
    }

    var firstAssignedUserArray = firstAssignedUser.split(',');
    var secondAssignedUserArray = secondAssignedUser.split(',');

    // AJAX call to update the leave approval
    $.ajax({
      url: "{{ route('leave-approvals.update', ':id') }}".replace(':id', $('#leaveApprovalId').val()), // Dynamically use leaveApprovalId
      type: 'PUT',
      data: {
        leaveApprovalId: $('#leaveApprovalId').val(),
        first_assigned_user: firstAssignedUserArray,
        second_assigned_user: secondAssignedUserArray,
        _token: '{{ csrf_token() }}'
      },
      success: function (response) {
        if (response.success) {
          $('#assign_leave').modal('hide'); // Close the modal
          $('#leave_approvals_table').DataTable().ajax.reload(); // Reload the table
          // Reset form fields
          $('#first_assigned_user').val('');
          $('#second_assigned_user').val('');
          window.location.reload();
        } else {
          alert('Error updating leave approval. Please try again.');
        }
      },
      error: function (xhr, status, error) {
        alert('Error updating leave approval. Please try again.');
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
    let currentPage = 1;
    let isLoading = false;
    let totalPages = 1;

    function updateSelectedTags() {
      console.log("clicked");

      // Clear the container only if necessary (for a full refresh on selection)
      // We will append the new tag without clearing the whole container now.
      selectedValues.forEach(userId => {
        // Check if tag already exists to avoid adding duplicates
        if (!document.getElementById('tag-' + userId)) {
          const tag = document.createElement('div');
          tag.classList.add('selected-tag');
          tag.id = 'tag-' + userId;  // Add unique ID for the tag to check later

          tag.textContent = userIdsMap[userId];

          const closeButton = document.createElement('span');
          closeButton.innerHTML = '&times;';
          closeButton.addEventListener('click', function () {
            removeSelectedTags(userId); // Remove tag when close button is clicked
          });

          tag.appendChild(closeButton);
          selectedTagsContainer.appendChild(tag); // Append the new tag
        }
      });

      // Update the hidden input field with the current selected values
      const input = document.getElementById('first_assigned_user');
      input.value = selectedValues.join(',');
    }

    // Function to remove a selected tag when the close button is clicked
    function removeSelectedTags(userId) {
      // Remove userId from the selectedValues array
      const index = selectedValues.indexOf(userId);
      if (index > -1) {
        selectedValues.splice(index, 1);  // Remove the userId from the array
      }

      // Remove the tag element from the DOM
      const tag = document.getElementById('tag-' + userId);
      if (tag) {
        tag.remove();  // Remove the tag from the container
      }

      // Call updateSelectedTags again to re-render the tags
      updateSelectedTags();
    }


    function toggleSelectedTags(userId) {
      // Convert userId to a string for consistency
      userId = String(userId);

      if (!selectedValues.includes(userId)) {
        // User not selected, add to the array
        selectedValues.push(userId);
      }

      updateSelectedTags();
    }

    function handleOptionSelection(userId, username) {
      // Convert `userId` to a string to ensure consistency
      userId = String(userId);

      // Retrieve old IDs from oldValueFirstAssigners (assume it's already a comma-separated string of IDs)
      const oldIds = oldValueFirstAssigners.split(',').filter(Boolean); // Split by commas and remove empty values

      // Add the new ID to selectedValues if not already present
      toggleSelectedTags(userId);

      // Combine old IDs with new selected values
      const combinedIds = [...new Set([...oldIds, ...selectedValues])].sort(); // Merge and remove duplicates

      // Update the dropdown option's appearance
      const option = document.getElementById(userId);
      if (option) {
        option.classList.toggle('selected');
      }

      // Update the hidden input field
      const input = document.getElementById('first_assigned_user');
      input.value = combinedIds.join(',');

      // Debugging output
      console.log("Combined IDs: ", combinedIds.join(','));
    }


    function fetchOptions(query = '', page = 1) {
      if (isLoading) return;

      isLoading = true;
      fetch("{{ route('searchAssigner') }}?search=" + query + "&page=" + page)
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


    function populateOptions(data) {
      data.forEach(user => {
        const option = document.createElement('div');
        option.classList.add('option');
        option
          .textContent = user.username;
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


    optionsContainer.addEventListener('scroll', function () {
      if (optionsContainer.scrollTop + optionsContainer.scrollHeight >= optionsContainer
        .scrollHeight) {
        if (currentPage <= totalPages) {
          fetchOptions(searchBox.value.trim(), currentPage);
        }
      }
    });
    searchBox.addEventListener('click', function () {
      optionsContainer.style.display = 'block';

      if (optionsContainer.children.length === 0) {
        fetchOptions('', 1);

      }
    });
    searchBox.addEventListener('input', function () {
      const query = searchBox.value.trim();
      if (query) {
        currentPage = 1;
        optionsContainer.innerHTML = '';
        fetchOptions(query);
      } else {
        currentPage = 1;
        optionsContainer.innerHTML = '';
        fetchOptions();
      }
    });
    document.addEventListener('click', function (event) {
      // Check if the click is outside the dropdown (use the correct class or ID)
      if (!event.target.closest('.MultiDropdown')) {
        optionsContainer.style.display = 'none'; // Close the dropdown
      }
    });

    fetchOptions();
  });
</script>


<script>
  document.addEventListener('DOMContentLoaded', function () {
    const searchBoxManager = document.getElementById('searchBoxManager');
    const optionsContainerManager = document.getElementById('optionsContainerManager');
    const selectedTagsContainerManager = document.getElementById('selectedTagsManager');
    let selectedValuesManager = []; // Store user IDs as integers
    const userIdsMapManager = {}; // Map to store username -> user ID for display purposes
    let currentPage = 1; // Keep track of the current page
    let isLoading = false; // Flag to prevent multiple simultaneous requests
    let totalPages = 1; // Track the total number of pages

    // Function to update selected tags inside the search box
    function updateSelectedTagsManager() {
      selectedTagsContainerManager.innerHTML = ''; // Clear current tags
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
      const input = document.getElementById('second_assigned_user');
      input.value = selectedValuesManager.join(','); // Update the hidden field with the selected user IDs (comma-separated)
    }

    // Function to remove a selected tag
    function removeSelectedTagManager(userId) {
      selectedValuesManager = selectedValuesManager.filter(item => item !== userId); // Remove by userId
      updateSelectedTagsManager();
      updateOptionsManager(); // Refresh options after removing the tag
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
      currentPage = 1; // Reset to page 1 when search input changes
      optionsContainerManager.innerHTML = ''; // Clear the options before fetching new results
      fetchOptionsManager(query); // Fetch options dynamically based on query
    });

    // Display options when the user clicks the search box
    searchBoxManager.addEventListener('click', function () {
      optionsContainerManager.style.display = 'block';

      // If the options container is empty, load the first 10 records (search or no search)
      if (optionsContainerManager.children.length === 0) {
        fetchOptionsManager(''); // Fetch the first 10 records, no search query
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
      const input = document.getElementById('second_assigned_user');
      const selectedIds = selectedValuesManager.join(','); // Comma-separated list of integer IDs
      input.value = selectedIds; // This will store the selected user IDs as integers
    }

    // Fetch data dynamically from the server (supports search or no search)
    function fetchOptionsManager(query) {
      if (isLoading) return; // Prevent multiple simultaneous requests

      isLoading = true; // Set loading flag
      fetch("{{ route('searchAssigner') }}?search=" + query + "&page=" + currentPage)  // Use currentPage here
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
        option.textContent = user.username; // Display username
        option.id = `user-manager-${user.id}`; // Use user ID to uniquely identify the option

        // Store the user ID and username mapping for later display
        userIdsMapManager[user.id] = user.username; // Map user ID to username

        // Add click event listener for selection
        option.addEventListener('click', function () {
          handleOptionSelectionManager(user.id, user.username); // Pass user ID to the handler
        });

        optionsContainerManager.appendChild(option);
      });
    }

    // Scroll event for lazy loading more data when reaching the bottom of the dropdown
    optionsContainerManager.addEventListener('scroll', function () {
      if (optionsContainerManager.scrollTop + optionsContainerManager.clientHeight >= optionsContainerManager.scrollHeight) {
        // If not on the last page, fetch more data
        if (currentPage <= totalPages) {
          fetchOptionsManager(searchBoxManager.value.trim()); // Fetch next page based on search query
        }
      }
    });

    // New function to refresh the options after removing a tag
    function updateOptionsManager() {
      // Refresh the options based on the remaining selected values
      const query = searchBoxManager.value.trim();
      fetchOptionsManager(query); // Re-fetch options to update the list
    }

    // Initial call to populate options when the page loads (first 10 records without search)
    fetchOptionsManager('');
  });

</script>

@endsection