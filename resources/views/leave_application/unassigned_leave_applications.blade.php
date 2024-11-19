@extends('layout.mainlayout')
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
                <h5 class="modal-title">Assign Leaves</h5>
                <button type="button" class="closed_btn" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="assignLeaveForm">
                    <!-- First Assign Name -->
                    <div class="input-block mb-3">
                        <label class="col-form-label" for="team_leader_ids">First Assign Name <span
                                class="text-danger">*</span></label>
                        <select name="team_leader_ids[]" id="team_leader_ids" multiple>
                            @foreach ($users as $user)
                                <option value="{{$user->id}}">{{$user->username}}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Second Assign Name -->
                    <div class="input-block mb-3">
                        <label class="col-form-label" for="manager_ids">Second Assign Name <span
                                class="text-danger">*</span></label>
                        <select name="manager_ids[]" id="manager_ids" multiple>
                            @foreach ($users as $user)
                                <option value="{{$user->id}}">{{$user->username}}</option>
                            @endforeach
                        </select>
                    </div>

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

    // Function to initialize MultiSelectTag
    function initializeMultiSelect() {
        firstAssignedSelect = new MultiSelectTag("team_leader_ids", {
            rounded: true,
            shadow: true,
            placeholder: "Search",
            tagColor: {
                textColor: "#327b2c",
                borderColor: "#92e681",
                bgColor: "#eaffe6",
            }
        });

        secondAssignedSelect = new MultiSelectTag("manager_ids", {
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

</script>

<script>
    $(document).ready(function () {
        $('#assignLeaveForm').submit(function (event) {
            event.preventDefault(); // Prevent the form from submitting normally

            // Get the values of team_leader_ids and manager_ids
            var teamLeaderIds = $('#team_leader_ids').val(); // Multiple selection returns an array
            var managerIds = $('#manager_ids').val();

            // Log to verify the data
            console.log('Team Leader IDs:', teamLeaderIds);
            console.log('Manager IDs:', managerIds);

            // Get the leave approval ID (if needed)
            var leaveApprovalId = $('#leaveApprovalId').val();

            // AJAX request
            $.ajax({
                url: '{{ route("leave.add_unassigned") }}', // The URL to send the data to (use route() helper for cleaner URL)
                type: 'POST',
                data: {
                    team_leader_ids: teamLeaderIds,
                    manager_ids: managerIds,
                    leaveApprovalId: leaveApprovalId,
                    _token: '{{ csrf_token() }}' // CSRF token for security
                },
                success: function (response) {
                    // Handle the response here (e.g., success message or modal close)
                    if (response.success) {
                        $('#Unassign_leave').modal('hide'); // Close the modal
                        $('#unassigned_leave_table').DataTable().ajax.reload();
                    } else {
                        alert('Error assigning leave. Please try again.');
                    }
                },
                error: function (xhr, status, error) {
                    // Handle any errors from the AJAX request
                    alert('Something went wrong. Please try again later.');
                }
            });
        });
    });
</script>
@endsection