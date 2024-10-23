@extends('layout.mainlayout')
@section('head')
<!-- Litepicker CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css"/>
<style>
    .select2-search__field {
        display: none;
    }

    .select2-selection__choice__remove {
        border: none;
        background-color: #E4E4E4;
    }

    .select2-selection__choice {
        margin-top: 0;
    }
     .select2-selection--multiple.is-invalid
   {
      border-color: red!important;
    }
  
            body {
                font-family: "Arial", sans-serif;
                background-color: #eaeaea;
                padding: 20px;
            }

            .container {
                max-width: 600px;
                margin: auto;
                background: white;
                padding: 20px;
                border-radius: 8px;
                box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
            }

            .result {
                margin-top: 20px;
                font-size: 1.1em;
                color: #333;
            }
     
</style>
@endsection
@section('content')
<div class="page-header">
  <div class="row align-items-center">
    <div class="col-md-4">
      <h3 class="page-title">Assign Holiday</h3>
      <ul class="breadcrumb">
        <li class="breadcrumb-item"><a href="admin-dashboard.html">Dashboard</a></li>
        <li class="breadcrumb-item active">Assign Holiday</li>
      </ul>
    </div>
   
  </div>
</div>

<div id="holiday" class="modal custom-modal fade" role="dialog">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Assign Holiday</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="assign-employee" method="post" enctype="multipart/form-data">
                    @csrf 
                <div class="row">

                 
                    <div class="col-sm-6">
                            <div class="input-block mb-3 ">
                                <label class="col-form-label" for="assign_label">Assign Employee <span
                                        class="text-danger">*</span></label>

                                <select class="form-select tagging assign " name="employee_id[]" multiple="multiple"
                                    id="assign_label">
                                   
                                  
                                </select>
                                <div id="assign"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                            <div class="input-block mb-3">
                                <label class="col-form-label" for="holiday_label">Holiday</label>
                                <select class="form-select holiday" name="off" id="hoildaySelect">
                                    <option disabled selected>SELECT OPTION</option>
                                   
                                        <option value="Saturday">Saturday</option>
                                        <option value="Sunday">Sunday</option>
                                        <option value="Monday">Monday</option>
                                        <option value="Tuesday">Tuesday</option>
                                        <option value="Wednesday">Wednesday</option>
                                        <option value="Thursday">Thursday</option>
                                        <option value="Friday">Friday</option>
                                </select>
                                <div id="company"></div>
                            </div>
                        </div>

                    <div class="submit-section">
                        <button class="btn btn-primary" id="">Submit</button>
                    </div>
        </form>
            
                </div>
        </div>
    </div>
</div>








<!-- PreLoader -->
<div id="loader" class="loader" style="display: none;">
    <div class="loader-animation"></div>
</div>
@endsection
@section('script-z')  
 <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
        <script>
 $(document).ready(function () {
        $('.tagging').select2({
            tags: true
        });
        $('#holiday').modal('show');
    });
</script>
@endsection 
