@extends('layout.mainlayout')
@section('css')
<!-- Litepicker CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css"/>


<style>
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
   .selection{
    display:none;
   }
   .dt-column-order{
    display:none!important;
 }

 .dt-type-numeric
 {
   
 }
</style>
@endsection
@section('content')
<div id="notification" aria-live="polite" aria-atomic="true"></div>
<div class="page-header">
  <div class="row align-items-center">
    <div class="col-md-4">
      <h3 class="page-title">Create Team </h3>
      <ul class="breadcrumb">
        <li class="breadcrumb-item"><a href="admin-dashboard.html">Dashboard</a></li>
        <li class="breadcrumb-item active">Create team</li>
      </ul>
    </div>
   
  </div>
</div>
<div class="col-auto ms-auto mb-3">
    <ul class="split-head">
        <li>
            <button class="btn add-btn text-white" onclick="groupAdd()">
                <i class="fa fa-plus"></i> Create Team</button>
        </li>
    </ul>
</div>



<div class="row">
    <div class="col-md-12">
        <div class="table-responsive">
            <table class="table table-striped text-left" id="groupTable">
                <thead class="text-left">
                   
                </thead>
                <tbody id="group-list" >
                </tbody>
            </table>
        </div>
    </div>
</div>
<div id="create-group" class="modal custom-modal fade" role="dialog">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Create Team</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form  method="POST" enctype="multipart/form-data" id="team-submit">
                    @csrf
                    <div class="row">
                    <div class="input-block mb-3">
                        
                            <div class="input-block mb-3">
                                <label for="selectLeader">Leader Name <span class="text-danger">*</span></label>
                                 <select class="form-select" name="leader_id[]" id="selectLeader"  multiple >
                                    
                                </select>
                            </div>
                        
                    </div>
                    </div>

                    <!-- Multiple Select With Search -->
                    <div class="col-sm-12">
                        <div class="input-block mb-3">
                            <label for="selectEmployee">Select Employee <span class="text-danger">*</span></label>
                            <select class="form-select" name="employee_id[]" id="selectEmployee"  multiple >
                               
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


<!-- Edit Modal -->
<div id="edit-team" class="modal custom-modal fade" role="dialog">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Team</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form  method="POST" enctype="multipart/form-data" id="team-update">
                    @csrf
                    <div class="row">
                    <div class="input-block mb-3">
                        
                            <div class="input-block mb-3">
                                <label for="edit-leader">Leader Name <span class="text-danger">*</span></label>
                                 <select class="form-select" name="leader_id[]" id="edit-leader"  multiple >
                                    
                                </select>
                            </div>
                        
                    </div>
                    </div>

                    <!-- Multiple Select With Search -->
                    <div class="col-sm-12">
                        <div class="input-block mb-3">
                            <label for="selectEmployee">Select Employee <span class="text-danger">*</span></label>
                            <select class="form-select" name="employee_id[]" id="edit-emp"  multiple >
                               
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
<!-- End -->
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
                <p>Are you sure you want to delete this Team Leader? This action cannot be undone.</p>
            </div>
            
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDelete">Delete</button>
            </div>
        </div>
    </div>
</div>

<!--dDelete Modal End -->


  <!-- The Modal -->
<div class="modal fade" id="employeeList"  aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">

      <!-- Modal Header -->
    
      <div class="modal-header ">
        
        <h4 class="modal-title">Group Details </h4>
        <button type="button"  class="close" data-bs-dismiss="modal" style="margin-left:300px;">&times;</button>
      </div>
      <div class="row mt-2" style="margin-left:10px;" id="groupInfo">
       
      </div>
      
      <!-- Modal body -->
      <div class="modal-body">
          <table class="table table-striped" >
      <thead>
       <tr class="text-center">
        <th>Employee Name</th>
        <th>Group Name</th>
        
      </tr>
    </thead>
    <tbody id="employeeData">
  
      
    </tbody>
  </table>
  </div>

      <!-- Modal footer -->
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Close</button>
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
<!-- <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script> -->



<script>

$(document).ready(function () {
   
    $('#selectEmployee').select2({
            placeholder: 'Search Employees',
            allowClear: true,
            width: '100%',
            closeOnSelect: false,
            multiple: true
        });
    $('#selectLeader').select2({
            placeholder: 'Search Employees',
            allowClear: true,
            width: '100%',
            closeOnSelect: false,
           
        });
    
        tableInfo();
    });

 function tableInfo()
 {
    

    $.ajax({ 
            url: "{{ route('data.datatable') }}",
            type: 'GET',
       
            success: function (data) {
            console.log(data); 
           
         
            $('#groupTable').DataTable({
                destroy: true,
                data: data.leaders, 
                
                columns: [
                    { data: 'lid', title: 'Leader Id' },
                    { data: 'name', title: 'Leader Name' },
                    {
                    data: 'action', 
                   title: 'action',
                    orderable: false,
                    searchable: false,
                    render: function (data, type, row) {
                    return `
                             
                            <button class="btn btn-primary" onclick="editTeam(${row.lid})">
                                <i class="fa fa-edit fa-1x"></i>
                            </button>
                            <button class="btn btn-danger" onclick="deleteTeam(${row.lid})">
                                <i class="fa fa-trash fa-1x"></i>
                            </button>`;
                }
            }     
                 
                    
                   
                ]
            });

          
        
        },
    });

 }   


function loadEmployees() {
    $.ajax({
        url: '{{ route('team.data') }}',
        type: 'GET',
        success: function(response) {

            console.log(response)


            var select = $('#selectEmployee');
            var leader = $('#selectLeader');
             select.empty(); 
             leader.empty();
            $.each(response.employees, function(key, value) {
                select.append('<option value="' + value.id + '">' + value.username + '</option>');
                leader.append('<option value="' + value.id + '">' + value.username + '</option>');
                
            });
  
            if (!select.data('multiselect-initialized')) {
                new MultiSelectTag("selectEmployee", {
                    rounded: true,
                    shadow: false,
                    placeholder: "Search",
                    tagColor: {
                        textColor: "#327b2c",
                        borderColor: "#92e681",
                        bgColor: "#eaffe6"
                    }
                });
              select.data('multiselect-initialized', true); 
            }          
            if (!leader.data('multiselect-initialized')) {
                new MultiSelectTag("selectLeader", {
                    rounded: true,
                    shadow: false,
                    placeholder: "Search",
                    tagColor: {
                        textColor: "#327b2c",
                        borderColor: "#92e681",
                        bgColor: "#eaffe6"
                       
                    }
                });
              leader.data('multiselect-initialized', true); 
            } 
        },
        error: function(err) {
            createToast('error', 'fa-solid fa-circle-exclamation', 'Error', 'Error loading employees.');
         }
    });
}



function groupAdd() {
   
    clearValidationStates(); 
    loadEmployees();

    $('#create-group').modal('show').on('shown.bs.modal', function () {
        
        if (!$('#selectLeader').hasClass("select2-hidden-accessible")) {
            $('#selectLeader').select2({
                placeholder: 'Search Employees',
                allowClear: true,
                width: '100%',
                closeOnSelect: false,
                
            });
        }
        if (!$('#selectEmployee').hasClass("select2-hidden-accessible")) {
            $('#selectEmployee').select2({
                placeholder: 'Search Employees',
                allowClear: true,
                width: '100%',
                closeOnSelect: false,
                multiple:false
            });
        }

    });
    
}

$('#create-group').on('hidden.bs.modal', function() {
    $('#selectEmployee').val(null).trigger('change');
    $('#selectLeader').val(null).trigger('change');
});

$('#team-submit').on('submit', function (event) {
event.preventDefault();
$('.select').select2();
var formData = new FormData();

  
var selectedEmployee = $('#selectEmployee').val();
var selectedLeader = $('#selectLeader').val();
formData.append('employee_id',selectedEmployee);
formData.append('leader_id',selectedLeader);

console.log(formData.leader_id)
var isValid = true;
clearValidationStates();
if (!validateEmployee(selectedEmployee)) isValid = false;
if (!validateLeader(selectedLeader)) isValid = false;
if (isValid) {
   showLoader();
   $.ajaxSetup({
       headers: {
           'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
       }
   });

   $.ajax({
       url: "{{ route('team.store') }}",
       type: 'POST',
       data: formData,
       processData: false,
       contentType: false,
       success: function (response) {
           hideLoader();
           $('#create-group').modal('hide');   
           tableInfo();
           createToast('info', 'fa-solid fa-circle-check', 'info', 'Team created successfully.');
          
          
},
       error: function (data) {
           hideLoader();
           $('#create-group').modal('hide');
           var errors = data.responseJSON;
           createToast('info', 'fa-solid fa-circle-uncheck', 'Fail', 'Team Leader Exist.Edit please');
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
        
function validateLeader(selectedLeader) {
            let parent = $('#selectLeader').closest('.input-block');
            parent.find('.text-danger').remove(); 

            if (!selectedLeader || selectedLeader.length === 0) {
                $('#assign_label').addClass('is-invalid');
                parent.append('<span class="text-danger">Please select at least one Leader.</span>');
                return false;
            } else {
                $('#assign_label').removeClass('is-invalid').addClass('is-valid');
                return true;
            }
        }      
        


$('#confirmDelete').on('click', function () {
   
    if (leaderId) {
       
        $.ajax({
            url: "{{route('delete.team','')}}"+ "/" + leaderId,
            type: 'Get',
            data: {
                _token: "{{ csrf_token() }}" 
            },
            success: function (result) {
                
               
                tableInfo();
                $('#deleteGroup').modal('hide'); 
                
                createToast('info', 'fa-solid fa-circle-check', 'info', 'Team deleted successfully.');
            },
            error: function (err) {
                createToast('error', 'fa-solid fa-circle-exclamation', 'Error', 'Error deleting .');
            }
        });
    }
}); 
function deleteTeam(id) {
      leaderId= id; 
    
    $('#deleteGroup').modal('show'); 
} 

function editTeam(id) {
       
        valdateCancel();

        $.ajax({
            url: "{{route('edit.team','')}}"+ "/" + id,
            type: 'GET',
            success: function (response) {
                var select = $('#edit-emp');
                var leader = $('#edit-leader');
                select.empty(); 
                leader.empty();
                $.each(response.employee, function (key, value) {
                  res="false";
                 
                    $.each(response.info, function (key, evalue) {
                       
                                if (value['id'] == evalue['eid']) {
                                    $("#edit-emp").append('<option value="' + evalue['eid'] + '" selected>' + evalue['name'] +
                                        '</option>');
                                    res="true";    
                                }

                               
                            });

                            leader.empty();        
                    $.each(response.leader, function (key, lvalue) {
                     
                       if (value['id'] == lvalue['id']) {
                           $("#edit-leader").append('<option value="' + lvalue['id'] + '" selected>' + lvalue['username'] +
                               '</option>');
                           res="true";    
                        }
                        col="true";
                      
                    });             
                          if(res=="false")
                          {
                            $("#edit-emp").append('<option value="' + value['id'] + '" >' + value['username'] +
                                        '</option>');
                            
                            }
                           

                            }); 
                        
                            $.each(response.employee, function (key, value) {
                                col="false";
                                
                                $.each(response.leader, function (key, lvalue) {
                                   
                                    if (value['id'] == lvalue['id']) {
                                                 $("#edit-leader").append('<option value="' + value['id'] + '" selected>' + value['username'] +
                                                '</option>');
                                                
                                                col="true";      
                                        
                                            }
                                       
                    
                                     });             
                       
                                 if(col=="false")
                                    {
                                        $("#edit-leader").append('<option value="' + value['id'] + '" >' + value['username'] +
                                        '</option>');
                          
                                    }

                          }); 

      
         if (!select.data('multiselect-initialized')) {
         new MultiSelectTag("edit-emp", {
            rounded: true,
            shadow: false,
            placeholder: "Search",
            tagColor: {
                textColor: "#327b2c",
                borderColor: "#92e681",
                 bgColor: "#eaffe6"
            }
        });
            select.data('multiselect-initialized', true); 
        }   
        if (!leader.data('multiselect-initialized')) {
         new MultiSelectTag("edit-leader", {
            rounded: true,
            shadow: false,
            placeholder: "Search",
            tagColor: {
                textColor: "#327b2c",
                borderColor: "#92e681",
                 bgColor: "#eaffe6"
            }
        });
            leader.data('multiselect-initialized', true); 
        }  
             
                 $('#edit-team').modal('show');
            },
            error: function (error) {
                alert('Error fetching employee details.');
            }
        });
    }

    $('#team-update').on('submit', function (event) {
        event.preventDefault();
      
      
        valdateCancel();
        var formData = new FormData();
        var isValid = true;

        var leader_id = $('#edit-leader').val();
       
        var employee_id= $('#edit-emp').val(); 
        formData.append('employee_id',employee_id);
        formData.append('leader_id',leader_id);
       

        if (!validateeEmployee(employee_id)) isValid = false;
        if (!validateeLeader(leader_id)) isValid = false;
       

        if (isValid) {
           
            showLoader();
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: "{{route('update.team')}}",
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function (response) {

                    hideLoader();
                    $('#edit-team').modal('hide');
                    createToast('info', 'fa-solid fa-circle-check', 'Success', 'Team Updated successfully.');
                    tableInfo();
                },
                error: function (error) {
                    hideLoader();
                    createToast('error', 'fa-solid fa-circle-exclamation', 'Error', 'Error Updating Team.');
                }
            });

        }



    });

function validateeEmployee(selectedEmployee) {
            let parent = $('#edit-emp').closest('.input-block');
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
        
function validateeLeader(selectedLeader) {
            let parent = $('#edit-leader').closest('.input-block');
            parent.find('.text-danger').remove(); 

            if (!selectedLeader || selectedLeader.length === 0) {
                $('#assign_label').addClass('is-invalid');
                parent.append('<span class="text-danger">Please select at least one Leader.</span>');
                return false;
            } else {
                $('#assign_label').removeClass('is-invalid').addClass('is-valid');
                return true;
            }
        }   

</script>
@endsection


