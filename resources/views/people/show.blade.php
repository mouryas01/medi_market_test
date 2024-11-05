@extends("layouts.app2")

@section('content')

<!-- Page header start -->
<div class="page-header">
    <ol class="breadcrumb">
        <li class="breadcrumb-item">Home</li>
        <li class="breadcrumb-item"><a href="{{ route('users.index') }}">Customers</a></li>
        <li class="breadcrumb-item active">View {{ $title }}</li>
    </ol>  
</div>
<!-- Page header end -->

<!-- Main container start -->
<div class="main-container">

    <!-- Row start -->
    <div class="row gutters">
        <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12 col-12">
            <div class="card h-100">                
                <div class="card-header">
                    <div class="card-title">
                    <h4>User Detail</h4>                                         
                    </div>                                  
                </div>

                <!--<div class="card-body">           
                    <img src="{{-- asset($userInfo['picture']) --}}" alt="{{-- $userInfo['name'] --}}" width="235px" height="250px">                    
                </div>-->

                <div class="card-body">           
                    <ul class="list-group">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            User ID
                            <span class="badge badge-primary badge-pill">{{ $userInfo['uid'] }}</span>
                        </li>
                        {{-- <li class="list-group-item d-flex justify-content-between align-items-center">
                            Firstname
                            <span class="badge badge-primary badge-pill">{{ ucfirst($userInfo['firstname']) }}</span>
                        </li> --}}
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Name
                            <span class="badge-pill">{{ ucfirst($userInfo['name']) }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Email
                            <span class="badge badge-primary badge-pill">{{ $userInfo['email'] }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Phone Number
                            <span class="badge badge-primary badge-pill">{{ $userInfo['phone'] }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Mobile Number
                            <span class="badge badge-primary badge-pill">{{ $userInfo['number'] }}</span>
                        </li>

                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            street
                            <span class="badge badge-primary badge-pill">{{ ucfirst($userInfo['street']) }}</span>
                        </li>
                        
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            city
                            <span class="badge badge-primary badge-pill">{{ ucfirst($userInfo['city']) }}</span>
                        </li> 
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            country
                            <span class="badge badge-primary badge-pill">{{ ucfirst($userInfo['country']) }}</span>
                        </li> 
                         <li class="list-group-item d-flex justify-content-between align-items-center">
                            postal
                            <span class="badge badge-primary badge-pill">{{ $userInfo['postal'] }}</span>
                        </li>

                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Sex
                            <span class="badge badge-primary badge-pill">{{ ucfirst($userInfo['sex']) }}</span>
                        </li>                        
                         
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Joined Date
                            <span class="badge badge-primary badge-pill">{{ $userInfo['joined'] }}</span>
                        </li>                                                
                        
                    </ul> 
                </div>
            </div>
        </div>

        <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12 col-12">
            <div class="card h-100">
                <div class="card-header">
                    <div class="card-title">
                    <h4>Account Detail</h4>                                         
                    </div>                                  
                </div>

                <div class="card-body">           
                    <ul class="list-group">
                        
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Card Number
                            <span class="badge badge-primary badge-pill">{{ $userInfo['cardnumber'] }}</span>
                        </li> 
                        
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Credit
                            <span class="badge badge-primary badge-pill">{{ $userInfo['credit'] }}</span>
                        </li> 
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Points
                            <span class="badge badge-primary badge-pill">{{ $userInfo['points'] }}</span>
                        </li>
                         
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Last Bought
                            <span class="badge badge-primary badge-pill">{{ $userInfo['lastbought'] }}</span>
                        </li> 
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Loyalty
                            <span class="badge badge-primary badge-pill">{{ $userInfo['loyalty'] }}</span>
                        </li>                        
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Loyalty Expiring
                            <span class="badge badge-primary badge-pill">{{ $userInfo['loyaltyexpiring'] }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Visits
                            <span class="badge badge-primary badge-pill">{{ $userInfo['visits'] }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Min Visits
                            <span class="badge badge-primary badge-pill">{{ $userInfo['minvisits'] }}</span>
                        </li>
                       
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Percentage
                            <span class="badge badge-primary badge-pill">{{ $userInfo['percentage'] }}</span>
                        </li>                                               
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Source
                            <span class="badge badge-primary badge-pill">{{ $userInfo['source'] }}</span>
                        </li>
                        
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Remarks
                            <span class="badge badge-primary badge-pill">{{ ucfirst($userInfo['remarks']) }}</span>
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