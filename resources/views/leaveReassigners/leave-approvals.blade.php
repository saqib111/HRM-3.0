@extends('layout.mainlayout')

@section('css')
<link href="{{ asset('assets/css/custom-multi.css') }}" rel="stylesheet">
@endsection



@section('content')
<!-- Page Header -->
<div class="page-header">
  <div class="row">
    <div class="col-sm-12">
      <h3 class="page-title">Leave Approvals</h3>
      <ul class="breadcrumb">
        <li class="breadcrumb-item"><a
            href="{{ auth()->user()->role == '1' ? url('admin-dashboard') : url('attendance-employee') }}">Dashboard</a>
        </li>
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


<!-- Assigned Leave Modal Starts -->
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
            <div class="select-box" id="select-box">
              <input type="hidden" class="tags_input" id="first_assigners_backend_field" name="tags" hidden>
              <div class="selected-options"></div>
              <div class="arrow">
                <i class="fa fa-angle-down"></i>
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
            <span class="tag_error_msg error"></span>
          </div>

          <div class="custom-select" id="second-assigner-select">
            <label for="second-select-box">Second Assigner Name:</label>
            <div class="select-box" id="select-box">
              <input type="text" class="tags_input" name="tags" hidden>
              <div class="selected-options"></div>
              <div class="arrow">
                <i class="fa fa-angle-down"></i>
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
            <span class="tag_error_msg error"></span>
          </div>


          <!-- Hidden field to store leave approval ID -->
          <input type="hidden" id="leaveApprovalId" name="leaveApprovalId">

          <!-- Submit bututt -->
          <div class="submit-section">
            <button type="button" class="btn btn-primary submit-btn" onclick="storeAssignersData()">Submit</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
<!-- Assigned Leave Modal Ends -->

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
        searchable: true
      },
      {
        data: 'first_assigned_user_name',
        name: 'first_assigned_user_name',
        orderable: false,
        searchable: false,
        render: function (data) {
          if (data) {
            // Join usernames in case of multiple and truncate to 30 characters
            let truncated = data.join(", ").slice(0, 50);
            if (data.join(", ").length > 50) {
              truncated += "..."; // Append "..." if the string is longer than 30 characters
            }
            return truncated;
          }
          return '';
        }
      },
      {
        data: 'second_assigned_user_name',
        name: 'second_assigned_user_name',
        orderable: false,
        searchable: false,
        render: function (data) {
          if (data) {
            // Join usernames in case of multiple and truncate to 30 characters
            let truncated = data.join(", ").slice(0, 50);
            if (data.join(", ").length > 50) {
              truncated += "..."; // Append "..." if the string is longer than 30 characters
            }
            return truncated;
          }
          return '';
        }
      },
      {
        data: 'action',
        name: 'action',
        orderable: false,
        searchable: false,
        render: function (data, type, row) {
          return `<div class="text-center">
                                    <button class="btn btn-success" 
                                            id="assign_btn"
                                            data-id="${row.id}">
                                        Assign
                                    </button>            
                                </div>
                            `;
        }
      }
      ],
      order: [
        [0, 'desc'] // Default order by the first column (index)
      ],
      // Handle pagination
      pageLength: 17, // Set the default number of records to show
      lengthMenu: [10, 17, 25, 50, 100] // Options for records per page
    });
  }

  // Initialize DataTable when the page loads
  $(document).ready(function () {
    initializeDataTable(); // Initialize DataTable
    // Attach a click event handler to the dynamically created "Assign" button
    $('#leave_approvals_table').on('click', '#assign_btn', function () {
      const leaveApprovalId = $(this).data('id'); // Get the ID from the button's data attribute
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

    $.ajax({
      url: `/edit/${leaveApprovalId}`, // Fetch data from backend
      method: "GET",
      success: function (response) {
        console.log('Response:', response); // Debugging the response
        // Populate first assigner dropdown and UI
        const firstAssignedUsers = response.first_assigned_user || {};
        populateCustomSelect(
          '#first-assigner-select',
          '#first_assigners_backend_field',
          firstAssignedUsers
        );

        // Populate second assigner dropdown and UI
        const secondAssignedUsers = response.second_assigned_user || {};
        populateCustomSelect(
          '#second-assigner-select',
          '#second_assigners_backend_field',
          secondAssignedUsers
        );

        $('#assign_leave').modal('show'); // Show the modal
        hideLoader(); // Stop loader

        first_backend_values = $('#first_assigners_backend_field').val();
        console.log(first_backend_values);
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
  }
</script>
@endsection