<style>
    #custom_leave_box {
        border: 1px solid #e9e9ea;
        margin-bottom: -1px !important;
        padding: 10px;
    }

    /* HOVER EFFECTS */
    .leave-info-box {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        /* Smooth transition for hover effect */
    }

    /* The hover effect when mouse enters */
    .leave-info-box.hovered {
        transform: scale(1.03);
        /* Slight zoom effect */
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        /* Adds a shadow to the box */
        background-color: #f8f9fa;
        /* Optional: Change the background color */
    }

    /* Optional: change cursor to pointer when hovering */
    .leave-info-box.hovered {
        cursor: pointer;
    }

    /* Ensure the profile section stays aligned to the right */
    .navbar-nav {
        display: flex;
        justify-content: space-between;
        width: 100%;
    }
</style>
<!-- Header -->
<div class="header">

    <!-- Logo -->
    <div class="header-left">
        <a href="{{ url('admin-dashboard') }}" class="logo">
            <img src="{{ URL::asset('/assets/img/logo.svg') }}" alt="Logo">
        </a>
        <a href="{{ url('admin-dashboard') }}" class="logo collapse-logo">
            <img src="{{ asset('/assets/img/collapse-logo.svg') }}" alt="Logo">
        </a>
        <a href="{{ url('admin-dashboard') }}" class="logo2">
            <img src="{{ asset('/assets/img/logo2.png') }}" width="40" height="40" alt="Logo">
        </a>
    </div>
    <!-- /Logo -->

    <a id="toggle_btn" href="javascript:void(0);">
        <span class="bar-icon">
            <span></span>
            <span></span>
            <span></span>
        </span>
    </a>


    <a id="mobile_btn" class="mobile_btn" href="#sidebar"><i class="fa-solid fa-bars"></i></a>

    <!-- Header Menu -->
    <ul class="nav user-menu d-flex justify-content-end flex-nowrap">
        @php
            $user = auth()->user();
        @endphp

        @if($user->role == 1 || $user->role == 2 || $user->role == 3)
            <!-- Search -->
            <li class="nav-item" id="search_bar">
                <div class="top-nav-search">
                    <a href="javascript:void(0);" class="responsive-search">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </a>
                    <form id="leave-search-form">
                        @csrf
                        <input type="hidden" id="user-id" value="{{ auth()->user()->id }}">
                        <input id="employee-name" class="form-control" type="text" placeholder="Search here">
                        <button id="search-button" class="btn" type="submit"><i
                                class="fa-solid fa-magnifying-glass"></i></button>
                    </form>
                    <div id="search-results-dropdown" class="dropdown-menu mt-0"
                        style="display: none; width: 85%; background:#FFFF; padding:0px ">
                        <!-- DYNAMIC CONTENT -->
                    </div>
                </div>
            </li>
        @endif
        <!-- /Search -->

        <li class="nav-item dropdown has-arrow main-drop">
            <a href="#" class="dropdown-toggle nav-link" data-bs-toggle="dropdown">
                <span class="user-img"><img src="{{ URL::asset('/uploads/' . auth()->user()->image) }}"
                        alt="User Image">
                    <span class="status online"></span></span>
                <span>{{auth()->user()->username}}</span>
            </a>
            <div class="dropdown-menu">
                <a class="dropdown-item" href="{{ route('user-profile.customDetails', Auth::id()) }}">My Profile</a>
                <a class="dropdown-item" href="{{ url('settings') }}">Settings</a>
                <form method="post" action="{{url('logout')}}">
                    @csrf
                    <button class="dropdown-item" style="color:#00c5fb;" type="submit">Logout</button>
                </form>
            </div>
        </li>
    </ul>
    <!-- /Header Menu -->

    <!-- Mobile Menu -->
    <div class="dropdown mobile-user-menu">
        <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false"><i
                class="fa-solid fa-ellipsis-vertical"></i></a>
        <div class="dropdown-menu dropdown-menu-right">
            <a class="dropdown-item" href="{{ route('user-profile.customDetails', Auth::id()) }}">My Profile</a>
            <a class="dropdown-item" href="{{ url('settings') }}">Settings</a>
            <form method="post" action="{{url('logout')}}">
                @csrf
                <button class="dropdown-item" type="submit">Logout</button>
            </form>
        </div>
    </div>
    <!-- /Mobile Menu -->

</div>
<!-- /Header -->