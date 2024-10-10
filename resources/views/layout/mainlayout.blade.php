<!DOCTYPE html>
<html lang="en" data-layout="vertical" data-topbar="light" data-layout-mode="blue" data-sidebar="dark" data-sidebar-size="lg"
    data-sidebar-image="none">

<head>
    <meta charset="utf-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Smarthr - Bootstrap Admin Template">
    <meta name="keywords"
        content="admin, estimates, bootstrap, business, corporate, creative, management, minimal, modern, accounts, invoice, html5, responsive, CRM, Projects">
    <meta name="author" content="Dreamstechnologies - Bootstrap Admin Template">
    
   
    <!-- Favicon -->
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('/asseys/img/favicon.png') }}">
    @include('layout.partials.head')
    @yield('css')
</head>


    <body>
    @include('layout.partials.header')
    @include('layout.partials.nav')
<div class="main-wrapper mt-4">
   
            <!-- Page Wrapper -->
            <div class="page-wrapper">
                <div class="content container-fluid">

                    @yield('content')
                </div>
            </div>
       
</div>

@include('layout.partials.footer-scripts')
@yield('script-z')


</body>

</html>
