@extends("layouts.app2")

@section("content")

<!-- Page header start -->
<div class="page-header">
    <ol class="breadcrumb">
        <li class="breadcrumb-item">Home</li>
        <li class="breadcrumb-item active">Vendors</li>
    </ol>   
    
    <a href="{{ route('register') }}">
        <button type="button" class="btn btn-primary btn-rounded">Add New</button>
    </a>
</div>
<!-- Page header end -->

<!-- Main container start -->
<div class="main-container">

    <!-- Row start -->
    <div class="row gutters">
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
            <div class="table-container">
                <div class="t-header">Vendors</div>
                <div class="table-responsive">
                @if(count($users) > 0)
                    <table id="copy-print-csv" class="table custom-table">
                        <thead>                            
                            <tr>
                                <th>S/n</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Join Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($users as $key => $user)                                            
                            <tr>
                                <td>{{ ($key + 1) }}</td>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                                <td>{{ $user->user_type }}</td>
                                <td>{{ $user->created_at }}</td>    
                                <td>
                                    <a href="{{ route('vendors.edit', [$user->user_key]) }}" class="btn btn-dark btn-sm" onclick="showloader()">Edit</a>
                                    {{-- <a href="{{ route('vendors.edit', [$user->user_key]) }}" class="btn btn-warning btn-sm" onclick="showloader()">Delete</a> --}}
                                </td>
                            </tr>                           
                        @endforeach                        
                        </tbody>
                    </table>
                @else
                    <p class="text text-center text-info">No user.</p>
                @endif   
                </div>
            </div>
        </div>
    </div>
    <!-- Row end -->
</div>
<!-- Main container end -->
@endsection