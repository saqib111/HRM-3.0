@extends('layout.mainlayout')
@section('content')

    @section('css')
        <style>
            /* Ensure the parent container is positioned correctly */
            .multi-select-container {
                position: relative;
                display: inline-block;
                width: 100%;
                /* Full width for the container */
            }

            /* Style for the dropdown button */
            .multi-select-btn {
                padding: 10px 15px;
                background-color: #ffffff;
                border: 1px solid #cccccc;
                border-radius: 4px;
                cursor: pointer;
                width: 100%;
                /* Full width for the button */
                text-align: left;
                box-sizing: border-box;
                font-size: 14px;
                color: #333333;
                display: flex;
                justify-content: space-between;
                align-items: center;
            }

            /* Dropdown menu options */
            .multi-select-options {
                display: none;
                position: absolute;
                z-index: 1000;
                background-color: white;
                border: 1px solid #cccccc;
                border-radius: 4px;
                width: 100%;
                /* Match dropdown to button width */
                max-height: 150px;
                overflow-y: auto;
                /* Scroll when content exceeds max-height */
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
                margin-top: 5px;
                box-sizing: border-box;
            }

            /* Individual label styling */
            .multi-select-options label {
                display: block;
                padding: 10px;
                cursor: pointer;
                font-size: 14px;
                color: #333333;
            }

            /* Highlight a label on hover */
            .multi-select-options label:hover {
                background-color: #f0f0f0;
            }

            /* Checkbox styling */
            input[type="checkbox"] {
                margin-right: 10px;
            }

            /* Ensure that the checkbox list doesn't overflow horizontally */
            .multi-select-options {
                overflow-x: hidden;
                /* Prevent horizontal overflow */
            }
        </style>
    @endsection

    @php
        $user = auth()->user();
        $permissions = getUserPermissions($user); // Use the helper function to fetch permissions

        $profileId = Route::current()->parameter('id');
    @endphp

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
                    <li class="breadcrumb-item"><a href="admin-dashboard.html">Dashboard</a></li>
                    <li class="breadcrumb-item active"><span data-translate="profile">Profile</span></li>
                </ul>
            </div>
        </div>
    </div>


    <!-- /Page Header -->
    <div class="card mb-0">
        <div class="card-body">
            <h3 class="pb-4"><span data-translate="personal_information">Personal Information</span></h3>
            <div class="row">
                <div class="col-md-12">
                    <div class="profile-view">
                        <div class="profile-img-wrap">
                            <div class="profile-img">
                                <a href="#"><img src="../uploads/{{ $mainUser->image}}" alt="User Image"></a>
                            </div>
                        </div>
                        <div class="profile-basic">
                            <div class="row">
                                <div class="col-md-5">
                                    <div class="profile-info-left" id="profileDisplay">
                                        <h3 class="user-name m-t-0 mb-0">{{ $mainUser->username }}</h3>
                                        <div class="text">{{ $mainUser->email }}</div>
                                        <h6 class="text-muted">{{ $mainUser->designationName }}</h6>
                                        <small class="text-muted">{{ $mainUser->departmentName }}</small>
                                        <div class="staff-id">Employee ID : {{ $mainUser->employee_id }}</div>
                                        <div class="small doj text-muted">Date of Join :
                                            {{ $mainUser->joining_date }}
                                        </div>
                                        @if ($mainUser->status == '1')
                                            <a href="#" class="btn_active">Active </a>
                                        @elseif($mainUser->status == '0')
                                            <a href="#" class="btn_inactive">Inactive </a>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-7">
                                    <ul class="personal-info">
                                        <li>
                                            <div class="title"><span data-translate="real_name">Real Name:</span></div>
                                            <div class="text" id="realName">
                                                {{ $profileUser->real_name }}
                                            </div>
                                        </li>


                                        <li>
                                            <div class="title"><span data-translate="birthday">Birthday:</span></div>
                                            <div class="text" id="dateofbirth">{{ $profileUser->dob }}</div>
                                        </li>
                                        <li>
                                            <div class="title"><span data-translate="accomodation">Accomodation:</span>
                                            </div>
                                            <div class="text" id="accommodation">{{ $profileUser->accomodation }}
                                            </div>
                                        </li>
                                        <li>
                                            <div class="title"><span data-translate="gender">Gender:</span></div>
                                            <div class="text" id="gender">{{ $profileUser->gender }}</div>
                                        </li>
                                        <li>
                                            <div class="title"><span data-translate="phone">Phone:</span></div>
                                            <div class="text" id="phone">{{ $profileUser->phone }}</div>
                                        </li>

                                        <li>
                                            <div class="title"><span data-translate="weekly_working_days">Weekly Working
                                                    Days:</span></div>
                                            <div class="text" id="weekDays">
                                                {{ $mainUser->week_days == 5 ? '5 Days' : '6 Days' }}
                                            </div>
                                        </li>

                                        <li>
                                            <div class="title"><span data-translate="nationality">Nationality:</span></div>
                                            <div class="text" id="nationality">{{ $profileUser->nationality }}</div>
                                        </li>
                                        <li>
                                            <div class="title"><span data-translate="office">Office:</span></div>
                                            <div class="text" id="office">{{ $profileUser->office }}</div>
                                        </li>
                                        <li>
                                            <div class="title"><span data-translate="telegram">Telegram:</span></div>
                                            <div class="text" id="telegram">{{ $profileUser->telegram }}</div>
                                        </li>
                                        <li>
                                            <div class="title"><span data-translate="unpaid_leaves">UnPaid Leaves
                                                    (UL):</span></div>
                                            <div class="text" id="allowed_ul">
                                                @php
                                                    // Convert the comma-separated string into an array
                                                    $allowedUlArray = explode(',', $profileUser->allowed_ul);

                                                    // Map the numbers to their corresponding leave types
                                                    $leaveTypes = [
                                                        4 => 'UL',  // Unpaid Leave
                                                        5 => 'HL',  // Hospitalisation Leave
                                                        6 => 'CL',  // Compassionate Leave
                                                        7 => 'MTL', // Maternity Leave
                                                        8 => 'PL'  // Paternity Leave
                                                    ];

                                                    // Map the numbers in allowedUlArray to their respective leave types
                                                    $mappedLeaveTypes = array_map(function ($number) use ($leaveTypes) {
                                                        return isset($leaveTypes[(int) $number]) ? $leaveTypes[(int) $number] : $number;
                                                    }, $allowedUlArray);

                                                    // Join the leave types into a comma-separated string
                                                    $leaveNamesString = implode(', ', $mappedLeaveTypes);
                                                @endphp

                                                {{ $leaveNamesString }}
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        @if ($profileId == auth()->user()->id)

                        @elseif($user->role == 1 || in_array('update_employee_info', $permissions))
                            <div class="pro-edit"><a data-bs-target="#profile_info" data-bs-toggle="modal" class="edit-icon"
                                    href="#"><i class="fa-solid fa-pencil"></i></a></div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card tab-box">
        <div class="row user-tabs">
            <div class="col-lg-12 col-md-12 col-sm-12 line-tabs">
                <ul class="nav nav-tabs nav-tabs-bottom" role="tablist">
                    <li class="nav-item" role="presentation"><a href="#emp_profile" data-bs-toggle="tab"
                            class="nav-link active" aria-selected="true" role="tab"><span
                                data-translate="profile">Profile</span></a></li>
                    <li class="nav-item" role="presentation"><a href="#emp_officeEquip" data-bs-toggle="tab"
                            class="nav-link" aria-selected="false" tabindex="-1" role="tab">Office Equipment</a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a href="#emp_dependant" data-bs-toggle="tab" class="nav-link" aria-selected="false" tabindex="-1"
                            role="tab"><span data-translate="dependant">Dependant</span></a>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <div class="tab-content p-0">

        <!-- Profile Info Tab -->
        <div id="emp_profile" class="pro-overview tab-pane fade show active" role="tabpanel">
            <div class="row">
                <div class="col-md-6 d-flex">
                    <div class="card profile-box flex-fill">
                        <div class="card-body">
                            <h3 class="card-title"><span data-translate="passport_and_visa_information">Passport & Visa
                                    Infomation</span>
                                @if ($profileId == auth()->user()->id)

                                @elseif($user->role == 1 || in_array('update_employee_info', $permissions))
                                    <a href="#" class="edit-icon" data-bs-toggle="modal"
                                        data-bs-target="#personal_info_modal"><i class="fa-solid fa-pencil"></i></a>
                                @endif
                            </h3>
                            <ul class="personal-info">
                                <li>
                                    <div class="title"><span data-translate="passport_number">Passport Number:</span></div>
                                    <div class="text" id="passport_no">{{ $visaInfo->passport_no }}</div>
                                </li>
                                <li>
                                    <div class="title"><span data-translate="passport_issue_date">Passport Issue
                                            Date:</span></div>
                                    <div class="text" id="p_issue_date">
                                        @if($visaInfo->p_issue_date)
                                            {{ \Carbon\Carbon::parse($visaInfo->p_issue_date)->format('d-M-Y') }}
                                        @else

                                        @endif
                                    </div>
                                </li>
                                <li>
                                    <div class="title"><span data-translate="passport_expiry_date">Passport Exp Date:</span>
                                    </div>
                                    <div class="text" id="p_expiry_date">
                                        @if($visaInfo->p_expiry_date)
                                            {{ \Carbon\Carbon::parse($visaInfo->p_expiry_date)->format('d-M-Y') }}
                                        @else

                                        @endif
                                    </div>
                                </li>
                                <li>
                                    <div class="title"><span data-translate="visa_number">Visa Number:</span></div>
                                    <div class="text" id="visa_no">{{ $visaInfo->visa_no }}</div>
                                </li>
                                <li>
                                    <div class="title"><span data-translate="visa_issue_date">Visa Issue Date:</span></div>
                                    <div class="text" id="v_issue_date">
                                        @if($visaInfo->v_issue_date)
                                            {{ \Carbon\Carbon::parse($visaInfo->v_issue_date)->format('d-M-Y') }}
                                        @else

                                        @endif
                                    </div>
                                </li>
                                <li>
                                    <div class="title"><span data-translate="visa_expiry_date">Visa Expiry Date:</span>
                                    </div>
                                    <div class="text" id="v_expiry_date">
                                        @if($visaInfo->v_expiry_date)
                                            {{ \Carbon\Carbon::parse($visaInfo->v_expiry_date)->format('d-M-Y') }}
                                        @else

                                        @endif
                                    </div>
                                </li>
                                <li>
                                    <div class="title"><span data-translate="foreign_number">Foreign Number:</span></div>
                                    <div class="text" id="foreign_no">{{ $visaInfo->foreign_no }}</div>
                                </li>
                                <li>
                                    <div class="title"><span data-translate="foreign_expiry_date">Foreign Expiry
                                            Date:</span></div>
                                    <div class="text" id="f_expiry_date">
                                        @if($visaInfo->f_expiry_date)
                                            {{ \Carbon\Carbon::parse($visaInfo->f_expiry_date)->format('d-M-Y') }}
                                        @else

                                        @endif
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- EmergencyContact -->
                <div class="col-md-6 d-flex">
                    <div class="card profile-box flex-fill">
                        <div class="card-body">
                            <h3 class="card-title"><span data-translate="emergency_contact">Emergency Contact</span>
                                @if ($profileId == auth()->user()->id)

                                @elseif($user->role == 1 || in_array('update_employee_info', $permissions))
                                    <a href="#" class="edit-icon" data-bs-toggle="modal"
                                        data-bs-target="#emergency_contact_modal"><i class="fa-solid fa-pencil"></i></a>
                                @endif
                            </h3>

                            <ul class="personal-info">
                                <li>
                                    <div class="title"><span data-translate="name">Name:</span></div>
                                    <div class="text" id="ename">{{ $EmergencyUser->e_name }}</div>
                                </li>
                                <li>
                                    <div class="title"><span data-translate="phone">Phone:</span></div>
                                    <div class="text" id="ephone">{{ $EmergencyUser->e_phone }}</div>
                                </li>
                                <li>
                                    <div class="title"><span data-translate="email">Email:</span></div>
                                    <div class="text" id="eemail">{{ $EmergencyUser->e_email }}</div>
                                </li>
                                <li>
                                    <div class="title"><span data-translate="address">Address:</span></div>
                                    <div class="text" id="eaddress">{{ $EmergencyUser->e_address }}</div>
                                </li>
                                <li>
                                    <div class="title"><span data-translate="country">Country:</span></div>
                                    <div class="text" id="ecountry">{{ $EmergencyUser->e_country }}</div>
                                </li>
                                <li>
                                    <div class="title"><span data-translate="gender">Gender:</span></div>
                                    <div class="text" id="egender">{{ $EmergencyUser->e_gender }}</div>
                                </li>
                                <li>
                                    <div class="title"><span data-translate="relation_with_employee">Relationship With
                                            Employee:</span></div>
                                    <div class="text" id="erelation">{{ $EmergencyUser->e_relationship }}</div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /Emergency -->

            <div class="row">
                <div class="col-md-6 d-flex">
                    <div class="card profile-box flex-fill">
                        <div class="card-body">
                            <h3 class="card-title"><span data-translate="bank_information">Bank information</span></h3>
                            <ul class="personal-info">
                                <li>
                                    <div class="title"><span data-translate="bank_name">Bank name:</span></div>
                                    <div class="text">ICICI Bank</div>
                                </li>
                                <li>
                                    <div class="title"><span data-translate="bank_account_number">Bank account
                                            Number:</span></div>
                                    <div class="text">159843014641</div>
                                </li>
                                <li>
                                    <div class="title"><span data-translate="ifsc_code">IFSC Code:</span></div>
                                    <div class="text">ICI24504</div>
                                </li>
                                <li>
                                    <div class="title"><span data-translate="pan_number">PAN Number:</span></div>
                                    <div class="text">TC000Y56</div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 d-flex">
                    <div class="card profile-box flex-fill">
                        <div class="card-body">
                            <h3 class="card-title"><span data-translate="family_information">Family Information</span> <a
                                    href="#" class="edit-icon" data-bs-toggle="modal" data-bs-target="#family_info_modal"><i
                                        class="fa-solid fa-pencil"></i></a></h3>
                            <div class="table-responsive">
                                <table class="table table-nowrap">
                                    <thead>
                                        <tr>
                                            <th><span data-translate="name">Name:</span></th>
                                            <th><span data-translate="relationship">Relationship:</span></th>
                                            <th><span data-translate="date_of_birth">Date of Birth:</span></th>
                                            <th><span data-translate="phone">Phone:</span></th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>Leo</td>
                                            <td>Brother</td>
                                            <td>Feb 16th, 2019</td>
                                            <td>9876543210</td>
                                            <td class="text-end">
                                                <div class="dropdown dropdown-action">
                                                    <a aria-expanded="false" data-bs-toggle="dropdown"
                                                        class="action-icon dropdown-toggle" href="#"><i
                                                            class="material-icons">more_vert</i></a>
                                                    <div class="dropdown-menu dropdown-menu-right">
                                                        <a href="#" class="dropdown-item"><i
                                                                class="fa-solid fa-pencil m-r-5"></i> Edit</a>
                                                        <a href="#" class="dropdown-item"><i
                                                                class="fa-regular fa-trash-can m-r-5"></i> Delete</a>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- /Profile Info Tab -->

        <!-- Projects Tab -->
        <div class="tab-pane fade" id="emp_officeEquip" role="tabpanel">
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12  ">
                    <div class="card profile-box p-4">
                        <div class="row ms-2 lh-lg">
                            <div class="row p-3">
                                <div class="col">
                                    <h4>Moniter</h4>
                                    <span>Brand:</span>
                                    <br>
                                    <span>SN:</span>
                                </div>
                                <div class="col">
                                    <h4>Mobile</h4>
                                    <span>Name:</span> <br>
                                    <span>SN:</span>
                                </div>
                                <div class="col">
                                    <h4>Back Office</h4>
                                    <span>Brand:</span> <br>
                                    <span>Name:</span>
                                </div>
                            </div>
                            <div class="row p-3">
                                <div class="col">
                                    <h4>Mouse</h4>
                                    <span>Brand:</span> <br>
                                    <span>SN:</span>
                                </div>
                                <div class="col">
                                    <h4>Headset</h4>
                                    <span>Brand:</span> <br>
                                    <span>SN:</span>
                                </div>
                                <div class="col">
                                    <h4>OpenVPN</h4>
                                    <span>Brand:</span> <br>
                                    <span>SN:</span>
                                </div>
                            </div>
                            <div class="row p-3">
                                <div class="col">
                                    <h4>Laptop</h4>
                                    <span>Brand:</span> <br>
                                    <span>SN:</span><br>
                                    <span>Charger:</span>
                                </div>
                                <div class="col">
                                    <h4>HDMI Connector</h4>
                                    <span>Brand:</span> <br>
                                    <span>SN:</span>
                                </div>
                                <div class="col">
                                    <h4>Miscellaneous Item</h4>
                                    <span>Brand:</span> <br>
                                    <span>Charger: </span>
                                </div>
                            </div>

                        </div>


                    </div>


                </div>

            </div>

        </div>

        <!-- dependant Tab -->
        <div class="tab-pane fade dependant-content" id="emp_dependant" role="tabpanel">
            <h3 class="mb-3"><span data-translate="dependant">Dependants</span></h3>
            <div class="">
                <div class="row">
                    <div class="col-md-4 mb-3 d-flex">
                        <div class="card flex-fill">
                            <div class="card-body d-flex flex-column">
                                <h3 class="card-title"><span data-translate="dependant_information">Dependant's
                                        Information</span>
                                    @if ($profileId == auth()->user()->id)

                                    @elseif($user->role == 1 || in_array('update_employee_info', $permissions))
                                        <a href="#" class="edit-icon" data-bs-toggle="modal"
                                            data-bs-target="#dependant_modal"><i class="fa-solid fa-pencil"></i></a>
                                    @endif
                                </h3>
                                <ul class="personal-info">
                                    <li>
                                        <div class="title"><span data-translate="name">Name:</span></div>
                                        <div class="text" id="dname"> {{ $DependantUser->d_name }}</div>
                                    </li>
                                    <li>
                                        <div class="title"><span data-translate="gender">Gender:</span></div>
                                        <div class="text" id="dgender">{{ $DependantUser->d_gender }}</div>
                                    </li>
                                    <li>
                                        <div class="title"><span data-translate="nationality">Nationality:</span></div>
                                        <div class="text" id="dnational">{{ $DependantUser->d_nationality }}</div>
                                    </li>
                                    <li>
                                        <div class="title"><span data-translate="date_of_birth">Date Of Birth:</span></div>
                                        <div class="text" id="ddob">{{ $DependantUser->d_dob }}</div>
                                    </li>
                                </ul>

                            </div>
                        </div>
                    </div>

                    <div class="col-md-4 mb-3 d-flex">
                        <div class="card flex-fill">
                            <div class="card-body d-flex flex-column">
                                <h3 class="card-title"><span data-translate="passport_information">Passport
                                        Information</span></h3>
                                <ul class="personal-info">
                                    <li>
                                        <div class="title"><span data-translate="passport_number">Passport Number:</span>
                                        </div>
                                        <div class="text" id="dpassport">{{ $DependantUser->d_passport_no }}</div>
                                    </li>
                                    <li>
                                        <div class="title"><span data-translate="passport_issue_date">Issue Date:</span>
                                        </div>
                                        <div class="text" id="dpassissue">{{ $DependantUser->d_pass_issue_date }}
                                        </div>
                                    </li>
                                    <li>
                                        <div class="title"><span data-translate="passport_expiry_date">Expiry Date:</span>
                                        </div>
                                        <div class="text" id="dpassexpiry">{{ $DependantUser->d_pass_expiry_date }}
                                        </div>
                                    </li>
                                </ul>

                            </div>
                        </div>
                    </div>

                    <div class="col-md-4 mb-3 d-flex">
                        <div class="card flex-fill">
                            <div class="card-body d-flex flex-column">
                                <h3 class="card-title"><span data-translate="visa_information">Visa Information</span></h3>
                                <ul class="personal-info">
                                    <li>
                                        <div class="title"><span data-translate="visa_number">Visa Number:</span></div>
                                        <div class="text" id="dvisa">{{ $DependantUser->d_visa_no }}</div>
                                    </li>
                                    <li>
                                        <div class="title"><span data-translate="visa_issue_date">Issue Date:</span></div>
                                        <div class="text" id="dvisaissue">{{ $DependantUser->d_visa_issue_date }}
                                        </div>
                                    </li>
                                    <li>
                                        <div class="title"><span data-translate="visa_expiry_date">Expiry Date:</span></div>
                                        <div class="text" id="dvisaexpiry">{{ $DependantUser->d_visa_expiry_date }}
                                        </div>
                                    </li>
                                </ul>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- /Dependant Tab -->
    </div>
    <!-- /Projects Tab -->
    </div>
    </div>
    <!-- /Page Content -->


    <!-- Profile Modal -->
    <div id="profile_info" class="modal custom-modal fade" role="dialog">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Profile Information</h5>
                    <button type="button" class="closed_btn" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="profileModal" method="POST" action="{{ route('user-profile.update', $profileUser->id) }}">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="userProfileId" value="{{ $profileUser->user_id }}">
                        <div class="row">

                            <!-- USERNAME -->
                            <div class="col-md-6">
                                <div class="input-block mb-3">
                                    <label class="col-form-label">UserName</label>
                                    <input type="text" class="form-control" value="{{ $mainUser->username }}" disabled
                                        name="username">
                                </div>
                            </div>

                            <!-- EMPLOYEE ID -->
                            <div class="col-md-6">
                                <div class="input-block mb-3">
                                    <label class="col-form-label">Employee ID</label>
                                    <input type="text" class="form-control" value="{{ $mainUser->employee_id }}"
                                        name="employee_id" disabled>
                                </div>
                            </div>

                            <!-- DATE OF JOINING -->
                            <div class="col-md-6">
                                <div class="input-block mb-3">
                                    <label class="col-form-label">Date Of Join</label>
                                    <input type="date" class="form-control" value="{{ $mainUser->joining_date }}"
                                        name="joining_date" disabled>
                                </div>
                            </div>

                            <!-- REAL NAME -->
                            <div class="col-md-6">
                                <div class="input-block mb-3">
                                    <label class="col-form-label">Real Name</label>
                                    <input type="text" class="form-control" value="{{ $profileUser->real_name }}"
                                        name="real_name">
                                </div>
                            </div>

                            <!-- EMAIL -->
                            <div class="col-md-6">
                                <div class="input-block mb-3">
                                    <label class="col-form-label">Email</label>
                                    <input type="text" class="form-control" value="{{ $mainUser->email }}" name="email"
                                        disabled required>
                                </div>
                            </div>

                            <!-- BIRTHDAY -->
                            <div class="col-md-6">
                                <div class="input-block mb-3">
                                    <label class="col-form-label">Birthday</label>
                                    <input type="date" class="form-control" value="{{ $profileUser->dob }}" name="dob">
                                </div>
                            </div>

                            <!-- ACCOMODATION -->
                            <div class="col-md-6">
                                <div class="input-block mb-3">
                                    <label class="col-form-label">Accommodation</label>
                                    <input type="text" class="form-control" value="{{ $profileUser->accomodation }}"
                                        name="accomodation">
                                </div>
                            </div>

                            <!-- GENDER -->
                            <div class="col-md-6">
                                <div class="input-block mb-3">
                                    <label class="col-form-label" for="gender">Gender</label>
                                    <select class="form-control" name="gender">
                                        <option value="" disabled {{ empty($profileUser->gender) ? 'selected' : '' }}>Select
                                            Gender</option>
                                        <option value="Male" {{ $profileUser->gender === 'Male' ? 'selected' : '' }}>Male
                                        </option>
                                        <option value="Female" {{ $profileUser->gender === 'Female' ? 'selected' : '' }}>
                                            Female</option>
                                        <option value="Other" {{ $profileUser->gender === 'Other' ? 'selected' : '' }}>Other
                                        </option>
                                    </select>
                                </div>
                            </div>

                            <!-- PHONE -->
                            <div class="col-md-6">
                                <div class="input-block mb-3">
                                    <label class="col-form-label">Phone Number</label>
                                    <input type="text" class="form-control" value="{{ $profileUser->phone }}" name="phone">
                                </div>
                            </div>

                            <!-- WEEK OFF-DAYS -->
                            <div class="col-md-6">
                                <div class="input-block mb-3">
                                    <label class="col-form-label" for="week_days_label">Weekly Working Days</label>
                                    <select class="form-select" name="week_days" id="week_days">
                                        <option disabled {{ empty($mainUser->week_days) ? 'selected' : '' }}>SELECT OPTION
                                        </option>
                                        <option value="5" {{ $mainUser->week_days == 5 ? 'selected' : '' }}>5 Days</option>
                                        <option value="6" {{ $mainUser->week_days == 6 ? 'selected' : '' }}>6 Days</option>
                                    </select>
                                </div>
                            </div>

                            <!-- NATIONALITY -->
                            <div class="col-md-6">
                                <div class="input-block mb-3">
                                    <label class="col-form-label">Nationality</label>
                                    <select class="form-select" name="nationality" id="nationality">
                                        <option disabled {{ empty($profileUser->nationality) ? 'selected' : '' }}>SELECT
                                            OPTION</option>
                                        <option value="India" {{ $profileUser->nationality == 'India' ? 'selected' : '' }}>
                                            Indian</option>
                                        <option value="Bangladesh" {{ $profileUser->nationality == 'Bangladesh' ? 'selected' : '' }}>Bangladeshi
                                        </option>
                                        <option value="Pakistan" {{ $profileUser->nationality == 'Pakistan' ? 'selected' : '' }}>Pakistani
                                        </option>
                                        <option value="Vietnam" {{ $profileUser->nationality == 'Vietnam' ? 'selected' : '' }}>Vietnamese
                                        </option>
                                        <option value="Malaysia" {{ $profileUser->nationality == 'Malaysia' ? 'selected' : '' }}>Malaysian
                                        </option>
                                        <option value="Thailand" {{ $profileUser->nationality == 'Thailand' ? 'selected' : '' }}>Thai</option>
                                        <option value="Philippines" {{ $profileUser->nationality == 'Philippines' ? 'selected' : '' }}>Filipinos
                                        </option>
                                        <option value="Cambodia" {{ $profileUser->nationality == 'Cambodia' ? 'selected' : '' }}>Cambodian
                                        </option>
                                        <option value="Korea" {{ $profileUser->nationality == 'Korea' ? 'selected' : '' }}>
                                            Korean</option>
                                        <option value="Indonesia" {{ $profileUser->nationality == 'Indonesia' ? 'selected' : '' }}>
                                            Indonesian</option>
                                        <option value="Srilanka" {{ $profileUser->nationality == 'Srilanka' ? 'selected' : '' }}>
                                            Sri Lankan</option>
                                    </select>
                                </div>
                            </div>

                            <!-- OFFICE -->
                            <div class="col-md-6">
                                <div class="input-block mb-3">
                                    <label class="col-form-label" for="office">Office</label>
                                    <select class="form-select" name="office" id="office">
                                        <option disabled selected {{ empty($profileUser->office) ? 'selected' : '' }}>SELECT
                                            OPTION</option>
                                        <option value="Bataan" {{ $profileUser->office == 'Bataan' ? 'selected' : '' }}>
                                            Bataan</option>
                                        <option value="Cebu" {{ $profileUser->office == 'Cebu' ? 'selected' : '' }}>
                                            Cebu</option>
                                        <option value="Sihanoukville" {{ $profileUser->office == 'Sihanoukville' ? 'selected' : '' }}>
                                            Sihanoukville
                                        </option>
                                        <option value="Bavet" {{ $profileUser->office == 'Bavet' ? 'selected' : '' }}>
                                            Bavet
                                        </option>
                                        <option value="PhnomPenh" {{ $profileUser->office == 'PhnomPenh' ? 'selected' : '' }}>
                                            Phnom Penh
                                        </option>
                                        <option value="Malaysia" {{ $profileUser->office == 'Malaysia' ? 'selected' : '' }}>
                                            Malaysia</option>
                                        <option value="Poipet" {{ $profileUser->office == 'Poipet' ? 'selected' : '' }}>
                                            Poi Pet
                                        </option>
                                        <option value="Srilanka" {{ $profileUser->office == 'Srilanka' ? 'selected' : '' }}>
                                            Sri Lanka
                                        </option>
                                    </select>
                                </div>
                            </div>

                            <!-- TELEGRAM -->
                            <div class="col-md-6">
                                <div class="input-block">
                                    <label class="col-form-label">Telegram</label>
                                    <input class="form-control" type="text" value="{{ $profileUser->telegram }}"
                                        name="telegram" id="telegram">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="input-block">
                                    <label class="col-form-label" for="allowed_ul">UnPaid Leaves (UL)</label>
                                    <div class="multi-select-container">
                                        <button type="button" class="multi-select-btn">Select Leave Types</button>
                                        <div class="multi-select-options">
                                            <label>
                                                <input type="checkbox" id="select-all"> Select All
                                            </label>
                                            <label>
                                                <input type="checkbox" value="4" name="allowed_ul[]" {{ in_array(4, $allowedUlArray) ? 'checked' : '' }}>
                                                Unpaid Leave (UL)
                                            </label>
                                            <label>
                                                <input type="checkbox" value="5" name="allowed_ul[]" {{ in_array(5, $allowedUlArray) ? 'checked' : '' }}>
                                                Hospitalisation Leave (HL)
                                            </label>
                                            <label>
                                                <input type="checkbox" value="6" name="allowed_ul[]" {{ in_array(6, $allowedUlArray) ? 'checked' : '' }}>
                                                Compassionate Leave (CL)
                                            </label>
                                            <label>
                                                <input type="checkbox" value="7" name="allowed_ul[]" {{ in_array(7, $allowedUlArray) ? 'checked' : '' }}>
                                                Maternity Leave (MTL)
                                            </label>
                                            <label>
                                                <input type="checkbox" value="8" name="allowed_ul[]" {{ in_array(8, $allowedUlArray) ? 'checked' : '' }}>
                                                Paternity Leave (PL)
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                </div>



                <div class="submit-section">
                    <button type="submit" class="btn btn-primary submit-btn mb-3">Submit</button>
                </div>
                </form>
            </div>
        </div>
    </div>
    </div>
    <!-- /Profile Modal -->




    <!-- Personal Info Modal -->
    <div id="personal_info_modal" class="modal custom-modal fade" role="dialog">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Passport & Visa Infomation</h5>
                    <button type="button" class="closed_btn" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">

                    <form id="personalInfoForm" method="POST">
                        <input type="hidden" name="userProfileId" value="{{ $visaInfo->id }}">

                        <!-- Assuming you have this ID -->
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="input-block mb-3">
                                    <label class="col-form-label">Passport No: <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="passport_no"
                                        value="{{ $visaInfo->passport_no }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="input-block mb-3">
                                    <label class="col-form-label">Passport Issue Date: <span
                                            class="text-danger">*</span></label>
                                    <input class="form-control" type="date" value="{{ $visaInfo->p_issue_date }}"
                                        name="p_issue_date">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="input-block mb-3">
                                    <label class="col-form-label">Passport Expiry Date: <span
                                            class="text-danger">*</span></label>
                                    <input class="form-control" type="date" value="{{ $visaInfo->p_expiry_date }}"
                                        name="p_expiry_date">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="input-block mb-3">
                                    <label class="col-form-label">Visa No: <span class="text-danger">*</span></label>
                                    <input class="form-control" type="text" value="{{ $visaInfo->visa_no }}" name="visa_no">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="input-block mb-3">
                                    <label class="col-form-label">Visa Issue Date: <span
                                            class="text-danger">*</span></label>
                                    <input class="form-control" type="date" value="{{ $visaInfo->v_issue_date }}"
                                        name="v_issue_date">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="input-block mb-3">
                                    <label class="col-form-label">Visa Expiry Date: <span
                                            class="text-danger">*</span></label>
                                    <input class="form-control" type="date" value="{{ $visaInfo->v_expiry_date }}"
                                        name="v_expiry_date">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="input-block mb-3">
                                    <label class="col-form-label">Foreign No:<span class="text-danger">*</span></label>
                                    <div class="">
                                        <input class="form-control" type="text" value="{{ $visaInfo->foreign_no }}"
                                            name="foreign_no">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="input-block mb-3">
                                    <label class="col-form-label">Foreign Expiry Date:<span
                                            class="text-danger">*</span></label>
                                    <input class="form-control" type="date" value="{{ $visaInfo->f_expiry_date }}"
                                        name="f_expiry_date">
                                </div>
                            </div>
                        </div>
                        <div class="submit-section">
                            <button type="submit" class="btn btn-primary submit-btn">Submit</button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
    <!-- /Personal Info Modal -->

    <!-- Family Info Modal -->
    <div id="family_info_modal" class="modal custom-modal fade" role="dialog">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"> Family Informations</h5>
                    <button type="button" class="closed_btn" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form>
                        <div class="form-scroll">
                            <div class="card">
                                <div class="card-body">
                                    <h3 class="card-title">Family Member <a href="javascript:void(0);"
                                            class="delete-icon"><i class="fa-regular fa-trash-can"></i></a></h3>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="input-block mb-3">
                                                <label class="col-form-label">Name <span
                                                        class="text-danger">*</span></label>
                                                <input class="form-control" type="text">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="input-block mb-3">
                                                <label class="col-form-label">Relationship <span
                                                        class="text-danger">*</span></label>
                                                <input class="form-control" type="text">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="input-block mb-3">
                                                <label class="col-form-label">Date of birth <span
                                                        class="text-danger">*</span></label>
                                                <input class="form-control" type="text">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="input-block mb-3">
                                                <label class="col-form-label">Phone <span
                                                        class="text-danger">*</span></label>
                                                <input class="form-control" type="text">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="card">
                                <div class="card-body">
                                    <h3 class="card-title">Education Informations <a href="javascript:void(0);"
                                            class="delete-icon"><i class="fa-regular fa-trash-can"></i></a></h3>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="input-block mb-3">
                                                <label class="col-form-label">Name <span
                                                        class="text-danger">*</span></label>
                                                <input class="form-control" type="text">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="input-block mb-3">
                                                <label class="col-form-label">Relationship <span
                                                        class="text-danger">*</span></label>
                                                <input class="form-control" type="text">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="input-block mb-3">
                                                <label class="col-form-label">Date of birth <span
                                                        class="text-danger">*</span></label>
                                                <input class="form-control" type="text">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="input-block mb-3">
                                                <label class="col-form-label">Phone <span
                                                        class="text-danger">*</span></label>
                                                <input class="form-control" type="text">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="add-more">
                                        <a href="javascript:void(0);"><i class="fa-solid fa-plus-circle"></i> Add
                                            More</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="submit-section">
                            <button class="btn btn-primary submit-btn"><span data-translate="submit">Submit</span></button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- /Family Info Modal -->

    <!-- Emergency Contact Modal -->
    <div id="emergency_contact_modal" class="modal custom-modal fade" role="dialog">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Emergency Contact</h5>
                    <button type="button" class="closed_btn" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="emergencyModal" method="POST">
                        @csrf
                        <input type="hidden" name="emergencyProfileId" value="{{ $EmergencyUser->id }}">
                        <div class="card">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="input-block mb-3">
                                            <label class="col-form-label">Name <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="emergency_name"
                                                value="{{ $EmergencyUser->e_name }}">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="input-block mb-3">
                                            <label class="col-form-label">phone <span class="text-danger">*</span></label>
                                            <input class="form-control" type="text" name="emergency_phone"
                                                value="{{ $EmergencyUser->e_phone }}">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="input-block mb-3">
                                            <label class="col-form-label">Email <span class="text-danger">*</span></label>
                                            <input class="form-control" type="email" name="emergency_email"
                                                value="{{ $EmergencyUser->e_email }}">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="input-block mb-3">
                                            <label class="col-form-label">Address</label>
                                            <input class="form-control" type="text" name="emergency_address"
                                                value="{{ $EmergencyUser->e_address }}">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="input-block mb-3">
                                            <label class="col-form-label">Country</label>
                                            <input class="form-control" type="text" name="emergency_country"
                                                value="{{ $EmergencyUser->e_country }}">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="input-block mb-3">
                                            <label class="col-form-label" for="e_gender">Gender</label>
                                            <select class="select form-control" name="emergency_gender" id="e_gender">
                                                <option value="" disabled {{ empty($EmergencyUser->e_gender) ? 'selected' : '' }}>Select Gender
                                                </option>
                                                <option value="Male" {{ $EmergencyUser->e_gender === 'Male' ? 'selected' : '' }}>Male
                                                </option>
                                                <option value="Female" {{ $EmergencyUser->e_gender === 'Female' ? 'selected' : '' }}>Female
                                                </option>
                                                <option value="Other" {{ $EmergencyUser->e_gender === 'Other' ? 'selected' : '' }}>Other
                                                </option>
                                            </select>

                                        </div>
                                    </div>

                                    <div class="col-md-6 align-item-center">
                                        <div class="input-block mb-3">
                                            <label class="col-form-label">Relationship with Employee</label>
                                            <input class="form-control" type="text" name="emergency_relation"
                                                value="{{ $EmergencyUser->e_relationship }}">

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <div class="submit-section">
                            <button class="btn btn-primary submit-btn">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- /Emergency Contact Modal -->



    <!-- dependant Info Modal -->
    <div id="dependant_modal" class="modal custom-modal fade" role="dialog">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Dependant Information</h5>
                    <button type="button" class="closed_btn" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="dependantModal" method="POST">
                        @csrf
                        <input type="hidden" name="dependantProfileId" value="{{ $DependantUser->id }}">
                        <div class="card">
                            <div class="card-body">
                                <h3 class="card-title">Dependant Info</h3>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="input-block mb-3">
                                            <label class="col-form-label">Name <span class="text-danger">*</span></label>
                                            <input type="text" name="dependant_name" class="form-control"
                                                value="{{ $DependantUser->d_name }}">
                                            <div class="val_error text-danger"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="input-block mb-3">
                                            <label class="col-form-label" for="d_gender">Gender</label>
                                            <select class="select form-control" name="dependant_gender" id="d_gender">
                                                <option value="" disabled {{ empty($DependantUser->d_gender) ? 'selected' : '' }}>Select Gender
                                                </option>
                                                <option value="Male" {{ $DependantUser->d_gender === 'Male' ? 'selected' : '' }}>Male
                                                </option>
                                                <option value="Female" {{ $DependantUser->d_gender === 'Female' ? 'selected' : '' }}>Female
                                                </option>
                                                <option value="Other" {{ $DependantUser->d_gender === 'Other' ? 'selected' : '' }}>Other
                                                </option>
                                            </select>
                                            <div class="val_error text-danger"></div>

                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="input-block mb-3">
                                            <label class="col-form-label">Nationality <span
                                                    class="text-danger">*</span></label>
                                            <input class="form-control" type="text" name="dependant_nationality"
                                                value="{{ $DependantUser->d_nationality }}">
                                            <div class="val_error text-danger"></div>

                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="input-block mb-3">
                                            <label class="col-form-label">Date Of Birth</label>
                                            <input class="form-control" type="date" name="dependant_dob"
                                                value="{{ $DependantUser->d_dob }}">
                                            <div class="val_error text-danger"></div>

                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="input-block mb-3">
                                            <label class="col-form-label">Passport No <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="dependant_passport_no"
                                                value="{{ $DependantUser->d_passport_no }}">
                                            <div class="val_error text-danger"></div>

                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="input-block mb-3">
                                            <label class="col-form-label">Passport Issue Date <span
                                                    class="text-danger">*</span></label>
                                            <input class="form-control" type="date" name="dependant_pass_issue_date"
                                                value="{{ $DependantUser->d_pass_issue_date }}">
                                            <div class="val_error text-danger"></div>

                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="input-block mb-3">
                                            <label class="col-form-label">Passport Expiry Date <span
                                                    class="text-danger">*</span></label>
                                            <input class="form-control" type="date" name="dependant_pass_expiry_date"
                                                value="{{ $DependantUser->d_pass_expiry_date }}">
                                            <div class="val_error text-danger"></div>

                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="input-block mb-3">
                                            <label class="col-form-label">Visa No <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="dependant_visa_no"
                                                value="{{ $DependantUser->d_visa_no }}">
                                            <div class="val_error text-danger"></div>

                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="input-block mb-3">
                                            <label class="col-form-label">Visa Issue Date <span
                                                    class="text-danger">*</span></label>
                                            <input class="form-control" type="date" name="dependant_visa_issue_date"
                                                value="{{ $DependantUser->d_visa_issue_date }}">
                                            <div class="val_error text-danger"></div>

                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="input-block mb-3">
                                            <label class="col-form-label">Visa Expiry Date <span
                                                    class="text-danger">*</span></label>
                                            <input class="form-control" type="date" name="dependant_visa_expiry_date"
                                                value="{{ $DependantUser->d_visa_expiry_date }}">
                                            <div class="val_error text-danger"></div>

                                        </div>
                                    </div>
                                </div>
                                <div class="submit-section">
                                    <button type="submit" class="btn btn-primary submit-btn"><span
                                            data-translate="submit">Submit</span></button>
                                </div>
                            </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- /dependant Info Modal -->

    <!-- PreLoader -->
    <div id="loader" class="loader" style="display: none;">
        <div class="loader-animation"></div>
    </div>
@endsection

@section('script-z')

    <script>
        $(document).ready(function () {
            const $btn = $('.multi-select-btn');
            const $options = $('.multi-select-options');
            const $selectAll = $('#select-all');
            const $checkboxes = $('input[type="checkbox"]').not('#select-all');

            $btn.on('click', function () {
                $options.toggle();
            });

            $(document).on('click', function (event) {
                if (!$btn.is(event.target) && $btn.has(event.target).length === 0 && !$options.is(event.target) && $options.has(event.target).length === 0) {
                    $options.hide();
                }
            });

            // Handle Select All functionality
            $selectAll.change(function () {
                if ($(this).is(':checked')) {
                    $checkboxes.prop('checked', true);
                } else {
                    $checkboxes.prop('checked', false);
                }
                updateButtonText();
            });

            // Update button text based on selected checkboxes
            $checkboxes.change(function () {
                if ($checkboxes.length === $checkboxes.filter(':checked').length) {
                    $selectAll.prop('checked', true);
                } else {
                    $selectAll.prop('checked', false);
                }
                updateButtonText();
            });

            function updateButtonText() {
                var selectedValues = $checkboxes.filter(':checked').map(function () {
                    return $(this).val();
                }).get();

                if (selectedValues.length > 0) {
                    $btn.text(selectedValues.length + " Leave Type(s) Selected");
                } else {
                    $btn.text('Select Leave Types');
                }
            }
            updateButtonText();
        });
    </script>
    <script>

        $(document).on('submit', 'form[id^="profileModal"]', function (e) {
            e.preventDefault();

            var formData = new FormData(this);
            var userId = $('input[name="userProfileId"]', this).val();

            showLoader();
            $.ajax({
                url: "{{ route('user-profile.update', ':id') }}".replace(':id', userId),
                method: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                success: function (response) {
                    if (response.status === 'success' && response.data) {
                        hideLoader();
                        $(this).closest('.modal').modal('hide');
                        console.log(response);

                        $('#realName').text(response.data.real_name || '');
                        $('#dateofbirth').text(response.data.dob || '');
                        $('#accommodation').text(response.data.accomodation || '');
                        $('#gender').text(response.data.gender || '');
                        $('#phone').text(response.data.phone || '');
                        $('#nationality').text(response.data.nationality || '');
                        $('#office').text(response.data.office || '');
                        $('#telegram').text(response.data.telegram || '');

                        // Update the weekDays text
                        $('#weekDays').text(response.data.week_days == 5 ? '5 Days' : '6 Days');

                        // Update the week_days select dropdown value
                        $('#week_days').val(response.data.week_days);

                        // Update the allowed_ul field with the selected values (multiselect)
                        // Check if the 'allowed_ul' field exists in the response and is an array
                        var allowedUlArray = response.data.allowed_ul_array || [];

                        // Mapping the allowedUlArray to their respective leave types
                        var leaveTypes = {
                            4: 'UL',  // Unpaid Leave
                            5: 'HL',  // Hospitalisation Leave
                            6: 'CL',  // Compassionate Leave
                            7: 'MTL', // Maternity Leave
                            8: 'PL'  // Paternity Leave
                        };

                        // Map the selected numbers to leave type names
                        var selectedLeaveTypes = allowedUlArray.map(function (leaveId) {
                            return leaveTypes[leaveId] || leaveId;
                        });

                        // Join the leave types into a string and update the #allowed_ul text
                        var leaveNamesString = selectedLeaveTypes.join(', ');

                        $('#allowed_ul').text(leaveNamesString);  // Update the text content of the #allowed_ul div

                        createToast('info', 'fa-solid fa-circle-check', 'Success',
                            'Profile Updated Successfully.');
                    } else {
                        createToast('error', 'fa-solid fa-circle-check', 'Error',
                            'Error in updating profile.');
                    }
                }.bind(this),

            });

        });
    </script>
    <script>
        $('#personalInfoForm').on('submit', function (e) {
            e.preventDefault();
            var userId = $('input[name="userProfileId"]').val();

            showLoader();
            $.ajax({
                type: 'POST',
                url: "{{ route('update.visainfo', ':id')}}".replace(':id', userId), // Set the correct URL
                data: $(this).serialize(), // Serialize the form data
                success: function (response) {
                    var updatedVisaInfo = response.visaInfo;
                    var updatedUserProfile = response.userProfile;

                    if (updatedVisaInfo) {
                        hideLoader();

                        // Helper function to format date in DD-MMM-YYYY format
                        function formatDate(date) {
                            if (!date) {
                                return ''; // Return 'N/A' if the date is null or empty
                            }
                            var d = new Date(date);
                            if (isNaN(d.getTime())) {
                                return ''; // Return 'N/A' if the date is invalid
                            }
                            var options = { day: '2-digit', month: 'short', year: 'numeric' };
                            return d.toLocaleDateString('en-GB', options); // Format as "DD-MMM-YYYY"
                        }

                        // Update the text with the formatted dates
                        $('#passport_no').text(updatedVisaInfo.passport_no);
                        $('#p_issue_date').text(formatDate(updatedVisaInfo.p_issue_date));
                        $('#p_expiry_date').text(formatDate(updatedVisaInfo.p_expiry_date));
                        $('#visa_no').text(updatedVisaInfo.visa_no);
                        $('#v_issue_date').text(formatDate(updatedVisaInfo.v_issue_date));
                        $('#v_expiry_date').text(formatDate(updatedVisaInfo.v_expiry_date));
                        $('#foreign_no').text(updatedVisaInfo.foreign_no);
                        $('#f_expiry_date').text(formatDate(updatedVisaInfo.f_expiry_date));

                        $('#personal_info_modal').modal('hide');
                        createToast('info', 'fa-solid fa-circle-check', 'Success',
                            'Visa Info Updated Successfully.');
                    } else {
                        createToast('error', 'fa-solid fa-circle-check', 'Error',
                            'No updated profile data found.');
                    }
                },
                error: function (xhr) {
                    // Handle error responses
                    let errorMessage = xhr.responseJSON && xhr.responseJSON.message ? xhr
                        .responseJSON
                        .message : 'Error updating information. Please try again.';
                    alert(errorMessage);
                }
            });

        });
    </script>

    <script>
        $(document).on('submit', '#emergencyModal', function (e) {

            e.preventDefault();

            var formData = new FormData(this); // Get the form data

            showLoader();
            $.ajax({
                url: "{{ route('emergency.update')}}",
                method: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                success: function (response) {
                    if (response.status === 'success' && response.data) {
                        hideLoader();
                        // Update the DOM elements with the new emergency contact information
                        $('#ename').text(response.data.e_name || '');
                        $('#ephone').text(response.data.e_phone || '');
                        $('#eemail').text(response.data.e_email || '');
                        $('#eaddress').text(response.data.e_address || '');
                        $('#ecountry').text(response.data.e_country || '');
                        $('#egender').text(response.data.e_gender || '');
                        $('#erelation').text(response.data.e_relationship || '');

                        $('#emergency_contact_modal').modal('hide'); // Hide the modal

                        createToast('info', 'fa-solid fa-circle-check', 'Success',
                            'Emergency Data Updated successfully.');
                    } else {
                        createToast('error', 'fa-solid fa-circle-check', 'Error',
                            'No emergency data found.');
                    }
                },
                error: function (xhr, status, error) {
                    console.error('Error:', xhr.responseText);
                    createToast('error', 'fa-solid fa-circle-check', 'success',
                        'An error occurred while updating the emergency contact.');
                }
            });

        });
    </script>

    <script>
        $(document).on('submit', '#dependantModal', function (event) {
            event.preventDefault();


            var formData = new FormData(this);

            showLoader();
            $.ajax({
                url: '{{ route("dependant.update") }}',
                method: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                success: function (response) {
                    hideLoader();
                    if (response.status === 'success' && response.data) {
                        $('#dname').text(response.data.d_name);
                        $('#dgender').text(response.data.d_gender);
                        $('#dnational').text(response.data.d_nationality);
                        $('#ddob').text(response.data.d_dob);
                        $('#dpassport').text(response.data.d_passport_no);
                        $('#dpassissue').text(response.data.d_pass_issue_date);
                        $('#dpassexpiry').text(response.data.d_pass_expiry_date);
                        $('#dvisa').text(response.data.d_visa_no);
                        $('#dvisaissue').text(response.data.d_visa_issue_date);
                        $('#dvisaexpiry').text(response.data.d_visa_expiry_date);

                        $('#dependant_modal').modal('hide');
                        createToast('info', 'fa-solid fa-circle-check', 'Success',
                            'Dependant Data Updated Successfully.');
                    } else {
                        createToast('error', 'fa-solid fa-circle-check', 'Error',
                            'No Dependant Data found.');
                    }
                },
                error: function (xhr, status, error) {
                    console.error('Error:', xhr.responseText);
                    createToast('error', 'fa-solid fa-circle-check', 'Error',
                        'An error occurred while updating the dependant data.');
                }
            });

        });
    </script>
@endsection