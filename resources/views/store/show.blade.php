@extends("layouts.app2")

@section("content")

<!-- Page header start -->
<div class="page-header">
    <ol class="breadcrumb">
        <li class="breadcrumb-item">Home</li>
        <li class="breadcrumb-item"><a href="{{ route('stores.index') }}">Store</a></li>
        <li class="breadcrumb-item active">View Store</li>
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
                    <img src="{{ asset($store->image) }}" alt="{{ $store->store_name }}" width="200px" height="200px">
                    <br>
                    <h3 class="head">{{ ucfirst($store->store_name) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-xl-9 col-lg-9 col-md-12 col-sm-12 col-12">
            <div class="card h-100">
                <div class="card-header">
                    <div class="card-title">
                    <p>Store Name: <a href=""> {{ ucfirst($store->store_name) }}</a></p> 
                    <p>Store Code: <a href=""> {{ ucfirst($store->store_code) }}</a></p>                    
                    </div>                    
                </div>
                <div class="card-body">                    
                    <ul class="list-group">
                         <li class="list-group-item d-flex justify-content-between align-items-center">
                            Vendor Name
                            <span class="badge-pill">{{ $store->vendor_name }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Vendor Email
                            <span class="badge badge-primary badge-pill">{{ $store->vendor_email }}</span>
                        </li>                       
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Contact No.
                            <span class="badge badge-primary badge-pill">{{ $store->phone }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Store Type:
                            <span class="badge badge-primary badge-pill">{{ ($store->store_type == 1) ? 'Parafarmacie':'Parashop' }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Store Address
                            <span class="badge-pill">{{ $store->address }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Store Opened Date:
                            <span class="badge-pill">{{ $store->created_at->date }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Rating
                            <span class="badge-pill">{{ $store->rating }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Payment Link
                            <span class="badge-pill">{{ $store->paylink ?? '' }}</span>
                        </li>                                                                
                    </ul>  
                    <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-6">                                    
                        <a href="{{ route('stores.index') }}" class="btn btn-secondary" onclick="showloader()"> Cancel</a>                                  
                    </div>                  
                </div>
            </div>
        </div>
    </div>
    <!-- Row end -->

</div>
<!-- Main container end -->

@endsection