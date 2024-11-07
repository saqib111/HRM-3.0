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
                <li class="menu-title">
                    <span>Employees</span>
                </li>
                <li class="submenu ">
                    <a href="#" class="noti-dot"><i class="fa fa-user"></i> <span> Employees</span> <span
                            class="menu-arrow"></span></a>
                    <ul>
                        <li><a class="{{ request()->routeIs('list.employee') ? 'active' : '' }}"
                                href="{{ route('list.employee')}}">All Employees</a></li>
                    </ul>
                </li>
                <li class="menu-title">
                    <span>Departments</span>
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

                <li class="menu-title">
                    <span>Team Leader Team</span>
                </li>
                <li class="submenu">
                    <a href="#"><i class="fa fa-user"></i> <span>Team</span> <span class="menu-arrow"></span></a>
                    <ul>
                        <li>
                            <a href="{{ route('create.team')}}"
                                class="{{ request()->routeIs('company.index') ? 'active' : '' }}">Create Team</a>
                        </li>

                    </ul>
                </li>

                <li class="menu-title">
                    <span>Schedule</span>
                </li>
                <li class="submenu ">
                    <a href="#"><i class="fa fa-calendar fa-1x"></i> <span> Schedule</span> <span
                            class="menu-arrow"></span></a>
                    <ul>
                        <li><a class="{{ Request::is('employees', 'employees-list') ? 'active' : '' }}"
                                href="{{ route('schedule')}}">Create Schedule</a></li>
                        <li><a class="{{ Request::is('groups', 'group-show') ? 'active' : '' }}"
                                href="{{ route('group.index')}}">Create Group</a></li>
                        <li><a class="{{ Request::is('employees', 'employees-list') ? 'active' : '' }}"
                                href="{{ route('group.change')}}">Change Group Members</a></li>
                        <li><a class="{{ Request::is('employees', 'employees-list') ? 'active' : '' }}"
                                href="{{ route('assign.employee')}}">Assign Group</a></li>
                        <li><a class="{{ Request::is('employees', 'employees-list') ? 'active' : '' }}"
                                href="{{route('add.holiday')}}">Assign Offday</a></li>
                        <li><a class="{{ Request::is('employees', 'employees-list') ? 'active' : '' }}"
                                href="{{route('schedule.manage')}}">Manage Schedule</a></li>
                        <li><a class="{{ Request::is('employees', 'employees-list') ? 'active' : '' }}"
                                href="{{route('attendance.manage')}}">Manage Attendance</a></li>
                    </ul>
                </li>
                <li class="menu-title">
                    <span>Attendance</span>
                </li>
                <li class="{{ request()->routeIs('leave.form.show') ? 'active' : '' }}">
                    <a href="{{route('attendanceemployee.record')}}"><i class="fa fa-home"></i> <span>Attendance</span>
                    </a>
                </li>
                <!-- Roles & Permissions -->
                <li class="menu-title">
                    <span>Roles and Permissions</span>
                </li>
                <li class="{{ request()->routeIs('roles-permissions.index') ? 'active' : '' }}">
                    <a href="{{route('roles-permissions.index')}}"><i class="la la-key"></i> <span>Roles and
                            Permissions</span>
                    </a>
                </li>
                <!-- Leave Application -->
                <li class="menu-title">
                    <span>Leave Application</span>
                </li>
                <li class="{{ request()->routeIs('leave.form.show') ? 'active' : '' }}">
                    <a href="{{route('leave.form.show')}}"><i class="fa fa-home"></i> <span> Leave Apply</span> </a>
                </li>
                <li class="{{ request()->routeIs('leave_application.data') ? 'active' : '' }}">
                    <a href="{{route('leave_application.data')}}"><i class="fa fa-home"></i> <span> Pending
                            Leaves</span> </a>
                </li>

                <!-- Wasif Created -->
                <li class="menu-title">
                    <span>All Employees Profile</span>
                </li>
                <li class="{{ request()->routeIs('all.employees') ? 'active' : '' }}">
                    <a href="{{route('all.employees')}}"><i class="la la-key"></i> <span>All Employees</span>
                    </a>
                </li>

                <li class="menu-title">
                    <span>Leave Approvals</span>
                </li>
                <li class="{{ request()->routeIs('leave-approvals.index') ? 'active' : '' }}">
                    <a href="{{route('leave-approvals.index')}}"><i class="la la-key"></i> <span>Leave Approvals</span>
                    </a>
                </li>


                <li class="menu-title">
                    <span>Leave Balance</span>
                </li>
                <li class="{{ request()->routeIs('annual-leaves.index') ? 'active' : '' }}">
                    <a href="{{route('annual-leaves.index')}}"><i class="la la-key"></i> <span>Leave Balance</span>
                    </a>
                </li>


                <li class="menu-title">
                    <span>Visa Documents</span>
                </li>
                <li class="{{ request()->routeIs('expired-visa-information.index') ? 'active' : '' }}">
                    <a href="{{route('expired-visa-information.index')}}"><i class="la la-key"></i> <span>Expired
                            Visa</span>
                    </a>
                </li>
            </ul>

        </div>
    </div>
</div>
<!-- /Sidebar -->