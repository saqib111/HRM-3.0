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
        }

        .sidebar .submenu a {
            color: #ffffff;
            text-decoration: none;
            display: block;
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 0.5rem;
        }

        .sidebar .submenu a:hover {
            color: #ffd700;
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

<body>
    <div class="sidebar">

        <div class="menu-item" onclick="window.location.href='{{ route('dashboard') }}'">
            Back to Home
        </div>


        <h2>Documentation</h2>
        {{-- Manage Emplyee --}}
        {{-- <div class="menu-item" onclick="toggleSubmenu('menu4')">
            Manage Emplyeeee
        </div>
        <div class="submenu" id="menu4">
            <a href="#addemployee">Create/Add Employee</a>
            <a href="#updatepass">Edit Password</a>
            <a href="#assignroles">Assign & Edit Permission</a>
            <a href="#editemployee">Edit Employee</a>
            <a href="#deletemployee">Delete Employee</a>
        </div> --}}

        <div class="menu-item" onclick="toggleSubmenu('menu1')">
            Manage Schedule
        </div>
        <div class="submenu" id="menu1">
            <a href="#module">Create Schedule</a>
            <a href="#module1">Create Group</a>
            <a href="#module2">Assign Group</a>
            <a href="#module3">Assign Offday</a>
            <a href="#module4">Change Group Members</a>
            <a href="#module5">Manage Schedule</a>
        </div>

        {{-- <div class="menu-item" onclick="toggleSubmenu('menu2')">
            Leave Application
        </div>
        <div class="submenu" id="menu2">
            <a href="#AnnualLeavemodule">Create Annual Leave</a>
            <a href="#HalfLeavemodule1">Create Half-Day Leave</a>
            <a href="#OffDayLeavemodule2">OFF-Day</a>
            <a href="#LeaveStatus">Leave Status</a>
        </div>
        <div class="menu-item" onclick="toggleSubmenu('menu3')">
            Manage Leaves
        </div>
        <div class="submenu" id="menu3">
            <a href="#unassigned">Unassigned Leaves</a>
            <a href="#pending">Pending Leaves</a>
            <a href="#hr">HR Work</a>
            <a href="#revoked">Revoked</a>
            <a href="#approval">Leave Approvals</a>
        </div> --}}
    </div>

    <main>

        {{-- ADD EMPLOYEE --}}

        {{-- <section id="addemployee">
            <h2 class="module-title">
                Add Employee
            </h2>
            <ol>
                <li>
                    <p>Click on Manage Employee</p>
                </li>

                <li>
                    <p>Click on Add Employee Button</p>
                </li>

                <li>
                    <p>Insert in Fields ( Employee ID , Username , Email , Joining Date , Password , Confirm Password,
                        Company , Department, Designation ,Brand ), Choose Profile Image
                    </p>
                </li>
                <li>
                    <p>Select Annual Leaves</p>
                </li>
                <li>
                    <p> Click On Submit</p>
                </li>

            </ol>
            <div class="image-container">
                <img src="{{ asset('assets/img/documents/Add_Employee.png') }}" alt="Leave Status" />
            </div>
            <a class="video-button"
                onclick="showVideoPopup('{{ asset('assets/img/documents/videos/main annual leave recording.mp4') }}')">Watch
                Video
            </a>
        </section>

        <section id="updatepass">
            <h2 class="module-title">Edit Password</h2>
            <ol>
                <li>
                    <p>Clcik on Manage Employee</p>
                </li>
                <li>
                    <p>Click on Lock Icon </p>
                </li>
                <li>
                    <p>Write Password In Text Field</p>
                </li>
                <li>
                    <p>Write Confirm Password In Text Field</p>
                </li>
                <li>
                    <p> Click On Update</p>
                </li>

            </ol>
            <div class="image-container">
                <img src="{{ asset('assets/img/documents/passwaord_change.png') }}" alt="Leave Status" />
            </div>
            <a class="video-button" onclick="showVideoPopup('{{ asset('') }}')">Watch
                Video
            </a>
        </section>


        <section id="assignroles">
            <h2 class="module-title">
                Add Employee
            </h2>
            <ol>
                <li>
                    <p>Click on Manage Employee</p>
                </li>

                <li>
                    <p>Click on Key Icon</p>
                </li>

                <li>
                    <p>Select Options from( Manage Employee , Manage Brand , Manage Department , Manage Designation ,
                        Manage Team , Manage Shift , Annual Leave Blance , FingerPrint Record , manage Leave , Employee
                        Info , Visa Document ) and check or uncheck to assign permissions
                    </p>
                </li>


                <li>
                    <p> Click On Save Changes</p>
                </li>

            </ol>
            <div class="image-container">
                <img src="{{ asset('assets/img/documents/assign$editpermission1.png') }}" alt="Leave Status" />
            </div>
            <a class="video-button" onclick="showVideoPopup('{{ asset('') }}')">Watch
                Video
            </a>
        </section>

        <section id="editemployee">
            <h2 class="module-title">
                Add Employee
            </h2>
            <ol>
                <li>
                    <p>Click on Manage Employee</p>
                </li>

                <li>
                    <p>Click on Edit Icon</p>
                </li>

                <li>
                    <p>Edit in Fields ( Employee ID , Username , Email , Joining Date )
                    </p>
                </li>
                <li>
                    <p>Select (Company , Department , Designation , Brand ) Option </p>
                </li>

                <p>Choose Profile Image </p>
                </li>

                <li>
                    <p> Click On Update</p>
                </li>

            </ol>
            <div class="image-container">
                <img src="{{ asset('assets/img/documents/edit_employee.png') }}" alt="Leave Status" />
            </div>
            <a class="video-button" onclick="showVideoPopup('{{ asset('') }}')">Watch
                Video
            </a>
        </section>


        <section id="deletemployee">
            <h2 class="module-title">Delete Employee</h2>
            <ol>
                <li>
                    <p>Clcik on Manage Employee</p>
                </li>
                <li>
                    <p>Click on Delete Icon </p>
                </li>

                <li>
                    <p> Click On Delete Button in popUp</p>
                </li>

            </ol>
            <div class="image-container">
                <img src="{{ asset('assets/img/documents/deleteemployee.png') }}" alt="Leave Status" />
            </div>
            <a class="video-button" onclick="showVideoPopup('{{ asset('') }}')">Watch
                Video
            </a>
        </section> --}}


        {{-- Manage Schedule --}}


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
            <a class="video-button" onclick="showVideoPopup('video3')">Watch Video</a>
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
            <a class="video-button" onclick="showVideoPopup('video4')">Watch Video</a>
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
            <a class="video-button" onclick="showVideoPopup('video5')">Watch Video</a>
        </section>





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
