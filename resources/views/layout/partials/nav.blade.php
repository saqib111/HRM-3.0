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
                @if($user->role == 1 || in_array('dashboard', $permissions))
                    <li class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <a href="{{route('dashboard')}}"><i class="fa fa-home"></i> <span> Dashboard</span> </a>
                    </li>
                @endif
                {{-- Show "Add Employee" if the user has the "create_user" permission or is Superadmin --}}
                @if($user->role == 1 || in_array('show_users', $permissions))
                    <li class="{{ request()->routeIs('list.employee') ? 'active' : '' }}">
                        <a href="{{route('list.employee')}}"><i class="fa fa-user"></i> <span> Manage Employee</span> </a>
                    </li>
                @endif

                <li class="{{ request()->routeIs('attendanceemployee.record') ? 'active' : '' }}">
                    <a href="{{route('attendanceemployee.record')}}"><i class="fa fa-user-check"></i>
                        <span>Attendance</span>
                    </a>
                </li>

                <li class="menu-title">
                    <span>Employees</span>
                </li>
                <li class="{{ request()->routeIs('all.employees') ? 'active' : '' }}">
                    <a href="{{route('all.employees')}}"><i class="la la-key"></i> <span>Employee Info</span>
                    </a>
                </li>
                <li class="submenu">
                    <a href="#"><i class="fa fa-user"></i> <span> Departments</span> <span
                            class="menu-arrow"></span></a>
                    <ul>
                        <li>
                            <a href="{{ route('company.index')}}"
                                class="{{ request()->routeIs('company.index') ? 'active' : '' }}">Company</a>
                        </li>
                        <li>
                            <a href="{{ route('brand.index')}}"
                                class="{{ request()->routeIs('brand.index') ? 'active' : '' }}">Brand</a>
                        </li>
                        <li>
                            <a href="{{ route('department.index')}}"
                                class="{{ request()->routeIs('department.index') ? 'active' : '' }}">Department</a>
                        </li>

                        <li>
                            <a href="{{ route('designation.index')}}"
                                class="{{ request()->routeIs('designation.index') ? 'active' : '' }}">Designation</a>
                        </li>
                    </ul>
                </li>
                <li class="submenu ">
                    <a href="#"><i class="fa fa-calendar fa-1x"></i> <span>Manage Schedule</span> <span
                            class="menu-arrow"></span></a>
                    <ul>
                        <li><a class="{{ request()->routeIs('schedule') ? 'active' : '' }}"
                                href="{{ route('schedule')}}">Create Schedule</a></li>
                        <li><a class="{{ request()->routeIs('group.index') ? 'active' : '' }}"
                                href="{{ route('group.index')}}">Create Group</a></li>
                        <li><a class="{{ request()->routeIs('group.change') ? 'active' : '' }}"
                                href="{{ route('group.change')}}">Change Group Members</a></li>
                        <li><a class="{{ request()->routeIs('assign.employee') ? 'active' : '' }}"
                                href="{{ route('assign.employee')}}">Assign Group</a></li>
                        <li><a class="{{ request()->routeIs('add.holiday') ? 'active' : '' }}"
                                href="{{route('add.holiday')}}">Assign Offday</a></li>
                        <li><a class="{{ request()->routeIs('schedule.manage') ? 'active' : '' }}"
                                href="{{route('schedule.manage')}}">Manage Schedule</a></li>
                        <li><a class="{{ request()->routeIs('attendance.manage') ? 'active' : '' }}"
                                href="{{route('attendance.manage')}}">Manage Attendance</a></li>
                    </ul>
                </li>

                @if($user->role == 1 || in_array('show_teams', $permissions))
                    <li class="{{ request()->routeIs('create.team') ? 'active' : '' }}">
                        <a href="{{route('create.team')}}"><i class="fa fa-people-group"></i> <span>Manage Team</span>
                        </a>
                    </li>
                @endif
                @if($user->role == 1 || in_array('show_al_balance', $permissions))
                    <li class="{{ request()->routeIs('annual-leaves.index') ? 'active' : '' }}">
                        <a href="{{route('annual-leaves.index')}}"><i class="fa fa-user-clock"></i> <span>AL Balance</span>
                        </a>
                    </li>
                @endif
                <li class="{{ request()->routeIs('leave.form.show') ? 'active' : '' }}">
                    <a href="{{route('emp.list')}}"><i class="fa fa-home"></i> <span>Manage Shift</span>
                    </a>
                </li>

                <li class="{{ request()->routeIs('fingerprint-record.index') ? 'active' : '' }}">
                    <a href="{{route('fingerprint-record.index')}}"><i class="fa-solid fa-fingerprint"></i>
                        <span>Fingerprint Record</span>
                    </a>
                </li>

                <!-- Leave Application -->
                <li class="menu-title">
                    <span>Leave Application</span>
                </li>
                <li class="{{ request()->routeIs('leave_application.unassigned') ? 'active' : '' }}">
                    <a href="{{route('leave_application.unassigned')}}"><i class="fa fa-file"></i> <span>
                            Unassigned
                            Leaves</span> </a>
                </li>
                <li class="{{ request()->routeIs('leave.form.show') ? 'active' : '' }}">
                    <a href="{{route('leave.form.show')}}"><i class="fa fa-person-walking-luggage"></i> <span> Leave
                            Apply</span> </a>
                </li>
                <li class="{{ request()->routeIs('leave.status') ? 'active' : '' }}">
                    <a href="{{route('leave.status')}}"><i class="fa fa-person-walking-luggage"></i> <span> Leave
                            Status</span> </a>
                </li>
                <li class="{{ request()->routeIs('leave_application.data') ? 'active' : '' }}">
                    <a href="{{route('leave_application.data')}}"><i class="fa fa-person-circle-exclamation"></i> <span>
                            Pending
                            Leaves</span> </a>
                </li>
                <li class="{{ request()->routeIs('leave.hr_work') ? 'active' : '' }}">
                    <a href="{{route('leave.hr_work')}}"><i class="fa fa-users-cog"></i> <span>
                            HR Work
                            (Leaves)</span> </a>
                </li>
                <li class="{{ request()->routeIs('leave-approvals.index') ? 'active' : '' }}">
                    <a href="{{route('leave-approvals.index')}}"><i class="fa fa-person-circle-check"></i> <span>Leave
                            Approvals</span>
                    </a>
                </li>
                <li class="menu-title">
                    <span>Payroll</span>
                </li>
                <li class="{{ request()->routeIs('payroll.salary_deduction') ? 'active' : '' }}">
                    <a href="{{route('payroll.salary_deduction')}}"><i class="fa fa-money-bill-wave"></i> <span>Salary
                            Deduction</span>
                    </a>
                </li>

                <li class="menu-title">
                    <span>Visa Documents</span>
                </li>
                <li class="{{ request()->routeIs('expired-visa-information.index') ? 'active' : '' }}">
                    <a href="{{route('expired-visa-information.index')}}"><i class="fa fa-passport"></i> <span>Expired
                            Visa</span>
                    </a>
                </li>

            </ul>

        </div>
    </div>
</div>
<!-- /Sidebar -->