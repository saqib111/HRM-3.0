<!DOCTYPE html>
<html lang="en" data-layout="vertical" data-topbar="light" data-sidebar="dark" data-sidebar-size="lg"
    data-sidebar-image="none">

<head>
    <meta charset="utf-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Smarthr - Bootstrap Admin Template">
    <meta name="keywords"
        content="admin, estimates, bootstrap, business, corporate, creative, management, minimal, modern, accounts, invoice, html5, responsive, CRM, Projects">
    <meta name="author" content="Dreamstechnologies - Bootstrap Admin Template">
    <title>Change Password - HRMS admin template</title>
    <!-- Favicon -->
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('/assets/img/favicon.png') }}">

    <link rel="stylesheet" href="{{ asset('/assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('/assets/plugins/fontawesome/css/fontawesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('/assets/plugins/fontawesome/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('/assets/css/line-awesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('/assets/css/material.css') }}">
    <link rel="stylesheet" href="{{ asset('/assets/css/style.css') }}">

    <style>
        .container1 {
            min-height: 40vh;
            display: flex;
            justify-content: center;
            align-items: center;
            margin-right: 100%;
            padding-right: 100px;
            margin-top: 30%;
        }


        .scanner {
            position: relative;
            width: 150px;
            height: 200px;
            border-radius: 15px;
            overflow: hidden;
            padding: 150px;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .fingerprint {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 150px;
            height: 150px;
            background: repeating-radial-gradient(circle,
                    #fff 0%,
                    #fff 5%,
                    transparent 5%,
                    transparent 10%);
            border-radius: 50%;
            animation: pulse 1.5s infinite ease-in-out;
        }

        .scan-line {
            position: absolute;
            top: 50%;
            /* Vertically centered */
            left: 20%;
            /* Horizontally centered with 60% width */
            transform: translateY(-50%);
            width: 60%;
            /* 60% of the scanner's width */
            height: 4px;
            /* Line thickness */
            background: #00ff7f;
            animation: scan 3s infinite linear;
        }

        @keyframes pulse {

            0%,
            100% {
                transform: translate(-50%, -50%) scale(1);
                opacity: 1;
            }

            50% {
                transform: translate(-50%, -50%) scale(1.1);
                opacity: 0.8;
            }
        }

        @keyframes scan {
            0% {
                top: -20%;
                /* Start above the container */
            }

            50% {
                top: 50%;
                /* Center during the animation */
            }

            100% {
                top: 120%;
                /* End below the container */
            }
        }
    </style>
</head>

<body class="account-page" style="
    background-image: url('/assets/img/background.jpg');
    background-repeat: no-repeat;
    background-size: cover;
    background-position: center center;
    background-color: #00000060;
    background-blend-mode: overlay;
    padding-right: 10%;
    ">
    <!-- Main Wrapper -->
    <div class="main-wrapper" style="margin-left: 28%;">
        <div class="account-content">
            <div class="container">
                <!-- Account Logo -->
                <div class="account-logo">
                    <a href="#"><img src="{{ asset('/assets/img/logo.png') }}" alt="SV-HRM System"></a>
                </div>
                <!-- /Account Logo -->

                <div class="account-box">
                    <div class="account-wrapper">
                        <h3 class="account-title">Change Password</h3>
                        <p class="account-subtitle">Access to dashboard</p>

                        <!-- Change Password Form -->
                        <form action="{{ route('changed.password') }}" method="POST" id="changePasswordForm">
                            @csrf

                            <!-- Current Password -->
                            <div class="input-block mb-4">
                                <label class="col-form-label">Current Password</label>
                                <input class="form-control" type="password" name="current_password" id="currentPassword"
                                    placeholder="Enter current Password">
                                <span id="error-current_password" class="text-danger"></span>
                            </div>

                            <!-- New Password -->
                            <div class="input-block mb-4">
                                <label class="col-form-label">New Password</label>
                                <input class="form-control" type="password" name="password" id="newPassword"
                                    placeholder="Enter new password">
                                <span id="error-password" class="text-danger"></span>
                            </div>

                            <!-- Confirm New Password -->
                            <div class="input-block mb-4">
                                <label class="col-form-label">Confirm New Password</label>
                                <input class="form-control" type="password" name="password_confirmation"
                                    id="confirmPassword" placeholder="Confirm new password">
                                <span id="error-password_confirmation" class="text-danger"></span>
                            </div>

                            <!-- Submit Button -->
                            <div class="input-block mb-4 text-center">
                                <button style="border: none;
                                background-color: #00c5fb; color:white;
                                padding: 10px 20px;
                                font-size: 16px;
                                cursor: pointer;
                                border-radius: 5px;
                                transition: 0.5s;
                                position: relative;
                                width: 100%;
                                " type="submit">Change
                                    Password</button>
                            </div>

                            <div id="success-message" class="alert alert-success mt-3 d-none"></div>
                        </form>
                        <!-- /Change Password Form -->
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div>
        <div class="scanner">
            <div class="fingerprint"></div>
            <div class="scan-line"></div>
        </div>
    </div>

    <!-- /Main Wrapper -->

    <!-- jQuery -->
    <script src="{{ asset('/assets/js/jquery-3.7.1.min.js') }}"></script>
    <script src="{{ asset('/assets/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('/assets/js/jquery.slimscroll.min.js') }}"></script>
    <script src="{{ asset('/assets/js/feather.min.js') }}"></script>

    <script>
        $(document).ready(function () {

            $('.text-danger').text('');
            $('#success-message').addClass('d-none').text('');

            $('#changePasswordForm').on('submit', function (e) {
                e.preventDefault();

                $('.text-danger').text('');
                $('#success-message').addClass('d-none').text('');

                const currentPassword = $('#currentPassword').val().trim();
                const newPassword = $('#newPassword').val().trim();
                const confirmPassword = $('#confirmPassword').val().trim();

                let isValid = true;

                // Validate current password
                if (currentPassword === '') {
                    $('#error-current_password').text('Current password is required.');
                    isValid = false;
                }

                // Validate new password
                if (newPassword === '') {
                    $('#error-password').text('New password is required.');
                    isValid = false;
                } else if (newPassword.length < 8 || newPassword.length > 30) {
                    $('#error-password').text('Password length must be between 8 and 30 characters.');
                    isValid = false;
                }
                // else if (!/[A-Z]/.test(newPassword) || !/[a-z]/.test(newPassword) || !/[!@#$%^&*(),.?":{}|<>]/.test(newPassword)) {
                //     $('#error-password').text('Password must contain an uppercase letter, a lowercase letter, and a special character.');
                //     isValid = false;
                // }

                // Validate confirm password
                if (confirmPassword === '') {
                    $('#error-password_confirmation').text('Confirm password is required.');
                    isValid = false;
                } else if (newPassword !== confirmPassword) {
                    $('#error-password_confirmation').text('Passwords do not match.');
                    isValid = false;
                }

                // If validation fails, stop submission
                if (!isValid) return;

                // Submit the form via AJAX
                $.ajax({
                    url: "{{ route('changed.password') }}",
                    method: 'POST',
                    data: $(this).serialize(),
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (response) {
                        console.log(response);
                        // Handle success (password changed)
                        if (response.message) {
                            $('#success-message').removeClass('d-none').text(response.message);
                            if (response.userRole === "1") {
                                setTimeout(() => window.location.href = "{{ route('dashboard') }}", 200);
                            } else {
                                setTimeout(() => window.location.href = "{{ route('attendanceemployee.record') }}", 200);
                            }
                        }
                    },
                    error: function (xhr) {
                        // Handle errors
                        if (xhr.status === 400) {
                            // Display backend validation errors (incorrect password, etc.)
                            $('#error-current_password').text(xhr.responseJSON.error); // For example, incorrect password
                            $('#error-current_password').show();
                        } else if (xhr.status === 422) {
                            // Form validation errors
                            $.each(xhr.responseJSON.errors, function (key, value) {
                                $('#error-' + key).text(value[0]);
                            });
                        } else {
                            alert('An unexpected error occurred. Please try again later.');
                        }
                    }
                });
            });
        });

    </script>

</body>

</html>