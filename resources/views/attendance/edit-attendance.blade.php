@extends('layout.mainlayout')
@section('css')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css" />
<style>
    .container {
        padding: 2rem 0rem;
    }


    .modal-header {
        .close {
            margin-top: -1.5rem;
        }
    }

    .form-title {
        margin: -2rem 0rem 2rem;
    }

    .btn-round {
        border-radius: 3rem;
    }

    .delimiter {
        padding: 1rem;
    }

    .social-buttons {
        .btn {
            margin: 0 0.5rem 1rem;
        }
    }

    .signup-section {
        padding: 0.3rem 0rem;
    }

    .progress-container {
        position: relative;
        width: 100px;
        height: 100px;
        margin: 20px auto;
    }

    .progress-circle {
        width: 100%;
        height: 100%;
        transform: rotate(-90deg);
    }

    .progress-bar {
        transition: stroke-dashoffset 0.35s;
    }

    .time-counter {
        position: absolute;
        top: 50% !important;
        left: 55px !important;
        transform: translate(-50%, -50%);
        font-size: 15px;
        font-weight: bold;
    }

    .punch-btn-section {

        min-width: 120px; /* Ensures buttons have a consistent size */
        font-size: 16px; /* Adjust font size for readability */
        padding: 10px 20px; /* Adjust padding for responsiveness *//* For alignment fallback */
    }

    .punch-btn-section .punch-btn {
        font-size: 18px !important;
        font-weight: 600;
        max-width: 100%;
        padding: 5px 20px !important;
        border-radius: 50px;
    }




    .dt-column-order {
        display: none !important;
    }

    .table tr.dayoff {
        --bs-table-bg: #D3D3D3;
    }

    .table tr.today {
        --bs-table-bg: #D3D3D3;
    }

    .table tr.late {
        --bs-table-bg:rgb(243, 112, 112);
    }
    .table tr.one {
        --bs-table-bg: #d9f7fffc;
    }
    .table tr.two {
        --bs-table-bg:rgb(147, 231, 122);
    }
    .table tr.three {
        --bs-table-bg: rgb(245, 143, 186);
    }
    .table tr.four {
        --bs-table-bg: #FF574A;  
    }
    .table tr.absent {
        --bs-table-bg: #C70039 ;  
    }

    table.dataTable th.dt-type-numeric,
    table.dataTable th.dt-type-date,
    table.dataTable td.dt-type-numeric,
    table.dataTable td.dt-type-date {
        text-align: center !important;
    }

    .fa-solid,
    .fas {
        color: #00c5fb !important;
        font-size: 20px;
    }

    .fa-times-circle lt {
        font-size: 20px !important;
        color: #f0f0f0 !important;
    }

    .fa-times-circle {
        font-size: 20px !important;
        color: #c85757 !important;
    }

    .recent-activity .res-activity-list {
        height: 180px !important;
    }
</style>
@endsection
@section('content')
<div id="notification" aria-live="polite" aria-atomic="true"></div>
<div class="page-header">
    <div class="row">
        <div class="col-sm-4">
            <h3 class="page-title">Attendance </h3>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="admin-dashboard.html">Dashboard</a></li>
                <li class="breadcrumb-item active">Attendance </li>
            </ul>
        </div>
        <div class="col-sm-8">
            <span class="mt-4 text-danger text-muted" style=""> Welcome! {{auth()->user()->username}} [
                {{auth()->user()->employee_id}} ] </span>
        </div>
    </div>
</div>
<!-- /Page Header -->
<input type="hidden" name="user_id" value="{{auth()->user()->id}}" id="userID">
<input type="hidden" name="id" value="" id="id">
<div class="row">

    <div class="col-md-4">
        <div class="card punch-status">
            <div class="card-body">
                <h5 class="card-title">Timesheet <small class="text-muted" id="punchDate">{{date('dS M Y')}}</small>
                </h5>
                <div class="punch-det">
                    <h6>Punch In at</h6>
                    <p id="punchInTime">--</p>
                </div>
                <div class="progress-container">
                    <svg class="progress-circle" viewBox="0 0 110 110">
                        <circle cx="55" cy="55" r="50" fill="none" stroke="#eee" stroke-width="8"></circle>
                        <circle class="progress-bar" cx="55" cy="55" r="50" stroke-dasharray="314"
                            stroke-dashoffset="314" fill="none" stroke="#3498db" stroke-width="8"
                            stroke-linecap="round"></circle>
                    </svg>
                    <div class="time-counter" id="timeCounter">0:00 hrs</div>
                </div>
                <div class="punch-btn-section d-flex justify-content-center mt-3">
                <!-- <button id="punchInBtn" class="btn btn-primary punch-btn mx-2">Punch In</button>
                <button id="punchOutBtn" class="btn btn-primary punch-btn mx-2" disabled>Punch Out</button> -->
            </div>

            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card att-statistics">
            <div class="card-body">
                <h5 class="card-title">Statistics</h5>
                <div class="stats-list">

                    <div class="stats-info">
                        <p>Late <strong><span id="day"></span> </strong></p>

                    </div>


                    <div class="stats-info">
                        <p>Total Hours <strong> <small><span id="hour"> </span></small></strong></p>

                    </div>

                    <div class="stats-info">
                        <p>Total Deduction <strong> <small><span id="deduction"> </span></small></strong></p>

                    </div>

                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card recent-activity">
            <div class="card-body">
                <h5 class="card-title">Today Activity</h5>
                <ul class="res-activity-list">
                    <li>
                        <p class="mb-0">Punch In at</p>
                        <p class="res-activity-time">
                            <i class="fa-regular fa-clock"></i>
                            <span id="check_in"></span>
                        </p>
                    </li>
                    <li>
                        <p class="mb-0">Punch Out at</p>
                        <p class="res-activity-time">
                            <i class="fa-regular fa-clock"></i>
                            <span id="check_out"></span>
                        </p>
                    </li>

                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Search Filter -->
<form method="post" id="searchData">
    @csrf
    <div class="row filter-row mt-5">
        <div class="col-sm-5">
            <label for=""> From date: </label>
            <div class="input-block mb-3 form-focus">
                <div class="cal-icon">
                    <input type="text" class="form-control floating datetimepicker" id="fromDate" name="fromDate">
                </div>
                <label class="focus-label">Date</label>
            </div>
        </div>
        <div class="col-sm-5">
            <label for=""> To date: </label>
            <div class="input-block mb-3 form-focus">
                <div class="cal-icon">
                    <input type="text" class="form-control floating datetimepicker" id="toDate" name="toDate">
                </div>
                <label class="focus-label">Date</label>
            </div>
        </div>

        <div class="col-sm-2  d-flex align-items-center">          
            <button type="button" class="btn btn-primary me-2" id="refresh" onclick="refreshDate()">
                <i class="fa fa-refresh" aria-hidden="true"></i>
            </button>       
            <button class="btn btn-primary" type="submit" id="submitButton"> Search </button>
        </div>
    </div>
</form>

        <div class="row">
            <div class="col-md-6">
            </div>
        <div class="col-md-6 text-end">
           <button id="deleteSelected" class="btn btn-danger">Bulk Delete</button>
        </div>
        </div>

<div class="row">
    <div class="col-lg-12">
        <div class="table-responsive">
            <table class="table custom-table mb-0" id="attendance-employee">
                <thead>
                    <tr>
                        <th>verify</th>
                        <th>Start Date</th>
                        <th>Shift In</th>
                        <th>End Date</th>
                        <th>Shift Out</th>
                        <th>Checkin Date</th>
                        <th>Check In</th>
                        <th>Checkout Date</th>
                        <th>Check Out</th>
                        <th>Duty Hours</th>
                        <th>Verify</th>
                        <th>Action</th> <!-- Action Column -->
                        <th><input type="checkbox" id="select-all"> </th> <!-- Checkbox Column at the End -->
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
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
<!--Edit schedule-->
<div id="schedule" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"  aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Schedule</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="schedule" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="row">

                      

                        <div class="col-sm-6">
                            <div class="input-block mb-3">
                                <label for="datepicker">Select Shift Start Dates:</label>
                                <input type="text" id="date-picker" name="startdate[]" class="form-control" />
                                <input type="hidden" name="startdate[]" id="startdate">

                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="input-block mb-3">
                                <label for="datepicker">Select Shift Start Time:</label>
                                <input type="time" id="time-input" name="start_time" class="form-control" />
                               
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="input-block mb-3">
                                <label for="datepicker2">Select Shift End Dates:</label>
                                <input type="text" id="date-picker2" name="enddate[]" class="form-control" />
                                <input type="hidden" name="enddate[]" id="enddate">

                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="input-block mb-3">
                                <label for="datepicker2">Select Shift End Time:</label>
                                <input type="time" id="end-time" name="end_time" class="form-control" />
                               
                            </div>
                        </div>

                            <input type="hidden" id="row_id" name="row_id" >
                    </div>

                    <div class="submit-section">
                        <button class="btn btn-primary" id="">Submit</button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>


</div>

<!--End -->

<!-- PreLoader -->
<div id="loader" class="loader" style="display: none;">
    <div class="loader-animation"></div>
</div>

@endsection
@section('script-z')  
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
    let uid=0;
    $(document).ready(function () {
        $("#fromDate").empty();
        $("#toDate").empty();
        uid = {{ $id }};
        statistics(uid);
        dataTab(uid);
    });

    function dataTab(id) {
        var date = moment();
        var format = date.format("D MMMM Y");
       
        $.ajax({
            url: "{{route('edit.attendance','')}}"+ "/" + id,
            type: 'GET',
            success: function (response) {
                console.log(response);

                $('#attendance-employee').DataTable({
                    destroy: true,
                    pageLength: 31,
                    data: response.data,
                    
                    columns: [
                        {
                            data: 'verify1',
                            title: 'Verify',
                            render: function (data, type, row) {

                                if (row.verify1 == "yes") { return ` <i class="fas fa-check-circle" ></i> `; }
                                else if (row.verify1 == "cross") { return ` <i class="fa fa-times-circle wt"></i> `; }

                                else {
                                    return "No Record";
                                }
                            }
                        },

                        
                        
                      
                        { data: 'start_date', title: 'Start Date' },
                        { data: 'shift_in', title: 'Shift In' },
                        { data: 'end_date', title: 'End Date' },
                        { data: 'shift_out', title: 'Shift Out' },
                        { data: 'in_date', title: 'Checkin Date' },
                        {
                            data: 'check_in',
                            title: 'Check In',
                            render: function (data, type, row) {
                                return row.check_in ? data : ' ';
                            }
                        },
                        { data: 'out_date', title: 'Checkout Date' },
                        {
                            data: 'check_out',
                            title: 'Check Out',
                            render: function (data, type, row) {
                                return row.check_out ? data : ' ';
                            }
                        },
                        {
                            data: 'duty_hours',
                            title: 'Duty Hours',
                            render: function (data, type, row) {
                                if (row.dayoff === "Yes") return "OFF";
                                if (row.color === '1') return "AL";
                                if (row.color === '2') return "BL";
                                if (row.color === '3') return "ML";
                                if (row.color === '4') return "UL";
                                if (row.absent === "Yes") return "Absent";
                                return data;
                            }
                        },
                        {
                            data: 'verify2',
                            title: 'Verify',
                            render: function (data, type, row) {
                                if (row.verify2 === "yes") {
                                    return `<i class="fas fa-check-circle"></i>`;
                                } else if (row.verify2 === "cross") {
                                    return `<i class="fa fa-times-circle"></i>`;
                                } else {
                                    return "No Record";
                                }
                            }
                        },
                        {
                            data: null,
                            title: 'Action',
                            render: function (data, type, row) {
                                console.log(row)
                                return `
                                    <i class="fas fa-edit edit-btn" data-id="${row.id}" style="cursor: pointer; margin-right: 10px;"></i>
                                    <i class="fas fa-trash delete-btn text-danger" data-id="${row.id}" style="cursor: pointer;color:#eb25d3!important;"></i> <!-- Red Delete Icon -->
                                `;
                            },
                            orderable: false
                        },
                        {
                        data: null,
                            title: '<input type="checkbox" id="select-all" />', 
                            render: function (data, type, row) {
                            return `<input type="checkbox" class="row-checkbox" data-id="${row.id}" />`;
                            },
                            orderable: false
                        },
                    ],
                    order: [],
                    createdRow: function (row, data, dataIndex) {
                        if (data.dayoff === "Yes") {
                            $(row).addClass("dayoff");
                        }

                        else if (data.color === '1') $(row).addClass("one");
                        else if (data.color === '2') $(row).addClass("two");
                        else if (data.color === '3') $(row).addClass("three");
                        else if (data.color === '4') $(row).addClass("four");
                        else if (data.absent === "Yes") $(row).addClass("absent");
                    }
                });

                
                $('#select-all-header').on('click', function () {
                    var isChecked = $(this).prop('checked');
                    $('.select-row').prop('checked', isChecked);
                });

               
                $('#attendance-employee').on('click', '.edit-btn', function () {
                    var id = $(this).data('id');
                   
                    
                });

                
                $('#attendance-employee').on('click', '.delete-btn', function () {
                    var id = $(this).data('id');
                   
                   
                });

            },
        });
    }
    
    $(document).on('change', '#select-all', function () {
        const isChecked = $(this).is(':checked');
        $('.row-checkbox').prop('checked', isChecked);
    });

    
    $(document).on('click', '#deleteSelected', function () {
        const selectedIds = [];
        $('.row-checkbox:checked').each(function () {
            selectedIds.push($(this).data('id'));
        });

        if (selectedIds.length === 0) {
            alert('Please select at least one record to delete.');
            return;
        }

        if (!confirm('Are you sure you want to delete the selected records?')) {
            return;
        }

       
        $.ajax({
            url: "{{ route('attendance.delete') }}", 
            type: "POST",
            data: {
                ids: selectedIds,
                _token: $('meta[name="csrf-token"]').attr('content') 
            },
            success: function (response) {
                alert('Selected records deleted successfully.');
                
                dataTab(uid);
            },
            error: function (err) {
                console.error(err);
                alert('An error occurred while deleting.');
            }
        });
    });

  
 $(document).on('click', '.delete-btn', function () {
        const id = $(this).data('id');
        deleteGroup(id);
    
    });

function deleteGroup(id) {
      groupId= id; 
    
    $('#deleteGroup').modal('show'); 
} 
$('#confirmDelete').on('click', function () {
    
    if (groupId) {
        $.ajax({
            url: "{{ route('attendance.delete.single') }}", 
            type: "POST",
            data: {
            id: groupId,
            _token: $('meta[name="csrf-token"]').attr('content') 
        },
            success: function (response) {
                $('#deleteGroup').modal('hide'); 
                createToast('info', 'fa-solid fa-circle-check', 'info', 'Record deleted successfully.');
        
            dataTab(uid);
        },
   
});
    }
}); 


    $('#searchData').on('submit', function (event) {
        event.preventDefault();
        var date = moment();
        var format = date.format("D MMMM Y");
        var fromDate = $('#fromDate').val();
        var toDate = $('#toDate').val();

        if ((fromDate != "") && (toDate != "")) {
            var formData = new FormData();
            formData.append('from_date', fromDate);
            formData.append('to_date', toDate);
            formData.append('id', uid);
            showLoader();
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                url: "{{ route('search.admin') }}",
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function (response) {
                    hideLoader();
                    var inf=response.in;
                    $('#day').empty();
                        $('#hour').empty();
                        $('#deduction').empty();



                    $('#day').append(`${inf[0].day}  Days `);
                    $('#hour').append(`${inf[0].hour} hr ${inf[0].minute} mins `);
                    $('#deduction').append(`${inf[0].deduction} % `);

                    $('#attendance-employee').DataTable({
                    destroy: true,
                    pageLength: 31,
                    data: response.data,
                    
                    columns: [
                        {
                            data: 'verify1',
                            title: 'Verify',
                            render: function (data, type, row) {

                                if (row.verify1 == "yes") { return ` <i class="fas fa-check-circle" ></i> `; }
                                else if (row.verify1 == "cross") { return ` <i class="fa fa-times-circle wt"></i> `; }

                                else {
                                    return "No Record";
                                }
                            }
                        },

                        
                        
                      
                        { data: 'start_date', title: 'Start Date' },
                        { data: 'shift_in', title: 'Shift In' },
                        { data: 'end_date', title: 'End Date' },
                        { data: 'shift_out', title: 'Shift Out' },
                        { data: 'in_date', title: 'Checkin Date' },
                        {
                            data: 'check_in',
                            title: 'Check In',
                            render: function (data, type, row) {
                                return row.check_in ? data : ' ';
                            }
                        },
                        { data: 'out_date', title: 'Checkout Date' },
                        {
                            data: 'check_out',
                            title: 'Check Out',
                            render: function (data, type, row) {
                                return row.check_out ? data : ' ';
                            }
                        },
                        {
                            data: 'duty_hours',
                            title: 'Duty Hours',
                            render: function (data, type, row) {
                                if (row.dayoff === "Yes") return "OFF";
                                if (row.color === '1') return "AL";
                                if (row.color === '2') return "BL";
                                if (row.color === '3') return "ML";
                                if (row.color === '4') return "UL";
                                if (row.absent === "Yes") return "Absent";
                                return data;
                            }
                        },
                        {
                            data: 'verify2',
                            title: 'Verify',
                            render: function (data, type, row) {
                                if (row.verify2 === "yes") {
                                    return `<i class="fas fa-check-circle"></i>`;
                                } else if (row.verify2 === "cross") {
                                    return `<i class="fa fa-times-circle"></i>`;
                                } else {
                                    return "No Record";
                                }
                            }
                        },
                        {
                            data: null,
                            title: 'Action',
                            render: function (data, type, row) {
                                console.log(row)
                                return `
                                    <i class="fas fa-edit edit-btn" data-id="${row.id}" style="cursor: pointer; margin-right: 10px;"></i>
                                    <i class="fas fa-trash delete-btn text-danger" data-id="${row.id}" style="cursor: pointer;color:#eb25d3!important;"></i> <!-- Red Delete Icon -->
                                `;
                            },
                            orderable: false
                        },
                        {
                        data: null,
                            title: '<input type="checkbox" id="select-all" />', 
                            render: function (data, type, row) {
                            return `<input type="checkbox" class="row-checkbox" data-id="${row.id}" />`;
                            },
                            orderable: false
                        },
                    ],
                    order: [],
                    createdRow: function (row, data, dataIndex) {
                        if (data.dayoff === "Yes") {
                            $(row).addClass("dayoff");
                        }

                        else if (data.color === '1') $(row).addClass("one");
                        else if (data.color === '2') $(row).addClass("two");
                        else if (data.color === '3') $(row).addClass("three");
                        else if (data.color === '4') $(row).addClass("four");
                        else if (data.absent === "Yes") $(row).addClass("absent");
                    }
                });

                
                $('#select-all-header').on('click', function () {
                    var isChecked = $(this).prop('checked');
                    $('.select-row').prop('checked', isChecked);
                });

               
                $('#attendance-employee').on('click', '.edit-btn', function () {
                    var id = $(this).data('id');
                   
                    
                });

                
                $('#attendance-employee').on('click', '.delete-btn', function () {
                    var id = $(this).data('id');
                   
                   
                });

                },
                error: function (err) {
                    hideLoader();
                    createToast('error', 'fa-solid fa-circle-exclamation', 'Error', 'Wrong Date selected. Last 3 months data Only');
                }
            });
        }
    });
    $(document).on('click', '.edit-btn', function () {
    const rowId = $(this).data('id'); 
    $('#id').append(rowId);   
    
    $.ajax({
        url: `/get-schedule/${rowId}`, 
        type: 'GET',
        success: function (response) {
            console.log(response)
            
            $('#date-picker').val(response.start_date);
            $('#time-input').val(convertTo24HourFormat(response.start_time));
            $('#date-picker2').val(response.end_date);
            $('#end-time').val(convertTo24HourFormat(response.end_time));
            $('#row_id').val(rowId);
            
            $('#schedule').modal('show');
        },
        error: function (error) {
            console.error('Error fetching data:', error);
            alert('Failed to fetch data for editing.');
        }
    });
});
function convertTo24HourFormat(time) {
    if (!time) return '';  
    
    const [timePart, ampm] = time.split(' ');  
    let [hours, minutes] = timePart.split(':').map(Number); 

    if (ampm.toUpperCase() === 'PM' && hours !== 12) {
        if (hours > 12) {
            hours -= 12; 
        }
    } else if (ampm.toUpperCase() === 'AM' && hours === 12) {
        hours = 0;  
    }

    console.log(`${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}${ampm}`)
    return `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}`;
}


const datePicker = flatpickr("#date-picker", {
        mode: "single",
        dateFormat: "Y-m-d",
        onChange: function (selectedDates) {

            const dates = datePicker.selectedDates;

            const timeValue = timeInput.value;

            const [startDate, endDate] = dates;
            const [hours, minutes] = timeValue.split(":").map(Number);
            const ampm = hours < 12 ? "AM" : "PM";
            const formattedHours = hours % 12 || 12;

            let resultArray = [];

            let currentDate = new Date(startDate);

            while (currentDate <= endDate) {
                const dateString = `${currentDate.getFullYear()}-${(
                    currentDate.getMonth() + 1
                )
                    .toString()
                    .padStart(2, "0")}-${currentDate
                        .getDate()
                        .toString()
                    } `;
                resultArray.push(dateString);
                currentDate.setDate(currentDate.getDate() + 1);
            }
            $('#startdate').val(resultArray);
           

        },
    });
    const datePicker2 = flatpickr("#date-picker2", {
        mode: "single",
        dateFormat: "Y-m-d",
        onChange: function (selectedDates) {



            const enddate = datePicker2.selectedDates;
            const endtime = endTime.value;

            const [startDate1, endDate1] = enddate;
            const [hours, minutes] = endtime.split(":").map(Number);
            const ampm = hours < 12 ? "AM" : "PM";
            const formattedHours = hours % 12 || 12;

            let resultArray2 = [];

            let currentDate = new Date(startDate1);

            while (currentDate <= endDate1) {
                const dateString = `${currentDate.getFullYear()}-${(
                    currentDate.getMonth() + 1
                )
                    .toString()
                    .padStart(2, "0")}-${currentDate
                        .getDate()
                        .toString()
                    } `;
                resultArray2.push(dateString);
                currentDate.setDate(currentDate.getDate() + 1);
            }
            $('#enddate').val(resultArray2);

        },
    });
    $(document).on('submit', '#schedule', function (e) {
    e.preventDefault();  
    const formData = $(this).serialize();  
    $.ajaxSetup({
       headers: {
           'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
       }
   });
    $.ajax({
        url: '{{route('schedule.update')}}',  
        type: 'POST',
        data: formData,
        success: function (response) {
            console.log(uid);

            if (response.success) {
                
                $('#schedule').modal('hide');

                dataTab(uid);
                createToast('info', 'fa-solid fa-circle-check', 'info', 'Schedule Updated successfully.');
            } 
        },
        
    });
});

    function refreshDate() {
        $('#fromDate').val('');
        $('#toDate').val('');
        statistics(uid);
        dataTab(uid);

    }
    function statistics(uid) {

        fetch(`/statistics-admin/${uid}`, {
            method: 'GET',
            headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json'
            }
        })
            .then(response => response.json())
            .then(data => {
           
                $('#day').empty();
                $('#hour').empty();
                $('#deduction').empty();
                $('#check_in').empty();
                $('#check_out').empty();
                if (data.status == 'success') {

                    $('#day').append(`${data.info[0].day}  Days `);
                    $('#hour').append(`${data.info[0].hour} hr ${data.info[0].minute} mins `);
                    $('#deduction').append(`${data.info[0].deduction} % `);
                    $('#check_in').append(`${data.info[0].check_in} . `);
                    $('#check_out').append(`${data.info[0].check_out} . `);
                }

            });
    }
    function clearValidationStates() {
        $('.form-control').removeClass('is-invalid is-valid');
        $('.text-danger').remove();

    }
    function valdateCancel() {

        $('#email').removeClass('is-invalid is-valid');
        $('#password').removeClass('is-invalid is-valid');

        $('.text-danger').remove();
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
    function validateEmail(selector) {
        let email = $(selector).val();
        let regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        let parent = $(selector).closest('.input-block');
        parent.find('.text-danger').remove();

        if (!email || !regex.test(email)) {
            $(selector).addClass('is-invalid');
            parent.append(`<span class="text-danger">Invalid email address.</span>`);
            return false;
        } else {
            $(selector).removeClass('is-invalid').addClass('is-valid');
            return true;
        }
    }
</script>

@endsection