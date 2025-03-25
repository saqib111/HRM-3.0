@extends('layout.mainlayout')
@section('content')
    <style>
        .hover-shadow:hover {
            transform: scale(1.1);
            transition: all 0.2s ease-in-out;
        }

        .error {
            color: red;
            font-size: 0.9em;
        }

        .loader {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 9999;
            background: rgba(255, 255, 255, 0.8);
            padding: 20px;
            border-radius: 10px;
        }
    </style>

    <div id="notification" aria-live="polite" aria-atomic="true"></div>

    <!-- Page Header -->
    <div class="page-header">
        <div id="loader" class="loader" style="display: none;">
            <div class="loader-animation"></div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <h3 class="page-title"><span data-translate="profile">Profile</span></h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('user.settings') }}"><span
                                data-translate="settings">Settings</span></a></li>
                    <li class="breadcrumb-item active"><span data-translate="profile">Profile</span></li>
                </ul>
            </div>
        </div>
    </div>
    <!-- /Page Header -->

    <div class="container mt-5">
        <div class="card">
            <div class="row g-0">
                <!-- Image Section -->
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">X</button>
                    </div>
                @endif

                <div class="col-md-6 d-flex flex-column align-items-center justify-content-center border-end">
                    <h2><span data-translate="profile_image">Profile Image</span></h2><br>
                    <div class="position-relative ">
                        <img id="profileImagePreview"
                            src="{{ $user->image ? url('uploads/' . $user->image) : url('uploads/images/default_profile_picture.png') }}"
                            alt="Profile Picture" class="rounded-circle img-fluid" style="width: 200px; height: 200px; padding: 5px; border: 3px solid #cccc; box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
                                ">
                        <label for="imageInput" class="position-absolute top-0 end-0 border rounded-circle p-2 m-1"
                            style="cursor: pointer; background-color:aliceblue">
                            <i class="box-shadow hover-pop">
                                <img src="uploads/images/camera.png" alt="icon"
                                    style="width: 25px; height: 25px; position: relative; top: 0px; right: 0px;"
                                    class="hover-shadow">
                            </i>
                        </label>
                    </div>

                    <form action="{{ route('settings.updateImage') }}" method="POST" id="imageForm"
                        enctype="multipart/form-data" class="mt-3">
                        @csrf
                        <input type="file" id="imageInput" name="image" accept="image/*" style="display: none;" required>
                        <button type="submit" class="btn btn-primary mt-3" id="updateButton" style="display: none;"><span
                                data-translate="update_image">Update Image</span></button>
                    </form>
                </div>

                <!-- Password Section -->

                <div id="loader" class="loader" style="display: none;">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>

                <div class="col-md-6 p-4">
                    <h5 class="card-title mb-5"><span data-translate="update_password">Update Password</span></h5>
                    <form id="passwordForm">

                        @csrf
                        <div class="mb-3">
                            <label for="currentPassword" class="form-label"><span data-translate="current_password">Current
                                    Password</span></label>
                            <input type="password" name="current_password" id="currentPassword"
                                placeholder="Enter your old password" class="form-control">
                            <span class="error text-danger" id="currentPasswordError"></span>
                        </div>
                        <div class="mb-3">
                            <label for="newPassword" class="form-label"><span data-translate="new_password">Current
                                    Password</span></label>
                            <input type="password" name="password" id="newPassword" placeholder="Enter new password"
                                class="form-control">
                            <span class="error text-danger" id="newPasswordError"></span>
                        </div>
                        <div class="mb-3">
                            <label for="confirmPassword" class="form-label"><span
                                    data-translate="confirm_new_password">Confirm New Password</span></label>
                            <input type="password" name="password_confirmation" id="confirmPassword"
                                placeholder="Confirm new password" class="form-control">
                            <span class="error text-danger" id="confirmPasswordError"></span>
                        </div>
                        <button type="submit" class="btn btn-success" id="submitButton"><span
                                data-translate="update_password">Update Password</span></button>
                        <div id="loader" style="display: none;">Updating...</div>
                    </form>
                </div>

            </div>
        </div>
    </div>
@endsection
@section('script-z')
    <script>
        //ajax code for password update. also vlidation and loader implimented.

        $(document).ready(function () {
            $('#passwordForm').on('submit', function (e) {
                e.preventDefault();

                $('.error').text('');

                let isValid = true;

                //validate Current name
                if ($('#currentPassword').val().trim() === '') {
                    $('#currentPasswordError').text('Current Password is required.');
                    isValid = false;
                }

                //Validate Password
                if ($('#newPassword').val().trim() == '') {
                    $('#newPasswordError').text('New Password is required.');
                    isValid = false
                }

                //Validate Confirm Password
                if ($('#confirmPassword').val().trim() == '') {
                    $('#confirmPasswordError').text('Confirm Password is required.');
                    isValid = false
                }


                //On valid
                if (isValid) {
                    $('#loader').show();
                    $.ajax({
                        url: "{{ route('update.password') }}", // Check if the route is correct
                        type: "POST",
                        data: $(this).serialize(),
                        success: function (response) {
                            console.log('Success:', response); // Debug success response
                            $('#loader').hide();
                            $('#submitButton').prop('disabled', false);

                            if (response.success) {
                                createToast('info', 'fa-solid fa-circle-check', 'Success',
                                    'Password updated Successfully.'
                                ); // Display success message
                                $('#passwordForm')[0].reset(); // Reset the form
                            }
                        },
                        error: function (xhr) {
                            console.log('Error:', xhr.responseText); // Debug error response
                            $('#loader').hide();
                            $('#submitButton').prop('disabled', false);

                            if (xhr.status === 422) {
                                const errors = xhr.responseJSON.errors;
                                if (errors.current_password) {
                                    $('#currentPasswordError').text(errors.current_password[0]);
                                }
                                if (errors.password) {
                                    $('#newPasswordError').text(errors.password[0]);
                                }
                                if (errors.password_confirmation) {
                                    $('#confirmPasswordError').text(errors
                                        .password_confirmation[
                                        0]);
                                }
                            } else {
                                alert('An error occurred. Please try again.');
                            }
                        }
                    });
                }


            });
        });





        // Profile Image Upload Preview
        document.getElementById('imageInput').addEventListener('change', function (event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    document.getElementById('profileImagePreview').src = e.target.result;
                };
                reader.readAsDataURL(file);
                document.getElementById('updateButton').style.display = 'block';
            }
        });
    </script>
@endsection