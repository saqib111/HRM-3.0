@extends('layout.mainlayout')
<div class="row staff-grid-row">
        <div class="col-md-4 col-sm-6 col-12 col-lg-4 col-xl-3">
            <div class="profile-widget">
                <div class="profile-img">
                    <a href="{{ url('profile') }}" class="avatar"><img
                            src="{{ URL::asset('assets/img/profiles/avatar-02.jpg') }}" alt=""></a>
                </div>
                <div class="dropdown profile-action">
                    <a href="#" class="action-icon dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false"><i
                            class="material-icons">more_vert</i></a>
                    <div class="dropdown-menu dropdown-menu-right">
                        <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#edit_employee"><i
                                class="fa fa-pencil m-r-5"></i> Edit</a>
                        <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#delete_employee"><i
                                class="fa-regular fa-trash-can m-r-5"></i> Delete</a>
                    </div>
                </div>
                <h4 class="user-name m-t-10 mb-0 text-ellipsis"><a href="{{ url('profile') }}">John Doe</a></h4>
                <div class="small text-muted">Web Designer</div>
            </div>
        </div>
        <div class="col-md-4 col-sm-6 col-12 col-lg-4 col-xl-3">
            <div class="profile-widget">
                <div class="profile-img">
                    <a href="{{ url('profile') }}" class="avatar"><img
                            src="{{ URL::asset('assets/img/profiles/avatar-09.jpg') }}" alt=""></a>
                </div>
                <div class="dropdown profile-action">
                    <a href="#" class="action-icon dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false"><i
                            class="material-icons">more_vert</i></a>
                    <div class="dropdown-menu dropdown-menu-right">
                        <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#edit_employee"><i
                                class="fa fa-pencil m-r-5"></i> Edit</a>
                        <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#delete_employee"><i
                                class="fa-regular fa-trash-can m-r-5"></i> Delete</a>
                    </div>
                </div>
                <h4 class="user-name m-t-10 mb-0 text-ellipsis"><a href="{{ url('profile') }}">Richard Miles</a></h4>
                <div class="small text-muted">Web Developer</div>
            </div>
        </div>
        <div class="col-md-4 col-sm-6 col-12 col-lg-4 col-xl-3">
            <div class="profile-widget">
                <div class="profile-img">
                    <a href="{{ url('profile') }}" class="avatar"><img
                            src="{{ URL::asset('assets/img/profiles/avatar-10.jpg') }}" alt=""></a>
                </div>
                <div class="dropdown profile-action">
                    <a href="#" class="action-icon dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false"><i
                            class="material-icons">more_vert</i></a>
                    <div class="dropdown-menu dropdown-menu-right">
                        <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#edit_employee"><i
                                class="fa fa-pencil m-r-5"></i> Edit</a>
                        <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#delete_employee"><i
                                class="fa-regular fa-trash-can m-r-5"></i> Delete</a>
                    </div>
                </div>
                <h4 class="user-name m-t-10 mb-0 text-ellipsis"><a href="{{ url('profile') }}">John Smith</a></h4>
                <div class="small text-muted">Android Developer</div>
            </div>
        </div>
        <div class="col-md-4 col-sm-6 col-12 col-lg-4 col-xl-3">
            <div class="profile-widget">
                <div class="profile-img">
                    <a href="{{ url('profile') }}" class="avatar"><img
                            src="{{ URL::asset('assets/img/profiles/avatar-05.jpg') }}" alt=""></a>
                </div>
                <div class="dropdown profile-action">
                    <a href="#" class="action-icon dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false"><i
                            class="material-icons">more_vert</i></a>
                    <div class="dropdown-menu dropdown-menu-right">
                        <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#edit_employee"><i
                                class="fa fa-pencil m-r-5"></i> Edit</a>
                        <a class="dropdown-item" href="#" data-bs-toggle="modal"
                            data-bs-target="#delete_employee"><i class="fa-regular fa-trash-can m-r-5"></i> Delete</a>
                    </div>
                </div>
                <h4 class="user-name m-t-10 mb-0 text-ellipsis"><a href="{{ url('profile') }}">Mike Litorus</a></h4>
                <div class="small text-muted">IOS Developer</div>
            </div>
        </div>
        <div class="col-md-4 col-sm-6 col-12 col-lg-4 col-xl-3">
            <div class="profile-widget">
                <div class="profile-img">
                    <a href="{{ url('profile') }}" class="avatar"><img
                            src="{{ URL::asset('assets/img/profiles/avatar-11.jpg') }}" alt=""></a>
                </div>
                <div class="dropdown profile-action">
                    <a href="#" class="action-icon dropdown-toggle" data-bs-toggle="dropdown"
                        aria-expanded="false"><i class="material-icons">more_vert</i></a>
                    <div class="dropdown-menu dropdown-menu-right">
                        <a class="dropdown-item" href="#" data-bs-toggle="modal"
                            data-bs-target="#edit_employee"><i class="fa fa-pencil m-r-5"></i> Edit</a>
                        <a class="dropdown-item" href="#" data-bs-toggle="modal"
                            data-bs-target="#delete_employee"><i class="fa-regular fa-trash-can m-r-5"></i> Delete</a>
                    </div>
                </div>
                <h4 class="user-name m-t-10 mb-0 text-ellipsis"><a href="{{ url('profile') }}">Wilmer Deluna</a></h4>
                <div class="small text-muted">Team Leader</div>
            </div>
        </div>
        <div class="col-md-4 col-sm-6 col-12 col-lg-4 col-xl-3">
            <div class="profile-widget">
                <div class="profile-img">
                    <a href="{{ url('profile') }}" class="avatar"><img
                            src="{{ URL::asset('assets/img/profiles/avatar-12.jpg') }}" alt=""></a>
                </div>
                <div class="dropdown profile-action">
                    <a href="#" class="action-icon dropdown-toggle" data-bs-toggle="dropdown"
                        aria-expanded="false"><i class="material-icons">more_vert</i></a>
                    <div class="dropdown-menu dropdown-menu-right">
                        <a class="dropdown-item" href="#" data-bs-toggle="modal"
                            data-bs-target="#edit_employee"><i class="fa fa-pencil m-r-5"></i> Edit</a>
                        <a class="dropdown-item" href="#" data-bs-toggle="modal"
                            data-bs-target="#delete_employee"><i class="fa-regular fa-trash-can m-r-5"></i> Delete</a>
                    </div>
                </div>
                <h4 class="user-name m-t-10 mb-0 text-ellipsis"><a href="{{ url('profile') }}">Jeffrey Warden</a></h4>
                <div class="small text-muted">Web Developer</div>
            </div>
        </div>
        <div class="col-md-4 col-sm-6 col-12 col-lg-4 col-xl-3">
            <div class="profile-widget">
                <div class="profile-img">
                    <a href="{{ url('profile') }}" class="avatar"><img
                            src="{{ URL::asset('assets/img/profiles/avatar-13.jpg') }}" alt=""></a>
                </div>
                <div class="dropdown profile-action">
                    <a href="#" class="action-icon dropdown-toggle" data-bs-toggle="dropdown"
                        aria-expanded="false"><i class="material-icons">more_vert</i></a>
                    <div class="dropdown-menu dropdown-menu-right">
                        <a class="dropdown-item" href="#" data-bs-toggle="modal"
                            data-bs-target="#edit_employee"><i class="fa fa-pencil m-r-5"></i> Edit</a>
                        <a class="dropdown-item" href="#" data-bs-toggle="modal"
                            data-bs-target="#delete_employee"><i class="fa-regular fa-trash-can m-r-5"></i> Delete</a>
                    </div>
                </div>
                <h4 class="user-name m-t-10 mb-0 text-ellipsis"><a href="{{ url('profile') }}">Bernardo Galaviz</a></h4>
                <div class="small text-muted">Web Developer</div>
            </div>
        </div>
        <div class="col-md-4 col-sm-6 col-12 col-lg-4 col-xl-3">
            <div class="profile-widget">
                <div class="profile-img">
                    <a href="{{ url('profile') }}" class="avatar"><img
                            src="{{ URL::asset('assets/img/profiles/avatar-01.jpg') }}" alt=""></a>
                </div>
                <div class="dropdown profile-action">
                    <a href="#" class="action-icon dropdown-toggle" data-bs-toggle="dropdown"
                        aria-expanded="false"><i class="material-icons">more_vert</i></a>
                    <div class="dropdown-menu dropdown-menu-right">
                        <a class="dropdown-item" href="#" data-bs-toggle="modal"
                            data-bs-target="#edit_employee"><i class="fa fa-pencil m-r-5"></i> Edit</a>
                        <a class="dropdown-item" href="#" data-bs-toggle="modal"
                            data-bs-target="#delete_employee"><i class="fa-regular fa-trash-can m-r-5"></i> Delete</a>
                    </div>
                </div>
                <h4 class="user-name m-t-10 mb-0 text-ellipsis"><a href="{{ url('profile') }}">Lesley Grauer</a></h4>
                <div class="small text-muted">Team Leader</div>
            </div>
        </div>
        <div class="col-md-4 col-sm-6 col-12 col-lg-4 col-xl-3">
            <div class="profile-widget">
                <div class="profile-img">
                    <a href="{{ url('profile') }}" class="avatar"><img
                            src="{{ URL::asset('assets/img/profiles/avatar-16.jpg') }}" alt=""></a>
                </div>
                <div class="dropdown profile-action">
                    <a href="#" class="action-icon dropdown-toggle" data-bs-toggle="dropdown"
                        aria-expanded="false"><i class="material-icons">more_vert</i></a>
                    <div class="dropdown-menu dropdown-menu-right">
                        <a class="dropdown-item" href="#" data-bs-toggle="modal"
                            data-bs-target="#edit_employee"><i class="fa fa-pencil m-r-5"></i> Edit</a>
                        <a class="dropdown-item" href="#" data-bs-toggle="modal"
                            data-bs-target="#delete_employee"><i class="fa-regular fa-trash-can m-r-5"></i> Delete</a>
                    </div>
                </div>
                <h4 class="user-name m-t-10 mb-0 text-ellipsis"><a href="{{ url('profile') }}">Jeffery Lalor</a></h4>
                <div class="small text-muted">Team Leader</div>
            </div>
        </div>
        <div class="col-md-4 col-sm-6 col-12 col-lg-4 col-xl-3">
            <div class="profile-widget">
                <div class="profile-img">
                    <a href="{{ url('profile') }}" class="avatar"><img
                            src="{{ URL::asset('assets/img/profiles/avatar-04.jpg') }}" alt=""></a>
                </div>
                <div class="dropdown profile-action">
                    <a href="#" class="action-icon dropdown-toggle" data-bs-toggle="dropdown"
                        aria-expanded="false"><i class="material-icons">more_vert</i></a>
                    <div class="dropdown-menu dropdown-menu-right">
                        <a class="dropdown-item" href="#" data-bs-toggle="modal"
                            data-bs-target="#edit_employee"><i class="fa fa-pencil m-r-5"></i> Edit</a>
                        <a class="dropdown-item" href="#" data-bs-toggle="modal"
                            data-bs-target="#delete_employee"><i class="fa-regular fa-trash-can m-r-5"></i> Delete</a>
                    </div>
                </div>
                <h4 class="user-name m-t-10 mb-0 text-ellipsis"><a href="{{ url('profile') }}">Loren Gatlin</a></h4>
                <div class="small text-muted">Android Developer</div>
            </div>
        </div>
        <div class="col-md-4 col-sm-6 col-12 col-lg-4 col-xl-3">
            <div class="profile-widget">
                <div class="profile-img">
                    <a href="{{ url('profile') }}" class="avatar"><img
                            src="{{ URL::asset('assets/img/profiles/avatar-03.jpg') }}" alt=""></a>
                </div>
                <div class="dropdown profile-action">
                    <a href="#" class="action-icon dropdown-toggle" data-bs-toggle="dropdown"
                        aria-expanded="false"><i class="material-icons">more_vert</i></a>
                    <div class="dropdown-menu dropdown-menu-right">
                        <a class="dropdown-item" href="#" data-bs-toggle="modal"
                            data-bs-target="#edit_employee"><i class="fa fa-pencil m-r-5"></i> Edit</a>
                        <a class="dropdown-item" href="#" data-bs-toggle="modal"
                            data-bs-target="#delete_employee"><i class="fa-regular fa-trash-can m-r-5"></i> Delete</a>
                    </div>
                </div>
                <h4 class="user-name m-t-10 mb-0 text-ellipsis"><a href="{{ url('profile') }}">Tarah Shropshire</a></h4>
                <div class="small text-muted">Android Developer</div>
            </div>
        </div>
        <div class="col-md-4 col-sm-6 col-12 col-lg-4 col-xl-3">
            <div class="profile-widget">
                <div class="profile-img">
                    <a href="{{ url('profile') }}" class="avatar"><img
                            src="{{ URL::asset('assets/img/profiles/avatar-08.jpg') }}" alt=""></a>
                </div>
                <div class="dropdown profile-action">
                    <a href="#" class="action-icon dropdown-toggle" data-bs-toggle="dropdown"
                        aria-expanded="false"><i class="material-icons">more_vert</i></a>
                    <div class="dropdown-menu dropdown-menu-right">
                        <a class="dropdown-item" href="#" data-bs-toggle="modal"
                            data-bs-target="#edit_employee"><i class="fa fa-pencil m-r-5"></i> Edit</a>
                        <a class="dropdown-item" href="#" data-bs-toggle="modal"
                            data-bs-target="#delete_employee"><i class="fa-regular fa-trash-can m-r-5"></i> Delete</a>
                    </div>
                </div>
                <h4 class="user-name m-t-10 mb-0 text-ellipsis"><a href="{{ url('profile') }}">Catherine Manseau</a></h4>
                <div class="small text-muted">Android Developer</div>
            </div>
        </div>
    </div>


@endsection