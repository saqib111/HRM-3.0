@extends('layout.mainlayout')
@section('content')

<!-- ---Header ---- -->
<div class="page-header">
  <div class="row align-items-center">
    <div class="col-md-4">
      <h3 class="page-title">All Employees Profile</h3>
      <ul class="breadcrumb">
        <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
        <li class="breadcrumb-item active">All Employees</li>
      </ul>
    </div>
  </div>
</div>
<!----- Header END ----->

<div class="row">
  <div class="col-md-12">
    <div class="table-responsive">
      <table class="table table-striped custom-table" id="employees-table">
        <thead>
          <tr>
            <th>#</th>
            <th>Employee ID</th>
            <th>Real Name</th>
            <th>Email</th>
            <th>Company</th>
            <th>Visa No</th>
            <th>Passport No</th>
            <th>Nationality</th>
            <th class="text-center">Action</th>
          </tr>
        </thead>
        <tbody id="all-employee-list">
        </tbody>
      </table>
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
  $(document).ready(function () {
    const table = $('#employees-table').DataTable({
      processing: true,
      serverSide: true,
      ajax: {
        url: "{{ route('all.employees') }}",
        type: 'GET'
      },
      columns: [
        {
          data: null,
          render: function (data, type, row, meta) {
            return meta.row + meta.settings._iDisplayStart + 1;
          },
          orderable: false,
          searchable: false // Typically, the index column isn't searchable
        },
        {
          data: 'employee_id',
          name: 'users.employee_id' // Specify the table name for clarity
        },
        {
          data: 'real_name',
          name: 'user_profiles.real_name' // Specify the table name for clarity
        },
        {
          data: 'email',
          name: 'users.email' // Specify the table name for clarity
        },
        {
          data: 'company_name',
          name: 'companies.name' // Specify the table name for clarity
        },
        {
          data: 'visa_no',
          name: 'visa_infos.visa_no' // Specify the table name for clarity
        },
        {
          data: 'passport_no',
          name: 'visa_infos.passport_no' // Specify the table name for clarity
        },
        {
          data: 'nationality',
          name: 'user_profiles.nationality' // Specify the table name for clarity
        },
        {
          data: 'action',
          orderable: false,
          searchable: false, // Actions usually aren't searchable
          render: function (data, type, row) {
            return `
              <div class="text-center" style="cursor:pointer;" onmouseover="this.querySelector('i').style.color='#0272D9'" 
              onmouseout="this.querySelector('i').style.color='#000'">
                <a href="/view-user-profile/${row.id}" title="View">
                  <i class="ion-eye" style="font-size: 24px; color: #000;"></i>
                </a>
              </div>
            `;
          }
        }
      ],
      order: [],
      pageLength: 20, // Set the default number of records to show
      lengthMenu: [10, 20, 25, 50, 100] // Options for records per page
      // Add additional options for paging, lengthChange, etc., as needed
    });
  });
</script>

@endsection