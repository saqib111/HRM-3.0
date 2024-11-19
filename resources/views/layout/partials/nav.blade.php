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
<!-- Sidebar -->
<div class="sidebar" id="sidebar">
    <div class="sidebar-inner slimscroll">
        <div id="sidebar-menu" class="sidebar-menu">
            <ul class="sidebar-vertical">
                <li class="menu-title">
                    <span>Main</span>
                </li>
                <li class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <a href="{{route('dashboard')}}"><i class="fa fa-home"></i> <span> Dashboard</span> </a>

                </li>
                <li class="{{ request()->routeIs('list.employee') ? 'active' : '' }}">
                    <a href="{{route('list.employee')}}"><i class="fa fa-user"></i> <span> Manage Employee</span> </a>

                </li>
                <li class="{{ request()->routeIs('roles-permissions.index') ? 'active' : '' }}">
                    <a href="{{route('roles-permissions.index')}}"><i class="fa fa-user-lock"></i> <span> Manage
                            Role</span>
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

                <li class="{{ request()->routeIs('create.team') ? 'active' : '' }}">
                    <a href="{{route('create.team')}}"><i class="fa fa-people-group"></i> <span>Manage Team</span>
                    </a>
                </li>
                <li class="{{ request()->routeIs('annual-leaves.index') ? 'active' : '' }}">
                    <a href="{{route('annual-leaves.index')}}"><i class="fa fa-user-clock"></i> <span>AL Balance</span>
                    </a>
                </li>

                <li class="{{ request()->routeIs('attendanceemployee.record') ? 'active' : '' }}">
                    <a href="{{route('attendanceemployee.record')}}"><i class="fa fa-user-check"></i>
                        <span>Attendance</span>
                    </a>
                </li>

                <!-- Leave Application -->
                <li class="menu-title">
                    <span>Leave Application</span>
                </li>
                <li class="{{ request()->routeIs('leave.form.show') ? 'active' : '' }}">
                    <a href="{{route('leave.form.show')}}"><i class="fa fa-person-walking-luggage"></i> <span> Leave
                            Apply</span> </a>
                </li>
                <li class="{{ request()->routeIs('leave_application.data') ? 'active' : '' }}">
                    <a href="{{route('leave_application.data')}}"><i class="fa fa-person-circle-exclamation"></i> <span>
                            Pending
                            Leaves</span> </a>
                </li>
                <li class="{{ request()->routeIs('leave_application.unassigned') ? 'active' : '' }}">
                    <a href="{{route('leave_application.unassigned')}}"><i class="fa fa-home"></i> <span> Unassigned
                            Leaves</span> </a>
                </li>
                <li class="{{ request()->routeIs('leave-approvals.index') ? 'active' : '' }}">
                    <a href="{{route('leave-approvals.index')}}"><i class="fa fa-person-circle-check"></i> <span>Leave
                            Approvals</span>
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