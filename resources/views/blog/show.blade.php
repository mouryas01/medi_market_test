@extends("layouts.app2")

@section("content")

<!-- Page header start -->
<div class="page-header">
    <ol class="breadcrumb">
        <li class="breadcrumb-item">Home</li>
        <li class="breadcrumb-item"><a href="{{ route('blogs.index') }}">Blog</a></li>
        <li class="breadcrumb-item active">View Blog</li>
    </ol> 

     <button class="btn btn-secondary" onclick="history.back();">Back</button> 
</div>
<!-- Page header end -->

<!-- Main container start -->
<div class="main-container">

    <!-- Row start -->
    <div class="row gutters">
        
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
            <div class="card h-100">
                {{-- <div class="card-header">
                    <div class="card-title">
                    <p>Blog Title: </p>                                        
                    </div>                  
                </div> --}}   

                <div class="card-body">                    
                     <div class="leftcolumn">
                        <div class="card">
                            <h2>{{ ucwords($blog['title']) ?? '' }}</h2>
                            <h6>Title description, {{ date('M d, Y',strtotime($blog['created_at'])) ?? '' }}</h6>
                            <br><br>                             
                            <img src="{{ url($blog['image']) }}" width="300px" height="250px"> <br><br>                               
                            <p>{{ $blog['description'] ?? '' }}</p>
                        </div>
                    </div>                 
                </div>

            </div>
        </div>

    </div>
    <!-- Row end -->

</div>
<!-- Main container end -->

@endsection