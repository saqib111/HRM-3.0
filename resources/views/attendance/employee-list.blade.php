@extends('layout.mainlayout')
@section('css')
@endsection
@section('content')
<div id="notification" aria-live="polite" aria-atomic="true"></div>
<div class="page-header">
    <div class="row align-items-center">
        <div class="col-md-4">
            <h3 class="page-title"><span data-translate="edit_schedule_employee_list">Edit schedule Employee List</span></h3>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a
                        href="{{ auth()->user()->role == '1' ? url('admin-dashboard') : url('attendance-employee') }}">Dashboard</a>
                </li>
                <li class="breadcrumb-item active"><span data-translate="employee_list">Employee List</span></li>
            </ul>
        </div>

    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="table-responsive">
            <table id="usersTable" class="table table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th><span data-translate="employee_id">Employee ID</span></th>
                        <th><span data-translate="name">Name</span></th>
                        <th><span data-translate="department">Department</span></th>
                        <th><span data-translate="action">Action</span></th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>
@endsection
@section('script-z')
<script>
    $(document).ready(function () {

        $('#usersTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('emp.edit') }}",
            columns: [
                { data: 'id', name: 'id' },
                { data: 'employee_id', name: 'Employee ID' },
                { data: 'username', name: 'name' },
                { data: 'department', name: 'department' },

                {
                    data: 'id',
                    name: 'Action',
                    orderable: false,
                    searchable: false,
                    render: function (data, type, row) {

                        return `<button class="btn btn-primary edit-button" data-id="${data}"><span data-translate="edit_schedule">Edit Schedule</span></button>`;
                    }
                },
            ],
            pageLength: 15, // Set the default number of records to show
            lengthMenu: [10, 15, 25, 50, 100] // Options for records per page
        });

        $(document).on('click', '.edit-button', function () {
            const userId = $(this).data('id');

            window.location.href = window.location.href = "{{route('edit.page', '')}}" + "/" + userId;
        })
    });

</script>
<!-- LANGUAGE SCRIPT -->
<script src="{{ asset('assets/js/switch.language.js') }}"></script>
@endsection