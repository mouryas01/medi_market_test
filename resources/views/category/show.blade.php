@extends("layouts.app2")

@section('content')

<!-- Page header start -->
<div class="page-header">
    <ol class="breadcrumb">
        <li class="breadcrumb-item">Home</li>
        <li class="breadcrumb-item"><a href="{{ route('categories.index') }}">Categories</a></li>
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
                    <img src="{{ asset($category['image']) }}" alt="{{ $category['name'] }}" width="200px" height="200px">
                    <h3 class="head">{{ ucfirst($category['name']) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-xl-9 col-lg-9 col-md-12 col-sm-12 col-12">
            <div class="card h-100">
                <div class="card-header">
                    <div class="card-title">
                    <h4>Category Name: {{ ucfirst($category['name']) }}</h4>                                         
                    </div>                                  
                </div>
                <div class="card-body">   
                    <h5>Sub-Categories :- </h5>                 
                    <ul class="list-group">                         
                        @forelse($subcategories as $key => $subcategory)   
                            <li class="list-group-item d-flex justify-content-between align-items-center">                                            
                                <span class="badge-pill"><a href="{{ route('categories.show', $key) }}">{{ ucfirst($subcategory['name']) }}</a></span>                        
                            </li>
                        @empty
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span class="badge-pill">No sub-category</span>
                            </li>
                        @endforelse                                                                              
                    </ul>  
                    <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-6">                                    
                        <a href="{{ route('categories.index') }}" class="btn btn-secondary"> Cancel</a>                                  
                    </div>                  
                </div>      
            </div>
        </div>
    </div>
    <!-- Row end -->

</div>
<!-- Main container end -->

@endsection