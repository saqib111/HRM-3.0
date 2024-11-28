@extends('layout.mainlayout')
@section('content')
<!-- Page Header -->
<div class="page-header">
  <div class="row align-items-center">
    <div class="col-md-4">
      <h3 class="page-title">Expired Visa Information</h3>
      <ul class="breadcrumb">
        <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
        <li class="breadcrumb-item active">Expired Visa</li>
      </ul>
    </div>
  </div>
</div>
<!-- /Page Header -->


<div class="row">
  <div class="col-md-12">
    <div class="table-responsive">
      <table class="table table-striped custom-table" id="expired_visa_table">
        <thead>
          <tr>
            <th class="text-center">#</th>
            <th>Name</th>
            <th class="text-center">Department</th>
            <th class="text-center">Visa No</th>
            <th class="text-center">Expiry Date</th>
            <th class="text-center">Remaining Days</th>
          </tr>
        </thead>
        <tbody></tbody>
      </table>
    </div>
  </div>
</div>

@endsection

@section('script-z')
<script>
  $(document).ready(function () {
    const table = $('#expired_visa_table').DataTable({
      processing: true,
      serverSide: true,
      ajax: {
        url: "{{ route('expired-visa-information.index') }}",
        type: 'GET'
      },
      columns: [{
        data: null,
        render: function (data, type, row, meta) {
          return meta.row + meta.settings._iDisplayStart + 1;
        },
        orderable: false,
        searchable: false,
        className: 'text-center'
      },
      {
        data: 'username',
        name: 'username'
      },
      {
        data: 'department_name',
        name: 'department_name',
        className: 'text-center'
      },
      {
        data: 'visa_no',
        name: 'visa_no',
        className: 'text-center'
      },
      {
        data: 'v_expiry_date',
        name: 'v_expiry_date',
        className: 'text-center'
      },
      {
        data: 'remaining_days',
        name: 'remaining_days',
        className: 'text-center'
      },
      ],
      order: [],
      pageLength: 24, // Set the default number of records to show
      lengthMenu: [10, 24, 25, 50, 100] // Options for records per page
    });
  });
</script>
@endsection