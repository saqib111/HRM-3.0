<!DOCTYPE html>
<html lang="en" data-layout="vertical" data-topbar="light" data-layout-mode="blue" data-sidebar="dark"
    data-sidebar-size="lg" data-sidebar-image="none">

<head>
    <meta charset="utf-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Smarthr - Bootstrap Admin Template">
    <meta name="keywords"
        content="admin, estimates, bootstrap, business, corporate, creative, management, minimal, modern, accounts, invoice, html5, responsive, CRM, Projects">
    <meta name="author" content="Dreamstechnologies - Bootstrap Admin Template">


    <!-- Favicon -->
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('/assets/img/favicon.png') }}">
    @include('layout.partials.head')
    @yield('head')
    @yield('css')
    <style>
        @media screen and (max-width:991px) {
            #search-results-dropdown {
                display: none;
            }

            #search_bar {
                display: none;
            }
        }


        @media screen and (max-width:1200px) {
            .header .top-nav-search form {
                width: 100%;
            }

        }

        @media screen and (min-width: 992px) and (max-width: 1160px) {
            .header .top-nav-search form {
                width: 100%;
            }

            .media-body {
                gap: 26px !important;
            }
        }
    </style>
</head>


<body>
    <div class="main-wrapper">
        @include('layout.partials.header')
        @include('layout.partials.nav')
        @include('layout.partials.twocolumnsidebar')

        <!-- Page Wrapper -->
        <div class="page-wrapper">
            <div class="content container-fluid">

                @yield('content')
            </div>
        </div>

    </div>

    @include('layout.partials.footer-scripts')
    @yield('script-z')
    <script>
        $(document).ready(function () {
            // Show the dropdown when the search button is clicked
            $('#search-button').on('click', function (event) {
                event.preventDefault();
                var query = $('#employee-name').val();

                // Show the dropdown if the input is not empty
                if (query.length > 0) {
                    $('#search-results-dropdown').show();
                }

            });

            // Show the dropdown when the user presses "Enter" in the search input
            $('#employee-name').on('keypress', function (event) {
                if (event.key === 'Enter') {
                    event.preventDefault();
                    var query = $('#employee-name').val();

                    // Show the dropdown if the input is not empty
                    if (query.length > 0) {
                        $('#search-results-dropdown').show();
                    }
                }
            });

            $('#employee-name').on('input', function () {
                var query = $('#employee-name').val();

                if (query.length === 0) {
                    $('#search-results-dropdown').hide(); // Hide the dropdown if input is cleared
                }
            });

            // Hide the dropdown if the user clicks outside the search area
            $(document).click(function (e) {
                if (!$(e.target).closest('.top-nav-search').length) {
                    $('#search-results-dropdown').hide(); // Hide the dropdown
                }
            });

            // HOVER FUNCTIONALITY ON MOUSE DRAG
            $(document).ready(function () {
                $('#search-results-dropdown').on('mouseenter', '.leave-info-box', function () {
                    $(this).addClass('hovered');
                }).on('mouseleave', '.leave-info-box', function () {
                    $(this).removeClass('hovered');
                });
            });

            $(document).ready(function () {
                // Define the redirect function globally (outside of $(document).ready)
                function redirectToLeaveDetail(leaveId) {
                    window.location.href = "/custom-leave-detail?leave_id=" + leaveId;
                }
                function redirectToAllLeaves(userId) {
                    window.location.href = "/custom-leave-detail?user_id=" + userId;
                }

                // Handle form submission (both button click and enter key)
                $('#leave-search-form').on('submit', function (e) {
                    e.preventDefault();

                    var userId = $('#user-id').val();
                    var employeeName = $('#employee-name').val();

                    // Display the loading message and show the dropdown
                    $('#search-results-dropdown').html('<div>Loading...</div>').show();

                    $.ajax({
                        url: '/search-leave',  // Endpoint where the AJAX request will go
                        type: 'GET',           // Use GET method for the search
                        data: {
                            user_id: userId,
                            employee_name: employeeName
                        },
                        success: function (response) {
                            if (response.success) {
                                var resultsHtml = '';
                                response.data.forEach(function (item) {

                                    var leaveFrom = item.leave_from;
                                    var leaveTo = item.leave_to;

                                    // TO TRUNCATE THE TITLE AND DESCRIPTION WITH .... AFTER 10 CHARS
                                    var truncatedTitle = item.leave_title.length > 10 ? item.leave_title.substring(0, 10) + "..." : item.leave_title;
                                    var truncatedDescription = (item.leave_description && item.leave_description.length > 10) ? item.leave_description.substring(0, 10) + "..." : item.leave_description;
                                    var statusClass = '';

                                    if (item.leave_status === 'Approved') {
                                        statusClass = 'bg-inverse-success';
                                    } else if (item.leave_status === 'Pending') {
                                        statusClass = 'bg-inverse-warning';
                                    } else if (item.leave_status === 'Rejected') {
                                        statusClass = 'bg-inverse-danger';
                                    } else if (item.leave_status === 'Revoked') {
                                        statusClass = 'bg-inverse-danger';
                                    }

                                    resultsHtml += '<div id="custom_leave_box" class="leave-info-box border rounded shadow-sm align-items-center justify-content-center">' +
                                        '<div class="media d-flex align-items-center mb-1 mt-2" data-leave-id="' + item.leave_id + '">' + // Removed the inline onclick handler
                                        '<div class="media-body flex-grow-1 d-flex align-items-center" style="gap:100px">' +
                                        '<h6 class="mb-0">Employee Name : <span class="link-info">' + item.name + '</span></h6>' +
                                        '<h6 class="mb-0">Employee ID : ' + item.employee_id + '</h6>' +
                                        '<h6 class="mb-0">Balance : <span class="text-success">' + item.leave_balance + ' Days</span></h6>' +
                                        '<h6 class="mb-0">Total Leave Days : <span class="' + (item.leave_days > item.leave_balance ? 'text-danger' : 'text-success') + '">' + item.leave_days + ' Days</span></h6>' +
                                        '<h6 class="mb-0">Title : ' + truncatedTitle + '</h6>' +
                                        '<input type="hidden" id="leave_id" value="' + item.leave_id + '">' +
                                        '</div>' +
                                        '</div>' +
                                        '<div class="row mt-0">' +
                                        '<div class="col">' +
                                        '<p class="mb-0 text-muted text-sm">From : ' + leaveFrom + '</p>' +
                                        '<p class="mb-0 text-muted text-sm">To : ' + leaveTo + '</p>' +
                                        '</div>' +
                                        '<div class="col" style="text-align: end;">' +
                                        '<h4><span class="badge ' + statusClass + '">' + item.leave_status + '</span></h4>' +
                                        '</div>' +
                                        '</div>' +
                                        '</div>';
                                });
                                resultsHtml +=
                                    '<div class="dropdown-divider mt-0"></div>' +
                                    '<a href="#" class="dropdown-item text-center" data-user-id="' + response.data[0].user_id + '">Show All Applications</a>';
                                // '<a href="" class="dropdown-item text-center" id="all_leaves" data-user-id="' + item.user_id + '">Show All Applications</a>';

                                // Update dropdown with search results
                                $('#search-results-dropdown').html(resultsHtml);
                            } else {
                                $('#search-results-dropdown').html('<div>No results found</div>');
                            }
                        },
                        error: function () {
                            $('#search-results-dropdown').html('<div>Error occurred while fetching data.</div>');
                        }
                    });
                });

                // Handle Enter key press (so pressing Enter triggers form submission)
                $('#employee-name').on('keypress', function (e) {
                    if (e.which === 13) { // Enter key
                        $('#leave-search-form').submit();
                    }
                });

                // Handle click on the magnifying glass button (trigger form submission)
                $('#search-button').on('click', function (e) {
                    e.preventDefault();

                    $('#leave-search-form').submit();
                });

                // Event delegation for dynamically added content
                $('#search-results-dropdown').on('click', '.media', function () {
                    var leaveId = $(this).data('leave-id');
                    redirectToLeaveDetail(leaveId);
                });

                $('#search-results-dropdown').on('click', '.dropdown-item', function () {
                    var userId = $(this).data('user-id');
                    redirectToAllLeaves(userId);
                });

            });

        });

    </script>
    <script>
        function updateNotificationBadge() {
            $.ajax({
                url: "{{ route('notify') }}",
                type: "GET",
                success: function (response) {
                    var count = response.notificount;
                    var totalPendingLeaves = response.totalPendingLeaves;
                    var hrpending = response.hrpending;

                    if (count > 0) {
                        $('#notifibadge').text(count).removeClass('hidden');


                    } else {
                        $('#notifibadge').addClass('hidden');
                    }

                    if (totalPendingLeaves > 0) {
                        $('#pendingLeavesBadge').text(totalPendingLeaves).removeClass('hidden');
                    } else {
                        $('#pendingLeavesBadge').addClass('hidden');
                    }



                    if (hrpending > 0) {
                        $('#hrpendingbadge').text(hrpending).removeClass('hidden');
                    } else {
                        $('#hrpendingbadge').addClass('hidden');
                    }


                },
                error: function (xhr, status, error) {
                    console.error("Error fetching notification count:", error);
                }
            });
        }

        // Call the function when the page loads
        $(document).ready(function () {
            updateNotificationBadge();

            // Optionally refresh every 30 seconds
            setInterval(updateNotificationBadge, 10000);
        });

    </script>
</body>

</html>