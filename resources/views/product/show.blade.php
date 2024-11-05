@extends("layouts.app2")

@section("content")

<!-- Page header start -->
<div class="page-header">
    <ol class="breadcrumb">
        <li class="breadcrumb-item">Home</li>
        <li class="breadcrumb-item"><a href="{{ route('products.index') }}">Products</a></li>
        <li class="breadcrumb-item active">View Product</li>
    </ol>
    {{-- <div>                                    
        <button class="btn btn-secondary" onclick="showloader(); history.back();">Back</button>                                  
    </div>   --}}
</div>
<!-- Page header end -->

<!-- Main container start -->
<div class="main-container">

@php
use App\Http\Controllers\StoreController;
use App\Http\Controllers\CategoriesController;
@endphp

    <!-- Row start -->
    <div class="row gutters">
        <div class="col-xl-3 col-lg-3 col-md-12 col-sm-12 col-12">
            <div class="card h-100">
                <div class="card-body">           
                    <img src="{{ asset($products->picture) }}" alt="{{ $products->name }}" width="200px" height="200px">
                    <br><h3 class="head">{{ $products->name }}</h3>
                </div>
            </div>
        </div>
        <div class="col-xl-9 col-lg-9 col-md-12 col-sm-12 col-12">
            <div class="card h-100">
                <div class="card-header">
                    <div class="card-title">
                    <p>Category: <a href=""> {{ CategoriesController::getCategory($products->category_id) }}</a></p> 
                    @if(!empty($products->subcategory_id))
                        <p>Sub Category: <a href=""> {{ CategoriesController::getCategory($products->subcategory_id) }}</a></p>                    
                    @endif
                    @if(!empty($products->sub_subcategory_id))
                        <p>Sub Sub-Category: <a href=""> {{ CategoriesController::getCategory($products->sub_subcategory_id) }}</a></p>                    
                     @endif
                    </div>                    
                </div>
                <div class="card-body">                    
                    <ul class="list-group">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Product Description
                            <span class="badge-pill">{{ $products->description }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Price
                            <span class="badge badge-primary badge-pill">€ {{ $products->price }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Sale Price:
                            <span class="badge badge-primary badge-pill">€ {{ $products->sale_price }}</span>
                        </li>                        
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            VAT:
                            <span class="badge-pill">{{ $products->vat }}</span>
                        </li>                          
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Created Date:
                            <span class="badge-pill">{{ $products->created_at }}</span>
                        </li>                                          
                    </ul> 
                    
                    

                    @if(!empty($suppliers))
                    <hr> 
                     <h5>Store wise stock</h5>
                    <hr> 
                    <ul class="list-group">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Store Name                             
                            <span class="badge-pill">Stock</span>
                        </li>
                        @foreach($suppliers as $supplier)                                           
                            @if(in_array($supplier->supplier, $store_codes))                        
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    ({{ $supplier->supplier }})  
                                    @php                                            
                                        $storeinfo = StoreController::getStoreName($supplier->supplier); 
                                        echo !empty($storeinfo['store_name']) ? $storeinfo['store_name']:'';
                                    @endphp
                                    <span class="badge-pill">{{ $supplier->stock }}</span>
                                </li>
                            @endif
                        @endforeach                                                                 
                    </ul> 
                    @endif                                      
                </div>
            </div>
        </div>
    </div>
    <!-- Row end -->

</div>
<!-- Main container end -->

@endsection