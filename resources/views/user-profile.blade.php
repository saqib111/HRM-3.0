@extends('layout.mainlayout')
@section('content')

<!-- Page Header -->
<div class="page-header">
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
@foreach($profileUsers as $profileUser)
<div class="card mb-0">
  <div class="card-body">
    <div class="row">
      <div class="col-md-12">
        <div class="profile-view">
          <div class="profile-img-wrap">
            <div class="profile-img">
              <a href="#"><img src="assets/img/profiles/avatar-02.jpg" alt="User Image"></a>
            </div>
          </div>
          <div class="profile-basic">
            <div class="row">
              <div class="col-md-5">
                <div class="profile-info-left">

                  <h3 class="user-name m-t-0 mb-0">{{$profileUser->username}}</h3>
                  <h6 class="text-muted">{{$profileUser->designationName}}</h6>
                  <small class="text-muted">{{$profileUser->departmentName}}</small>
                  <div class="staff-id">Employee ID : {{$profileUser->employee_id}}</div>
                  <div class="small doj text-muted">Date of Join : {{$profileUser->joining_date}}</div>
                  <!-- <div class="staff-msg"><a class="btn btn-custom" href="chat.html">Send Message</a></div> -->
                  @if ($profileUser->status == "1")
                  <a href="#" class="btn btn-white btn-sm badge-outline-success "> Active </a>
                  @elseif($profileUser->status == "0")
                  <a href="#" class="btn btn-white btn-sm badge-outline-danger "> Inactive </a>
                  @endif



                </div>
              </div>
              <div class="col-md-7">
                <ul class="personal-info">
                  <li>
                    <div class="title">Real Name:</div>
                    <div class="text">
                      <div class="avatar-box">
                        <div class="avatar avatar-xs">
                          <img src="assets/img/profiles/avatar-16.jpg" alt="User Image">
                        </div>
                      </div>
                      <a href="#">
                        {{$profileUser->real_name}}
                      </a>
                    </div>
                  </li>

                  <li>
                    <div class="title">Email:</div>
                    <div class="text"><a href="#">{{$profileUser->email}}</a></div>
                  </li>
                  <li>
                    <div class="title">Birthday:</div>
                    <div class="text">{{$profileUser->dob}}</div>
                  </li>
                  <li>
                    <div class="title">Accomodation:</div>
                    <div class="text">{{$profileUser->accomodation}}</div>
                  </li>
                  <li>
                    <div class="title">Gender:</div>
                    <div class="text">{{$profileUser->gender}}</div>
                  </li>
                  <li>
                    <div class="title">Phone:</div>
                    <div class="text"><a href="#">{{$profileUser->phone}}</a></div>
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
        <li class="nav-item" role="presentation"><a href="#emp_projects" data-bs-toggle="tab" class="nav-link"
            aria-selected="false" tabindex="-1" role="tab">Projects</a></li>
        <li class="nav-item" role="presentation"><a href="#bank_statutory" data-bs-toggle="tab" class="nav-link"
            aria-selected="false" tabindex="-1" role="tab">Bank &amp; Statutory <small class="text-danger">(Admin
              Only)</small></a></li>
        <li class="nav-item" role="presentation"><a href="#emp_assets" data-bs-toggle="tab" class="nav-link"
            aria-selected="false" tabindex="-1" role="tab">Assets</a></li>
      </ul>
    </div>
  </div>
</div>

<div class="tab-content">

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
                <div class="title">Passport No.</div>
                <div class="text">{{$profileUser->passport_no}}</div>
              </li>
              <li>
                <div class="title">Passport Issue Date.</div>
                <div class="text">{{$profileUser->p_issue_date}}</div>
              </li>
              <li>
                <div class="title">Passport Exp Date.</div>
                <div class="text">{{$profileUser->p_expiry_date}}</div>
              </li>
              <li>
                <div class="title">Visa No.</div>
                <div class="text"><a href="#">{{$profileUser->visa_no}}</a></div>
              </li>
              <li>
                <div class="title">Visa Issue Date.</div>
                <div class="text"><a href="#">{{$profileUser->v_issue_date}}</a></div>
              </li>
              <li>
                <div class="title">Foreign No.</div>
                <div class="text"><a href="#">{{$profileUser->foreign_no}}</a></div>
              </li>
              <li>
                <div class="title">Foreign Expiry Date.</div>
                <div class="text"><a href="#">{{$profileUser->f_expiry_date}}</a></div>
              </li>
              <li>
                <div class="title">Nationality</div>
                <div class="text">{{$profileUser->nationality}}</div>
              </li>
              <li>
                <div class="title">Religion</div>
                <div class="text">{{$profileUser->religion}}</div>
              </li>
              <li>
                <div class="title">Marital status</div>
                <div class="text">Married</div>
              </li>
              <!-- <li>
      <div class="title">Employment of spouse</div>
      <div class="text">No</div>
      </li> -->
              <li>
                <div class="title">Telegram</div>
                <div class="text">{{$profileUser->telegram}}</div>
              </li>
            </ul>
          </div>
        </div>
      </div>
      <div class="col-md-6 d-flex">
        <div class="card profile-box flex-fill">
          <div class="card-body">
            <h3 class="card-title">Emergency Contact <a href="#" class="edit-icon" data-bs-toggle="modal"
                data-bs-target="#emergency_contact_modal"><i class="fa-solid fa-pencil"></i></a></h3>
            <ul class="personal-info">
              <li>
                <div class="title">Name:</div>
                <div class="text"></div>
              </li>
              <li>
                <div class="title">Phone:</div>
                <div class="text"></div>
              </li>
              <li>
                <div class="title">Email:</div>
                <div class="text"></div>
              </li>
              <li>
                <div class="title">Address:</div>
                <div class="text"></div>
              </li>
              <li>
                <div class="title">Country:</div>
                <div class="text"></div>
              </li>
              <li>
                <div class="title">Gender:</div>
                <div class="text"></div>
              </li>
              <li>
                <div class="title">Relationship With Employee:</div>
                <div class="text"></div>
              </li>
            </ul>
          </div>
        </div>
      </div>
    </div>
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
    <div class="row">
      <div class="col-md-6 d-flex">
        <div class="card profile-box flex-fill">
          <div class="card-body">
            <h3 class="card-title">Education Informations <a href="#" class="edit-icon" data-bs-toggle="modal"
                data-bs-target="#education_info"><i class="fa-solid fa-pencil"></i></a></h3>
            <div class="experience-box">
              <ul class="experience-list">
                <li>
                  <div class="experience-user">
                    <div class="before-circle"></div>
                  </div>
                  <div class="experience-content">
                    <div class="timeline-content">
                      <a href="#/" class="name">International College of Arts and Science (UG)</a>
                      <div>Bsc Computer Science</div>
                      <span class="time">2000 - 2003</span>
                    </div>
                  </div>
                </li>
                <li>
                  <div class="experience-user">
                    <div class="before-circle"></div>
                  </div>
                  <div class="experience-content">
                    <div class="timeline-content">
                      <a href="#/" class="name">International College of Arts and Science (PG)</a>
                      <div>Msc Computer Science</div>
                      <span class="time">2000 - 2003</span>
                    </div>
                  </div>
                </li>
              </ul>
            </div>
          </div>
        </div>
      </div>
      <div class="col-md-6 d-flex">
        <div class="card profile-box flex-fill">
          <div class="card-body">
            <h3 class="card-title">Experience <a href="#" class="edit-icon" data-bs-toggle="modal"
                data-bs-target="#experience_info"><i class="fa-solid fa-pencil"></i></a></h3>
            <div class="experience-box">
              <ul class="experience-list">
                <li>
                  <div class="experience-user">
                    <div class="before-circle"></div>
                  </div>
                  <div class="experience-content">
                    <div class="timeline-content">
                      <a href="#/" class="name">Web Designer at Zen Corporation</a>
                      <span class="time">Jan 2013 - Present (5 years 2 months)</span>
                    </div>
                  </div>
                </li>
                <li>
                  <div class="experience-user">
                    <div class="before-circle"></div>
                  </div>
                  <div class="experience-content">
                    <div class="timeline-content">
                      <a href="#/" class="name">Web Designer at Ron-tech</a>
                      <span class="time">Jan 2013 - Present (5 years 2 months)</span>
                    </div>
                  </div>
                </li>
                <li>
                  <div class="experience-user">
                    <div class="before-circle"></div>
                  </div>
                  <div class="experience-content">
                    <div class="timeline-content">
                      <a href="#/" class="name">Web Designer at Dalt Technology</a>
                      <span class="time">Jan 2013 - Present (5 years 2 months)</span>
                    </div>
                  </div>
                </li>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- /Profile Info Tab -->

  <!-- Projects Tab -->
  <div class="tab-pane fade" id="emp_projects" role="tabpanel">
    <div class="row">
      <div class="col-lg-4 col-sm-6 col-md-4 col-xl-3">
        <div class="card">
          <div class="card-body">
            <div class="dropdown profile-action">
              <a aria-expanded="false" data-bs-toggle="dropdown" class="action-icon dropdown-toggle" href="#"><i
                  class="material-icons">more_vert</i></a>
              <div class="dropdown-menu dropdown-menu-right">
                <a data-bs-target="#edit_project" data-bs-toggle="modal" href="#" class="dropdown-item"><i
                    class="fa-solid fa-pencil m-r-5"></i> Edit</a>
                <a data-bs-target="#delete_project" data-bs-toggle="modal" href="#" class="dropdown-item"><i
                    class="fa-regular fa-trash-can m-r-5"></i> Delete</a>
              </div>
            </div>
            <h4 class="project-title"><a href="project-view.html">Office Management</a></h4>
            <small class="block text-ellipsis m-b-15">
              <span class="text-xs">1</span> <span class="text-muted">open tasks, </span>
              <span class="text-xs">9</span> <span class="text-muted">tasks completed</span>
            </small>
            <p class="text-muted">Lorem Ipsum is simply dummy text of the printing and
              typesetting industry. When an unknown printer took a galley of type and
              scrambled it...
            </p>
            <div class="pro-deadline m-b-15">
              <div class="sub-title">
                Deadline:
              </div>
              <div class="text-muted">
                17 Apr 2019
              </div>
            </div>
            <div class="project-members m-b-15">
              <div>Project Leader :</div>
              <ul class="team-members">
                <li>
                  <a href="#" data-bs-toggle="tooltip" aria-label="Jeffery Lalor"
                    data-bs-original-title="Jeffery Lalor"><img src="assets/img/profiles/avatar-16.jpg"
                      alt="User Image"></a>
                </li>
              </ul>
            </div>
            <div class="project-members m-b-15">
              <div>Team :</div>
              <ul class="team-members">
                <li>
                  <a href="#" data-bs-toggle="tooltip" aria-label="John Doe" data-bs-original-title="John Doe"><img
                      src="assets/img/profiles/avatar-02.jpg" alt="User Image"></a>
                </li>
                <li>
                  <a href="#" data-bs-toggle="tooltip" aria-label="Richard Miles"
                    data-bs-original-title="Richard Miles"><img src="assets/img/profiles/avatar-09.jpg"
                      alt="User Image"></a>
                </li>
                <li>
                  <a href="#" data-bs-toggle="tooltip" aria-label="John Smith" data-bs-original-title="John Smith"><img
                      src="assets/img/profiles/avatar-10.jpg" alt="User Image"></a>
                </li>
                <li>
                  <a href="#" data-bs-toggle="tooltip" aria-label="Mike Litorus"
                    data-bs-original-title="Mike Litorus"><img src="assets/img/profiles/avatar-05.jpg"
                      alt="User Image"></a>
                </li>
                <li>
                  <a href="#" class="all-users">+15</a>
                </li>
              </ul>
            </div>
            <p class="m-b-5">Progress <span class="text-success float-end">40%</span></p>
            <div class="progress progress-xs mb-0">
              <div class="w-40" title="" data-bs-toggle="tooltip" role="progressbar" data-original-title="40%">
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="col-lg-4 col-sm-6 col-md-4 col-xl-3">
        <div class="card">
          <div class="card-body">
            <div class="dropdown profile-action">
              <a aria-expanded="false" data-bs-toggle="dropdown" class="action-icon dropdown-toggle" href="#"><i
                  class="material-icons">more_vert</i></a>
              <div class="dropdown-menu dropdown-menu-right">
                <a data-bs-target="#edit_project" data-bs-toggle="modal" href="#" class="dropdown-item"><i
                    class="fa-solid fa-pencil m-r-5"></i> Edit</a>
                <a data-bs-target="#delete_project" data-bs-toggle="modal" href="#" class="dropdown-item"><i
                    class="fa-regular fa-trash-can m-r-5"></i> Delete</a>
              </div>
            </div>
            <h4 class="project-title"><a href="project-view.html">Project Management</a></h4>
            <small class="block text-ellipsis m-b-15">
              <span class="text-xs">2</span> <span class="text-muted">open tasks, </span>
              <span class="text-xs">5</span> <span class="text-muted">tasks completed</span>
            </small>
            <p class="text-muted">Lorem Ipsum is simply dummy text of the printing and
              typesetting industry. When an unknown printer took a galley of type and
              scrambled it...
            </p>
            <div class="pro-deadline m-b-15">
              <div class="sub-title">
                Deadline:
              </div>
              <div class="text-muted">
                17 Apr 2019
              </div>
            </div>
            <div class="project-members m-b-15">
              <div>Project Leader :</div>
              <ul class="team-members">
                <li>
                  <a href="#" data-bs-toggle="tooltip" aria-label="Jeffery Lalor"
                    data-bs-original-title="Jeffery Lalor"><img src="assets/img/profiles/avatar-16.jpg"
                      alt="User Image"></a>
                </li>
              </ul>
            </div>
            <div class="project-members m-b-15">
              <div>Team :</div>
              <ul class="team-members">
                <li>
                  <a href="#" data-bs-toggle="tooltip" aria-label="John Doe" data-bs-original-title="John Doe"><img
                      src="assets/img/profiles/avatar-02.jpg" alt="User Image"></a>
                </li>
                <li>
                  <a href="#" data-bs-toggle="tooltip" aria-label="Richard Miles"
                    data-bs-original-title="Richard Miles"><img src="assets/img/profiles/avatar-09.jpg"
                      alt="User Image"></a>
                </li>
                <li>
                  <a href="#" data-bs-toggle="tooltip" aria-label="John Smith" data-bs-original-title="John Smith"><img
                      src="assets/img/profiles/avatar-10.jpg" alt="User Image"></a>
                </li>
                <li>
                  <a href="#" data-bs-toggle="tooltip" aria-label="Mike Litorus"
                    data-bs-original-title="Mike Litorus"><img src="assets/img/profiles/avatar-05.jpg"
                      alt="User Image"></a>
                </li>
                <li>
                  <a href="#" class="all-users">+15</a>
                </li>
              </ul>
            </div>
            <p class="m-b-5">Progress <span class="text-success float-end">40%</span></p>
            <div class="progress progress-xs mb-0">
              <div class="w-40" title="" data-bs-toggle="tooltip" role="progressbar" data-original-title="40%">
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="col-lg-4 col-sm-6 col-md-4 col-xl-3">
        <div class="card">
          <div class="card-body">
            <div class="dropdown profile-action">
              <a aria-expanded="false" data-bs-toggle="dropdown" class="action-icon dropdown-toggle" href="#"><i
                  class="material-icons">more_vert</i></a>
              <div class="dropdown-menu dropdown-menu-right">
                <a data-bs-target="#edit_project" data-bs-toggle="modal" href="#" class="dropdown-item"><i
                    class="fa-solid fa-pencil m-r-5"></i> Edit</a>
                <a data-bs-target="#delete_project" data-bs-toggle="modal" href="#" class="dropdown-item"><i
                    class="fa-regular fa-trash-can m-r-5"></i> Delete</a>
              </div>
            </div>
            <h4 class="project-title"><a href="project-view.html">Video Calling App</a></h4>
            <small class="block text-ellipsis m-b-15">
              <span class="text-xs">3</span> <span class="text-muted">open tasks, </span>
              <span class="text-xs">3</span> <span class="text-muted">tasks completed</span>
            </small>
            <p class="text-muted">Lorem Ipsum is simply dummy text of the printing and
              typesetting industry. When an unknown printer took a galley of type and
              scrambled it...
            </p>
            <div class="pro-deadline m-b-15">
              <div class="sub-title">
                Deadline:
              </div>
              <div class="text-muted">
                17 Apr 2019
              </div>
            </div>
            <div class="project-members m-b-15">
              <div>Project Leader :</div>
              <ul class="team-members">
                <li>
                  <a href="#" data-bs-toggle="tooltip" aria-label="Jeffery Lalor"
                    data-bs-original-title="Jeffery Lalor"><img src="assets/img/profiles/avatar-16.jpg"
                      alt="User Image"></a>
                </li>
              </ul>
            </div>
            <div class="project-members m-b-15">
              <div>Team :</div>
              <ul class="team-members">
                <li>
                  <a href="#" data-bs-toggle="tooltip" aria-label="John Doe" data-bs-original-title="John Doe"><img
                      src="assets/img/profiles/avatar-02.jpg" alt="User Image"></a>
                </li>
                <li>
                  <a href="#" data-bs-toggle="tooltip" aria-label="Richard Miles"
                    data-bs-original-title="Richard Miles"><img src="assets/img/profiles/avatar-09.jpg"
                      alt="User Image"></a>
                </li>
                <li>
                  <a href="#" data-bs-toggle="tooltip" aria-label="John Smith" data-bs-original-title="John Smith"><img
                      src="assets/img/profiles/avatar-10.jpg" alt="User Image"></a>
                </li>
                <li>
                  <a href="#" data-bs-toggle="tooltip" aria-label="Mike Litorus"
                    data-bs-original-title="Mike Litorus"><img src="assets/img/profiles/avatar-05.jpg"
                      alt="User Image"></a>
                </li>
                <li>
                  <a href="#" class="all-users">+15</a>
                </li>
              </ul>
            </div>
            <p class="m-b-5">Progress <span class="text-success float-end">40%</span></p>
            <div class="progress progress-xs mb-0">
              <div class="w-40" title="" data-bs-toggle="tooltip" role="progressbar" data-original-title="40%">
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="col-lg-4 col-sm-6 col-md-4 col-xl-3">
        <div class="card">
          <div class="card-body">
            <div class="dropdown profile-action">
              <a aria-expanded="false" data-bs-toggle="dropdown" class="action-icon dropdown-toggle" href="#"><i
                  class="material-icons">more_vert</i></a>
              <div class="dropdown-menu dropdown-menu-right">
                <a data-bs-target="#edit_project" data-bs-toggle="modal" href="#" class="dropdown-item"><i
                    class="fa-solid fa-pencil m-r-5"></i> Edit</a>
                <a data-bs-target="#delete_project" data-bs-toggle="modal" href="#" class="dropdown-item"><i
                    class="fa-regular fa-trash-can m-r-5"></i> Delete</a>
              </div>
            </div>
            <h4 class="project-title"><a href="project-view.html">Hospital Administration</a></h4>
            <small class="block text-ellipsis m-b-15">
              <span class="text-xs">12</span> <span class="text-muted">open tasks, </span>
              <span class="text-xs">4</span> <span class="text-muted">tasks completed</span>
            </small>
            <p class="text-muted">Lorem Ipsum is simply dummy text of the printing and
              typesetting industry. When an unknown printer took a galley of type and
              scrambled it...
            </p>
            <div class="pro-deadline m-b-15">
              <div class="sub-title">
                Deadline:
              </div>
              <div class="text-muted">
                17 Apr 2019
              </div>
            </div>
            <div class="project-members m-b-15">
              <div>Project Leader :</div>
              <ul class="team-members">
                <li>
                  <a href="#" data-bs-toggle="tooltip" aria-label="Jeffery Lalor"
                    data-bs-original-title="Jeffery Lalor"><img src="assets/img/profiles/avatar-16.jpg"
                      alt="User Image"></a>
                </li>
              </ul>
            </div>
            <div class="project-members m-b-15">
              <div>Team :</div>
              <ul class="team-members">
                <li>
                  <a href="#" data-bs-toggle="tooltip" aria-label="John Doe" data-bs-original-title="John Doe"><img
                      src="assets/img/profiles/avatar-02.jpg" alt="User Image"></a>
                </li>
                <li>
                  <a href="#" data-bs-toggle="tooltip" aria-label="Richard Miles"
                    data-bs-original-title="Richard Miles"><img src="assets/img/profiles/avatar-09.jpg"
                      alt="User Image"></a>
                </li>
                <li>
                  <a href="#" data-bs-toggle="tooltip" aria-label="John Smith" data-bs-original-title="John Smith"><img
                      src="assets/img/profiles/avatar-10.jpg" alt="User Image"></a>
                </li>
                <li>
                  <a href="#" data-bs-toggle="tooltip" aria-label="Mike Litorus"
                    data-bs-original-title="Mike Litorus"><img src="assets/img/profiles/avatar-05.jpg"
                      alt="User Image"></a>
                </li>
                <li>
                  <a href="#" class="all-users">+15</a>
                </li>
              </ul>
            </div>
            <p class="m-b-5">Progress <span class="text-success float-end">40%</span></p>
            <div class="progress progress-xs mb-0">
              <div class="w-40" title="" data-bs-toggle="tooltip" role="progressbar" data-original-title="40%">
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- /Projects Tab -->

  <!-- Bank Statutory Tab -->
  <div class="tab-pane fade" id="bank_statutory" role="tabpanel">
    <div class="card">
      <div class="card-body">
        <h3 class="card-title"> Basic Salary Information</h3>
        <form>
          <div class="row">
            <div class="col-sm-4">
              <div class="input-block mb-3">
                <label class="col-form-label">Salary basis <span class="text-danger">*</span></label>
                <select class="select select2-hidden-accessible" data-select2-id="select2-data-1-34oh" tabindex="-1"
                  aria-hidden="true">
                  <option data-select2-id="select2-data-3-f44p">Select salary basis type</option>
                  <option>Hourly</option>
                  <option>Daily</option>
                  <option>Weekly</option>
                  <option>Monthly</option>
                </select><span class="select2 select2-container select2-container--default" dir="ltr"
                  data-select2-id="select2-data-2-rlv6" style="width: 100%;"><span class="selection"><span
                      class="select2-selection select2-selection--single" role="combobox" aria-haspopup="true"
                      aria-expanded="false" tabindex="0" aria-disabled="false" aria-labelledby="select2-ciy7-container"
                      aria-controls="select2-ciy7-container"><span class="select2-selection__rendered"
                        id="select2-ciy7-container" role="textbox" aria-readonly="true"
                        title="Select salary basis type">Select salary basis type</span><span
                        class="select2-selection__arrow" role="presentation"><b
                          role="presentation"></b></span></span></span><span class="dropdown-wrapper"
                    aria-hidden="true"></span></span>
              </div>
            </div>
            <div class="col-sm-4">
              <div class="input-block mb-3">
                <label class="col-form-label">Salary amount <small class="text-muted">per month</small></label>
                <div class="input-group">
                  <span class="input-group-text">$</span>
                  <input type="text" class="form-control" placeholder="Type your salary amount" value="0.00">
                </div>
              </div>
            </div>
            <div class="col-sm-4">
              <div class="input-block mb-3">
                <label class="col-form-label">Payment type</label>
                <select class="select select2-hidden-accessible" data-select2-id="select2-data-4-21a2" tabindex="-1"
                  aria-hidden="true">
                  <option data-select2-id="select2-data-6-vwx4">Select payment type</option>
                  <option>Bank transfer</option>
                  <option>Check</option>
                  <option>Cash</option>
                </select><span class="select2 select2-container select2-container--default" dir="ltr"
                  data-select2-id="select2-data-5-zvg1" style="width: 100%;"><span class="selection"><span
                      class="select2-selection select2-selection--single" role="combobox" aria-haspopup="true"
                      aria-expanded="false" tabindex="0" aria-disabled="false" aria-labelledby="select2-jefd-container"
                      aria-controls="select2-jefd-container"><span class="select2-selection__rendered"
                        id="select2-jefd-container" role="textbox" aria-readonly="true"
                        title="Select payment type">Select payment type</span><span class="select2-selection__arrow"
                        role="presentation"><b role="presentation"></b></span></span></span><span
                    class="dropdown-wrapper" aria-hidden="true"></span></span>
              </div>
            </div>
          </div>
          <hr>
          <h3 class="card-title"> PF Information</h3>
          <div class="row">
            <div class="col-sm-4">
              <div class="input-block mb-3">
                <label class="col-form-label">PF contribution</label>
                <select class="select select2-hidden-accessible" data-select2-id="select2-data-7-016t" tabindex="-1"
                  aria-hidden="true">
                  <option data-select2-id="select2-data-9-ir7j">Select PF contribution</option>
                  <option>Yes</option>
                  <option>No</option>
                </select><span class="select2 select2-container select2-container--default" dir="ltr"
                  data-select2-id="select2-data-8-zhzz" style="width: 100%;"><span class="selection"><span
                      class="select2-selection select2-selection--single" role="combobox" aria-haspopup="true"
                      aria-expanded="false" tabindex="0" aria-disabled="false" aria-labelledby="select2-2jkq-container"
                      aria-controls="select2-2jkq-container"><span class="select2-selection__rendered"
                        id="select2-2jkq-container" role="textbox" aria-readonly="true"
                        title="Select PF contribution">Select PF contribution</span><span
                        class="select2-selection__arrow" role="presentation"><b
                          role="presentation"></b></span></span></span><span class="dropdown-wrapper"
                    aria-hidden="true"></span></span>
              </div>
            </div>
            <div class="col-sm-4">
              <div class="input-block mb-3">
                <label class="col-form-label">PF No. <span class="text-danger">*</span></label>
                <select class="select select2-hidden-accessible" data-select2-id="select2-data-10-wqoq" tabindex="-1"
                  aria-hidden="true">
                  <option data-select2-id="select2-data-12-w34i">Select PF contribution</option>
                  <option>Yes</option>
                  <option>No</option>
                </select><span class="select2 select2-container select2-container--default" dir="ltr"
                  data-select2-id="select2-data-11-lzkd" style="width: 100%;"><span class="selection"><span
                      class="select2-selection select2-selection--single" role="combobox" aria-haspopup="true"
                      aria-expanded="false" tabindex="0" aria-disabled="false" aria-labelledby="select2-mz5k-container"
                      aria-controls="select2-mz5k-container"><span class="select2-selection__rendered"
                        id="select2-mz5k-container" role="textbox" aria-readonly="true"
                        title="Select PF contribution">Select PF contribution</span><span
                        class="select2-selection__arrow" role="presentation"><b
                          role="presentation"></b></span></span></span><span class="dropdown-wrapper"
                    aria-hidden="true"></span></span>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-sm-4">
              <div class="input-block mb-3">
                <label class="col-form-label">Employee PF rate</label>
                <select class="select select2-hidden-accessible" data-select2-id="select2-data-13-1dj7" tabindex="-1"
                  aria-hidden="true">
                  <option data-select2-id="select2-data-15-h9y5">Select PF contribution</option>
                  <option>Yes</option>
                  <option>No</option>
                </select><span class="select2 select2-container select2-container--default" dir="ltr"
                  data-select2-id="select2-data-14-l8lj" style="width: 100%;"><span class="selection"><span
                      class="select2-selection select2-selection--single" role="combobox" aria-haspopup="true"
                      aria-expanded="false" tabindex="0" aria-disabled="false" aria-labelledby="select2-1jc0-container"
                      aria-controls="select2-1jc0-container"><span class="select2-selection__rendered"
                        id="select2-1jc0-container" role="textbox" aria-readonly="true"
                        title="Select PF contribution">Select PF contribution</span><span
                        class="select2-selection__arrow" role="presentation"><b
                          role="presentation"></b></span></span></span><span class="dropdown-wrapper"
                    aria-hidden="true"></span></span>
              </div>
            </div>
            <div class="col-sm-4">
              <div class="input-block mb-3">
                <label class="col-form-label">Additional rate <span class="text-danger">*</span></label>
                <select class="select select2-hidden-accessible" data-select2-id="select2-data-16-vv7i" tabindex="-1"
                  aria-hidden="true">
                  <option data-select2-id="select2-data-18-2ass">Select additional rate</option>
                  <option>0%</option>
                  <option>1%</option>
                  <option>2%</option>
                  <option>3%</option>
                  <option>4%</option>
                  <option>5%</option>
                  <option>6%</option>
                  <option>7%</option>
                  <option>8%</option>
                  <option>9%</option>
                  <option>10%</option>
                </select><span class="select2 select2-container select2-container--default" dir="ltr"
                  data-select2-id="select2-data-17-f6n4" style="width: 100%;"><span class="selection"><span
                      class="select2-selection select2-selection--single" role="combobox" aria-haspopup="true"
                      aria-expanded="false" tabindex="0" aria-disabled="false" aria-labelledby="select2-qkf5-container"
                      aria-controls="select2-qkf5-container"><span class="select2-selection__rendered"
                        id="select2-qkf5-container" role="textbox" aria-readonly="true"
                        title="Select additional rate">Select additional rate</span><span
                        class="select2-selection__arrow" role="presentation"><b
                          role="presentation"></b></span></span></span><span class="dropdown-wrapper"
                    aria-hidden="true"></span></span>
              </div>
            </div>
            <div class="col-sm-4">
              <div class="input-block mb-3">
                <label class="col-form-label">Total rate</label>
                <input type="text" class="form-control" placeholder="N/A" value="11%">
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-sm-4">
              <div class="input-block mb-3">
                <label class="col-form-label">Employee PF rate</label>
                <select class="select select2-hidden-accessible" data-select2-id="select2-data-19-pyqx" tabindex="-1"
                  aria-hidden="true">
                  <option data-select2-id="select2-data-21-kxgc">Select PF contribution</option>
                  <option>Yes</option>
                  <option>No</option>
                </select><span class="select2 select2-container select2-container--default" dir="ltr"
                  data-select2-id="select2-data-20-31sr" style="width: 100%;"><span class="selection"><span
                      class="select2-selection select2-selection--single" role="combobox" aria-haspopup="true"
                      aria-expanded="false" tabindex="0" aria-disabled="false" aria-labelledby="select2-f9dj-container"
                      aria-controls="select2-f9dj-container"><span class="select2-selection__rendered"
                        id="select2-f9dj-container" role="textbox" aria-readonly="true"
                        title="Select PF contribution">Select PF contribution</span><span
                        class="select2-selection__arrow" role="presentation"><b
                          role="presentation"></b></span></span></span><span class="dropdown-wrapper"
                    aria-hidden="true"></span></span>
              </div>
            </div>
            <div class="col-sm-4">
              <div class="input-block mb-3">
                <label class="col-form-label">Additional rate <span class="text-danger">*</span></label>
                <select class="select select2-hidden-accessible" data-select2-id="select2-data-22-qpst" tabindex="-1"
                  aria-hidden="true">
                  <option data-select2-id="select2-data-24-vsy4">Select additional rate</option>
                  <option>0%</option>
                  <option>1%</option>
                  <option>2%</option>
                  <option>3%</option>
                  <option>4%</option>
                  <option>5%</option>
                  <option>6%</option>
                  <option>7%</option>
                  <option>8%</option>
                  <option>9%</option>
                  <option>10%</option>
                </select><span class="select2 select2-container select2-container--default" dir="ltr"
                  data-select2-id="select2-data-23-salt" style="width: 100%;"><span class="selection"><span
                      class="select2-selection select2-selection--single" role="combobox" aria-haspopup="true"
                      aria-expanded="false" tabindex="0" aria-disabled="false" aria-labelledby="select2-4ngl-container"
                      aria-controls="select2-4ngl-container"><span class="select2-selection__rendered"
                        id="select2-4ngl-container" role="textbox" aria-readonly="true"
                        title="Select additional rate">Select additional rate</span><span
                        class="select2-selection__arrow" role="presentation"><b
                          role="presentation"></b></span></span></span><span class="dropdown-wrapper"
                    aria-hidden="true"></span></span>
              </div>
            </div>
            <div class="col-sm-4">
              <div class="input-block mb-3">
                <label class="col-form-label">Total rate</label>
                <input type="text" class="form-control" placeholder="N/A" value="11%">
              </div>
            </div>
          </div>

          <hr>
          <h3 class="card-title"> ESI Information</h3>
          <div class="row">
            <div class="col-sm-4">
              <div class="input-block mb-3">
                <label class="col-form-label">ESI contribution</label>
                <select class="select select2-hidden-accessible" data-select2-id="select2-data-25-vb16" tabindex="-1"
                  aria-hidden="true">
                  <option data-select2-id="select2-data-27-ox28">Select ESI contribution</option>
                  <option>Yes</option>
                  <option>No</option>
                </select><span class="select2 select2-container select2-container--default" dir="ltr"
                  data-select2-id="select2-data-26-d9jv" style="width: 100%;"><span class="selection"><span
                      class="select2-selection select2-selection--single" role="combobox" aria-haspopup="true"
                      aria-expanded="false" tabindex="0" aria-disabled="false" aria-labelledby="select2-d7rq-container"
                      aria-controls="select2-d7rq-container"><span class="select2-selection__rendered"
                        id="select2-d7rq-container" role="textbox" aria-readonly="true"
                        title="Select ESI contribution">Select ESI contribution</span><span
                        class="select2-selection__arrow" role="presentation"><b
                          role="presentation"></b></span></span></span><span class="dropdown-wrapper"
                    aria-hidden="true"></span></span>
              </div>
            </div>
            <div class="col-sm-4">
              <div class="input-block mb-3">
                <label class="col-form-label">ESI No. <span class="text-danger">*</span></label>
                <select class="select select2-hidden-accessible" data-select2-id="select2-data-28-sf3j" tabindex="-1"
                  aria-hidden="true">
                  <option data-select2-id="select2-data-30-vh3b">Select ESI contribution</option>
                  <option>Yes</option>
                  <option>No</option>
                </select><span class="select2 select2-container select2-container--default" dir="ltr"
                  data-select2-id="select2-data-29-18p7" style="width: 100%;"><span class="selection"><span
                      class="select2-selection select2-selection--single" role="combobox" aria-haspopup="true"
                      aria-expanded="false" tabindex="0" aria-disabled="false" aria-labelledby="select2-ch92-container"
                      aria-controls="select2-ch92-container"><span class="select2-selection__rendered"
                        id="select2-ch92-container" role="textbox" aria-readonly="true"
                        title="Select ESI contribution">Select ESI contribution</span><span
                        class="select2-selection__arrow" role="presentation"><b
                          role="presentation"></b></span></span></span><span class="dropdown-wrapper"
                    aria-hidden="true"></span></span>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-sm-4">
              <div class="input-block mb-3">
                <label class="col-form-label">Employee ESI rate</label>
                <select class="select select2-hidden-accessible" data-select2-id="select2-data-31-of00" tabindex="-1"
                  aria-hidden="true">
                  <option data-select2-id="select2-data-33-ajww">Select ESI contribution</option>
                  <option>Yes</option>
                  <option>No</option>
                </select><span class="select2 select2-container select2-container--default" dir="ltr"
                  data-select2-id="select2-data-32-zj70" style="width: 100%;"><span class="selection"><span
                      class="select2-selection select2-selection--single" role="combobox" aria-haspopup="true"
                      aria-expanded="false" tabindex="0" aria-disabled="false" aria-labelledby="select2-hwc0-container"
                      aria-controls="select2-hwc0-container"><span class="select2-selection__rendered"
                        id="select2-hwc0-container" role="textbox" aria-readonly="true"
                        title="Select ESI contribution">Select ESI contribution</span><span
                        class="select2-selection__arrow" role="presentation"><b
                          role="presentation"></b></span></span></span><span class="dropdown-wrapper"
                    aria-hidden="true"></span></span>
              </div>
            </div>
            <div class="col-sm-4">
              <div class="input-block mb-3">
                <label class="col-form-label">Additional rate <span class="text-danger">*</span></label>
                <select class="select select2-hidden-accessible" data-select2-id="select2-data-34-1qmn" tabindex="-1"
                  aria-hidden="true">
                  <option data-select2-id="select2-data-36-xgiu">Select additional rate</option>
                  <option>0%</option>
                  <option>1%</option>
                  <option>2%</option>
                  <option>3%</option>
                  <option>4%</option>
                  <option>5%</option>
                  <option>6%</option>
                  <option>7%</option>
                  <option>8%</option>
                  <option>9%</option>
                  <option>10%</option>
                </select><span class="select2 select2-container select2-container--default" dir="ltr"
                  data-select2-id="select2-data-35-pf3t" style="width: 100%;"><span class="selection"><span
                      class="select2-selection select2-selection--single" role="combobox" aria-haspopup="true"
                      aria-expanded="false" tabindex="0" aria-disabled="false" aria-labelledby="select2-pfp7-container"
                      aria-controls="select2-pfp7-container"><span class="select2-selection__rendered"
                        id="select2-pfp7-container" role="textbox" aria-readonly="true"
                        title="Select additional rate">Select additional rate</span><span
                        class="select2-selection__arrow" role="presentation"><b
                          role="presentation"></b></span></span></span><span class="dropdown-wrapper"
                    aria-hidden="true"></span></span>
              </div>
            </div>
            <div class="col-sm-4">
              <div class="input-block mb-3">
                <label class="col-form-label">Total rate</label>
                <input type="text" class="form-control" placeholder="N/A" value="11%">
              </div>
            </div>
          </div>

          <div class="submit-section">
            <button class="btn btn-primary submit-btn" type="submit">Save</button>
          </div>
        </form>
      </div>
    </div>
  </div>
  <!-- /Bank Statutory Tab -->

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
                    aria-label="Name: activate to sort column ascending" style="width: 0px;">Name</th>
                  <th class="sorting" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1"
                    aria-label="Asset ID: activate to sort column ascending" style="width: 0px;">Asset ID</th>
                  <th class="sorting" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1"
                    aria-label="Assigned Date: activate to sort column ascending" style="width: 0px;">Assigned Date
                  </th>
                  <th class="sorting" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1"
                    aria-label="Assignee: activate to sort column ascending" style="width: 0px;">Assignee</th>
                  <th class="sorting" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1"
                    aria-label="Action: activate to sort column ascending" style="width: 0px;">Action</th>
                </tr>
              </thead>
              <tbody>






                <tr class="odd">
                  <td class="sorting_1">1</td>
                  <td>
                    <a href="assets-details.html" class="table-imgname">
                      <img src="assets/img/laptop.png" class="me-2" alt="Laptop Image">
                      <span>Laptop</span>
                    </a>
                  </td>
                  <td>AST - 001</td>
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
                      <span>Laptop</span>
                    </a>
                  </td>
                  <td>AST - 002</td>
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
                      <span>Dell Keyboard</span>
                    </a>
                  </td>
                  <td>AST - 003</td>
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
                      <span>Logitech Mouse</span>
                    </a>
                  </td>
                  <td>AST - 0024</td>
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
                      <span>Laptop</span>
                    </a>
                  </td>
                  <td>AST - 005</td>
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
                      <span>Laptop</span>
                    </a>
                  </td>
                  <td>AST - 006</td>
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
              </tbody>
            </table>
          </div>
        </div>
        <div class="row">
          <div class="col-sm-12 col-md-5">
            <div class="dataTables_info" id="DataTables_Table_0_info" role="status" aria-live="polite">Showing 1 to
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
                <li class="paginate_button page-item next disabled" id="DataTables_Table_0_next"><a href="#"
                    aria-controls="DataTables_Table_0" data-dt-idx="2" tabindex="0" class="page-link"> <i
                      class=" fa fa-angle-double-right"></i></a></li>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- /Assets -->

</div>
</div>
<!-- /Page Content -->

<!-- Profile Modal -->
<div id="profile_info" class="modal custom-modal fade" role="dialog">
  <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Profile Information</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">×</span>
        </button>
      </div>
      <div class="modal-body">
        <form>
          <div class="row">
            <div class="col-md-12">
              <div class="profile-img-wrap edit-img">
                <img class="inline-block" src="assets/img/profiles/avatar-02.jpg" alt="User Image">
                <div class="fileupload btn">
                  <span class="btn-text">edit</span>
                  <input class="upload" type="file">
                </div>
              </div>
              <div class="row">
                <div class="col-md-6">
                  <div class="input-block mb-3">
                    <label class="col-form-label">First Name</label>
                    <input type="text" class="form-control" value="John">
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="input-block mb-3">
                    <label class="col-form-label">Last Name</label>
                    <input type="text" class="form-control" value="Doe">
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="input-block mb-3">
                    <label class="col-form-label">Birth Date</label>
                    <div class="cal-icon">
                      <input class="form-control datetimepicker" type="text" value="05/06/1985">
                    </div>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="input-block mb-3">
                    <label class="col-form-label">Gender</label>
                    <select class="select form-control select2-hidden-accessible" data-select2-id="select2-data-37-cgg6"
                      tabindex="-1" aria-hidden="true">
                      <option value="male selected" data-select2-id="select2-data-39-4n12">Male</option>
                      <option value="female">Female</option>
                    </select><span class="select2 select2-container select2-container--default" dir="ltr"
                      data-select2-id="select2-data-38-0w10" style="width: 100%;"><span class="selection"><span
                          class="select2-selection select2-selection--single" role="combobox" aria-haspopup="true"
                          aria-expanded="false" tabindex="0" aria-disabled="false"
                          aria-labelledby="select2-nwjf-container" aria-controls="select2-nwjf-container"><span
                            class="select2-selection__rendered" id="select2-nwjf-container" role="textbox"
                            aria-readonly="true" title="Male">Male</span><span class="select2-selection__arrow"
                            role="presentation"><b role="presentation"></b></span></span></span><span
                        class="dropdown-wrapper" aria-hidden="true"></span></span>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-12">
              <div class="input-block mb-3">
                <label class="col-form-label">Address</label>
                <input type="text" class="form-control" value="4487 Snowbird Lane">
              </div>
            </div>
            <div class="col-md-6">
              <div class="input-block mb-3">
                <label class="col-form-label">State</label>
                <input type="text" class="form-control" value="New York">
              </div>
            </div>
            <div class="col-md-6">
              <div class="input-block mb-3">
                <label class="col-form-label">Country</label>
                <input type="text" class="form-control" value="United States">
              </div>
            </div>
            <div class="col-md-6">
              <div class="input-block mb-3">
                <label class="col-form-label">Pin Code</label>
                <input type="text" class="form-control" value="10523">
              </div>
            </div>
            <div class="col-md-6">
              <div class="input-block mb-3">
                <label class="col-form-label">Phone Number</label>
                <input type="text" class="form-control" value="631-889-3206">
              </div>
            </div>
            <div class="col-md-6">
              <div class="input-block mb-3">
                <label class="col-form-label">Department <span class="text-danger">*</span></label>
                <select class="select select2-hidden-accessible" data-select2-id="select2-data-40-3dt6" tabindex="-1"
                  aria-hidden="true">
                  <option data-select2-id="select2-data-42-5t7c">Select Department</option>
                  <option>Web Development</option>
                  <option>IT Management</option>
                  <option>Marketing</option>
                </select><span class="select2 select2-container select2-container--default" dir="ltr"
                  data-select2-id="select2-data-41-t6xd" style="width: 100%;"><span class="selection"><span
                      class="select2-selection select2-selection--single" role="combobox" aria-haspopup="true"
                      aria-expanded="false" tabindex="0" aria-disabled="false" aria-labelledby="select2-dg0g-container"
                      aria-controls="select2-dg0g-container"><span class="select2-selection__rendered"
                        id="select2-dg0g-container" role="textbox" aria-readonly="true" title="Select Department">Select
                        Department</span><span class="select2-selection__arrow" role="presentation"><b
                          role="presentation"></b></span></span></span><span class="dropdown-wrapper"
                    aria-hidden="true"></span></span>
              </div>
            </div>
            <div class="col-md-6">
              <div class="input-block mb-3">
                <label class="col-form-label">Designation <span class="text-danger">*</span></label>
                <select class="select select2-hidden-accessible" data-select2-id="select2-data-43-iaw1" tabindex="-1"
                  aria-hidden="true">
                  <option data-select2-id="select2-data-45-yd7a">Select Designation</option>
                  <option>Web Designer</option>
                  <option>Web Developer</option>
                  <option>Android Developer</option>
                </select><span class="select2 select2-container select2-container--default" dir="ltr"
                  data-select2-id="select2-data-44-x0oh" style="width: 100%;"><span class="selection"><span
                      class="select2-selection select2-selection--single" role="combobox" aria-haspopup="true"
                      aria-expanded="false" tabindex="0" aria-disabled="false" aria-labelledby="select2-ip79-container"
                      aria-controls="select2-ip79-container"><span class="select2-selection__rendered"
                        id="select2-ip79-container" role="textbox" aria-readonly="true"
                        title="Select Designation">Select Designation</span><span class="select2-selection__arrow"
                        role="presentation"><b role="presentation"></b></span></span></span><span
                    class="dropdown-wrapper" aria-hidden="true"></span></span>
              </div>
            </div>
            <div class="col-md-6">
              <div class="input-block mb-3">
                <label class="col-form-label">Reports To <span class="text-danger">*</span></label>
                <select class="select select2-hidden-accessible" data-select2-id="select2-data-46-hxu8" tabindex="-1"
                  aria-hidden="true">
                  <option data-select2-id="select2-data-48-emw6">-</option>
                  <option>Wilmer Deluna</option>
                  <option>Lesley Grauer</option>
                  <option>Jeffery Lalor</option>
                </select><span class="select2 select2-container select2-container--default" dir="ltr"
                  data-select2-id="select2-data-47-crb3" style="width: 100%;"><span class="selection"><span
                      class="select2-selection select2-selection--single" role="combobox" aria-haspopup="true"
                      aria-expanded="false" tabindex="0" aria-disabled="false" aria-labelledby="select2-zu37-container"
                      aria-controls="select2-zu37-container"><span class="select2-selection__rendered"
                        id="select2-zu37-container" role="textbox" aria-readonly="true" title="-">-</span><span
                        class="select2-selection__arrow" role="presentation"><b
                          role="presentation"></b></span></span></span><span class="dropdown-wrapper"
                    aria-hidden="true"></span></span>
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
<!-- /Profile Modal -->

<!-- Personal Info Modal -->
<div id="personal_info_modal" class="modal custom-modal fade" role="dialog">
  <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Personal Information</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">×</span>
        </button>
      </div>
      <div class="modal-body">
        <form>
          <div class="row">
            <div class="col-md-6">
              <div class="input-block mb-3">
                <label class="col-form-label">Passport No</label>
                <input type="text" class="form-control">
              </div>
            </div>
            <div class="col-md-6">
              <div class="input-block mb-3">
                <label class="col-form-label">Passport Expiry Date</label>
                <div class="cal-icon">
                  <input class="form-control datetimepicker" type="text">
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="input-block mb-3">
                <label class="col-form-label">Tel</label>
                <input class="form-control" type="text">
              </div>
            </div>
            <div class="col-md-6">
              <div class="input-block mb-3">
                <label class="col-form-label">Nationality <span class="text-danger">*</span></label>
                <input class="form-control" type="text">
              </div>
            </div>
            <div class="col-md-6">
              <div class="input-block mb-3">
                <label class="col-form-label">Religion</label>
                <div class="cal-icon">
                  <input class="form-control" type="text">
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="input-block mb-3">
                <label class="col-form-label">Marital status <span class="text-danger">*</span></label>
                <select class="select form-control select2-hidden-accessible" data-select2-id="select2-data-49-annx"
                  tabindex="-1" aria-hidden="true">
                  <option data-select2-id="select2-data-51-mm4g">-</option>
                  <option>Single</option>
                  <option>Married</option>
                </select><span class="select2 select2-container select2-container--default" dir="ltr"
                  data-select2-id="select2-data-50-n0i3" style="width: 100%;"><span class="selection"><span
                      class="select2-selection select2-selection--single" role="combobox" aria-haspopup="true"
                      aria-expanded="false" tabindex="0" aria-disabled="false" aria-labelledby="select2-f773-container"
                      aria-controls="select2-f773-container"><span class="select2-selection__rendered"
                        id="select2-f773-container" role="textbox" aria-readonly="true" title="-">-</span><span
                        class="select2-selection__arrow" role="presentation"><b
                          role="presentation"></b></span></span></span><span class="dropdown-wrapper"
                    aria-hidden="true"></span></span>
              </div>
            </div>
            <div class="col-md-6">
              <div class="input-block mb-3">
                <label class="col-form-label">Employment of spouse</label>
                <input class="form-control" type="text">
              </div>
            </div>
            <div class="col-md-6">
              <div class="input-block mb-3">
                <label class="col-form-label">No. of children </label>
                <input class="form-control" type="text">
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
<!-- /Personal Info Modal -->

<!-- Family Info Modal -->
<div id="family_info_modal" class="modal custom-modal fade" role="dialog">
  <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"> Family Informations</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
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
                  <a href="javascript:void(0);"><i class="fa-solid fa-plus-circle"></i> Add More</a>
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
        <h5 class="modal-title">Personal Information</h5>
        <buttocieype="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">×</span>
          </buttocieype=>
      </div>
      <div class="modal-body">
        <form>
          <div class="card">
            <div class="card-body">
              <h3 class="card-title">Primary Contact</h3>
              <div class="row">
                <div class="col-md-6">
                  <div class="input-block mb-3">
                    <label class="col-form-label">Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control">
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
                    <label class="col-form-label">Phone <span class="text-danger">*</span></label>
                    <input class="form-control" type="text">
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="input-block mb-3">
                    <label class="col-form-label">Phone 2</label>
                    <input class="form-control" type="text">
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="card">
            <div class="card-body">
              <h3 class="card-title">Primary Contact</h3>
              <div class="row">
                <div class="col-md-6">
                  <div class="input-block mb-3">
                    <label class="col-form-label">Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control">
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
                    <label class="col-form-label">Phone <span class="text-danger">*</span></label>
                    <input class="form-control" type="text">
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="input-block mb-3">
                    <label class="col-form-label">Phone 2</label>
                    <input class="form-control" type="text">
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

<!-- Education Modal -->
<div id="education_info" class="modal custom-modal fade" role="dialog">
  <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"> Education Informations</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">×</span>
        </button>
      </div>
      <div class="modal-body">
        <form>
          <div class="form-scroll">
            <div class="card">
              <div class="card-body">
                <h3 class="card-title">Education Informations <a href="javascript:void(0);" class="delete-icon"><i
                      class="fa-regular fa-trash-can"></i></a></h3>
                <div class="row">
                  <div class="col-md-6">
                    <div class="input-block mb-3 form-focus focused">
                      <input type="text" value="Oxford University" class="form-control floating">
                      <label class="focus-label">Institution</label>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="input-block mb-3 form-focus focused">
                      <input type="text" value="Computer Science" class="form-control floating">
                      <label class="focus-label">Subject</label>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="input-block mb-3 form-focus focused">
                      <div class="cal-icon">
                        <input type="text" value="01/06/2002" class="form-control floating datetimepicker">
                      </div>
                      <label class="focus-label">Starting Date</label>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="input-block mb-3 form-focus focused">
                      <div class="cal-icon">
                        <input type="text" value="31/05/2006" class="form-control floating datetimepicker">
                      </div>
                      <label class="focus-label">Complete Date</label>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="input-block mb-3 form-focus focused">
                      <input type="text" value="BE Computer Science" class="form-control floating">
                      <label class="focus-label">Degree</label>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="input-block mb-3 form-focus focused">
                      <input type="text" value="Grade A" class="form-control floating">
                      <label class="focus-label">Grade</label>
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
                    <div class="input-block mb-3 form-focus focused">
                      <input type="text" value="Oxford University" class="form-control floating">
                      <label class="focus-label">Institution</label>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="input-block mb-3 form-focus focused">
                      <input type="text" value="Computer Science" class="form-control floating">
                      <label class="focus-label">Subject</label>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="input-block mb-3 form-focus focused">
                      <div class="cal-icon">
                        <input type="text" value="01/06/2002" class="form-control floating datetimepicker">
                      </div>
                      <label class="focus-label">Starting Date</label>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="input-block mb-3 form-focus focused">
                      <div class="cal-icon">
                        <input type="text" value="31/05/2006" class="form-control floating datetimepicker">
                      </div>
                      <label class="focus-label">Complete Date</label>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="input-block mb-3 form-focus focused">
                      <input type="text" value="BE Computer Science" class="form-control floating">
                      <label class="focus-label">Degree</label>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="input-block mb-3 form-focus focused">
                      <input type="text" value="Grade A" class="form-control floating">
                      <label class="focus-label">Grade</label>
                    </div>
                  </div>
                </div>
                <div class="add-more">
                  <a href="javascript:void(0);"><i class="fa-solid fa-plus-circle"></i> Add More</a>
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
<!-- /Education Modal -->

<!-- Experience Modal -->
<div id="experience_info" class="modal custom-modal fade" role="dialog">
  <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Experience Informations</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">×</span>
        </button>
      </div>
      <div class="modal-body">
        <form>
          <div class="form-scroll">
            <div class="card">
              <div class="card-body">
                <h3 class="card-title">Experience Informations <a href="javascript:void(0);" class="delete-icon"><i
                      class="fa-regular fa-trash-can"></i></a></h3>
                <div class="row">
                  <div class="col-md-6">
                    <div class="input-block mb-3 form-focus focused">
                      <input type="text" class="form-control floating" value="Digital Devlopment Inc">
                      <label class="focus-label">Company Name</label>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="input-block mb-3 form-focus focused">
                      <input type="text" class="form-control floating" value="United States">
                      <label class="focus-label">Location</label>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="input-block mb-3 form-focus focused">
                      <input type="text" class="form-control floating" value="Web Developer">
                      <label class="focus-label">Job Position</label>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="input-block mb-3 form-focus focused">
                      <div class="cal-icon">
                        <input type="text" class="form-control floating datetimepicker" value="01/07/2007">
                      </div>
                      <label class="focus-label">Period From</label>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="input-block mb-3 form-focus focused">
                      <div class="cal-icon">
                        <input type="text" class="form-control floating datetimepicker" value="08/06/2018">
                      </div>
                      <label class="focus-label">Period To</label>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="card">
              <div class="card-body">
                <h3 class="card-title">Experience Informations <a href="javascript:void(0);" class="delete-icon"><i
                      class="fa-regular fa-trash-can"></i></a></h3>
                <div class="row">
                  <div class="col-md-6">
                    <div class="input-block mb-3 form-focus focused">
                      <input type="text" class="form-control floating" value="Digital Devlopment Inc">
                      <label class="focus-label">Company Name</label>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="input-block mb-3 form-focus focused">
                      <input type="text" class="form-control floating" value="United States">
                      <label class="focus-label">Location</label>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="input-block mb-3 form-focus focused">
                      <input type="text" class="form-control floating" value="Web Developer">
                      <label class="focus-label">Job Position</label>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="input-block mb-3 form-focus focused">
                      <div class="cal-icon">
                        <input type="text" class="form-control floating datetimepicker" value="01/07/2007">
                      </div>
                      <label class="focus-label">Period From</label>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="input-block mb-3 form-focus focused">
                      <div class="cal-icon">
                        <input type="text" class="form-control floating datetimepicker" value="08/06/2018">
                      </div>
                      <label class="focus-label">Period To</label>
                    </div>
                  </div>
                </div>
                <div class="add-more">
                  <a href="javascript:void(0);"><i class="fa-solid fa-plus-circle"></i> Add More</a>
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
  @endforeach

  @endsection