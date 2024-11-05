@extends("layouts.app2")

@section("content")

<!-- Page header start -->
<div class="page-header">
    <ol class="breadcrumb">
        <li class="breadcrumb-item">Home</li>
        <li class="breadcrumb-item"><a href="{{ route('promotions.index') }}">Promotion</a></li>
        <li class="breadcrumb-item active">View Promotion</li>
    </ol>  
</div>
<!-- Page header end -->

<!-- Main container start -->
<div class="main-container">

    <!-- Row start -->
    <div class="row gutters">
        <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 col-12">
            <div class="card h-100">
                <div class="card-body">
                    <p><b>Promotion Title:</b> <a href=""> {{ ucfirst($promotion->title) }}</a></p> 
                    <p><b>Description:</b> <a href=""> {{ ucfirst($promotion->description) }}</a></p>
                    <br>  
                    <img src="{{ asset($promotion->image) }}" alt="{{ $promotion->title }}" width="200px" height="200px">
                    <br>                    
                </div>
                <div class="card-body">                                         
                    <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-6">                                    
                        <a href="{{ route('promotions.index') }}" class="btn btn-secondary" onclick="showloader()"> Cancel</a>                                  
                    </div>                  
                </div>
            </div>
        </div>
    </div>
    <!-- Row end -->
</div>
<!-- Main container end -->

@endsection