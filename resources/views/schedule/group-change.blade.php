@extends('layout.mainlayout')
@section('css')
<!-- Litepicker CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css"/>
<link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet" />
<link href="https://cdn.datatables.net/2.1.8/css/dataTables.bootstrap5.css" rel="stylesheet" />
<style>
 .dt-column-order{
    display:none!important;
 }
     
</style>
@endsection
@section('content')
<div class="page-header">
  <div class="row align-items-center">
    <div class="col-md-4">
      <h3 class="page-title">Change Group</h3>
      <ul class="breadcrumb">
        <li class="breadcrumb-item"><a href="admin-dashboard.html">Dashboard</a></li>
        <li class="breadcrumb-item active">Change Group</li>
      </ul>
    </div>
   
  </div>
</div>


<div id="notification" aria-live="polite" aria-atomic="true"></div>
<div class="row">
    <div class="col-md-12">
        <div class="table-responsive">
<!--                      -->
<table id="changeGroup" class="table table-striped" style="width:100%">
        <thead>
           
            <tr>
                <th>#</th>
                <th>Employee Name</th>
                <th>Group Name</th>
                <th>Change</th>
                <th>Submit</th>
              
            </tr>
           
        </thead>
        <tbody>
            @if($groups)
            @foreach($groups  as $group)

            @php 
          
            $user_id=explode(',',$group->user_id);
            for($i=0;$i<sizeof($user_id);$i++){
          
              $val="";
                   if(session('oldId')){
                  if(session('oldId')==$user_id[$i])
                  {
                      $val="is-invalid";
                  }
                }
           
              @endphp
            <tr>

                <td class="text-center">{{$user_id[$i]}}</td>
                @if($user_id[$i])
                <td>{{getName($user_id[$i])}}</td>
                @else
                <td><span class="text-danger font-weight-bold">No User</span></td>
                @endif
                <td>{{$group->name}}</td>
                <form action="{{route('changegroup.data')}}" method="post" enctype="multipart/form-data">
                    @csrf
                <td class="text-center">

                    <input type="hidden" name="old_group_id" value="{{$group->id}}">
                   
                    <input type="hidden" name="employee_id" value="{{$user_id[$i]}}">
                   
                  <select class="form-control {{$val}}" name="group_id" id="change">
                    <option disabled selected>Change Group</option>
                    @foreach($groups as $gr)
                   
                    @if($group->id != $gr->id)
                    <option value="{{$gr->id}}">{{$gr->name}}</option>
                    @endif
                    @endforeach
                  </select> 
                  @if(session('oldId'))
                  @if(session('oldId')==$user_id[$i])
                  <div id="message" class="mt-1">
                      <h6 class="text-danger">{{Session('message')}}</h4>
                  </div>
                  @endif
                  @endif
                </td>
                <td>
                    <button class="btn btn-primary " type="submit">Change</button>
                </td>
            </form>
            </tr>
            @php } @endphp
            @endforeach
            @endif 
            
           
        </tbody>
       
    </table>

<!--   -->
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/2.1.8/js/dataTables.js"></script>
<script src="https://cdn.datatables.net/2.1.8/js/dataTables.bootstrap5.js"></script>
<script>

$(document).ready(function () {
    new DataTable('#changeGroup');
   
setTimeout(function(){
$('#message').fadeOut('fast');

},3000);
});
</script>
@endsection


