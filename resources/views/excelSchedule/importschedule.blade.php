@extends('layout.mainlayout')

@section('css')
<style>
    .container1 {
        --transition: 350ms;
        --folder-W: 120px;
        --folder-H: 80px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: flex-end;
        padding: 10px;
        width: 20%;
        justify-self: center;
        background: linear-gradient(135deg, #6dd5ed, #2193b0);
        border-radius: 15px;
        box-shadow: 0 15px 30px rgba(0, 0, 0, 0.2);
        height: calc(var(--folder-H) * 1.7);
        position: relative;
    }

    .folder {
        position: absolute;
        top: -20px;
        left: calc(50% - 60px);
        animation: float 2.5s infinite ease-in-out;
        transition: transform var(--transition) ease;
    }

    .folder:hover {
        transform: scale(1.05);
    }

    .folder .front-side,
    .folder .back-side {
        position: absolute;
        transition: transform var(--transition);
        transform-origin: bottom center;
        top: -12px
    }

    .folder .back-side::before,
    .folder .back-side::after {
        content: "";
        display: block;
        background-color: white;
        opacity: 0.5;
        z-index: 0;
        width: var(--folder-W);
        height: var(--folder-H);
        position: absolute;
        transform-origin: bottom center;
        border-radius: 15px;
        transition: transform 350ms;
        z-index: 0;
    }

    .container:hover .back-side::before {
        transform: rotateX(-5deg) skewX(5deg);
    }

    .container:hover .back-side::after {
        transform: rotateX(-15deg) skewX(12deg);
    }

    .folder .front-side {
        z-index: 1;
    }

    .container:hover .front-side {
        transform: rotateX(-40deg) skewX(15deg);
    }

    .folder .tip {
        background: linear-gradient(135deg, #ff9a56, #ff6f56);
        width: 80px;
        height: 20px;
        border-radius: 12px 12px 0 0;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        position: absolute;
        top: -10px;
        z-index: 2;
    }

    .folder .cover {
        background: linear-gradient(135deg, #ffe563, #ffc663);
        width: var(--folder-W);
        height: var(--folder-H);
        box-shadow: 0 15px 30px rgba(0, 0, 0, 0.3);
        border-radius: 10px;
    }

    .custom-file-upload {
        font-size: 1.1em;
        color: #ffffff;
        text-align: center;
        background: rgba(255, 255, 255, 0.2);
        border: none;
        border-radius: 10px;
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        cursor: pointer;
        transition: background var(--transition) ease;
        display: inline-block;
        width: 100%;
        padding: 10px 35px;
        position: relative;
        /* margin-bottom: -9px !important; */
    }

    .custom-file-upload:hover {
        background: rgba(255, 255, 255, 0.4);
    }

    .custom-file-upload input[type="file"] {
        display: none;
    }

    #file-name {
        color: white;
    }

    #file-name:hover {
        cursor: pointer;
        color: red;
    }


    @keyframes float {
        0% {
            transform: translateY(0px);
        }

        50% {
            transform: translateY(-20px);
        }

        100% {
            transform: translateY(0px);
        }
    }

    .hidden {
        display: none;
    }

    .modal-body {
        overflow-y: scroll;
        scrollbar-width: thin;
        /* Firefox */
        scrollbar-color: #888 #f1f1f1;
        /* Firefox (thumb and track color) */
    }

    .modal-body::-webkit-scrollbar {
        width: 6px;
        /* Adjust the width here */
    }

    .modal-body::-webkit-scrollbar-thumb {
        background-color: #888;
        border-radius: 3px;
    }

    .modal-body::-webkit-scrollbar-track {
        background-color: #f1f1f1;
    }
</style>
@endsection

@section('content')
<div class="page-header">
    <div class="row align-items-center">
        <div class="col-md-4">
            <h3 class="page-title">Upload Schedule</h3>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a
                        href="{{ auth()->user()->role == '1' ? url('admin-dashboard') : url('attendance-employee') }}">Dashboard</a>
                </li>
                <li class="breadcrumb-item active">Upload Schedule</li>
            </ul>
        </div>
    </div>
</div>
<!-- CONTENT -->
<div class="container mt-5">
    <div class="card shadow-lg border-light">
        <div class="card-body">
            <div class="mb-4">
                <p class="card-text text-muted">
                    <strong>NOTE:</strong>
                <ul class="list-unstyled mt-3">
                    <li><i class="fa-solid fa-check-circle text-success"></i> <strong>Download</strong> the demo file
                        before proceeding for refrence.</li>
                    <li class="mt-2"><i class="fa-solid fa-check-circle text-success"></i> <strong>Review</strong> your
                        schedule data thoroughly.</li>
                    <li class="mt-2"><i class="fa-solid fa-check-circle text-success"></i> <strong>Ensure</strong>
                        accuracy before submitting for better management.</li>
                </ul>
                </p>
            </div>
            <div class="d-flex justify-content-end mb-2">
                <a class="btn btn-primary px-4 py-2" href="{{ asset('files/demo_attendance.xlsx') }}">
                    <i class="fa-solid fa-download me-2"></i>Download Demo File
                </a>
            </div>
        </div>
    </div>
    <div class="card mt-5 shadow-lg border-light py-4">
        <div class="card-body text-center">

            <form id="importform" enctype="multipart/form-data" class="mt-3">
                @csrf
                <div class="form-group">
                    <div class="container1">
                        <div class="folder">
                            <div class="front-side">
                                <div class="tip"></div>
                                <div class="cover"></div>
                            </div>
                            <div class="back-side cover"></div>
                        </div>
                        <label for="file" id="chosefile" class="custom-file-upload mb-2">
                            <i class="fa-solid fa-folder-open me-2"></i> Choose File
                        </label>
                        <input type="file" name="file" id="file" style="display: none;" class="form-control" required>
                        <div id="submit-dv" class="hidden mt-5">
                            <button type="submit" id="submit" class="custom-file-upload">
                                <i class="fa-solid fa-upload"></i> Upload File
                            </button>
                            <span id="file-name" class="d-block mt-2"></span>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- CONTENT -->

<!-- ERROR MODAL STARTS -->
<div class="modal fade" id="deleteConfirmationModal" tabindex="-1" aria-labelledby="exampleModalScrollable2"
    aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
        <div class="modal-content" style="max-height: 90%">
            <div class="modal-header d-flex justify-content-center">
                <h3 class="modal-title" id="modalTitle">Modal Heading</h3>
            </div>
            <div class="modal-body">
                <div id="message-area">
                    <!-- DYNAMIC CONTENT -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<!-- ERROR MODAL ENDS -->

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
            const fileInput = document.getElementById('file');
            const submitButton = document.getElementById('submit-dv');
            const filebtn = document.getElementById('chosefile');

            $.ajax({
                type: 'POST',
                url: "{{ route('import') }}", // Ensure this route is correct
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                success: function (response) {
                    hideLoader();
                    $("#modalTitle").html('<i class="fa-regular fa-circle-check me-1" style="color: #44bf52;"></i> <span class="text-success">Success!</span>');
                    $('#message-area').html(
                        `<div class="alert alert-success mt-3">${response.message}</div>`
                    );
                    $('#deleteConfirmationModal').modal('show');
                    $('#importform')[0].reset();
                    submitButton.classList.add('hidden');
                    filebtn.classList.remove('hidden');
                    $('#file-name').text('');
                },
                error: function (xhr) {
                    $("#modalTitle").html('<i class="fa-solid fa-triangle-exclamation me-2" style="color: #e60505;"></i> <span class="text-danger">Error!</span>');
                    hideLoader();
                    var errorMessage =
                        '<div class="mt-2"><strong>Following are the incorrect entries in the schedule:</strong><ul>';

                    // Check if the backend sent a structured error message
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        var errors = xhr.responseJSON.message.split('|'); // Split errors by '|'
                        errors.forEach(function (error, index) {
                            errorMessage +=
                                `<li class="mt-3"><strong>${index + 1}.</strong> ${error.trim()}</li>`; // Add numbering
                        });
                    } else {
                        errorMessage += `<li>Error - ${xhr.status}: ${xhr.statusText}</li>`;
                        $('#deleteConfirmationModal').modal('show');
                    }

                    errorMessage += '</ul></div>';
                    $('#deleteConfirmationModal').modal('show');
                    $('#message-area').html(errorMessage);
                }
            });
        });
    });

    document.getElementById('file').addEventListener('change', function () {
        const fileNameDisplay = document.getElementById('file-name');
        if (this.files.length > 0) {
            document.getElementById('submit-dv').classList.remove('hidden');
            document.getElementById('chosefile').classList.add('hidden');
            fileNameDisplay.textContent = ` ${this.files[0].name}`;
            fileNameDisplay.classList.remove('hidden');
        } else {
            document.getElementById('submit-dv').classList.add('hidden');
            document.getElementById('chosefile').classList.remove('hidden');
            fileNameDisplay.textContent = '';
            fileNameDisplay.classList.add('hidden');
        }
    });

    // TO REMOVE THE SELECTED FILE  
    $('#file-name').on('click', function () {
        $('#file').val('');
        $('#file-name').text("");
        $('#submit-dv').addClass('hidden');
        $('#chosefile').removeClass('hidden');
    });
</script>

@endsection