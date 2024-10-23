@extends('layout.mainlayout')
@section('head')
<!-- Litepicker CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css"/>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<style>
   body {
        font-family: "Arial", sans-serif;
        background-color: #eaeaea;
        padding: 20px;
    }
    .container {
        max-width: 600px;
        height:100px;
        margin: auto;
        background: white;
        padding: 20px;
        border-radius: 8px;
        border-color:green;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
    }
    .select2-container .select2-selection--multiple{
        min-height:50px!important;
        border-color:#D3D3D4!important;
    }
     
</style>
@endsection
@section('content')
<div class="page-header">
  <div class="row align-items-center">
    <div class="col-md-4">
      <h3 class="page-title">Create Group</h3>
      <ul class="breadcrumb">
        <li class="breadcrumb-item"><a href="admin-dashboard.html">Dashboard</a></li>
        <li class="breadcrumb-item active">Create Group</li>
      </ul>
    </div>
   
  </div>
</div>
<div class="col-auto ms-auto mb-3">
    <ul class="split-head">
        <li>
            <a href="#" class="btn add-btn text-white" onclick="groupAdd()">
                <i class="fa fa-plus"></i> Add Group</a>
        </li>
    </ul>
</div>


<div id="notification" aria-live="polite" aria-atomic="true"></div>
<div class="row">
    <div class="col-md-12">
        <div class="table-responsive">
            <table class="table table-striped custom-table" id="groupTable">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Group Name</th>
                        
                        <th >Action</th>
                    </tr>
                </thead>
                <tbody id="group-list">
                </tbody>
            </table>
        </div>
    </div>
</div>
<div id="create-group" class="modal custom-modal fade" role="dialog">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Create Group</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form  method="POST" enctype="multipart/form-data" id="group-submit">
                    @csrf
                    <div class="row">
                    <div class="input-block mb-3">
                        
                            <div class="input-block mb-3">
                                <label for="groupName">Group Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="group_name" id="groupName" >
                            </div>
                        
                    </div>
                    </div>

                    <!-- Multiple Select With Search -->
                    <div class="col-sm-12">
                        <div class="input-block mb-3">
                            <label for="selectEmployee">Select Employee <span class="text-danger">*</span></label>
                            <select class="form-control select2" multiple="multiple" name="employee_id[]" id="selectEmployee" style="width: 100%;" >
                               
                            </select>
                        </div>
                    </div>
                    <!-- Multiple Select With Search End-->
                    <div class="submit-section">
                        <button class="btn btn-primary">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


<div id="create-group" class="modal custom-modal fade" role="dialog">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Create Group</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
            <table id="changeGroup" class="table table-striped" style="width:100%">
              <thead>
                <tr>
                  <th>#</th>
                  <th>Employee Name</th>
                  <th>Group Name</th>  
               </tr>
             </thead>
             <tbody>

             </tbody>
            </table>
            </div>
        </div>
    </div>
</div>


<!--Delete Modal -->
<div class="modal fade" id="deleteGroup" tabindex="-1" aria-labelledby="exampleModalScrollable2"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="modalTitle">Delete Confirmation</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this Group? This action cannot be undone.</p>
            </div>
            
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDelete">Delete</button>
            </div>
        </div>
    </div>
</div>

<!--dDelete Modal End -->

<!-- PreLoader -->
<div id="loader" class="loader" style="display: none;">
    <div class="loader-animation"></div>
</div>
@endsection
@section('script-z')  
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>

$(document).ready(function () {
    
    const selectElement = $('#selectEmployee');
    if (selectElement.length) {
        selectElement.select2({
            placeholder: 'Search Employees',
            allowClear: true,
            width: '100%'
        });
    } else {
        console.error('Select Employee element not found.');
    }
    let table = $('#groupTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('group.data') }}",
            type: 'GET'
        },
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'name', name: 'name', orderable: false },
            {
                data: 'action', 
                name: 'action',
                orderable: false,
                searchable: false,
                render: function (data, type, row) {
                    return `
                            <button class="btn btn-info " onclick="showGroup(${row.id})">
                                <i class="fa fa-eye fa-1x"></i>
                            </button>
                            <button class="btn btn-danger " onclick="deleteGroup(${row.id})">
                                <i class="fa fa-trash fa-1x"></i>
                            </button>`;
                }
            }
        ],
        order: []
    });

    $('#selectEmployee').on('change', function() {
        var selectedValues = $(this).val();
      
      
        $('#selectEmployee').select2({
            placeholder: 'Search Employees',
            allowClear: true,
            closeOnSelect: true,
        });
    });
});

function loadEmployees() {
    $.ajax({
        url: '{{ route('group.employee') }}',
        type: 'GET',
        success: function(response) {
            var select = $('#selectEmployee');
            select.empty(); 

            $.each(response.employee, function(key, value) {
                select.append('<option value="' + value.id + '">' + value.name + '</option>');
            });

          
            select.select2({
                placeholder: 'Search Employees',
                allowClear: true,
                width: '100%',
                closeOnSelect: false
            });
        },
        error: function() {
            alert('Error fetching employee data.');
        }
    });
}



function groupAdd() {
    $('#groupName').val(''); 
    clearValidationStates(); 
    loadEmployees();

    $('#create-group').modal('show').on('shown.bs.modal', function () {
        if (!$('#selectEmployee').hasClass("select2-hidden-accessible")) {
            $('#selectEmployee').select2({
                placeholder: 'Search Employees',
                allowClear: true,
                width: '100%',
                closeOnSelect: false
            });
        }
    });
}

$('#create-group').on('hidden.bs.modal', function() {
    $('#selectEmployee').val(null).trigger('change');
});

$('#group-submit').on('submit', function (event) {
event.preventDefault();
$('.select').select2();
var formData = new FormData();
console.log($('#groupName').val());
 formData.append('group_name', $('#groupName').val());  
  
var selectedEmployee = $('#selectEmployee').val(); 
if (selectedEmployee) {
selectedEmployee.forEach(function (employee_id) {
formData.append('employee_id[]',employee_id);
  });
}
var isValid = true;
clearValidationStates();
if (!validateEmployee(selectedEmployee)) isValid = false;
if (!validateField('#groupName', 'Group Name')) isValid = false;
if (isValid) {
   showLoader();
   $.ajaxSetup({
       headers: {
           'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
       }
   });

   $.ajax({
       url: "{{ route('group.store') }}",
       type: 'POST',
       data: formData,
       processData: false,
       contentType: false,
       success: function (response) {
           hideLoader();
           $('#create-group').modal('hide');
         
            $('#groupTable').DataTable().ajax.reload();
           createToast('info', 'fa-solid fa-circle-check', 'info', 'Group created successfully.');
          
},
       error: function (data) {
           hideLoader();
           
           var errors = data.responseJSON;
           createToast('info', 'fa-solid fa-circle-uncheck', 'Fail',data.responseJSON.employee_name+' ' + 'Schedule already exist.');
       }
   });
}
});

function clearValidationStates() {
            $('.form-control').removeClass('is-invalid is-valid'); 
            $('.text-danger').remove();

        }
function valdateCancel() {
          
          $('#selectEmployee').removeClass('is-invalid is-valid');
          $('#groupName').removeClass('is-invalid is-valid');
          
          $('.text-danger').remove();
      }
function validateEmployee(selectedEmployee) {
            let parent = $('#selectEmployee').closest('.input-block');
            parent.find('.text-danger').remove(); 

            if (!selectedEmployee || selectedEmployee.length === 0) {
                $('#assign_label').addClass('is-invalid');
                parent.append('<span class="text-danger">Please select at least one Employee.</span>');
                return false;
            } else {
                $('#assign_label').removeClass('is-invalid').addClass('is-valid');
                return true;
            }
        }  
function validateField(selector, fieldName) {
            let value = $(selector).val();
            let parent = $(selector).closest('.input-block'); 
            parent.find('.text-danger').remove(); 

            if (!value) {
                $(selector).addClass('is-invalid');
                parent.append(`<span class="text-danger">${fieldName} field cannot be empty.</span>`);
                return false;
            } else {
                $(selector).removeClass('is-invalid').addClass('is-valid'); 
                return true;
            }
        }

$('#confirmDelete').on('click', function () {
    
    if (groupId) {
        $.ajax({
            url: '/group/' + groupId,
            type: 'DELETE',
            data: {
                _token: "{{ csrf_token() }}" 
            },
            success: function (result) {
                
                
                $('#groupTable').DataTable().ajax.reload();
                $('#deleteGroup').modal('hide'); 
                
                createToast('info', 'fa-solid fa-circle-check', 'info', 'Group deleted successfully.');
            },
            error: function (err) {
                createToast('error', 'fa-solid fa-circle-exclamation', 'Error', 'Error deleting .');
            }
        });
    }
}); 
function deleteGroup(id) {
      groupId= id; 
    
    $('#deleteGroup').modal('show'); 
}  
function showGroup(id)
{
    group_id=id;
    
    if (group_id) {
        $.ajax({
            url: "{{ route('group.member', '') }}" + "/" +group_id,
            type: 'get',
            data: {
                _token: "{{ csrf_token() }}" 
            },
            success: function (response) {
                
                console.log(getName(response[0]))
              
                
                
            },
            error: function (err) {
                createToast('error', 'fa-solid fa-circle-exclamation', 'Error', 'Error deleting .');
            }
        });
    }



} 
</script>
@endsection


