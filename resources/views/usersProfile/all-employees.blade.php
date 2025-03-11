@extends('layout.mainlayout')

@section('content')
    @php
        $user = auth()->user();
        $permissions = getUserPermissions($user);

        $companies = DB::table('companies')->where('status', '1')->pluck('name')->toArray();

        $allowed_groups = $companies;
        $matching_groups = array_intersect($allowed_groups, $permissions);
        $group_based_permission = !empty($matching_groups) ? array_values($matching_groups) : [];
    @endphp

    <!-- Header -->
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col-md-4">
                <h3 class="page-title">All Employees Profile</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{ auth()->user()->role == 1 ? url('admin-dashboard') : url('attendance-employee') }}">
                            Dashboard
                        </a>
                    </li>
                    <li class="breadcrumb-item active">All Employees</li>
                </ul>
            </div>

            <!-- Show company buttons -->
            @if(isset($company) && count($company) > 0)
                <div class="col-md-8 d-flex justify-content-end">
                    @foreach($company as $index => $item)
                        @if($user->role == 1 || in_array($item['name'], $group_based_permission))
                            <button class="btn btn-outline-primary mx-1 company-btn {{ $index === 0 ? 'active' : '' }}"
                                onclick="filterByCompany('{{ $item['name'] }}')">
                                {{ $item['name'] }}
                            </button>
                        @endif
                    @endforeach
                </div>
            @endif
        </div>
    </div>
    <!-- Header End -->

    <!-- Employee Table -->
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
                    <tbody id="all-employee-list"></tbody>
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
        let table; // Declare globally

        function initializeDataTable(companyName) {
            const columns = [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'employee_id', name: 'users.employee_id', orderable: false },
                { data: 'real_name', name: 'user_profiles.real_name', orderable: false },
                { data: 'email', name: 'users.email', orderable: false },
                { data: 'company_name', name: 'companies.name', orderable: false },
                { data: 'visa_no', name: 'visa_infos.visa_no', orderable: false },
                { data: 'passport_no', name: 'visa_infos.passport_no', orderable: false },
                { data: 'nationality', name: 'user_profiles.nationality', orderable: false },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false,
                    render: function (data, type, row) {
                        return `
                                <div class="text-center" style="cursor:pointer;" 
                                    onmouseover="this.querySelector('i').style.color='#0272D9'" 
                                    onmouseout="this.querySelector('i').style.color='#000'">
                                    <a href="/view-user-profile/${row.id}" title="View">
                                        <i class="ion-eye" style="font-size: 24px; color: #000;"></i>
                                    </a>
                                </div>
                            `;
                    }
                }
            ];

            // Destroy existing table instance if it exists
            if ($.fn.DataTable.isDataTable('#employees-table')) {
                table.destroy();
            }

            table = $('#employees-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('all.employees') }}",
                    type: 'GET',
                    data: { company: companyName } // Pass selected company name
                },
                columns: columns, // Use dynamically defined columns
                order: [],
                pageLength: 14,
                lengthMenu: [10, 14, 25, 50, 100] // Records per page options
            });
        }

        // ✅ Global function for filtering by company
        function filterByCompany(companyName) {
            if ($.fn.DataTable.isDataTable('#employees-table')) {
                table.destroy();
            }
            initializeDataTable(companyName);

            // Update active button
            $('.company-btn').removeClass('active');
            $('.company-btn:contains("' + companyName + '")').addClass('active');
        }

        $(document).ready(function () {
            $('.tagging').select2({ tags: true });

            // ✅ Get first company's name and initialize table
            const defaultCompanyName = $('.company-btn:first').text().trim();
            initializeDataTable(defaultCompanyName);
        });
    </script>
@endsection