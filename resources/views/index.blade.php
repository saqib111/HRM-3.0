<!DOCTYPE html>
<html lang="en" data-layout="vertical" data-topbar="light" data-sidebar="dark" data-sidebar-size="lg"
    data-sidebar-image="none">

<head>
    <meta charset="utf-8">
    <meta name="csrf-token" content="itA1Ny454sf8Rb9AoDXzhXmr0Y3nrCYDhNL6VAiJ">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Smarthr - Bootstrap Admin Template">
    <meta name="keywords" content="Affan Ahmed">
    <meta name="author" content="Created By Affan Ahmed">
    <title>Login - HRMS admin template</title>
    <!-- Favicon -->
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('/assets/img/logo5.png') }}">

    <link rel="stylesheet" href="{{ asset('/assets/css/bootstrap.min.css') }}">


    <link rel="stylesheet" href="{{ asset('/assets/plugins/fontawesome/css/fontawesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('/assets/plugins/fontawesome/css/all.min.css') }}">



    <link rel="stylesheet" href="{{ asset('/assets/css/line-awesome.min.css') }}/">
    <!-- Lineawesome CSS -->
    <link rel="stylesheet" href="{{ asset('/assets/css/line-awesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('/assets/css/material.css') }}">
    <!-- Main CSS -->
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
            left: 20%;
            transform: translateY(-50%);
            width: 60%;
            height: 4px;
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
    <div style="margin-left:28%" class="main-wrapper">
        <div class="account-content">
            <div class="container">

                <!-- Account Logo -->
                <div class="account-logo">
                    <a href=""><img src="{{ asset('/assets/img/logo.png') }}" alt="SV-HRM System"></a>
                </div>
                <!-- /Account Logo -->

                <div class="account-box">
                    <div class="account-wrapper">
                        <h3 class="account-title">Login</h3>
                        <p class="account-subtitle">Access to dashboard</p>
                        @if ($errors->has('status_disabled'))
                            <span class="text-danger">
                                {{ $errors->first('status_disabled') }}
                            </span>
                        @endif

                        <!-- Account Form -->
                        <form action=" {{ route('loginto') }}" method="POST" id="login-form">
                            @csrf

                            <div class="input-block mb-4">
                                <label class="col-form-label">Email Address</label>
                                <input class="form-control" type="text" name="email" id="email"
                                    placeholder="Please enter your email" autocomplete="off"
                                    value="affan.ahmed@auroramy.com">
                                <span id="email-error" class="text-danger"></span> <!-- For displaying errors -->
                                @if ($errors->has('credentials'))
                                    <span class="text-danger">
                                        {{ $errors->first('credentials') }}
                                    </span>
                                @endif
                            </div>
                            <div class="input-block mb-4">
                                <label class="col-form-label">Password</label>
                                <div class="position-relative">
                                    <input class="form-control" type="password" name="password"
                                        placeholder="Please enter your password" id="password" autocomplete="off"
                                        value="12345678">
                                    <!-- <span class="fa-solid fa-eye-slash" id="toggle-password"></span> -->
                                </div>
                                <span id="password-error" class="text-danger"></span> <!-- For displaying errors -->
                            </div>
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
                                " type="submit" id="submitbtn">Login</button>
                            </div>
                            <div class="account-footer">
                                <p></p>
                            </div>
                        </form>
                        <!-- /Account Form -->
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

    <!-- Bootstrap Core JS -->
    <script src="{{ asset('/assets/js/bootstrap.bundle.min.js') }}"></script>

    <!-- Slimscroll JS -->
    <script src="{{ asset('/assets/js/jquery.slimscroll.min.js') }}"></script>
    <script src="{{ asset('/assets/js/feather.min.js') }}"></script>
    <script data-navigate-once="true">
        window.livewireScriptConfig = {
            "csrf": "itA1Ny454sf8Rb9AoDXzhXmr0Y3nrCYDhNL6VAiJ",
            "uri": "\/livewire\/update",
            "progressBar": "",
            "nonce": ""
        };
    </script>
    <script src="http://127.0.0.1:8000/assets/js/app.js"></script>

    <script type="text/javascript">
        $(document).ready(function () {
            $('#email-error').hide();
            $('#password-error').hide();

            var user_err = true;
            var pass_err = true;

            $('#email').keyup(function () {
                useremail_check();
            });

            function useremail_check() {
                var user_val = $('#email').val();
                const emailPattern = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;

                if (user_val.length == '') {
                    $('#email-error').show();
                    $('#email-error').html("Email is Required");
                    $('#email-error').focus();
                    user_err = false;
                    return false;
                } else {
                    $('#email-error').hide();
                }


                // Check if the email is valid
                if (!emailPattern.test(user_val)) {
                    $('#email-error').show();
                    $('#email-error').html("Please enter a valid email address");
                    user_err = false;
                    return false;
                } else {
                    $('#email-error').hide();
                    user_err = true;
                    return true;
                }

            }

            $('#password').keyup(function () {
                password_check();
            });

            function password_check() {
                var password_str = $('#password').val();
                const hasUpperCase = /[A-Z]/.test(password_str);
                const hasLowerCase = /[a-z]/.test(password_str);
                const hasSpecialChar = /[!@#$%^&*(),.?":{}|<>]/.test(password_str);

                if (password_str.length == '') {
                    $('#password-error').show();
                    $('#password-error').html("Password is Required");
                    $('#password-error').focus();
                    pass_err = false;
                    return false;
                } else {
                    $('#password-error').hide();
                }

                if ((password_str.length < 6) || (password_str.length > 30)) {
                    $('#password-error').show();
                    $('#password-error').html("Password length must be between 6 to 30");
                    $('#password-error').focus();
                    // $('#password-error').css("color", "red");
                    pass_err = false;
                    return false;
                } else {
                    $('#password-error').hide();
                }

            }


            $('#submitbtn').click(function () {

                user_err = true;
                pass_err = true;

                useremail_check();
                password_check();
                if ((user_err == true) && (pass_err == true)) {
                    return true;
                } else {
                    return false;
                }

            })

        });
    </script>
</body>

</html>