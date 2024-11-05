@extends("layouts.app2")

@section('content')

<!-- Page header start -->
<div class="page-header">
    <ol class="breadcrumb">
        <li class="breadcrumb-item">Home</li>
        <li class="breadcrumb-item"><a href="{{ route('users.index') }}">Users</a></li>
        <li class="breadcrumb-item active">View {{ $title }}</li>
    </ol>  
</div>
<!-- Page header end -->

<!-- Main container start -->
<div class="main-container">

    <!-- Row start -->
    <div class="row gutters">
        <div class="col-xl-3 col-lg-3 col-md-12 col-sm-12 col-12">
            <div class="card h-100">
                <div class="card-body">           
                    <img src="{{ asset($userInfo['picture']) }}" alt="{{ $userInfo['name'] }}" width="235px" height="250px">                    
                </div>
            </div>
        </div>
        <div class="col-xl-9 col-lg-9 col-md-12 col-sm-12 col-12">
            <div class="card h-100">
                <div class="card-header">
                    <div class="card-title">
                    <h4>User Detail</h4>                                         
                    </div>                                  
                </div>

                <div class="card-body">           
                    <ul class="list-group">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Name
                            <span class="badge-pill">{{ ucfirst($userInfo['name']) }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Email
                            <span class="badge badge-primary badge-pill">{{ ucfirst($userInfo['email']) }}</span>
                        </li>                                                                                     
                    </ul> 
                </div>

            </div>
        </div>
    </div>
    <!-- Row end -->

</div>
<!-- Main container end -->

@endsection