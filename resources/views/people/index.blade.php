@extends("layouts.app2")

@section("content")

<!-- Page header start -->
<div class="page-header">
    <ol class="breadcrumb">
        <li class="breadcrumb-item">Home</li>
        <li class="breadcrumb-item active">Customers</li>
    </ol>  
</div>
<!-- Page header end -->

<!-- Main container start -->
<div class="main-container">

    <!-- Row start -->
    <div class="row gutters">
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
            <div class="table-container">
                <div class="t-header">Customers</div>
                <div class="table-responsive">                
                    <table id="copy-print-csv" class="table custom-table">
                        <thead>                            
                            <tr>
                                <th>S/n</th>
                                <th>User ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>City</th>
                                <th>Country</th>
                                <th>Join Date</th> 
                                <th>Action</th>                                
                            </tr>
                        </thead>
                        <tbody>                        
                        @if(count($users) > 0)                                                   
                            @php
                                $sr = 1;
                                // sort array with given user-defined function
                                function compareByTimeStamp($a, $b) 
                                { 
                                    if (strtotime($a['joined']) < strtotime($b['joined'])) 
                                        return 1; 
                                    else if (strtotime($a['joined']) > strtotime($b['joined']))  
                                        return -1; 
                                    else
                                        return 0; 
                                } 
                                usort($users, "compareByTimeStamp"); 
                            @endphp

                            @foreach($users as $key => $user)                                              
                                <tr>
                                    <td>{{ $sr }}</td>
                                    <td>{{ $user->user_id }}</td>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>{{ $user->city }}</td>
                                    <td>{{ $user->country }}</td>
                                    <td>{{ $user->joined }}</td> 
                                    <td>
                                        <a href="{{ route('users.show', ['customer_id' => $user->user_key]) }}" class="btn btn-primary btn-sm" onclick="showloader()">
                                        View</a>
                                    </td>                                   
                                </tr>   
                                @php $sr++; @endphp                        
                            @endforeach                              
                        @endif                        
                        </tbody>
                    </table>                  
                </div>
            </div>
        </div>
    </div>
    <!-- Row end -->
</div>
<!-- Main container end -->
@endsection