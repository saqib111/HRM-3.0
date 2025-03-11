<style>
    .sidebar .sidebar-menu ul li a i,
    .two-col-bar .sidebar-menu ul li a i {

        font-size: 16px !important;
    }

    .sidebar .sidebar-menu ul ul li a.active::before {
        background: #00c5fb;
    }

    .sidebar .sidebar-menu ul li.menu-title span:before,
    .two-col-bar .sidebar-menu ul li.menu-title span:before {
        background: #00c5fb;
    }

    .notification-badge {
        position: absolute;
        top: 50%;
        right: 40px;
        background-color: red;
        color: white;
        border-radius: 50%;
        font-size: 11px;
        width: 25px;
        text-align: center;
        height: 25px;
        padding: 4px 2px 0px 0px;
        font-weight: bold;
        transform: translate(50%, -50%);
    }

    .hidden {
        display: none;
    }
</style>

@php
    $user = auth()->user();
    $permissions = getUserPermissions($user); // Use the helper function to fetch permissions
    // Check if the user has at least one of the required permissions or is a superadmin

    $hasActionPermission = $user->role == 1 ||
        in_array('show_users', $permissions) ||
        in_array('update_user', $permissions) ||
        in_array('change_password', $permissions) ||
        in_array('delete_user', $permissions) ||
        in_array('manage_permissions', $permissions);
@endphp


<!-- Sidebar -->
<div class="sidebar" id="sidebar">
    <div class="sidebar-inner slimscroll">
        <div id="sidebar-menu" class="sidebar-menu">
            <ul class="sidebar-vertical">
                <li class="menu-title">
                    <span>Main</span>
                </li>
                {{-- Show "Add Employee" if the user has the "create_user" permission or is Superadmin --}}
                @if($user->role == 1 || ($user->role == 2 && in_array('dashboard', $permissions)))
                    <li class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <a href="{{route('dashboard')}}"><i class="fa fa-home"></i> <span> Dashboard</span> </a>
                    </li>
                @endif
                {{-- Show "Add Employee" if the user has the "create_user" permission or is Superadmin --}}
                @if($user->role == 1 || ($user->role == 2 && in_array('show_users', $permissions)))
                    <li class="{{ request()->routeIs('list.employee') ? 'active' : '' }}">
                        <a href="{{route('list.employee')}}"><i class="fa fa-user"></i> <span> Manage Employee</span> </a>
                    </li>
                @endif

                <li class="{{ request()->routeIs('attendanceemployee.record') ? 'active' : '' }}">
                    <a href="{{route('attendanceemployee.record')}}"><i class="fa fa-user-check"></i>
                        <span data-translate="attendance">Attendance</span>
                    </a>
                </li>

                <!-- SCHEDULES -->
                @if($user->role == 1 || in_array('view_manage_shift', $permissions))
                    <li class="menu-title">
                        <span data-translate="manage_schedule">Manage Schedule</span>
                    </li>
                    <li class="submenu">
                        <a href="#"><i class="fa fa-cloud-arrow-up"></i><span data-translate="upload_schedule">Upload
                                Schedule</span><span class="menu-arrow"></span></a>
                        <ul>
                            <li class="{{ request()->routeIs('importshcedule') ? 'active' : '' }}">
                                <a href="{{ url('importshcedule') }}"><i class="fa fa-newspaper"></i>
                                    <span data-translate="import_excel_file">Import Excel File</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li class="submenu ">
                        <a href="#"><i class="fa fa-calendar fa-1x"></i> <span data-translate="manage_schedule">Manage
                                Schedule</span> <span class="menu-arrow"></span></a>
                        <ul>
                            <li><a class="{{ request()->routeIs('schedule') ? 'active' : '' }}"
                                    href="{{ route('schedule')}}"><span data-translate="create_schedule" class="ms-0">Create
                                        Schedule</span></a></li>
                            <li><a class="{{ request()->routeIs('group.index') ? 'active' : '' }}"
                                    href="{{ route('group.index')}}"><span data-translate="create_group" class="ms-0">Create
                                        Group</span></a></li>
                            <li><a class="{{ request()->routeIs('assign.employee') ? 'active' : '' }}"
                                    href="{{ route('assign.employee')}}"><span data-translate="assign_group"
                                        class="ms-0">Assign Group</span></a></li>
                            <li><a class="{{ request()->routeIs('add.holiday') ? 'active' : '' }}"
                                    href="{{route('add.holiday')}}"><span data-translate="assign_offday" class="ms-0">Assign
                                        Offday</span></a></li>
                            <li><a class="{{ request()->routeIs('group.change') ? 'active' : '' }}"
                                    href="{{ route('group.change')}}"><span data-translate="change_group_members"
                                        class="ms-0">Change Group Members</span></a></li>
                            <li><a class="{{ request()->routeIs('schedule.manage') ? 'active' : '' }}"
                                    href="{{route('schedule.manage')}}"><span data-translate="manage_schedule"
                                        class="ms-0">Manage Schedule</span></a></li>
                        </ul>
                    </li>
                @endif
                <!-- SCHEDULES -->
                @if(
                    $user->role == 1 || in_array('show_all_employee_info', $permissions)
                    || in_array('show_brands', $permissions)
                    || in_array('show_departments', $permissions)
                    || in_array('show_designations', $permissions)
                    || in_array('view_manage_shift', $permissions)
                    || in_array('show_teams', $permissions)
                    || in_array('show_al_balance', $permissions)
                    || in_array('show_fingerprint_record', $permissions)
                )
                                    <li class="menu-title">
                                        <span data-translate="employees">Employees</span>
                                    </li>
                @endif
                @if($user->role == 1 || ($user->role == 2 && in_array('show_all_employee_info', $permissions)))
                    <li class="{{ request()->routeIs('all.employees') ? 'active' : '' }}">
                        <a href="{{route('all.employees')}}"><i class="la la-key"></i> <span>Employee Info</span>
                        </a>
                    </li>
                @endif
                @if($user->role == 1 || in_array('show_brands', $permissions) || in_array('show_departments', $permissions) || in_array('show_designations', $permissions))
                        <li class="submenu">
                            <a href="#"><i class="fa fa-user"></i> <span> Departments</span> <span
                                    class="menu-arrow"></span></a>
                            <ul>
                        </li>
                        @if($user->role == 1 || in_array('show_brands', $permissions))
                            <li>
                                <a href="{{ route('brand.index')}}"
                                    class="{{ request()->routeIs('brand.index') ? 'active' : '' }}">Brand</a>
                            </li>
                        @endif
                        @if($user->role == 1 || in_array('show_departments', $permissions))
                            <li>
                                <a href="{{ route('department.index')}}"
                                    class="{{ request()->routeIs('department.index') ? 'active' : '' }}">Department</a>
                            </li>
                        @endif
                        @if($user->role == 1 || in_array('show_designations', $permissions))
                            <li>
                                <a href="{{ route('designation.index')}}"
                                    class="{{ request()->routeIs('designation.index') ? 'active' : '' }}">Designation</a>
                            </li>
                        @endif
                    </ul>
                    </li>
                @endif
            @if($user->role == 1 || in_array('show_teams', $permissions))
                <li class="{{ request()->routeIs('create.team') ? 'active' : '' }}">
                    <a href="{{route('create.team')}}"><i class="fa fa-people-group"></i> <span>Manage Team</span>
                    </a>
                </li>
            @endif
            @if($user->role == 1 || in_array('show_al_balance', $permissions))
                <li class="{{ request()->routeIs('annual-leaves.index') ? 'active' : '' }}">
                    <a href="{{route('annual-leaves.index')}}"><i class="fa fa-user-clock"></i>
                        <span data-translate="nav_al_balance">AL Balance</span>
                    </a>
                </li>
            @endif
            @if($user->role == 1 || in_array('view_manage_shift', $permissions))
                <li class="{{ request()->routeIs('emp.list') ? 'active' : '' }}">
                    <a href="{{route('emp.list')}}"><i class="fa fa-home"></i>
                        <span data-translate="manage_shift">Manage Shift</span>
                    </a>
                </li>
            @endif
            @if($user->role == 1 || in_array('show_fingerprint_record', $permissions))
                <li class="{{ request()->routeIs('fingerprint-record.index') ? 'active' : '' }}">
                    <a href="{{route('fingerprint-record.index')}}"><i class="fa-solid fa-fingerprint"></i>
                        <span data-translate="fingerprint_record">Fingerprint Record</span>
                    </a>
                </li>
            @endif
            @if($user->role == 1 || in_array('show_late_employee_details', $permissions))
                <li class="{{ request()->routeIs('late.employee') ? 'active' : '' }}">
                    <a href="{{route('late.employee')}}"><i class="fa-solid fa-user-clock"></i>
                        <span data-translate="late_employee_details">Late Employee Details</span>
                    </a>
                </li>
            @endif
            <!-- Leave Application -->
            <li class="menu-title">
                <span data-translate="leave_application">Leave Application</span>
            </li>
            <li class="{{ request()->routeIs('leave.form.show') ? 'active' : '' }}">
                <a href="{{route('leave.form.show')}}"><i class="fa fa-person-walking-luggage"></i><span
                        data-translate="leave_application">Leave Apply</span></a>
            </li>
            <li class="{{ request()->routeIs('leave.status') ? 'active' : '' }}">
                <a href="{{route('leave.status')}}"><i class="fa fa-person-walking-luggage"></i><span span
                        data-translate="leave_status">Leave Status</span></a>
            </li>
            @if(
                $user->role == 1 || in_array('unassigned_leaves', $permissions) ||
                in_array('pending_leaves', $permissions) ||
                in_array('hr_work', $permissions) ||
                in_array('revoked_leaves', $permissions) ||
                in_array('leave_approvals', $permissions)
            )
                            <li class="menu-title">
                                <span>Manage Leaves</span>
                            </li>
            @endif
            @if ($user->role == 1 || in_array('unassigned_leaves', $permissions))
                <li class="{{ request()->routeIs('leave_application.unassigned') ? 'active' : '' }}">
                    <a href="{{ route('leave_application.unassigned') }}" style="position: relative;">
                        <i class="fa fa-file"></i>
                        <span>Unassigned Leaves</span>
                        <span id="notifibadge" class="notification-badge hidden"></span>
                    </a>
                </li>
            @endif
            @if ($user->role == 1 || in_array('pending_leaves', $permissions))
                <li class="{{ request()->routeIs('leave_application.data') ? 'active' : '' }}">
                    <a href="{{ route('leave_application.data') }}"><i class="fa fa-person-circle-exclamation"></i>
                        <span data-translate="pending_leaves">Pending Leaves</span>
                        <span id="pendingLeavesBadge" class="notification-badge hidden"></span>
                    </a>
                </li>
            @endif
            @if ($user->role == 1 || in_array('hr_work', $permissions))
                <li class="{{ request()->routeIs('leave.hr_work') ? 'active' : '' }}">
                    <a href="{{ route('leave.hr_work') }}"><i class="fa fa-users-cog"></i> <span>
                            HR Work
                            (Leaves)</span>
                        <span id="hrpendingbadge" class="notification-badge hidden"></span> </a>
                </li>
            @endif
            @if($user->role == 1 || in_array('revoked_leaves', $permissions))
                <li class="{{ request()->routeIs('revoked_leave.index') ? 'active' : '' }}">
                    <a href="{{route('revoked_leave.index')}}"><i class="fa fa-users-cog"></i> <span>
                            Revoked
                            (Leaves)</span> </a>
                </li>
            @endif
            @if($user->role == 1 || in_array('leave_approvals', $permissions))
                <li class="{{ request()->routeIs('leave-approvals.index') ? 'active' : '' }}">
                    <a href="{{route('leave-approvals.index')}}"><i class="fa fa-person-circle-check"></i> <span>Leave
                            Approvals</span>
                    </a>
                </li>
            @endif
            @if ($user->role == 1 || in_array('search_leaves', $permissions))
                <li>
                    <a href="{{route('show.searchleaves')}}"><i class="fa-brands fa-searchengin"></i> <span
                            data-translate="search_leaves">Customize Search Leaves</span>
                    </a>
                </li>
            @endif
            <!-- IT ACCESS -->
            @if($user->role == 1 || in_array('view_whitelist_IPs', $permissions) || in_array('view_manage_IP_restrictions', $permissions))
                <li class="menu-title">
                    <span>IP Restrictions</span>
                </li>
                @if($user->role == 1 || in_array('view_whitelist_IPs', $permissions))
                    <li class="{{ request()->routeIs('view') ? 'active' : '' }}">
                        <a href="{{route('view')}}"><i class="fa fa-user-check"></i>
                            <span>Whitelist IPs</span>
                        </a>
                    </li>
                @endif
                @if($user->role == 1 || in_array('view_manage_IP_restrictions', $permissions))
                    <li class="{{ request()->routeIs('view.manageIPs') ? 'active' : '' }}">
                        <a href="{{route('view.manageIPs')}}"><i class="fa fa-user-check"></i>
                            <span>Manage IP Restrictions</span>
                        </a>
                    </li>
                @endif
            @endif
            <li class="menu-title">
                <span data-translate="payroll"><span>Payroll</span></span>
            </li>
            <li class="{{ request()->routeIs('payroll.salary_deduction') ? 'active' : '' }}">
                <a href="{{route('payroll.salary_deduction')}}"><i class="fa fa-money-bill-wave"></i><span span
                        data-translate="salary_deduction">Salary Deduction</span>
                </a>
            </li>
            @if($user->role == 1 || in_array('expired_visa', $permissions))
                <li class="menu-title">
                    <span>Visa Documents</span>
                </li>
                <li class="{{ request()->routeIs('expired-visa-information.index') ? 'active' : '' }}">
                    <a href="{{route('expired-visa-information.index')}}"><i class="fa fa-passport"></i> <span>Expired
                            Visa</span>
                    </a>
                </li>
            @endif

            <li class="menu-title">
                <span>Documentation</span>
            </li>
            <li class="{{ request()->routeIs('expired-visa-information.index') ? 'active' : '' }}">
                <a href="{{ route('documentation') }}"><i class="fa fa-newspaper"></i>
                    <span>System
                        Documentation</span>
                </a>
            </li>
            </ul>

        </div>
    </div>
</div>
<!-- /Sidebar -->