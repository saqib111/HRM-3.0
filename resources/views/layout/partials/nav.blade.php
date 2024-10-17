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
                    <span>Schedule</span>
                </li>
                <li class="submenu ">
                    <a href="#"><i class="fa fa-calendar fa-1x"></i> <span> Schedule</span> <span
                            class="menu-arrow"></span></a>
                    <ul>
                        <li><a class="{{ request()->routeIs('schedule') ? 'active' : '' }}"
                                href="{{ route('schedule')}}">Create Schedule</a></li>
                    </ul>
                </li>
                <li class="menu-title">
                    <span>Leave Application</span>
                </li>
                <li class="{{ request()->routeIs('leave.form.show') ? 'active' : '' }}">
                    <a href="{{route('leave.form.show')}}"><i class="fa fa-home"></i> <span> Leave Apply</span> </a>
                </li>
            </ul>

        </div>
    </div>
</div>
<!-- /Sidebar -->