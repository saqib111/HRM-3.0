@foreach($employees as $employee)
<tr>
    <td>{{ $employee->first_name }} {{ $employee->last_name }}</td>
    <td>{{ $employee->employee_id }}</td>
    <td>{{ $employee->email }}</td>
    <td>{{ $employee->phone }}</td>
    <td>{{ $employee->joining_date }}</td>
    <td>{{ $employee->position }}</td>
    <td class="text-end">
        <div class="dropdown">
            <a href="#" class="action-icon dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="fa fa-ellipsis-v"></i>
            </a>
            <div class="dropdown-menu dropdown-menu-right">
                <a class="dropdown-item" href="#">
                    <i class="fa fa-pencil me-2"></i> Edit
                </a>
                <a class="dropdown-item" href="#">
                    <i class="fa fa-trash me-2"></i> Deletesdfs
                </a>
            </div>
        </div>
    </td>
</tr>
@endforeach
