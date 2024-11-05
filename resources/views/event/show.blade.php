@extends("layouts.app2")

@section("content")

<!-- Page header start -->
<div class="page-header">
    <ol class="breadcrumb">
        <li class="breadcrumb-item">Home</li>
        <li class="breadcrumb-item"><a href="{{ route('stores.index') }}">Events</a></li>
        <li class="breadcrumb-item active">View Event</li>
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
                    <img src="{{ asset($event->image) }}" alt="{{ $event->title }}" width="200px" height="200px">
                    <br>
                    <h3 class="head">{{ ucfirst($event->title) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-xl-9 col-lg-9 col-md-12 col-sm-12 col-12">
            <div class="card h-100">
                <div class="card-header">
                    <div class="card-title">
                    <p>Event Title: <a href=""> {{ ucfirst($event->title) }}</a></p> 
                    <p>Description: <a href=""> {{ ucfirst($event->description) }}</a></p>                    
                    </div>                    
                </div>
                <div class="card-body">                    
                    <ul class="list-group">
                         <li class="list-group-item d-flex justify-content-between align-items-center">
                            Start Date
                            <span class="badge badge-primary badge-pill">{{ $event->start_date }}</span>
                        </li>
                         <li class="list-group-item d-flex justify-content-between align-items-center">
                            Start Time
                            <span class="badge badge-primary badge-pill">{{ $event->start_time }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            End Date
                            <span class="badge badge-primary badge-pill">{{ $event->end_date  }}</span>
                        </li> 
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            End Time
                            <span class="badge badge-primary badge-pill">{{ $event->end_time  }}</span>
                        </li> 
                         <li class="list-group-item d-flex justify-content-between align-items-center">
                            Address
                            <span class="badge badge-primary badge-pill">{{ $event->address  }}</span>
                        </li>                                                                                                             
                    </ul>  
                    <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-6">                                    
                        <a href="{{ route('events.index') }}" class="btn btn-secondary" onclick="showloader()"> Cancel</a>                                  
                    </div>                  
                </div>
            </div>
        </div>
    </div>
    <!-- Row end -->

</div>
<!-- Main container end -->

@endsection