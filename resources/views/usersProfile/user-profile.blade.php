@extends('layout.mainlayout')
@section('content')
<div id="notification" aria-live="polite" aria-atomic="true"></div>
<!-- Page Header -->
<div class="page-header">
  <div id="loader" class="loader" style="display: none;">
    <div class="loader-animation"></div>
  </div>
  <div class="row">
    <div class="col-sm-12">
      <h3 class="page-title">Profile</h3>
      <ul class="breadcrumb">
        <li class="breadcrumb-item"><a href="admin-dashboard.html">Dashboard</a></li>
        <li class="breadcrumb-item active">Profile</li>
      </ul>
    </div>
  </div>
</div>
<!-- /Page Header -->
<div class="card mb-0">
  <div class="card-body">
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
                    <div class="title">Real Name:</div>
                    <div class="text" id="realName">
                      {{ $profileUser->real_name }}
                    </div>
                  </li>

                  <li>
                    <div class="title">Email:</div>
                    <div class="text">{{ $mainUser->email }}</div>
                  </li>
                  <li>
                    <div class="title">Birthday:</div>
                    <div class="text">{{ $profileUser->dob }}</div>
                  </li>
                  <li>
                    <div class="title">Accomodation:</div>
                    <div class="text" id="accommodation">{{ $profileUser->accomodation }}
                    </div>
                  </li>
                  <li>
                    <div class="title">Gender:</div>
                    <div class="text" id="gender">{{ $profileUser->gender }}</div>
                  </li>
                  <li>
                    <div class="title">Phone:</div>
                    <div class="text" id="phone">{{ $profileUser->phone }}</div>
                  </li>
                </ul>
              </div>
            </div>
          </div>
          <div class="pro-edit"><a data-bs-target="#profile_info" data-bs-toggle="modal" class="edit-icon" href="#"><i
                class="fa-solid fa-pencil"></i></a></div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="card tab-box">
  <div class="row user-tabs">
    <div class="col-lg-12 col-md-12 col-sm-12 line-tabs">
      <ul class="nav nav-tabs nav-tabs-bottom" role="tablist">
        <li class="nav-item" role="presentation"><a href="#emp_profile" data-bs-toggle="tab" class="nav-link active"
            aria-selected="true" role="tab">Profile</a></li>
        <li class="nav-item" role="presentation"><a href="#emp_officeEquip" data-bs-toggle="tab" class="nav-link"
            aria-selected="false" tabindex="-1" role="tab">Office Equipment</a>
        </li>
        <li class="nav-item" role="presentation">
          <a href="#emp_dependant" data-bs-toggle="tab" class="nav-link" aria-selected="false" tabindex="-1"
            role="tab">Dependant</a>
        </li>
        <li class="nav-item" role="presentation"><a href="#emp_assets" data-bs-toggle="tab" class="nav-link"
            aria-selected="false" tabindex="-1" role="tab">Assets</a></li>
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
            <h3 class="card-title">Personal Informations <a href="#" class="edit-icon" data-bs-toggle="modal"
                data-bs-target="#personal_info_modal"><i class="fa-solid fa-pencil"></i></a></h3>
            <ul class="personal-info">
              <li>
                <div class="title">Passport No:</div>
                <div class="text" id="passport_no">{{ $visaInfo->passport_no }}</div>
              </li>
              <li>
                <div class="title">Passport Issue Date:</div>
                <div class="text" id="p_issue_date">{{ $visaInfo->p_issue_date }}</div>
              </li>
              <li>
                <div class="title">Passport Exp Date:</div>
                <div class="text" id="p_expiry_date">{{ $visaInfo->p_expiry_date }}</div>
              </li>
              <li>
                <div class="title">Visa No:</div>
                <div class="text" id="visa_no">{{ $visaInfo->visa_no }}</div>
              </li>
              <li>
                <div class="title">Visa Issue Date.</div>
                <div class="text" id="v_issue_date">{{ $visaInfo->v_issue_date }}</div>
              </li>
              <li>
                <div class="title">Visa Expiry Date.</div>
                <div class="text" id="v_expiry_date">{{ $visaInfo->v_expiry_date }}</div>
              </li>
              <li>
                <div class="title">Foreign No:</div>
                <div class="text" id="foreign_no">{{ $visaInfo->foreign_no }}</div>
              </li>
              <li>
                <div class="title">Foreign Expiry Date:</div>
                <div class="text" id="f_expiry_date">{{ $visaInfo->f_expiry_date }}</div>
              </li>
              <li>
                <div class="title">Nationality:</div>
                <div class="text" id="nationality">{{ $profileUser->nationality }}</div>
              </li>
              <li>
                <div class="title">Religion:</div>
                <div class="text" id="religion">{{ $profileUser->religion }}</div>
              </li>
              <li>
                <div class="title">Telegram:</div>
                <div class="text" id="telegram">{{ $profileUser->telegram }}</div>
              </li>

            </ul>
          </div>
        </div>
      </div>

      <!-- EmergencyContact -->
      <div class="col-md-6 d-flex">
        <div class="card profile-box flex-fill">
          <div class="card-body">
            <h3 class="card-title">Emergency Contact <a href="#" class="edit-icon" data-bs-toggle="modal"
                data-bs-target="#emergency_contact_modal"><i class="fa-solid fa-pencil"></i></a></h3>

            <ul class="personal-info">
              <li>
                <div class="title">Name:</div>
                <div class="text" id="ename">{{ $EmergencyUser->e_name }}</div>
              </li>
              <li>
                <div class="title">Phone:</div>
                <div class="text" id="ephone">{{ $EmergencyUser->e_phone }}</div>
              </li>
              <li>
                <div class="title">Email:</div>
                <div class="text" id="eemail">{{ $EmergencyUser->e_email }}</div>
              </li>
              <li>
                <div class="title">Address:</div>
                <div class="text" id="eaddress">{{ $EmergencyUser->e_address }}</div>
              </li>
              <li>
                <div class="title">Country:</div>
                <div class="text" id="ecountry">{{ $EmergencyUser->e_country }}</div>
              </li>
              <li>
                <div class="title">Gender:</div>
                <div class="text" id="egender">{{ $EmergencyUser->e_gender }}</div>
              </li>
              <li>
                <div class="title">Relationship With Employee:</div>
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
            <h3 class="card-title">Bank information</h3>
            <ul class="personal-info">
              <li>
                <div class="title">Bank name</div>
                <div class="text">ICICI Bank</div>
              </li>
              <li>
                <div class="title">Bank account No.</div>
                <div class="text">159843014641</div>
              </li>
              <li>
                <div class="title">IFSC Code</div>
                <div class="text">ICI24504</div>
              </li>
              <li>
                <div class="title">PAN No</div>
                <div class="text">TC000Y56</div>
              </li>
            </ul>
          </div>
        </div>
      </div>
      <div class="col-md-6 d-flex">
        <div class="card profile-box flex-fill">
          <div class="card-body">
            <h3 class="card-title">Family Informations <a href="#" class="edit-icon" data-bs-toggle="modal"
                data-bs-target="#family_info_modal"><i class="fa-solid fa-pencil"></i></a></h3>
            <div class="table-responsive">
              <table class="table table-nowrap">
                <thead>
                  <tr>
                    <th>Name</th>
                    <th>Relationship</th>
                    <th>Date of Birth</th>
                    <th>Phone</th>
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
                        <a aria-expanded="false" data-bs-toggle="dropdown" class="action-icon dropdown-toggle"
                          href="#"><i class="material-icons">more_vert</i></a>
                        <div class="dropdown-menu dropdown-menu-right">
                          <a href="#" class="dropdown-item"><i class="fa-solid fa-pencil m-r-5"></i> Edit</a>
                          <a href="#" class="dropdown-item"><i class="fa-regular fa-trash-can m-r-5"></i> Delete</a>
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
    <h3 class="mb-3">Dependants</h3>
    <div class="">
      <div class="row">
        <div class="col-md-4 mb-3 d-flex">
          <div class="card flex-fill">
            <div class="card-body d-flex flex-column">
              <h3 class="card-title">Dependant Information <a href="#" class="edit-icon" data-bs-toggle="modal"
                  data-bs-target="#dependant_modal"><i class="fa-solid fa-pencil"></i></a></h3>
              <ul class="personal-info">
                <li>
                  <div class="title">Name:</div>
                  <div class="text" id="dname"> {{ $DependantUser->d_name }}</div>
                </li>
                <li>
                  <div class="title">Gender:</div>
                  <div class="text" id="dgender">{{ $DependantUser->d_gender }}</div>
                </li>
                <li>
                  <div class="title">Nationality:</div>
                  <div class="text" id="dnational">{{ $DependantUser->d_nationality }}</div>
                </li>
                <li>
                  <div class="title">Date Of Birth:</div>
                  <div class="text" id="ddob">{{ $DependantUser->d_dob }}</div>
                </li>
              </ul>

            </div>
          </div>
        </div>

        <div class="col-md-4 mb-3 d-flex">
          <div class="card flex-fill">
            <div class="card-body d-flex flex-column">
              <h3 class="card-title">Passport Information </h3>
              <ul class="personal-info">
                <li>
                  <div class="title">Passport No:</div>
                  <div class="text" id="dpassport">{{ $DependantUser->d_passport_no }}</div>
                </li>
                <li>
                  <div class="title">Issue Date:</div>
                  <div class="text" id="dpassissue">{{ $DependantUser->d_pass_issue_date }}
                  </div>
                </li>
                <li>
                  <div class="title">Expiry Date:</div>
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
              <h3 class="card-title">Visa Information</h3>
              <ul class="personal-info">
                <li>
                  <div class="title">Visa No:</div>
                  <div class="text" id="dvisa">{{ $DependantUser->d_visa_no }}</div>
                </li>
                <li>
                  <div class="title">Issue Date:</div>
                  <div class="text" id="dvisaissue">{{ $DependantUser->d_visa_issue_date }}
                  </div>
                </li>
                <li>
                  <div class="title">Expiry Date:</div>
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
  <!-- Assets -->
  <div class="tab-pane fade" id="emp_assets" role="tabpanel">
    <div class="table-responsive table-newdatatable">
      <div id="DataTables_Table_0_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer">
        <div class="row">
          <div class="col-sm-12 col-md-6">
            <div class="dataTables_length" id="DataTables_Table_0_length"><label>Show <select
                  name="DataTables_Table_0_length" aria-controls="DataTables_Table_0"
                  class="custom-select custom-select-sm form-control form-control-sm">
                  <option value="10">10</option>
                  <option value="25">25</option>
                  <option value="50">50</option>
                  <option value="100">100</option>
                </select> entries</label></div>
          </div>
          <div class="col-sm-12 col-md-6"></div>
        </div>
        <div class="row">
          <div class="col-sm-12">
            <table class="table table-new custom-table mb-0 datatable dataTable no-footer" id="DataTables_Table_0"
              role="grid" aria-describedby="DataTables_Table_0_info">
              <thead>
                <tr role="row">
                  <th class="sorting_asc" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1"
                    aria-sort="ascending" aria-label="#: activate to sort column descending" style="width: 0px;">#
                  </th>
                  <th class="sorting" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1"
                    aria-label="Name: activate to sort column ascending" style="width: 0px;">
                    Name
                  </th>
                  <th class="sorting" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1"
                    aria-label="Asset ID: activate to sort column ascending" style="width: 0px;">
                    SN</th>
                  <th class="sorting" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1"
                    aria-label="Assigned Date: activate to sort column ascending" style="width: 0px;">Brand
                  </th>
                  <th class="sorting" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1"
                    aria-label="Assigned Date: activate to sort column ascending" style="width: 0px;">Assigned Date
                  </th>
                  <th class="sorting" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1"
                    aria-label="Assignee: activate to sort column ascending" style="width: 0px;">
                    Assignee</th>
                  <th class="sorting" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1"
                    aria-label="Action: activate to sort column ascending" style="width: 0px;">
                    Action</th>
                </tr>
              </thead>
              <tbody>
                <tr class="odd">
                  <td class="sorting_1">1</td>
                  <td>
                    <a href="assets-details.html" class="table-imgname">
                      <img src="assets/img/laptop.png" class="me-2" alt="Laptop Image">
                      <span>Moniter</span>
                    </a>
                  </td>
                  <td>AST - 001</td>
                  <td>Hp</td>
                  <td>22 Nov, 2022 10:32AM</td>
                  <td class="table-namesplit">
                    <a href="javascript:void(0);" class="table-profileimage">
                      <img src="assets/img/profiles/avatar-02.jpg" class="me-2" alt="User Image">
                    </a>
                    <a href="javascript:void(0);" class="table-name">
                      <span>John Paul Raj</span>
                      <p>john@dreamguystech.com</p>
                    </a>
                  </td>
                  <td>
                    <div class="table-actions d-flex">
                      <a class="delete-table me-2" href="user-asset-details.html">
                        <img src="assets/img/icons/eye.svg" alt="Eye Icon">
                      </a>
                    </div>
                  </td>
                </tr>
                <tr class="even">
                  <td class="sorting_1">2</td>
                  <td>
                    <a href="assets-details.html" class="table-imgname">
                      <img src="assets/img/laptop.png" class="me-2" alt="Laptop Image">
                      <span>Mobile</span>
                    </a>
                  </td>
                  <td>AST - 002</td>
                  <td>Dell</td>
                  <td>22 Nov, 2022 10:32AM</td>
                  <td class="table-namesplit">
                    <a href="javascript:void(0);" class="table-profileimage" data-bs-toggle="modal"
                      data-bs-target="#edit-asset">
                      <img src="assets/img/profiles/avatar-05.jpg" class="me-2" alt="User Image">
                    </a>
                    <a href="javascript:void(0);" class="table-name">
                      <span>Vinod Selvaraj</span>
                      <p>vinod.s@dreamguystech.com</p>
                    </a>
                  </td>
                  <td>
                    <div class="table-actions d-flex">
                      <a class="delete-table me-2" href="user-asset-details.html">
                        <img src="assets/img/icons/eye.svg" alt="Eye Icon">
                      </a>
                    </div>
                  </td>
                </tr>
                <tr class="odd">
                  <td class="sorting_1">3</td>
                  <td>
                    <a href="assets-details.html" class="table-imgname">
                      <img src="assets/img/keyboard.png" class="me-2" alt="Keyboard Image">
                      <span>Back Office</span>
                    </a>
                  </td>
                  <td>AST - 003</td>
                  <td>Hp</td>
                  <td>22 Nov, 2022 10:32AM</td>
                  <td class="table-namesplit">
                    <a href="javascript:void(0);" class="table-profileimage" data-bs-toggle="modal"
                      data-bs-target="#edit-asset">
                      <img src="assets/img/profiles/avatar-03.jpg" class="me-2" alt="User Image">
                    </a>
                    <a href="javascript:void(0);" class="table-name">
                      <span>Harika </span>
                      <p>harika.v@dreamguystech.com</p>
                    </a>
                  </td>
                  <td>
                    <div class="table-actions d-flex">
                      <a class="delete-table me-2" href="user-asset-details.html">
                        <img src="assets/img/icons/eye.svg" alt="Eye Icon">
                      </a>
                    </div>
                  </td>
                </tr>
                <tr class="even">
                  <td class="sorting_1">4</td>
                  <td>
                    <a href="#" class="table-imgname">
                      <img src="assets/img/mouse.png" class="me-2" alt="Mouse Image">
                      <span>Mouse</span>
                    </a>
                  </td>
                  <td>AST - 0024</td>
                  <td>Dell</td>
                  <td>22 Nov, 2022 10:32AM</td>
                  <td class="table-namesplit">
                    <a href="assets-details.html" class="table-profileimage">
                      <img src="assets/img/profiles/avatar-02.jpg" class="me-2" alt="User Image">
                    </a>
                    <a href="assets-details.html" class="table-name">
                      <span>Mythili</span>
                      <p>mythili@dreamguystech.com</p>
                    </a>
                  </td>
                  <td>
                    <div class="table-actions d-flex">
                      <a class="delete-table me-2" href="user-asset-details.html">
                        <img src="assets/img/icons/eye.svg" alt="Eye Icon">
                      </a>
                    </div>
                  </td>
                </tr>
                <tr class="odd">
                  <td class="sorting_1">5</td>
                  <td>
                    <a href="#" class="table-imgname">
                      <img src="assets/img/laptop.png" class="me-2" alt="Laptop Image">
                      <span>Headset</span>
                    </a>
                  </td>
                  <td>AST - 005</td>
                  <td>ASUS</td>
                  <td>22 Nov, 2022 10:32AM</td>
                  <td class="table-namesplit">
                    <a href="assets-details.html" class="table-profileimage">
                      <img src="assets/img/profiles/avatar-02.jpg" class="me-2" alt="User Image">
                    </a>
                    <a href="assets-details.html" class="table-name">
                      <span>John Paul Raj</span>
                      <p>john@dreamguystech.com</p>
                    </a>
                  </td>
                  <td>
                    <div class="table-actions d-flex">
                      <a class="delete-table me-2" href="user-asset-details.html">
                        <img src="assets/img/icons/eye.svg" alt="Eye Icon">
                      </a>
                    </div>
                  </td>
                </tr>
                <tr class="even">
                  <td class="sorting_1">6</td>
                  <td>
                    <a href="#" class="table-imgname">
                      <img src="assets/img/laptop.png" class="me-2" alt="Laptop Image">
                      <span>OpenVpn</span>
                    </a>
                  </td>
                  <td>AST - 006</td>
                  <td>Lenovo</td>
                  <td>22 Nov, 2022 10:32AM</td>
                  <td class="table-namesplit">
                    <a href="javascript:void(0);" class="table-profileimage">
                      <img src="assets/img/profiles/avatar-05.jpg" class="me-2" alt="User Image">
                    </a>
                    <a href="javascript:void(0);" class="table-name">
                      <span>Vinod Selvaraj</span>
                      <p>vinod.s@dreamguystech.com</p>
                    </a>
                  </td>
                  <td>
                    <div class="table-actions d-flex">
                      <a class="delete-table me-2" href="user-asset-details.html">
                        <img src="assets/img/icons/eye.svg" alt="Eye Icon">
                      </a>
                    </div>
                  </td>
                </tr>
                <tr class="even">
                  <td class="sorting_1">7</td>
                  <td>
                    <a href="#" class="table-imgname">
                      <img src="assets/img/laptop.png" class="me-2" alt="Laptop Image">
                      <span>Laptop</span>
                    </a>
                  </td>
                  <td>AST - 006</td>
                  <td>Lenovo</td>
                  <td>22 Nov, 2022 10:32AM</td>
                  <td class="table-namesplit">
                    <a href="javascript:void(0);" class="table-profileimage">
                      <img src="assets/img/profiles/avatar-05.jpg" class="me-2" alt="User Image">
                    </a>
                    <a href="javascript:void(0);" class="table-name">
                      <span>Vinod Selvaraj</span>
                      <p>vinod.s@dreamguystech.com</p>
                    </a>
                  </td>
                  <td>
                    <div class="table-actions d-flex">
                      <a class="delete-table me-2" href="user-asset-details.html">
                        <img src="assets/img/icons/eye.svg" alt="Eye Icon">
                      </a>
                    </div>
                  </td>
                </tr>
                <tr class="odd">
                  <td class="sorting_1">8</td>
                  <td>
                    <a href="#" class="table-imgname">
                      <img src="assets/img/laptop.png" class="me-2" alt="Laptop Image">
                      <span>HDMI Connector</span>
                    </a>
                  </td>
                  <td>AST - 005</td>
                  <td>ASUS</td>
                  <td>22 Nov, 2022 10:32AM</td>
                  <td class="table-namesplit">
                    <a href="assets-details.html" class="table-profileimage">
                      <img src="assets/img/profiles/avatar-02.jpg" class="me-2" alt="User Image">
                    </a>
                    <a href="assets-details.html" class="table-name">
                      <span>John Paul Raj</span>
                      <p>john@dreamguystech.com</p>
                    </a>
                  </td>
                  <td>
                    <div class="table-actions d-flex">
                      <a class="delete-table me-2" href="user-asset-details.html">
                        <img src="assets/img/icons/eye.svg" alt="Eye Icon">
                      </a>
                    </div>
                  </td>
                </tr>

                <tr class="odd">
                  <td class="sorting_1">9</td>
                  <td>
                    <a href="#" class="table-imgname">
                      <img src="assets/img/laptop.png" class="me-2" alt="Laptop Image">
                      <span>Miscellaneous Item</span>
                    </a>
                  </td>
                  <td>AST - 005</td>
                  <td>ASUS</td>
                  <td>22 Nov, 2022 10:32AM</td>
                  <td class="table-namesplit">
                    <a href="assets-details.html" class="table-profileimage">
                      <img src="assets/img/profiles/avatar-02.jpg" class="me-2" alt="User Image">
                    </a>
                    <a href="assets-details.html" class="table-name">
                      <span>John Paul Raj</span>
                      <p>john@dreamguystech.com</p>
                    </a>
                  </td>
                  <td>
                    <div class="table-actions d-flex">
                      <a class="delete-table me-2" href="user-asset-details.html">
                        <img src="assets/img/icons/eye.svg" alt="Eye Icon">
                      </a>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
        <div class="row">
          <div class="col-sm-12 col-md-5">
            <div class="dataTables_info" id="DataTables_Table_0_info" role="status" aria-live="polite">
              Showing 1 to
              6 of 6 entries</div>
          </div>
          <div class="col-sm-12 col-md-7">
            <div class="dataTables_paginate paging_simple_numbers" id="DataTables_Table_0_paginate">
              <ul class="pagination">
                <li class="paginate_button page-item previous disabled" id="DataTables_Table_0_previous"><a href="#"
                    aria-controls="DataTables_Table_0" data-dt-idx="0" tabindex="0" class="page-link"><i
                      class="fa fa-angle-double-left"></i> </a></li>
                <li class="paginate_button page-item active"><a href="#" aria-controls="DataTables_Table_0"
                    data-dt-idx="1" tabindex="0" class="page-link">1</a></li>
                <li class="paginate_button page-item next disabled" id="DataTables_Table_0_next">
                  <a href="#" aria-controls="DataTables_Table_0" data-dt-idx="2" tabindex="0" class="page-link"> <i
                      class=" fa fa-angle-double-right"></i></a>
                </li>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- /Assets -->
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
        <form id="profileModal" method="POST">
          @csrf
          @method('PUT')
          <input type="hidden" name="userProfileId" value="{{ $profileUser->id }}">
          <!-- Assuming you have this ID -->
          <div class="row">
            <div class="col-md-6">
              <div class="input-block mb-3">
                <label class="col-form-label">UserName</label>
                <input type="text" class="form-control" value="{{ $mainUser->username }}" disabled name="username">
              </div>
            </div>
            <div class="col-md-6">
              <div class="input-block mb-3">
                <label class="col-form-label">Designation</label>
                <select class="select form-control" name="designation_id" disabled>
                  <option value="" selected>{{ $profileUser->designationName }}
                  </option>
                </select>
                <div class="val_error text-danger"></div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="input-block mb-3">
                <label class="col-form-label">Department</label>
                <select class="select form-control " tabindex="-1" aria-hidden="true" name="departmentName" disabled>
                  <option value="" selected>{{ $profileUser->departmentName }}</option>
                </select>
              </div>
            </div>
            <div class="col-md-6">
              <div class="input-block mb-3">
                <label class="col-form-label">Employee ID:</label>
                <input type="text" class="form-control" value="{{ $mainUser->employee_id }}" name="employee_id"
                  disabled>
              </div>
            </div>
            <div class="col-md-6">
              <div class="input-block mb-3">
                <label class="col-form-label">Date Of Join</label>
                <input type="date" class="form-control" value="{{ $mainUser->joining_date }}" name="joining_date"
                  disabled>
              </div>
            </div>
            <div class="col-md-6">
              <div class="input-block mb-3">
                <label class="col-form-label">Status</label>
                <select class="select form-control" name="status" disabled>
                  <option value="" disabled>Select Status</option>
                  <option value="1" {{ $profileUser->status == '1' ? 'selected' : '' }}>
                    Active</option>
                  <option value="0" {{ $profileUser->status == '0' ? 'selected' : '' }}>
                    Inactive</option>
                </select>
              </div>
            </div>
            <div class="col-md-6">
              <div class="input-block mb-3">
                <label class="col-form-label">Real Name</label>
                <input type="text" class="form-control" id="profileName" value="{{ $profileUser->real_name }}"
                  name="real_name" id="realName">
              </div>
            </div>
            <div class="col-md-6">
              <div class="input-block mb-3">
                <label class="col-form-label">Email</label>
                <input type="text" class="form-control" value="{{ $mainUser->email }}" name="email" disabled required>
              </div>
            </div>
            <div class="col-md-6">
              <div class="input-block mb-3">
                <label class="col-form-label">Birthday</label>
                <input type="date" class="form-control" value="{{ $profileUser->dob }}" name="dob" disabled>
              </div>
            </div>
            <div class="col-md-6">
              <div class="input-block mb-3">
                <label class="col-form-label">Accommodation</label>
                <input type="text" class="form-control" id="profileAccommodation"
                  value="{{ $profileUser->accomodation }}" name="accomodation">
              </div>
            </div>

            <div class="col-md-6">
              <div class="input-block mb-3">
                <label class="col-form-label" for="gender">Gender</label>
                <select class="select form-control" name="gender" id="profileGender">
                  <option value="" disabled {{ empty($profileUser->gender) ? 'selected' : '' }}>Select Gender</option>
                  <option value="Male" {{ $profileUser->gender === 'Male' ? 'selected' : '' }}>
                    Male</option>
                  <option value="Female" {{ $profileUser->gender === 'Female' ? 'selected' : '' }}>
                    Female</option>
                  <option value="Other" {{ $profileUser->gender === 'Other' ? 'selected' : '' }}>
                    Other</option>
                </select>
              </div>
            </div>

            <div class="col-md-6">
              <div class="input-block mb-3">
                <label class="col-form-label">Phone Number</label>
                <input type="text" class="form-control" id="profilePhone" value="{{ $profileUser->phone }}"
                  name="phone">
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
<!-- /Profile Modal -->



<!-- Personal Info Modal -->
<div id="personal_info_modal" class="modal custom-modal fade" role="dialog">
  <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Personal Information</h5>
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
                <input type="text" class="form-control" name="passport_no" value="{{ $visaInfo->passport_no }}">
              </div>
            </div>
            <div class="col-md-6">
              <div class="input-block mb-3">
                <label class="col-form-label">Passport Issue Date: <span class="text-danger">*</span></label>
                <input class="form-control" type="date" value="{{ $visaInfo->p_issue_date }}" name="p_issue_date">
              </div>
            </div>
            <div class="col-md-6">
              <div class="input-block mb-3">
                <label class="col-form-label">Passport Expiry Date: <span class="text-danger">*</span></label>
                <input class="form-control" type="date" value="{{ $visaInfo->p_expiry_date }}" name="p_expiry_date">
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
                <label class="col-form-label">Visa Issue Date: <span class="text-danger">*</span></label>
                <input class="form-control" type="date" value="{{ $visaInfo->v_issue_date }}" name="v_issue_date">
              </div>
            </div>
            <div class="col-md-6">
              <div class="input-block mb-3">
                <label class="col-form-label">Visa Expiry Date: <span class="text-danger">*</span></label>
                <input class="form-control" type="date" value="{{ $visaInfo->v_expiry_date }}" name="v_expiry_date">
              </div>
            </div>
            <div class="col-md-6">
              <div class="input-block mb-3">
                <label class="col-form-label">Foreign No:<span class="text-danger">*</span></label>
                <div class="">
                  <input class="form-control" type="text" value="{{ $visaInfo->foreign_no }}" name="foreign_no">
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="input-block mb-3">
                <label class="col-form-label">Foreign Expiry Date:<span class="text-danger">*</span></label>
                <input class="form-control" type="date" value="{{ $visaInfo->f_expiry_date }}" name="f_expiry_date">
              </div>
            </div>

            <div class="col-md-6">
              <div class="input-block mb-3">
                <label class="col-form-label">Nationailty:<span class="text-danger">*</span></label>
                <input class="form-control" type="text" value="{{ $profileUser->nationality }}" name="nationality">
              </div>
            </div>
            <div class="col-md-6">
              <div class="input-block mb-3">
                <label class="col-form-label">Religion:<span class="text-danger">*</span></label>
                <input class="form-control" type="text" value="{{ $profileUser->religion }}" name="religion">
              </div>
            </div>
            <div class="col-md-6">
              <div class="input-block mb-3">
                <label class="col-form-label">Telegram:<span class="text-danger">*</span></label>
                <input class="form-control" type="text" value="{{ $profileUser->telegram }}" name="telegram">
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
                <h3 class="card-title">Family Member <a href="javascript:void(0);" class="delete-icon"><i
                      class="fa-regular fa-trash-can"></i></a></h3>
                <div class="row">
                  <div class="col-md-6">
                    <div class="input-block mb-3">
                      <label class="col-form-label">Name <span class="text-danger">*</span></label>
                      <input class="form-control" type="text">
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="input-block mb-3">
                      <label class="col-form-label">Relationship <span class="text-danger">*</span></label>
                      <input class="form-control" type="text">
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="input-block mb-3">
                      <label class="col-form-label">Date of birth <span class="text-danger">*</span></label>
                      <input class="form-control" type="text">
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="input-block mb-3">
                      <label class="col-form-label">Phone <span class="text-danger">*</span></label>
                      <input class="form-control" type="text">
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="card">
              <div class="card-body">
                <h3 class="card-title">Education Informations <a href="javascript:void(0);" class="delete-icon"><i
                      class="fa-regular fa-trash-can"></i></a></h3>
                <div class="row">
                  <div class="col-md-6">
                    <div class="input-block mb-3">
                      <label class="col-form-label">Name <span class="text-danger">*</span></label>
                      <input class="form-control" type="text">
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="input-block mb-3">
                      <label class="col-form-label">Relationship <span class="text-danger">*</span></label>
                      <input class="form-control" type="text">
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="input-block mb-3">
                      <label class="col-form-label">Date of birth <span class="text-danger">*</span></label>
                      <input class="form-control" type="text">
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="input-block mb-3">
                      <label class="col-form-label">Phone <span class="text-danger">*</span></label>
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
            <button class="btn btn-primary submit-btn">Submit</button>
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
                    <input type="text" class="form-control" name="emergency_name" value="{{ $EmergencyUser->e_name }}">
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
                    <label class="col-form-label">Rerlationship with Employee</label>
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
                    <input type="text" name="dependant_name" class="form-control" value="{{ $DependantUser->d_name }}">
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
                    <label class="col-form-label">Nationality <span class="text-danger">*</span></label>
                    <input class="form-control" type="text" name="dependant_nationality"
                      value="{{ $DependantUser->d_nationality }}">
                    <div class="val_error text-danger"></div>

                  </div>
                </div>
                <div class="col-md-6">
                  <div class="input-block mb-3">
                    <label class="col-form-label">Date Of Birth</label>
                    <input class="form-control" type="date" name="dependant_dob" value="{{ $DependantUser->d_dob }}">
                    <div class="val_error text-danger"></div>

                  </div>
                </div>

                <div class="col-md-6">
                  <div class="input-block mb-3">
                    <label class="col-form-label">Passport No <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="dependant_passport_no"
                      value="{{ $DependantUser->d_passport_no }}">
                    <div class="val_error text-danger"></div>

                  </div>
                </div>
                <div class="col-md-6">
                  <div class="input-block mb-3">
                    <label class="col-form-label">Passport Issue Date <span class="text-danger">*</span></label>
                    <input class="form-control" type="date" name="dependant_pass_issue_date"
                      value="{{ $DependantUser->d_pass_issue_date }}">
                    <div class="val_error text-danger"></div>

                  </div>
                </div>

                <div class="col-md-6">
                  <div class="input-block mb-3">
                    <label class="col-form-label">Passport Expiry Date <span class="text-danger">*</span></label>
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
                    <label class="col-form-label">Visa Issue Date <span class="text-danger">*</span></label>
                    <input class="form-control" type="date" name="dependant_visa_issue_date"
                      value="{{ $DependantUser->d_visa_issue_date }}">
                    <div class="val_error text-danger"></div>

                  </div>
                </div>

                <div class="col-md-6">
                  <div class="input-block mb-3">
                    <label class="col-form-label">Visa Expiry Date <span class="text-danger">*</span></label>
                    <input class="form-control" type="date" name="dependant_visa_expiry_date"
                      value="{{ $DependantUser->d_visa_expiry_date }}">
                    <div class="val_error text-danger"></div>

                  </div>
                </div>
              </div>


              <div class="submit-section">
                <button type="submit" class="btn btn-primary submit-btn">Submit</button>
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

  $(document).on('submit', 'form[id^="profileModal"]', function (e) {
    e.preventDefault();

    var formData = new FormData(this);
    var userId = $('input[name="userProfileId"]', this).val();

    showLoader();
    $.ajax({
      url: '{{ route('user-profile.update', ':id') }}'.replace(':id', userId),
      method: 'POST',
      data: formData,
      contentType: false,
      processData: false,
      success: function (response) {
        if (response.status === 'success' && response.data) {
          hideLoader();
          $(this).closest('.modal').modal('hide');

          $('#realName').text(response.data.real_name || '');
          $('#accommodation').text(response.data.accomodation || '');
          $('#gender').text(response.data.gender || '');
          $('#phone').text(response.data.phone || '');
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
      url: '{{ route('update.visainfo', ':id') }}'.replace(':id',
        userId), // Set the correct URL
      data: $(this).serialize(), // Serialize the form data
      success: function (response) {
        var updatedVisaInfo = response.visaInfo;
        var updatedUserProfile = response.userProfile;

        if (updatedVisaInfo) {
          hideLoader();

          $('#passport_no').text(updatedVisaInfo.passport_no);
          $('#p_issue_date').text(updatedVisaInfo.p_issue_date);
          $('#p_expiry_date').text(updatedVisaInfo.p_expiry_date);
          $('#visa_no').text(updatedVisaInfo.visa_no);
          $('#v_issue_date').text(updatedVisaInfo.v_issue_date);
          $('#v_expiry_date').text(updatedVisaInfo.v_expiry_date);
          $('#foreign_no').text(updatedVisaInfo.foreign_no);
          $('#f_expiry_date').text(updatedVisaInfo.f_expiry_date);
          $('#nationality').text(updatedUserProfile.nationality);
          $('#religion').text(updatedUserProfile.religion);
          $('#telegram').text(updatedUserProfile.telegram);

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
      url: '{{ route('emergency.update') }}',
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
      url: '{{ route('dependant.update') }}',
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