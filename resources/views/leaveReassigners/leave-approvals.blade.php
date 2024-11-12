@extends('layout.mainlayout')
@section('content')
<!-- Page Header -->
<div class="page-header">
  <div class="row">
    <div class="col-sm-12">
      <h3 class="page-title">Leave Approvals</h3>
      <ul class="breadcrumb">
        <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
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
          <!-- First Assign Name -->
          <div class="input-block mb-3">
            <label class="col-form-label" for="first_assigned_user">First Assign Name <span
                class="text-danger">*</span></label>
            <select name="first_assigned_user[]" id="first_assigned_user" multiple>
              @foreach ($users as $user)
          <option value="{{ $user->id }}">{{ $user->username }}</option>
        @endforeach
            </select>
          </div>

          <!-- Second Assign Name -->
          <div class="input-block mb-3">
            <label class="col-form-label" for="second_assigned_user">Second Assign Name <span
                class="text-danger">*</span></label>
            <select name="second_assigned_user[]" id="second_assigned_user" multiple>
              @foreach ($users as $user)
          <option value="{{ $user->id }}">{{ $user->username }}</option>
        @endforeach
            </select>
          </div>

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

  // Initialize the DataTable
  let table = $('#leave_approvals_table').DataTable({
    processing: true,
    serverSide: true,
    ajax: {
      url: "{{ route('leave-approvals.index') }}",
      type: 'GET'
    },
    columns: [{
      data: null, // For serial number
      render: function (data, type, row, meta) {
        return meta.row + meta.settings._iDisplayStart + 1;
      },
      orderable: false,
      searchable: false
    },
    {
      data: 'user_name', // Username of the requesting user
      name: 'user_name',
      orderable: false
    },
    {
      data: 'first_assigned_user_name', // First assigned user name
      name: 'first_assigned_user_name',

      orderable: false
    },
    {
      data: 'second_assigned_user_name', // Second assigned user name
      name: 'second_assigned_user_name',
      orderable: false
    },
    {
      data: 'action', // Action column with Assign button
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
    order: [] // No default ordering
  });




  // Handle form submission for assigning leave
  $(document).ready(function () {
    $('#assignLeaveForm').on('submit', function (event) {
      event.preventDefault(); // Prevent default form submission

      // Gather form data
      let formData = $(this).serialize();
      showLoader();
      // Make an AJAX POST request to store the data
      $.ajax({
        url: "{{ route('leave-approvals.store') }}",
        type: 'POST',
        data: formData,
        success: function (response) {
          hideLoader();
          if (response.success) {
            // Close the modal
            $('#assign_leave').modal('hide');

            // Reload the DataTable
            table.ajax.reload();

            // Optionally show a success message
            createToast('info', 'fa-solid fa-circle-check', 'Success', 'Leave assigned successfully!');

          }
        },
        error: function (xhr) {
          hideLoader();
          createToast('error', 'fa-solid fa-circle-xmark', 'Success', 'Error in Leave assigning!');
        }
      });
    });
  });
</script>

<script>
  $(document).ready(function () {
    // Function to initialize MultiSelectTag
    function initializeMultiSelect() {
      firstAssignedSelect = new MultiSelectTag("first_assigned_user", {
        rounded: true,
        shadow: true,
        placeholder: "Search",
        tagColor: {
          textColor: "#327b2c",
          borderColor: "#92e681",
          bgColor: "#eaffe6",
        }
      });

      secondAssignedSelect = new MultiSelectTag("second_assigned_user", {
        rounded: true,
        shadow: true,
        placeholder: "Search",
        tagColor: {
          textColor: "#327b2c",
          borderColor: "#92e681",
          bgColor: "#eaffe6",
        }
      });
    }

    // Initialize MultiSelectTag on page load
    initializeMultiSelect();

    // Function to fetch and set values when Assign button is clicked
    function AssignLeaveApprovals(id) {
      $.ajax({
        url: '{{ route("leave-approvals.edit", ":id") }}'.replace(':id', id), // Edit route
        type: 'GET',
        success: function (response) {
          if (response.success) {
            // Set selected values
            $('#first_assigned_user').val(response.data.first_assigned_user_id);
            $('#second_assigned_user').val(response.data.second_assigned_user_id);
            $('#leaveApprovalId').val(id);

            // Trigger change to update MultiSelectTag display
            $('#first_assigned_user').trigger('change');
            $('#second_assigned_user').trigger('change');

            $('#assign_leave').modal('show');
          } else {
            alert('Error: ' + response.message);
          }
        },
        error: function (xhr) {
          alert('Error fetching leave approval data: ' + xhr.responseText);
        }
      });
    }

    window.AssignLeaveApprovals = AssignLeaveApprovals;
  });
</script>

@endsection