@extends('layout.mainlayout')
@section('content')
<!-- ---- Page Header ----- -->

<div class="page-header">
  <div class="row align-items-center">
    <div class="col-md-4">
      <h3 class="page-title">Roles &amp; Permissions</h3>
      <ul class="breadcrumb">
        <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
        <li class="breadcrumb-item active">Roles &amp; Permissions</li>
      </ul>
    </div>

  </div>
</div>

<!-- -------Page header End ------ -->


<div class="row justify-content-around">


  <div class="col-sm-8 col-md-8 col-lg-8 col-xl-9 column-container">


    <select name="" id="" class="form-control mb-3">
      <option value="">User 1</option>
      <option value="">User 2</option>
      <option value="">User 3</option>
      <option value="">User 4</option>
      <option value="">User 5</option>
    </select>


    <h6 class="card-title m-b-20">Manage Permissions</h6>
    <div class="main-container">

      <div class="card card-role py-4 px-4">
        <h3 class="mb-3">Manage Employee</h3>
        <div class="roles-container">



          <div class="check-role">
            <div class="checkbox-role">
              <label class="custom_check">
                <input type="checkbox" checked="">
                <span class="checkmark"></span>
              </label>
            </div>
            <div class="permissions-one">
              <h5>Create User</h5>
            </div>
          </div>

          <div class="check-role">
            <div class="checkbox-role">
              <label class="custom_check">
                <input type="checkbox" checked="">
                <span class="checkmark"></span>
              </label>
            </div>
            <div class="permissions-one">
              <h5>Show Users</h5>
            </div>
          </div>

          <div class="check-role">
            <div class="checkbox-role">
              <label class="custom_check">
                <input type="checkbox" checked="">
                <span class="checkmark"></span>
              </label>
            </div>
            <div class="permissions-one">
              <h5>Update User Info</h5>
            </div>
          </div>

          <div class="check-role">
            <div class="checkbox-role">
              <label class="custom_check">
                <input type="checkbox" checked="">
                <span class="checkmark"></span>
              </label>
            </div>
            <div class="permissions-one">
              <h5>Delete User</h5>
            </div>
          </div>

          <div class="check-role">
            <div class="checkbox-role">
              <label class="custom_check">
                <input type="checkbox" checked="">
                <span class="checkmark"></span>
              </label>
            </div>
            <div class="permissions-one">
              <h5>Change Password</h5>
            </div>
          </div>

          <div class="check-role">
            <div class="checkbox-role">
              <label class="custom_check">
                <input type="checkbox" checked="">
                <span class="checkmark"></span>
              </label>
            </div>
            <div class="permissions-one">
              <h5>Update Active/Disable Status</h5>
            </div>
          </div>


        </div>
      </div>

      <div class="card card-role py-4 px-4">
        <h3 class="mb-3">Manage Team</h3>
        <div class="roles-container">



          <div class="check-role">
            <div class="checkbox-role">
              <label class="custom_check">
                <input type="checkbox" checked="">
                <span class="checkmark"></span>
              </label>
            </div>
            <div class="permissions-one">
              <h5>Create Team</h5>
            </div>
          </div>

          <div class="check-role">
            <div class="checkbox-role">
              <label class="custom_check">
                <input type="checkbox" checked="">
                <span class="checkmark"></span>
              </label>
            </div>
            <div class="permissions-one">
              <h5>Show Teams</h5>
            </div>
          </div>

          <div class="check-role">
            <div class="checkbox-role">
              <label class="custom_check">
                <input type="checkbox" checked="">
                <span class="checkmark"></span>
              </label>
            </div>
            <div class="permissions-one">
              <h5>Update Team</h5>
            </div>
          </div>

          <div class="check-role">
            <div class="checkbox-role">
              <label class="custom_check">
                <input type="checkbox" checked="">
                <span class="checkmark"></span>
              </label>
            </div>
            <div class="permissions-one">
              <h5>Delete Team</h5>
            </div>
          </div>

        </div>
      </div>


      <div class="card card-role py-4 px-4">
        <h3 class="mb-3">Manage Shift</h3>
        <div class="roles-container">



          <div class="check-role">
            <div class="checkbox-role">
              <label class="custom_check">
                <input type="checkbox" checked="">
                <span class="checkmark"></span>
              </label>
            </div>
            <div class="permissions-one">
              <h5>Create Schedule</h5>
            </div>
          </div>

          <div class="check-role">
            <div class="checkbox-role">
              <label class="custom_check">
                <input type="checkbox" checked="">
                <span class="checkmark"></span>
              </label>
            </div>
            <div class="permissions-one">
              <h5>Update Schedule Timing</h5>
            </div>
          </div>

          <div class="check-role">
            <div class="checkbox-role">
              <label class="custom_check">
                <input type="checkbox" checked="">
                <span class="checkmark"></span>
              </label>
            </div>
            <div class="permissions-one">
              <h5>Delete Schedule</h5>
            </div>
          </div>

        </div>
      </div>
    </div>
  </div>
</div>
@endsection