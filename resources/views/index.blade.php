<!DOCTYPE html>
<html lang="en" data-layout="vertical" data-topbar="light" data-sidebar="dark" data-sidebar-size="lg"
    data-sidebar-image="none">

<head>
    <meta charset="utf-8">
    <meta name="csrf-token" content="itA1Ny454sf8Rb9AoDXzhXmr0Y3nrCYDhNL6VAiJ">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Smarthr - Bootstrap Admin Template">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="keywords" content="Affan Ahmed">
    <meta name="author" content="Created By Affan Ahmed">
    <title>Login - HRMS admin template</title>
    <!-- Favicon -->
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('/assets/img/logo5.png') }}">

    <link rel="stylesheet" href="{{ asset('/assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('/assets/plugins/fontawesome/css/fontawesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('/assets/plugins/fontawesome/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('/assets/css/line-awesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('/assets/css/material.css') }}">
    <link rel="stylesheet" href="{{ asset('/assets/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('/assets/css/myAllCustomStyles.css') }}">
    <style>
        .mainSec {
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            text-align: left;
            /* Align text to the left */
        }

        /* Section background radial gradient */
        .background-radial-gradient {
            /* background-color: hsl(218, 41%, 15%); */
            background-color: hsl(218deg 19.78% 10.05%);
            background-image: radial-gradient(650px circle at 0% 0%, hsl(218, 41%, 35%) 15%, hsl(218, 41%, 30%) 35%, hsl(218, 41%, 20%) 75%, hsl(218, 41%, 19%) 80%, transparent 100%), radial-gradient(1250px circle at 100% 100%, hsl(218, 41%, 45%) 15%, hsl(218, 41%, 30%) 35%, hsl(218, 41%, 20%) 75%, hsl(218, 41%, 19%) 80%, transparent 100%);
        }

        /* Shapes used for visual effects */
        #radius-shape-1,
        #radius-shape-2 {
            position: absolute;
            background: radial-gradient(#00C5FB, #3d85f1);
            overflow: hidden;
        }

        #radius-shape-1 {
            height: 220px;
            width: 220px;
            top: -60px;
            left: -130px;
        }

        #radius-shape-2 {
            border-radius: 38% 62% 63% 37% / 70% 33% 67% 30%;
            bottom: -60px;
            right: -110px;
            width: 300px;
            height: 300px;
        }

        /* Glassmorphism effect for the card */
        .bg-glass {
            background-color: hsl(0deg 0% 0% / 45.5%) !important;
            backdrop-filter: saturate(200%) blur(50px);
        }

        /* Custom styles for the form and text */
        .form-outline input,
        .form-outline label {
            color: hsl(0, 0%, 100%) !important;

        }

        .form-outline input {
            color: black !important;
        }

        .account-title {
            font-size: 2.5rem;
            font-weight: 700;
            line-height: 1;
            color: white;
            margin-bottom: 20px;
        }

        .account-subtitle {
            font-size: 1.5rem;
            font-weight: 700;
            line-height: 1;
            color: white;
            margin-bottom: 20px;
        }

        .formdiv {
            margin-top: 160px !important;
        }

        /* Mobile specific styles */
        @media (max-width: 768px) {
            .formdiv {
                margin-top: 0 !important;
            }

            .account-title {
                font-size: 2rem;
            }

            .account-subtitle {
                font-size: 1.2rem;
            }

            .col-lg-6 {
                text-align: left;
                /* Ensure text is aligned left on smaller screens */
            }

            #radius-shape-1,
            #radius-shape-2 {
                height: 150px;
                width: 150px;
                top: -30px;
                left: -60px;
                bottom: -40px;
                right: -60px;
            }

            .bg-glass {
                padding: 25px 30px;
            }

            .card-body {
                padding: 30px;
            }

            .mainSec {
                height: auto;
                padding: 20px;
            }

            .container {
                padding: 0 15px;
            }
        }

        /* Larger devices like tablets, laptops */
        @media (max-width: 1024px) {
            .col-md-6 {
                flex: 0 0 100%;
                max-width: 100%;
            }

            .mainSec {
                padding: 20px;
            }
        }

        /* Very small screens (phones in portrait) */
        @media (max-width: 576px) {
            .account-title {
                font-size: 1.8rem;
            }



            .account-subtitle {
                font-size: 1.1rem;
            }

            .card-body {
                padding: 25px;
            }

            #radius-shape-1,
            #radius-shape-2 {
                display: none;
            }
        }

        /* Add animations to make the shapes round and bouncing */
        @keyframes bounce {

            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-20px);
            }
        }

        @keyframes bounce-opposite {

            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(20px);
            }
        }

        #radius-shape-1,
        #radius-shape-2 {
            border-radius: 50%;
            /* Makes the shapes round */
            position: absolute;
            animation-timing-function: ease-in-out;
            animation-iteration-count: infinite;
            animation-duration: 3s;
        }

        #radius-shape-1 {
            background: radial-gradient(#00C5FB, #3d85f1);
            height: 220px;
            width: 220px;
            top: -60px;
            left: -130px;
            animation-name: bounce;
        }

        #radius-shape-2 {
            background: radial-gradient(#3d85f1, #00C5FB);
            height: 300px;
            width: 300px;
            bottom: -60px;
            right: -110px;
            animation-name: bounce-opposite;
        }

        /* @keyframes rotate {
            from {
                transform: rotate(0deg);
            }

            to {
                transform: rotate(360deg);
            }
        } */

        .imgcontainer img {
            /* animation: rotate 30s linear infinite; */
            /* Adjust duration and easing as needed */
            display: block;
            margin: 0 auto;
            /* Center the image within its container */
        }

        .headingcontainer {
            display: flex;
            justify-content: space-between;

        }

        .imgcontainer {
            margin: 0;
            height: 100px;
            width: 100px;
        }

        button {
            background-color: #00C5FB;
            color: white;
            width: 100%;
            border: none;
            height: 38px;
            font-size: 18px;
            font-weight: bold;
            border-radius: 5px;
        }


        @media only screen and (max-width: 768px) {

            .rs_display_none {
                display: none !important;
            }

            body {
                display: none;
            }
        }

        @media only screen and (min-width: 768px) and (max-width:1024px) {

            .rs_display_none {
                display: none !important;

            }

            .loginContainerDiv {
                width: 75% !important;
            }

            .formdiv {
                display: flex;
                align-items: center;
                justify-content: space-evenly;
            }

            .innerDiv {
                width: 100%;
                display: flex;
                justify-content: center;
            }
        }

        .sideimgcontainer {
            margin-right: 50px;
            background-image: url("/assets/img/sideimages/sideimage.png");
            background-size: cover;
            background-repeat: no-repeat;
            background-position: center;
            display: flex;
            align-items: center;
            justify-content: center;
            top: 50%;
            height: 500px;
            width: 100%;
            animation: zoom 3s infinite;
        }

        /* Animation: Scanning Line */
        @keyframes scan {

            0%,
            100% {
                top: 0;
            }

            50% {
                top: calc(100% - 10px);
            }
        }

        /* Animation: Zoom In and Out */
        @keyframes zoom {

            0%,
            100% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.1);
            }
        }

        /* end image scanner */

        .hover-shadow:hover {
            transform: scale(1.1);
            transition: all 0.2s ease-in-out;
        }

        .error {
            color: red;
            font-size: 0.9em;
        }
    </style>

</head>

<body>

    <div id="loader" class="loader" style="display: none;">
        <div class="loader-animation"></div>
    </div>

    <section class="background-radial-gradient overflow-hidden mainSec">
        <div class="container px-5 py-5 px-md-5 text-lg-start my-5 formdiv">
            <div class="row gx-lg-5 align-items-center mb-5 innerDiv">
                <div class="col-lg-6 mb-5 mb-lg-0 rs_display_none" style="z-index: 10">
                    <div class="sideimgcontainer">
                    </div>
                </div>

                <div class="col-lg-6 mb-5 mb-lg-0 position-relative loginContainerDiv">
                    <div id="radius-shape-1" class="position-absolute rounded-circle shadow-5-strong"></div>
                    <div id="radius-shape-2" class="position-absolute shadow-5-strong"></div>

                    <div class="card bg-glass h-100">
                        <div class="container card-body px-4 py-5 px-md-5"
                            style="height: 600px; display: flex; flex-direction: column; gap: 30px; justify-content: space-evenly;">
                            <div class="headingcontainer">
                                <div class="textcontainer">
                                    <h3 class="account-title">Login</h3>
                                    <p class="account-subtitle">Access to dashboard</p>
                                    <span id="message_error" class="text-danger"></span>
                                </div>
                                <div class="imgcontainer">
                                    <img src="{{ asset('assets/img/logo/logo.png') }}" class="img-fluid rounded-circle">
                                </div>
                            </div>
                            <form action="{{ route('loginto') }}" method="POST" id="login-form">
                                @csrf
                                <div class="form-outline mb-4">
                                    <label class="col-form-label" for="employee_id">Employee ID</label>
                                    <input type="text" name="employee_id" id="employee_id"
                                        placeholder="Please enter your employee ID" autocomplete="off"
                                        class="form-control" value="AHNV00315" />
                                    <span id="employee_id-error" class="text-danger"></span>
                                </div>

                                <div class="form-outline mb-4">
                                    <label class="col-form-label" for="password">Password</label>
                                    <input type="password" name="password" id="password"
                                        placeholder="Please enter your password" autocomplete="off" class="form-control"
                                        value="12345678" />
                                    <span id="password-error" class="text-danger"></span>
                                </div>

                                <button type="submit" id="submitbtn">Login</button>
                                <div id="general-error" class="text-danger mt-3"></div>
                            </form>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script src="{{ asset('/assets/js/jquery-3.7.1.min.js') }}"></script>
    <script src="{{ asset('/assets/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('/assets/js/jquery.slimscroll.min.js') }}"></script>
    <script src="{{ asset('/assets/js/feather.min.js') }}"></script>


    <script>
        document.getElementById('login-form').addEventListener('submit', async function (e) {
            e.preventDefault();

            // Clear previous errors
            document.getElementById('employee_id-error').textContent = '';
            document.getElementById('password-error').textContent = '';
            document.getElementById('general-error').textContent = '';

            const loader = document.getElementById('loader');
            const form = e.target;
            const formData = new FormData(form);
            const url = form.action;

            // Client-side validation
            const employee_id = formData.get('employee_id');
            const password = formData.get('password');
            let hasError = false;

            // Reset borders before validation
            $('#employee_id').css('border', '');
            $('#password').css('border', '');

            // Employee ID validation
            if (employee_id === '') {
                document.getElementById('employee_id-error').textContent = 'Employee ID is required.';
                $('#employee_id').css('border', '1px solid red');
                hasError = true;
            }

            // Password validation
            if (password === '') {
                hasError = true;
                document.getElementById('password-error').textContent = 'Password is required.';
                $('#password').css('border', '1px solid red');
            } else {
                if (!password || password.length < 8) {
                    document.getElementById('password-error').textContent =
                        'Password must be at least 8 characters.';
                    $('#password').css('border', '1px solid red');
                    hasError = true;
                }
            }

            if (hasError) {
                return; // Stop submission if client-side validation fails
            }

            try {
                // Show loader
                $('#loader').show();

                const response = await fetch(url, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json', // Ensure the response is JSON
                    },
                });

                // Check if the response is JSON
                const contentType = response.headers.get('content-type');
                if (contentType && contentType.includes('application/json')) {
                    const result = await response.json();

                    if (response.ok) {
                        if (result.redirect_url) {
                            window.location.href = result.redirect_url;
                        } else {
                            alert(result.message);
                        }
                    } else if (response.status === 403) {
                        window.location.href = "/access-denied-ip";
                    } else {
                        if (response.status === 401) {
                            document.getElementById('general-error').textContent = 'Your credentials are invalid. Please contact your administrator for assistance.';
                            $('#password').css('border', '1px solid red');
                            $('#employee_id').css('border', '1px solid red');
                        } else if (response.status === 402) {
                            if (result.redirect_url) {
                                window.location.href = result.redirect_url;
                            } else {
                                document.getElementById('message_error').innerHTML =
                                    'Your account has been disabled by the administrator.<br>Please contact HR';
                                $('#password').css('border', '1px solid red');
                                $('#employee_id').css('border', '1px solid red');
                            }
                        }
                    }
                } else {
                    // If not JSON, log the response body as text for debugging
                    const text = await response.text();
                    console.error('Non-JSON response:', text);
                    document.getElementById('general-error').textContent =
                        'An error occurred. Please try again later.';
                }
            } catch (error) {
                console.error('Error:', error);
                document.getElementById('general-error').textContent =
                    'An error occurred. Please try again later.';
            } finally {
                // Hide loader
                loader.style.display = 'none';
            }
        });
    </script>



</body>

</html>