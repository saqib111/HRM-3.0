<style>
    .sidebar .sidebar-menu ul li a i,
    .two-col-bar .sidebar-menu ul li a i{

        font-size:16px!important;
    }
    .sidebar .sidebar-menu ul ul li a.active::before
     {
        background: #00c5fb;
    }
    .sidebar .sidebar-menu ul li.menu-title span:before, .two-col-bar .sidebar-menu ul li.menu-title span:before {
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
                <li >
                    <a href="{{route('dashboard')}}"><i class="fa fa-home"></i> <span> Dashboard</span> </a>
                   
                </li>
                <li class="menu-title">
                    <span>Employees</span>
                </li>
                <li class="submenu ">
                    <a href="#" class="noti-dot"><i class="fa fa-user"></i> <span> Employees</span> <span
                            class="menu-arrow"></span></a>
                    <ul>
                        <li><a class="{{ Request::is('employees', 'employees-list') ? 'active' : '' }}"
                                href="{{ route('list.employee')}}">All Employees</a></li>
                    </ul>
                </li>
                <li class="menu-title">
                    <span>Schedule</span>
                </li>
                <li class="submenu ">
                    <a href="#" class="noti-dot"><i class="fa fa-calendar fa-1x"></i> <span> Schedule</span> <span
                            class="menu-arrow"></span></a>
                    <ul>
                        <li><a class="{{ Request::is('employees', 'employees-list') ? 'active' : '' }}"
                                href="{{ route('schedule')}}">Create Schedule</a></li>
                    </ul>
                </li>
            </ul>

        </div>
    </div>
</div>
<!-- /Sidebar -->