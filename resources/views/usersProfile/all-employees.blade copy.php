@extends('layout.mainlayout')
@section('content')
    @php
        $user = auth()->user();
        $permissions = getUserPermissions($user); // Use the helper function to fetch permissions
        // Check if the user has at least one of the required permissions or is a superadmin
        $hasActionPermission = $user->role == 1 ||
            in_array('update_user', $permissions) ||
            in_array('change_password', $permissions) ||
            in_array('delete_user', $permissions) ||
            in_array('manage_permissions', $permissions);

        $allowed_groups = ['Group A', 'Group B'];
        $matching_groups = array_intersect($allowed_groups, $permissions);

        if (!empty($matching_groups)) {
            $group_based_permission = array_values($matching_groups);
        } else {
            $group_based_permission = [];
        }

      @endphp
    <!-- ---Header ---- -->
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col-md-4">
                <h3 class="page-title">All Employees Profile</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a
                            href="{{ auth()->user()->role == '1' ? url('admin-dashboard') : url('attendance-employee') }}">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item active">All Employees</li>
                    <li>
                        @if(auth()->user()->role === "1" || !empty($company))
                            <div class="d-flex justify-content-end">
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
                    </li>
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

            // Get the first company's name
            const defaultCompanyName = $('.company-btn:first').text().trim();
            initializeDataTable(defaultCompanyName);

            let table;
            function initializeDataTable(companyName) {
                const columns: [
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

                                    // Add the "Status" column if the user has permission
                                    if (canUpdateStatus) {
            columns.push({
                data: 'status',
                name: 'status',
                orderable: false,
                render: function (data, type, row) {
                    let checked = (data === '1') ? 'checked' : '';
                    return `<div class="status-toggle-container" style="display:flex;">
                                <div class="status-toggle">
                                    <input type="checkbox" id="staff_module_${row.id}" class="check" ${checked} onchange="toggleStatus(${row.id}, this)">
                                    <label for="staff_module_${row.id}" class="checktoggle">checkbox</label>
                                </div>
                            </div>`;
                }
            });
        }

        table = $('#users_table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('all.employees') }}",
                type: 'GET',
                data: { company: companyName } // Pass the selected company name
            },
            columns: columns, // Use the dynamically defined columns
            order: [],
            pageLength: 14, // Set the default number of records to show
            lengthMenu: [10, 14, 25, 50, 100] // Options for records per page
        });
                                }

        // const table = $('#employees-table').DataTable({
        //     processing: true,
        //     serverSide: true,
        //     ajax: {
        //         url: "{!{ route('all.employees') }}",
        //         type: 'GET'
        //     },
        //     columns: [
        //         {
        //             data: null,
        //             render: function (data, type, row, meta) {
        //                 return meta.row + meta.settings._iDisplayStart + 1;
        //             },
        //             orderable: false,
        //             searchable: false // Typically, the index column isn't searchable
        //         },
        //         {
        //             data: 'employee_id',
        //             name: 'users.employee_id' // Specify the table name for clarity
        //         },
        //         {
        //             data: 'real_name',
        //             name: 'user_profiles.real_name' // Specify the table name for clarity
        //         },
        //         {
        //             data: 'email',
        //             name: 'users.email' // Specify the table name for clarity
        //         },
        //         {
        //             data: 'company_name',
        //             name: 'companies.name' // Specify the table name for clarity
        //         },
        //         {
        //             data: 'visa_no',
        //             name: 'visa_infos.visa_no' // Specify the table name for clarity
        //         },
        //         {
        //             data: 'passport_no',
        //             name: 'visa_infos.passport_no' // Specify the table name for clarity
        //         },
        //         {
        //             data: 'nationality',
        //             name: 'user_profiles.nationality' // Specify the table name for clarity
        //         },
        //         {
        //             data: 'action',
        //             orderable: false,
        //             searchable: false, // Actions usually aren't searchable
        //             render: function (data, type, row) {
        //                 return `
        //                         <div class="text-center" style="cursor:pointer;" onmouseover="this.querySelector('i').style.color='#0272D9'" 
        //                         onmouseout="this.querySelector('i').style.color='#000'">
        //                         <a href="/view-user-profile/${row.id}" title="View">
        //                         <i class="ion-eye" style="font-size: 24px; color: #000;"></i>
        //                         </a>
        //                         </div>
        //                       `;
        //             }
        //         }
        //     ],
        //     order: [],
        //     pageLength: 20, // Set the default number of records to show
        //     lengthMenu: [10, 20, 25, 50, 100] // Options for records per page
        //     // Add additional options for paging, lengthChange, etc., as needed
        // });

        // Function to filter data by company
        function filterByCompany(companyName) {
            // Clear existing data and destroy the DataTable
            if (table) {
                table.destroy();
            }

            // Re-initialize the DataTable with the selected company
            initializeDataTable(companyName);

            // Remove active class from all buttons and add to the clicked button
            $('.company-btn').removeClass('active');
            $('.company-btn:contains("' + companyName + '")').addClass('active');
        }
                            });
    </script>

@endsection