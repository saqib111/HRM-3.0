<?php $page = 'employees-list'; ?>
@extends('layout.mainlayout')
@section('content')
@component('components.breadcrumb')
@slot('title')
Employee
@endslot
@slot('li_1')
Dashboard
@endslot
@slot('li_2')
Employee
@endslot
@endcomponent


<div class="row">
    <div class="col-md-12">
        <div class="table-responsive">
            <table class="table table-striped custom-table datatable">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Employee ID</th>
                        <th>Email</th>
                        <th>Mobile</th>
                        <th class="text-nowrap">Join Date</th>
                        <th>Role</th>
                        <th class="text-end no-sort">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <h2 class="table-avatar">
                                <a href="{{ url('profile') }}" class="avatar"><img
                                        src="{{ URL::asset('assets/img/profiles/avatar-02.jpg')}}" alt="User Image"></a>
                                <a href="{{ url('profile') }}">John Doe <span>Web Designer</span></a>
                            </h2>
                        </td>
                        <td>FT-0001</td>
                        <td>johndoe@example.com</td>
                        <td>9876543210</td>
                        <td>1 Jan 2013</td>
                        <td>
                            <div class="dropdown">
                                <a href="" class="btn btn-white btn-sm btn-rounded dropdown-toggle"
                                    data-bs-toggle="dropdown" aria-expanded="false">Web Developer </a>
                                <div class="dropdown-menu">
                                    <a class="dropdown-item" href="#">Software Engineer</a>
                                    <a class="dropdown-item" href="#">Software Tester</a>
                                    <a class="dropdown-item" href="#">Frontend Developer</a>
                                    <a class="dropdown-item" href="#">UI/UX Developer</a>
                                </div>
                            </div>
                        </td>
                        <td class="text-end">
                            <div class="dropdown dropdown-action">
                                <a href="#" class="action-icon dropdown-toggle" data-bs-toggle="dropdown"
                                    aria-expanded="false"><i class="material-icons">more_vert</i></a>
                                <div class="dropdown-menu dropdown-menu-right">
                                    <a class="dropdown-item" href="#" data-bs-toggle="modal"
                                        data-bs-target="#edit_employee"><i class="fa-solid fa-pencil m-r-5"></i>
                                        Edit</a>
                                    <a class="dropdown-item" href="#" data-bs-toggle="modal"
                                        data-bs-target="#delete_employee"><i class="fa-regular fa-trash-can m-r-5"></i>
                                        Delete</a>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <h2 class="table-avatar">
                                <a href="{{ url('profile') }}" class="avatar"><img
                                        src="{{ URL::asset('assets/img/profiles/avatar-09.jpg')}}" alt="User Image"></a>
                                <a href="{{ url('profile') }}">Richard Miles <span>Web Developer</span></a>
                            </h2>
                        </td>
                        <td>FT-0002</td>
                        <td>richardmiles@example.com</td>
                        <td>9876543210</td>
                        <td>18 Mar 2014</td>
                        <td>
                            <div class="dropdown">
                                <a href="" class="btn btn-white btn-sm btn-rounded dropdown-toggle"
                                    data-bs-toggle="dropdown" aria-expanded="false">Web Developer </a>
                                <div class="dropdown-menu">
                                    <a class="dropdown-item" href="#">Software Engineer</a>
                                    <a class="dropdown-item" href="#">Software Tester</a>
                                    <a class="dropdown-item" href="#">Frontend Developer</a>
                                    <a class="dropdown-item" href="#">UI/UX Developer</a>
                                </div>
                            </div>
                        </td>
                        <td class="text-end">
                            <div class="dropdown dropdown-action">
                                <a href="#" class="action-icon dropdown-toggle" data-bs-toggle="dropdown"
                                    aria-expanded="false"><i class="material-icons">more_vert</i></a>
                                <div class="dropdown-menu dropdown-menu-right">
                                    <a class="dropdown-item" href="#" data-bs-toggle="modal"
                                        data-bs-target="#edit_employee"><i class="fa-solid fa-pencil m-r-5"></i>
                                        Edit</a>
                                    <a class="dropdown-item" href="#" data-bs-toggle="modal"
                                        data-bs-target="#delete_employee"><i class="fa-regular fa-trash-can m-r-5"></i>
                                        Delete</a>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <h2 class="table-avatar">
                                <a href="{{ url('profile') }}" class="avatar"><img
                                        src="{{ URL::asset('assets/img/profiles/avatar-10.jpg')}}" alt="User Image"></a>
                                <a href="{{ url('profile') }}">John Smith <span>Android Developer</span></a>
                            </h2>
                        </td>
                        <td>FT-0003</td>
                        <td>johnsmith@example.com</td>
                        <td>9876543210</td>
                        <td>1 Apr 2014</td>
                        <td>
                            <div class="dropdown">
                                <a href="" class="btn btn-white btn-sm btn-rounded dropdown-toggle"
                                    data-bs-toggle="dropdown" aria-expanded="false">Web Developer </a>
                                <div class="dropdown-menu">
                                    <a class="dropdown-item" href="#">Software Engineer</a>
                                    <a class="dropdown-item" href="#">Software Tester</a>
                                    <a class="dropdown-item" href="#">Frontend Developer</a>
                                    <a class="dropdown-item" href="#">UI/UX Developer</a>
                                </div>
                            </div>
                        </td>
                        <td class="text-end">
                            <div class="dropdown dropdown-action">
                                <a href="#" class="action-icon dropdown-toggle" data-bs-toggle="dropdown"
                                    aria-expanded="false"><i class="material-icons">more_vert</i></a>
                                <div class="dropdown-menu dropdown-menu-right">
                                    <a class="dropdown-item" href="#" data-bs-toggle="modal"
                                        data-bs-target="#edit_employee"><i class="fa-solid fa-pencil m-r-5"></i>
                                        Edit</a>
                                    <a class="dropdown-item" href="#" data-bs-toggle="modal"
                                        data-bs-target="#delete_employee"><i class="fa-regular fa-trash-can m-r-5"></i>
                                        Delete</a>
                                </div>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>


@component('components.model-popup')
@endcomponent
@endsection