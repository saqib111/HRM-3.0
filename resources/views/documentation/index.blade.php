<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>HRM Documentation</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            height: 100vh;
            background-color: #eaf7fb;
            color: #333;
        }

        .sidebar {
            width: 250px;
            background-color: #00c5fb;
            color: white;
            display: flex;
            flex-direction: column;
            padding: 1rem;
            height: 100%;
            overflow: scroll;
        }

        .sidebar h2 {
            color: #ffffff;
            margin-bottom: 1rem;
        }

        .sidebar .menu-item {
            cursor: pointer;
            margin-bottom: 1rem;
            padding: 0.5rem;
            border: 1px solid #0288d1;
            border-radius: 5px;
            background-color: #0288d1;
            transition: all 0.3s ease;
        }

        .sidebar .menu-item:hover {
            background-color: #026699;
        }

        .sidebar .submenu {
            display: none;
            padding-left: 1rem;
            margin-top: 0.5rem;
            left: ;
        }

        .sidebar .submenu a {
            color: #ffffff;
            text-decoration: none;
            display: block;
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 0.5rem;

        }

        .sidebar .submenu a:hover {
            color: #f1bb07;
        }

        .sidebar .submenu a::before {
            content: "\2192";
            position: absolute;
            left: 10px;
            font-size: 18px;
            font-weight: bold;
            color: #13132c;
            transition: all 0.3s;
        }

        main {
            flex: 1;
            overflow-y: auto;
            padding: 2rem;
            background-color: #f9f9f9;
        }

        section {
            max-width: 80%;
            margin: 0 auto;
            padding: 1rem;
            height: 100vh;
            background: white;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            margin-bottom: 2rem;
            animation: fadeIn 1s ease;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .image-container {
            text-align: center;
            margin: 1rem 0;
        }

        .image-container img {
            max-width: 100%;
            height: auto;
            border: 2px solid #00c5fb;
            border-radius: 8px;
        }

        .video-button {
            display: inline-block;
            margin-top: 1rem;
            padding: 0.5rem 1rem;
            background-color: #00c5fb;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .video-button:hover {
            background-color: #026699;
        }

        .video-popup {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.9);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }

        .video-popup video {
            max-width: 90%;
            max-height: 90%;
            border: 2px solid #00c5fb;
            border-radius: 8px;
        }

        .video-popup .close-btn {
            position: absolute;
            top: 20px;
            right: 30px;
            color: white;
            font-size: 2rem;
            cursor: pointer;
            z-index: 1001;
        }

        .overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 999;
        }

        footer {
            text-align: center;
            padding: 1rem;
            background-color: #00c5fb;
            color: white;
            margin-top: 2rem;
        }

        button {
            background-color: #0288d1;
            border: none;
            height: 35px;
            border-radius: 5px;
            color: white;
            font-size: 14px;
            font-weight: bold;
            cursor: pointer;
        }
    </style>
    <script>
        function toggleSubmenu(id) {
            const submenu = document.getElementById(id);
            submenu.style.display =
                submenu.style.display === 'block' ? 'none' : 'block';
        }

        function showVideo(videoSrc) {
            const videoPopup = document.getElementById('videoPopup');
            const videoElement = document.getElementById('videoElement');

            videoElement.src = videoSrc;

            videoPopup.style.display = 'flex';

            videoElement.play();
        }

        function closeVideo() {
            const videoPopup = document.getElementById('videoPopup');
            const videoElement = document.getElementById('videoElement');

            videoElement.pause();
            videoElement.src = '';

            videoPopup.style.display = 'none';
        }

        document.addEventListener('DOMContentLoaded', () => {
            const videoPopup = document.getElementById('videoPopup');
            videoPopup.style.display = 'none';
        });
    </script>
</head>
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

<body>
    <div class="sidebar">

        <div class="menu-item" onclick="window.location.href='{{ route('attendanceemployee.record') }}'">
            Back to Home
        </div>

        <h2>Documentation</h2>
        @if(
                $user->role == 1 || in_array('show_users', $permissions) ||
                in_array('create_user', $permissions) ||
                in_array('update_user', $permissions) ||
                in_array('delete_user', $permissions)
            )
                    <div class="menu-item" onclick="toggleSubmenu('memp')">Manage Employee</div>
                    {{-- Manage Employee --}}
                    <div class="submenu" id="memp">
                        <a href="#manageemployee">Manage Employee</a>
                    </div>
        @endif

        <div class="menu-item" onclick="toggleSubmenu('att')">Attendance</div>
        {{-- Attendance --}}
        <div class="submenu" id="att">
            <a href="#attendance">Attendance</a>
        </div>

        @if($user->role == 1 || in_array('show_all_employee_info', $permissions))
            <div class="menu-item" onclick="toggleSubmenu('empin')">Employee Info</div>
            {{-- Employee Info --}}
            <div class="submenu" id="empin">
                <a href="#empinfo">Employee Info</a>
            </div>
        @endif

        @if($user->role == 1 || in_array('show_brands', $permissions) || in_array('show_departments', $permissions) || in_array('show_designations', $permissions))
            <div class="menu-item" onclick="toggleSubmenu('menu0')">
                Departments
            </div>
            {{-- manage departments --}}
            <div class="submenu" id="menu0">
                @if($user->role == 1 || in_array('show_brands', $permissions))
                    <a href="#brand">Brand</a>
                @endif
                @if($user->role == 1 || in_array('show_departments', $permissions))
                    <a href="#department">Department</a>
                @endif
                @if($user->role == 1 || in_array('show_designations', $permissions))
                    <a href="#designation">Designation</a>
                @endif
            </div>
        @endif

        @if($user->role == 1 || in_array('view_manage_shift', $permissions))
            <div class="menu-item" onclick="toggleSubmenu('menu1')">
                Manage Schedule
            </div>
            {{-- manage schedule --}}
            <div class="submenu" id="menu1">
                <a href="#module">Create Schedule</a>
                <a href="#module1">Create Group</a>
                <a href="#module2">Assign Group</a>
                <a href="#module3">Assign Offday</a>
                <a href="#module4">Change Group Members</a>
                <a href="#module5">Manage Schedule</a>
            </div>
        @endif

        @if($user->role == 1 || in_array('show_teams', $permissions))
            {{-- manage team --}}
            <div class="menu-item" onclick="toggleSubmenu('manageteam')">
                Manage Team
            </div>
            <div class="submenu" id="manageteam">
                <a href="#team">Manage Team</a>
            </div>
        @endif

        @if($user->role == 1 || in_array('show_al_balance', $permissions))
            {{-- AL Balance --}}
            <div class="menu-item" onclick="toggleSubmenu('albalance')">
                AL Balance
            </div>
            <div class="submenu" id="albalance">
                <a href="#al">AL Balance</a>
            </div>
        @endif

        @if($user->role == 1 || in_array('view_manage_shift', $permissions))
            {{-- Manage Shift --}}
            <div class="menu-item" onclick="toggleSubmenu('ms')">
                Manage Shift
            </div>
            <div class="submenu" id="ms">
                <a href="#msw">Manage Shift</a>
            </div>
        @endif

        @if($user->role == 1 || in_array('show_fingerprint_record', $permissions))
            <div class="menu-item" onclick="toggleSubmenu('menu3')">
                FingerPrint Record
            </div>
            {{-- FingerPrint Record --}}
            <div class="submenu" id="menu3">
                <a href="#finger">FingerPrint Record</a>

            </div>
        @endif


        <div class="menu-item" onclick="toggleSubmenu('menu2')">
            Leave Application
        </div>
        {{-- leave application --}}
        <div class="submenu" id="menu2">
            <a href="#AnnualLeavemodule">Create Annual Leave</a>
            {{-- <a href="#HalfLeavemodule1">Create Half-Day Leave</a>
            <a href="#OffDayLeavemodule2">OFF-Day</a> --}}
            <a href="#LeaveStatus">Leave Status</a>
        </div>


        @if(
                $user->role == 1 || in_array('unassigned_leaves', $permissions) ||
                in_array('pending_leaves', $permissions) ||
                in_array('hr_work', $permissions) ||
                in_array('revoked_leaves', $permissions) ||
                in_array('leave_approvals', $permissions)
            )
                    <div class="menu-item" onclick="toggleSubmenu('ml')">
                        Manage Leaves
                    </div>
                    {{-- manage leaves --}}
                    <div class="submenu" id="ml">
                        @if ($user->role == 1 || in_array('unassigned_leaves', $permissions))
                            <a href="#unassigned">Unassigned Leaves</a>
                        @endif
                        @if ($user->role == 1 || in_array('pending_leaves', $permissions))
                            <a href="#pending">Pending Leaves</a>
                        @endif
                        @if ($user->role == 1 || in_array('hr_work', $permissions))
                            <a href="#hr">HR Work</a>
                        @endif
                        @if ($user->role == 1 || in_array('revoked_leaves', $permissions))
                            <a href="#revoked">Revoked</a>
                        @endif
                        @if ($user->role == 1 || in_array('leave_approvals', $permissions))
                            <a href="#approval">Leave Approvals</a>
                        @endif
                    </div>
        @endif

        <div class="menu-item" onclick="toggleSubmenu('sd')">
            PayRoll
        </div>
        <div class="submenu" id="sd">
            <a href="#salaryde">Salary Deduction</a>

        </div>

        @if($user->role == 1 || in_array('expired_visa', $permissions))
            <div class="menu-item" onclick="toggleSubmenu('visa')">
                Visa Documents
            </div>
            <div class="submenu" id="visa">
                <a href="#expiredvisa">Expired Visa</a>

            </div>
        @endif
    </div>

    <main>
        {{-- Manage Employee --}}
        @if(
                $user->role == 1 || in_array('show_users', $permissions) ||
                in_array('create_user', $permissions) ||
                in_array('update_user', $permissions) ||
                in_array('delete_user', $permissions)
            )
                    <section id="manageemployee">
                        <h2 class="module-title">Manage Employee</h2>
                        <ol>
                            <li>Click on <strong>Manage Employee</strong> and you will the widow about Employee,
                                in the table.
                            </li>
                            <br />
                            <li>
                                You can Active Deactivate Emplyee status, Also Reset Password, manage Permisions, and Edit Info and
                                Delete the Employee.
                            </li>
                            <br />
                            <li>
                                In the table Action Section can perform <strong>Edit</strong>, <strong>Delete</strong>,
                                <strong>Reset Password</strong>, <strong>Active , Deactive</strong>, set the
                                <strong>Permissions.</strong> under the Action and Status section.
                            </li>

                        </ol>
                        <div class="image-container">
                            <img src="{{ asset('assets/img/documents/manageemployee.png') }}" alt="Create Group Example" />
                        </div>
                        <a class="video-button" onclick="showVideo('{{ url('assets/img/documents/videos/memp.mp4') }}')">Watch
                            Video</a>
                    </section>
        @endif
        {{-- attendance --}}
        <section id="attendance">
            <h2 class="module-title">Attendance</h2>
            <ol>
                <li>Click on <strong>Attendance</strong> new window will open about Attendance, in the table.</li>
                <br />
                <li> You can Punch In, Punch Out, Also can check your schedule </li>
                <br />
                <li>
                    And can check you deduction by setting fillter
                </li>

            </ol>
            <div class="image-container">
                <img src="{{ asset('assets/img/documents/attendance.png') }}" alt="Create Group Example" />
            </div>
            <a class="video-button" onclick="showVideo('{{ url('assets/img/documents/videos/attendance.mp4') }}')">Watch
                Video</a>
        </section>

        {{-- Employee Info --}}
        @if($user->role == 1 || in_array('show_all_employee_info', $permissions))
            <section id="empinfo">
                <h2 class="module-title">Employee Info</h2>
                <ol>
                    <li>Click on <strong>Employee Info</strong> new window will open about All Employee Profile, in the
                        table.</li>
                    <br />
                    <li> Just click on eye icon under Action section and check Employee profile </li>
                    <br />
                    <li>
                        And also you can edit Family Information bottom of the page
                    </li>

                </ol>
                <div class="image-container">
                    <img src="{{ asset('assets/img/documents/emp_inf.png') }}" alt="Create Group Example" />
                </div>
                <a class="video-button" onclick="showVideo('{{ url('assets/img/documents/videos/empinfo.mp4') }}')">Watch
                    Video</a>
            </section>
        @endif

        @if($user->role == 1 || in_array('show_brands', $permissions) || in_array('show_departments', $permissions) || in_array('show_designations', $permissions))

            {{-- manage brands --}}
            @if($user->role == 1 || in_array('show_brands', $permissions))
                <section id="brand">
                    <h2 class="module-title">Manage Brand</h2>
                    <ol>
                        <li>Click on <strong>Departments</strong> below in the list select <strong>Brand</strong></li>
                        <br />
                        <li>
                            You can Edit Delete also Add Brands with the given functionality below.
                        </li>
                        <br />
                        <li>
                            In the table Action Section can perform <strong>Edit</strong>, <strong>Delete</strong> Brand
                        </li>
                        <br />
                        <li>
                            For Add New <strong>Brand</strong> Hit the top the Right <strong>Add Brand</strong> Button,
                            and fill the Editable field with desire brand name. hit the submit button.
                        </li>
                    </ol>
                    <div class="image-container">
                        <img src="{{ asset('assets/img/documents/brand.png') }}" alt="Create Group Example" />
                    </div>
                    <a class="video-button" onclick="showVideo('{{ url('assets/img/documents/videos/brands.mp4') }}')">Watch
                        Video</a>
                </section>
            @endif

            {{-- manage department --}}
            @if($user->role == 1 || in_array('show_departments', $permissions))
                <section id="department">
                    <h2 class="module-title">Manage Department</h2>
                    <ol>
                        <li>Click on <strong>Departments</strong> below in the list select <strong>Department</strong></li>
                        <br />
                        <li>
                            You can Edit Delete also Add Departments with the given functionality below.
                        </li>
                        <br />
                        <li>
                            In the table Action Section can perform <strong>Edit</strong>, <strong>Delete</strong> Department
                        </li>
                        <br />
                        <li>
                            For Add New <strong>Department</strong> Hit the top the Right <strong>Add Department</strong>
                            Button,
                            and fill the Editable field with desire department name. hit the submit button.
                        </li>
                    </ol>
                    <div class="image-container">
                        <img src="{{ asset('assets/img/documents/department.png') }}" alt="Create Group Example" />
                    </div>
                    <a class="video-button"
                        onclick="showVideo('{{ url('assets/img/documents/videos/departments.mp4') }}')">Watch
                        Video</a>
                </section>
            @endif

            {{-- manage designation --}}
            @if($user->role == 1 || in_array('show_designations', $permissions))
                <section id="designation">
                    <h2 class="module-title">Manage Designation</h2>
                    <ol>
                        <li>Click on <strong>Departments</strong> below in the list select <strong>Designation</strong></li>
                        <br />
                        <li>
                            You can Edit Delete also Add Designations with the given functionality below.
                        </li>
                        <br />
                        <li>
                            In the table Action Section can perform <strong>Edit</strong>, <strong>Delete</strong> Designation
                        </li>
                        <br />
                        <li>
                            For Add New <strong>Designation</strong> Hit the top the Right <strong>Add Designation</strong>
                            Button,
                            and fill the Editable field with desire designation name. hit the submit button.
                        </li>
                    </ol>
                    <div class="image-container">
                        <img src="{{ asset('assets/img/documents/designation.png') }}" alt="Create Group Example" />
                    </div>
                    <a class="video-button"
                        onclick="showVideo('{{ url('assets/img/documents/videos/designation.mp4') }}')">Watch
                        Video</a>
                </section>
            @endif
        @endif

        @if($user->role == 1 || in_array('view_manage_shift', $permissions))
            {{-- manage schedule --}}
            <section id="module">
                <h2 class="module-title">Module 1: Create Schedule</h2>
                <ol>
                    <li>Click on <strong>Manage Schedule</strong>.</li>
                    <br />
                    <li>
                        Select <strong>Create Schedule</strong> from the
                        dropdown.
                    </li>
                    <br />
                    <li>
                        Click <strong>Add Schedule</strong> (+ icon) to open the
                        form.
                    </li>
                    <br />
                    <li>
                        Enter the schedule details and click
                        <strong>Submit</strong>.
                    </li>
                </ol>
                <div class="image-container">
                    <img src="{{ asset('assets/img/documents/createschedule.png') }}" alt="Create Group Example" />
                </div>
                <a class="video-button"
                    onclick="showVideo('{{ url('assets/img/documents/videos/howtocreateshedule.mp4') }}')">Watch
                    Video</a>
            </section>

            <section id="module1">
                <h2 class="module-title">Module 2: How Create Group</h2>

                <ol>
                    <li>Click <strong>Manage Schedule</strong>.</li>
                    <br />
                    <li>Choose <strong>Create Group</strong>.</li>
                    <br />
                    <li>Click <strong>Add Group</strong> to open the form.</li>
                    <br />
                    <li>
                        Write Group Name &
                        <strong>Select the Assigner Name</strong>.
                    </li>
                    <br />
                    <li>
                        Fill in the group details and click
                        <strong>Submit</strong>.
                    </li>
                </ol>
                <div class="image-container">
                    <img src="{{ asset('assets/img/documents/creategroup.png') }}" alt="Create Group Example" />
                </div>
                <a class="video-button" onclick="showVideo('assets/img/documents/videos/howtocreategroup.mp4')">Watch
                    Video</a>
            </section>

            <section id="module2">
                <h2 class="module-title">Module 3:How Assign Group</h2>
                <ol>
                    <li>Click <strong>Manage Schedule</strong>.</li>
                    <br />
                    <li>Choose <strong>Assign Group</strong>.</li>
                    <br />
                    <li>
                        Click <strong>The Icon Under Action Section</strong> to
                        open the form.
                    </li>
                    <br />
                    <li>Fill the Assign Group <strong>Submit</strong>.</li>
                </ol>
                <div class="image-container">
                    <img src="{{ asset('assets/img/documents/assigngroup.png') }}" alt="Assign Group Example" />
                </div>
                <a class="video-button" onclick="showVideo('assets/img/documents/videos/howtoassigngroup.mp4')">Watch
                    Video</a>
            </section>

            <section id="module3">
                <h2 class="module-title">Module 4: How Assign Offday</h2>
                <ol>
                    <li>Click <strong>Manage Schedule</strong>.</li>
                    <br />
                    <li>Choose <strong>Assign Offdays</strong>.</li>
                    <br />
                    <li>Click <strong>Add Offday</strong> to open the form.</li>
                    <br />
                    <li>
                        Fill in the Assigner name and select the dates and click
                        <strong>Submit</strong>.
                    </li>
                </ol>
                <div class="image-container">
                    <img src="{{ asset('assets/img/documents/assignofdays.png') }}" alt="Assign Offday Example" />
                </div>
                <a class="video-button" onclick="showVideo('assets/img/documents/videos/offdays.mp4')">Watch
                    Video</a>
            </section>

            <section id="module4">
                <h2 class="module-title">Module 5: How Change Group Members</h2>
                <ol>
                    <li>Click <strong>Manage Schedule</strong>.</li>
                    <br />
                    <li>Choose <strong>Change Group Members</strong>.</li>
                    <br />
                    <li>
                        Click <strong>Change Group</strong> Under the Change
                        section
                    </li>
                    <br />
                    <li>
                        Select the group want change with and
                        <strong>Submit</strong>.
                    </li>
                </ol>
                <div class="image-container">
                    <img src="{{ asset('assets/img/documents/changemember.png') }}" alt="Change Members Example" />
                </div>
                <a class="video-button" onclick="showVideo('assets/img/documents/videos/changegroup.mp4')">Watch
                    Video</a>
            </section>

            <section id="module5">
                <h2 class="module-title">Module 6: Manage Schedule</h2>
                <ol>
                    <li>Click <strong>Manage Schedule</strong>.</li>
                    <br />
                    <li>Choose <strong>Manage Schedule</strong>.</li>
                    <br />
                    <li>
                        Click The<strong> Button</strong> Status/Active section
                    </li>
                    <br />
                    <li>Turn or Off to change<strong>Status</strong>.</li>
                </ol>
                <div class="image-container">
                    <img src="{{ asset('assets/img/documents/manageschedule.png') }}" alt="Annual Leave" />
                </div>
                <a class="video-button" onclick="showVideo('assets/img/documents/videos/manageshedule.mp4')">Watch
                    Video</a>
            </section>
        @endif

        {{-- manage team --}}

        @if($user->role == 1 || in_array('show_teams', $permissions))
            <section id="team">
                <h2 class="module-title">Manage Team</h2>
                <ol>
                    <li>Click on <strong>Manage Team</strong>.</li>
                    <br />
                    <li>
                        Top right bottom of navbar <strong>Add Team</strong> Button click on it.
                        and fill with first field with leader name, second field with multiple employees.
                    </li>
                    <br />
                    <li>
                        Hit the submit to save team. also can <strong>Delete</strong> and <strong>Edit</strong> Teams,
                        in the Action section in table.
                    </li>

                </ol>
                <div class="image-container">
                    <img src="{{ asset('assets/img/documents/manageteam.png') }}" alt="Create Group Example" />
                </div>
                <a class="video-button" onclick="showVideo('{{ url('assets/img/documents/videos/manageteam.mp4') }}')">Watch
                    Video</a>
            </section>
        @endif

        @if($user->role == 1 || in_array('show_al_balance', $permissions))
            {{-- AL Balance --}}
            <section id="al">
                <h2 class="module-title">AL Balance</h2>
                <ol>
                    <li>Click on <strong>AL Balance</strong>.</li>
                    <br />
                    <li>
                        You can see the employee (Name, Leave Type, Leave Balance) also Last Year Balance.
                        for Edit you just click the Button in Action section, and fill with updated record.
                    </li>
                    <br />
                    <li>
                        And Then Click Update Button and update Leave of Current Employee.
                    </li>

                </ol>
                <div class="image-container">
                    <img src="{{ asset('assets/img/documents/albalance.png') }}" alt="Create Group Example" />
                </div>
                <a class="video-button" onclick="showVideo('{{ url('assets/img/documents/videos/albalance.mp4') }}')">Watch
                    Video</a>
            </section>
        @endif


        @if($user->role == 1 || in_array('view_manage_shift', $permissions))
            {{-- Manage Shift --}}
            <section id="msw">
                <h2 class="module-title">Manage Shift</h2>
                <ol>
                    <li>Click on <strong>Manage Shift</strong> then you can see all the Emplyees in the tabel and last at
                        right side Edit Schedule in Action Section.</li>
                    <br />
                    <li>
                        Click on Edit Schedule and then you can edit schedule delete shedule also bulk delete.
                    </li>

                </ol>
                <div class="image-container">
                    <img src="{{ asset('assets/img/documents/manageshift.png') }}" alt="Create Group Example" />
                </div>
                <a class="video-button"
                    onclick="showVideo('{{ url('assets/img/documents/videos/manageshift.mp4') }}')">Watch
                    Video</a>
            </section>
        @endif

        @if($user->role == 1 || in_array('show_fingerprint_record', $permissions))
            {{-- Fingerprint Record --}}
            <section id="finger">
                <h2 class="module-title">FingerPrint Record</h2>
                <ol>
                    <li>Click on <strong>FingerPrint Record</strong> then you can see all the Emplyees FingerPrint Records.
                        also can Edit, Delete</li>
                    <br />
                    <li>
                        also can add fillter to search specific.
                    </li>

                </ol>
                <div class="image-container">
                    <img src="{{ asset('assets/img/documents/createschedule.png') }}" alt="Create Group Example" />
                </div>
                <a class="video-button"
                    onclick="showVideo('{{ url('assets/img/documents/videos/fingerprint.mp4') }}')">Watch
                    Video</a>
            </section>
        @endif

        {{-- Leave Application --}}
        <section id="AnnualLeavemodule">
            <h2 class="module-title">Leave Module 1: Leave Apply</h2>
            <ol>
                <li>Click on <strong>Leave Apply</strong>.</li>
                <br />
                <li>
                    Click <strong> Write Leave Title</strong> inSide the
                    Box.
                </li>
                <br />

                <li>Click <strong> Write Description </strong></li>
                <br />

                <li>
                    Click
                    <strong>On Plus Sign Full-Day Leave</strong> Fill The
                    data
                </li>

                <br />
                <li>
                    Click Select The <strong>Category </strong>
                    <strong>Annual Leave </strong> Select
                    <strong>From </strong> Date <strong>To</strong> Date
                </li>
                <br />
                <li>
                    Submit the Annual Leave and click
                    <strong>Submit</strong>.
                </li>
            </ol>
            <div class="image-container">
                <img src="{{ asset('assets/img/documents/annual_leave.png') }}" alt="Create Annual Leave " />
            </div>
            <a class="video-button"
                onclick="showVideo('{{ url('assets/img/documents/videos/leaveapply.mp4') }} ')">Watch
                Video</a>
        </section>

        <section id="HalfLeavemodule1">
            <h2 class="module-title">
                Leave Module 2: How Create Half Day Leave
            </h2>

            <ol>
                <li>Click on <strong>Leave Apply</strong>.</li>
                <br />
                <li>
                    Click <strong> Write Leave Title</strong> inSide the
                    Box.
                </li>
                <br />

                <li>Click <strong> Write Description </strong></li>
                <br />

                <li>
                    Click
                    <strong>On Plus Sign Half-Day Leave </strong> Fill The
                    data
                </li>

                <br />
                <li>
                    Click
                    <strong>Half-Day Leave </strong> Select
                    <strong> Date </strong> select Time
                    <strong>Start Time </strong> and
                    <strong>End Time</strong> Select
                </li>
                <br />
                <li>
                    Submit the Half-Day Leave and click
                    <strong>Submit</strong>.
                </li>
            </ol>
            <div class="image-container">
                <img src="{{ asset('assets/img/documents/Half Day.png') }}" alt="Leave Half Day" />
            </div>
            <a class="video-button" onclick="showVideo('videos/howtocreategroup.mp4')">Watch Video</a>
        </section>

        <section id="OffDayLeavemodule2">
            <h2 class="module-title">Leave Module 3:How Apply OFF-Day</h2>
            <ol>
                <li>Click on <strong>Leave Apply</strong>.</li>
                <br />
                <li>
                    Click <strong> Write Leave Title</strong> inSide the
                    Box.
                </li>
                <br />

                <li>Click <strong> Write Description </strong></li>
                <br />

                <li>
                    Click <strong>On Plus Sign OFF-Day </strong> Fill The
                    data
                </li>

                <br />
                <li>
                    Click
                    <strong>OFF-Day </strong> Select
                    <strong> Date </strong>
                </li>
                <br />
                <li>
                    Submit the OFF-Day and click
                    <strong>Submit</strong>.
                </li>
            </ol>
            <div class="image-container">
                <img src="{{ asset('assets/img/documents/OFF Day.png') }}" alt="Leave OFF Day" />
            </div>
            <a class="video-button" onclick="showVideo('videos/howtoassigngroup.mp4')">Watch Video</a>
        </section>

        <section id="LeaveStatus">
            <h2 class="module-title">
                Leave Module 4: How Look Leave Status
            </h2>
            <ol>
                <li>Click <strong>Leave Status</strong>.</li>
                <br />
                <li>
                    Click <strong>On Action Button</strong> Open Modal and
                    You Look <strong>Leave Status</strong>.
                </li>
                <br />
                <li>
                    It's <strong>Pendding</strong>
                    <strong>Approved</strong> or <strong>Rejected</strong>.
                </li>
                <br />
                <li>
                    Fill in the Assigner name and select the dates and click
                    <strong>Submit</strong>.
                </li>
            </ol>
            <div class="image-container">
                <img src="{{ asset('assets/img/documents/Leave Status.png') }}" alt="Leave Status " />
            </div>
            <a class="video-button"
                onclick="showVideo('{{ asset('assets/img/documents/videos/leavestatus.mp4') }}')">Watch
                Video</a>
        </section>


        {{-- Manage Leaves --}}
        @if ($user->role == 1 || in_array('unassigned_leaves', $permissions))
            <section id="unassigned">
                <h2 class="module-title">
                    Unassigned Leaves
                </h2>
                <ol>
                    <li>click on <strong>Unassigned leave</strong>.</li>

                    <li>
                        <p>click on Assign Button</p>
                    </li>

                    <li>
                        <p>select First Assigner Name and second Assigner name</p>
                    </li>

                    <li>
                        <p><strong>Click Submit</strong></p>
                    </li>

                </ol>
                <div class="image-container">
                    <img src="{{ asset('assets/img/documents/unassigned.png') }}" alt="Leave Status" />
                </div>
                <a class="video-button"
                    onclick="showVideo('{{ asset('assets/img/documents/videos/unassigned.mp4') }}')">Watch
                    Video
                </a>
            </section>
        @endif

        @if ($user->role == 1 || in_array('pending_leaves', $permissions))
            <section id="pending">
                <h2 class="module-title">
                    Pending Leaves
                </h2>
                <ol>
                    <li>
                        <p>Click On <strong>Pending Leave</strong></p>
                    </li>

                    <li>
                        <p> Click on Eye Icon in Table</p>
                    </li>

                    <li>
                        <p>Click Approved if Accepted And Click Decline if you Rejected</p>
                    </li>

                    <li>
                        Fill in the Assigner name and select the dates and click
                        <strong>Submit</strong>.
                    </li>
                    <li>
                        <p>Also You check the <strong>Approved</strong> / <strong>Rejected</strong> and revoked leaves </p>
                    </li>
                </ol>
                <div class="image-container">
                    <img src="{{ asset('assets/img/documents/Leave Status.png') }}" alt="Leave Status" />
                </div>
                <a class="video-button" onclick="showVideo('{{ asset('assets/img/documents/videos/pendingl.mp4') }}')">Watch
                    Video
                </a>
            </section>
        @endif

        {{-- hr work --}}
        @if ($user->role == 1 || in_array('hr_work', $permissions))
            <section id="hr">
                <h2 class="module-title">
                    HR Work(Leave)
                </h2>
                <ol>
                    <li>
                        <p>Click On HR Work( Leave ) (By default Show All Pending Leave)</p>
                    </li>

                    <li>
                        <p> Click on Eye Icon in Table if Pending leave are Exist</p>
                    </li>

                    <li>
                        <p>Click on HR Task Button</p>
                    </li>

                    <li>
                        <p> Click on Eye Icon in Table</p>
                    </li>
                    <li>
                        <p>Also You check the <strong>Approved</strong> / <strong>Rejected</strong> and revoked leaves </p>
                    </li>
                    <li>
                        <p>Click Button HR Task Done </p>
                    </li>
                </ol>
                <div class="image-container">
                    <img src="{{ asset('assets/img/documents/hrwork.png') }}" alt="Leave Status" />
                </div>
                <a class="video-button" onclick="showVideo('{{ asset('assets/img/documents/videos/hrtask.mp4') }}')">Watch
                    Video
                </a>
            </section>
        @endif

        {{-- revoked leaves --}}
        @if($user->role == 1 || in_array('revoked_leaves', $permissions))
            <section id="revoked">
                <h2 class="module-title">
                    Revoked(Leave)
                </h2>
                <ol>
                    <li>
                        <p>Click On Revoked(leave) (By default Show All Approved Leave)</p>
                    </li>

                    <li>
                        <p> Click On Eye Icon in Table</p>
                    </li>

                    <li>
                        <p>Click On Revoked Button To Revoked Application</p>
                    </li>

                    <li>
                        <p> Also You check the All Revoked Leave Click On Revoked Button on Page</p>
                    </li>

                </ol>
                <div class="image-container">
                    <img src="{{ asset('assets/img/documents/revoked.png') }}" alt="Leave Status" />
                </div>
                <a class="video-button" onclick="showVideo('{{ asset('assets/img/documents/videos/revokedl.mp4') }}')">Watch
                    Video
                </a>
            </section>
        @endif

        {{-- leave approval --}}
        @if($user->role == 1 || in_array('leave_approvals', $permissions))
            <section id="approval">
                <h2 class="module-title">
                    Leave Approvals
                </h2>
                <ol>
                    <li>
                        <p>Click on Leave approvals</p>
                    </li>

                    <li>
                        <p>Click on Assign Button</p>
                    </li>

                    <li>
                        <p>select First Assigner Name: And Also Select Second Assigner Name</p>
                    </li>

                    <li>
                        <p> Click On Submit</p>
                    </li>

                </ol>
                <div class="image-container">
                    <img src="{{ asset('assets/img/documents/approvals.png') }}" alt="Leave Status" />
                </div>
                <a class="video-button"
                    onclick="showVideo('{{ asset('assets/img/documents/videos/leaveapprovals.mp4') }}')">Watch
                    Video
                </a>
            </section>
        @endif

        {{-- payrolls --}}
        <section id="salaryde">
            <h2 class="module-title">
                Salary Deduction
            </h2>
            <ol>
                <li>
                    <p>Click on Salary Deduction</p>
                </li>

                <li>
                    <p>Set the filter <strong>Date Range</strong>, Nationality, Office, and click filter</p>
                </li>

                <li>
                    <p>also you can download the excel sheet</p>
                </li>


            </ol>
            <div class="image-container">
                <img src="{{ asset('assets/img/documents/salaryde.png') }}" alt="Leave Status" />
            </div>
            <a class="video-button" onclick="showVideo('{{ asset('assets/img/documents/videos/salaryde.mp4') }}')">Watch
                Video
            </a>
        </section>

        @if($user->role == 1 || in_array('expired_visa', $permissions))
            <section id="expiredvisa">
                <h2 class="module-title">
                    Visa Documents
                </h2>
                <ol>
                    <li>
                        <p>Click on Expired Visa</p>
                    </li>

                    <li>
                        After click on Expired Visa you can see all the Employees those are gets Visa Expired.
                    </li>
                    <li>
                        and stay up to Date
                    </li>

                </ol>
                <div class="image-container">
                    <img src="{{ asset('assets/img/documents/visaexpire.png') }}" alt="Leave Status" />
                </div>
                <a class="video-button" onclick="showVideo('{{ asset('assets/img/documents/videos/visaexp.mp4') }}')">Watch
                    Video
                </a>
            </section>
        @endif
        <!-- Video Popup -->
        <div id="videoPopup" class="video-popup">
            <span class="close-btn" onclick="closeVideo()">&times;</span>
            <video id="videoElement" controls></video>
        </div>

        {{-- <footer>
            <p>&copy; 2024 USP Technology Company. All Rights Reserved.</p>
        </footer> --}}
    </main>
</body>

</html>