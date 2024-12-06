@extends('layout.mainlayout')
@section('css')
@endsection
@section('content')
<div id="notification" aria-live="polite" aria-atomic="true"></div>
<div class="page-header">
    <div class="row align-items-center">
        <div class="col-md-4">
            <h3 class="page-title">Edit schedule Employee List </h3>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="admin-dashboard.html">Dashboard</a></li>
                <li class="breadcrumb-item active"> Employee List</li>
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
                        <th>Employee ID</th>
                        <th>Name</th>
                        <th>Department</th>
                        <th>Action</th>
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

                        return `<button class="btn btn-primary edit-button" data-id="${data}">Edit Schedule</button>`;
                    }
                }
            ],
        });

        $(document).on('click', '.edit-button', function () {
            const userId = $(this).data('id');

            window.location.href = window.location.href = "{{route('edit.page', '')}}" + "/" + userId;
        })
    });

</script>
@endsection