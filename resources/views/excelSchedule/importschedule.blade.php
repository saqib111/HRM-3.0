@extends('layout.mainlayout')

@section('content')
<div class="container mt-5">
    <!-- Note and Download Button Card -->
    <div class="card">
        <div class="card-body">
            <div class="mb-4">
                <p class="card-text">
                    <strong>NOTE: </strong> Dear Leader please vari data before upload.
                </p>
            </div>
            <div class="d-flex justify-content-end mb-4">
                <a class="btn btn-primary" href="{{ asset('files/demo_attendance.xlsx') }}">DOWNLOAD FILE <i
                        class="fa-solid fa-download"></i></a>
            </div>
        </div>
    </div>
    <div class="card mt-4">
        <div class="card-body text-center">
            <h5 class="card-title">Upload Section</h5>
            <form id="importform" enctype="multipart/form-data" class="mt-3">
                @csrf
                <div class="form-group">
                    <label for="file">
                        <div class="d-flex justify-content-center align-items-center"
                            style="height: 8rem; width: 8rem; border: 2px dashed #ccc; border-radius: 10px; cursor: pointer; background-color: #f9f9f9;">
                            <div class="card card-body d-flex justify-content-center align-items-center"
                                style="height: 100%; width: 100%; text-align: center;">
                                <i class="fa fa-cloud-upload" style="font-size: 2rem; color: #888;"></i>
                            </div>
                        </div>
                    </label>
                    <input type="file" name="file" id="file" style="display: none;" class="form-control" required>
                    <div class="d-flex justify-content-center mt-3">
                        <button type="submit" id="submit" class="btn btn-success"><i class="fa-solid fa-upload"></i>
                            UPLOAD FILE</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- Message Display Area -->
<div id="message-area">
    @if (session('success'))
        <div class="alert alert-success mt-3">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger mt-3">{{ session('error') }}</div>
    @endif
</div>
<!-- PreLoader -->
<div id="loader" class="loader" style="display: none;">
    <div class="loader-animation"></div>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script>
    $(document).ready(function () {
        $('#importform').submit(function (event) {
            event.preventDefault();
            showLoader();
            // Clear previous messages
            $('#message-area').html('');

            var formData = new FormData(this);

            $.ajax({
                type: 'POST',
                url: "{{ route('import') }}", // Ensure this route is correct
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                success: function (response) {
                    hideLoader();
                    $('#message-area').html(
                        `<div class="alert alert-success mt-3">${response.message}</div>`
                    );
                },
                error: function (xhr) {
                    hideLoader();
                    var errorMessage = '<div class="alert alert-danger mt-3"><strong>File validation failed. Errors:</strong><ul>';

                    // Check if the backend sent a structured error message
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        var errors = xhr.responseJSON.message.split('|'); // Split errors by '|'
                        errors.forEach(function (error, index) {
                            errorMessage += `<li><strong>${index + 1}.</strong> ${error.trim()}</li>`; // Add numbering
                        });
                    } else {
                        errorMessage += `<li>Error - ${xhr.status}: ${xhr.statusText}</li>`;
                    }

                    errorMessage += '</ul></div>';

                    $('#message-area').html(errorMessage);
                }
            });
        });
    });
</script>
@endsection