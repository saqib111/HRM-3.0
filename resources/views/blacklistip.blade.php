<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>

    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('/assets/img/logo5.png') }}">
    <link rel="stylesheet" href="{{ asset('/assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('/assets/plugins/fontawesome/css/fontawesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('/assets/plugins/fontawesome/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('/assets/css/line-awesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('/assets/css/material.css') }}">
    <link rel="stylesheet" href="{{ asset('/assets/css/style.css') }}">

    <style>
        /* Background Radial Gradient */
        .background-radial-gradient {
            background-color: hsl(218deg 19.78% 30%);
            background-color: hsl(218deg 19.78% 10.05%);
            background-image: radial-gradient(650px circle at 0% 0%, hsl(218, 41%, 35%) 15%, hsl(218, 41%, 30%) 35%, hsl(218, 41%, 20%) 75%, hsl(218, 41%, 19%) 80%, transparent 100%), radial-gradient(1250px circle at 100% 100%, hsl(218, 41%, 45%) 15%, hsl(218, 41%, 30%) 35%, hsl(218, 41%, 20%) 75%, hsl(218, 41%, 19%) 80%, transparent 100%);
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 0;
        }

        /* Notification Box Styling */
        .notification-box {
            background: rgba(255, 255, 255, 0.95);
            padding: 40px 40px 50px 40px;
            border-radius: 20px;
            border: 2px solid #3D85F1;
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.1);
            max-width: 750px;
            width: 100%;
            text-align: center;
            transform: scale(1);
            transition: transform 0.3s ease-in-out;
        }

        .notification-box:hover {
            transform: scale(1.05);
        }

        .notification-header {
            border-bottom: 2px solid;
            color: #3D85F1;
            padding-bottom: 15px;
            margin-bottom: 25px;
        }

        .notification-title {
            font-size: 2rem;
            color: #3D85F1;
            font-weight: 600;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .notification-body {
            font-size: 1.2rem;
            color: #333;
            line-height: 1.8;
            margin-top: 15px;
        }

        .notification-body p {
            font-weight: 500;
        }

        .notification-body ol {
            background: #f9f9f9;
            padding: 20px;
            border-radius: 10px;
            margin-top: 15px;
            list-style-type: none;
            /* Remove numbering */
            counter-reset: list-item;
            text-align: left;
            position: relative;
            padding-left: 20px;
            /* Add padding on the left for spacing */
        }

        .notification-body ol li {
            display: flex;
            align-items: center;
            margin-bottom: 12px;
            font-weight: 400;
            position: relative;
        }

        .notification-body ol li::before {

            color: #3D85F1;
            font-weight: bold;
            font-size: 24px;

            position: absolute;

            transform: translateY(-30%);
        }

        .notification-body ol li span {
            margin-left: 12px;
            /* Space between the dot and the text */
        }

        /* Warning Icon Styling */
        .warning-icon {
            font-size: 35px;
            color: #ff4b2b;
            margin-right: 12px;
        }

        /* Button Styling */
        .back-button {
            background: linear-gradient(to right, #00C5FB, #3D85F1);
            color: white;
            font-size: 1.25rem;
            padding: 12px 25px;
            border: none;
            border-radius: 30px;
            cursor: pointer;
            margin-top: 30px;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.15);
        }

        .back-button i {
            margin-right: 12px;
        }

        .back-button:hover {
            background-color: #4b11d1;
            transform: scale(1.08);
        }

        .back-button:focus {
            outline: none;
            /* box-shadow: 0 0 0 4px rgba(255, 75, 43, 0.5); */
        }

        /* Mobile Responsive */
        @media (max-width: 768px) {
            .notification-box {
                max-width: 90%;
            }

            .notification-title {
                font-size: 1.5rem;
            }

            .back-button {
                font-size: 1.1rem;
                padding: 10px 20px;
            }
        }
    </style>
</head>

<body class="background-radial-gradient">

    <!-- Notification Box -->
    <div class="notification-box">
        <div class="notification-header">
            <h5 class="notification-title">
                <i class="fas fa-exclamation-circle warning-icon"></i> Access Denied
            </h5>
        </div>
        <div class="notification-body">
            <p>
                Unfortunately, you cannot log in from your current location.
                For security reasons, login is only permitted from authorized locations.
            </p>
            <p><strong>What you can do:</strong></p>
            <ul>
                <li>Contact your IT administrator for assistance in adding your IP address to the
                    whitelist.</li>
            </ul>
        </div>
        <!-- Back Button -->
        <a class="back-button" href="{{url('/')}}">
            <i class="fas fa-arrow-left"></i> Back to Login
        </a>
    </div>

    <script src="{{ asset('/assets/js/jquery-3.7.1.min.js') }}"></script>
    <script src="{{ asset('/assets/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('/assets/js/jquery.slimscroll.min.js') }}"></script>
    <script src="{{ asset('/assets/js/feather.min.js') }}"></script>
    <script src="http://127.0.0.1:8000/assets/js/app.js"></script>

</body>

</html>