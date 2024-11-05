@extends("layouts.app2")

@section("content")

<!-- Page header start -->
<div class="page-header">
    <ol class="breadcrumb">
        <li class="breadcrumb-item">Home</li>
        <li class="breadcrumb-item active">Events</li>
    </ol>   
    <div>         
    <a href="{{ route('events.create') }}">
        <button type="button" class="btn btn-primary btn-rounded" onclick="showloader()">Add New</button>
    </a>    
    </div>
</div>
<!-- Page header end -->

<!-- Main container start -->
<div class="main-container">



    <!-- Row start -->
    <div class="row gutters">                       
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
        <div class="table-container">
            <section class="panel">
                <div class="t-header">Search By</div>
                <div class="panel-body">
                    <form class="form-inline" action="{{ route('eventFilterByDate') }}" method="post" autocomplete="off" id="myForm">
                        {{ csrf_field() }}
                        <div class="form-group row">
                            <label for="startdate" class="col-sm-4 col-form-label">Start Date</label>
                            <div class="col-sm-8">
                                <input type="text" name="startdate" value="{{ isset($_POST['startdate']) ? $_POST['startdate'] :'' }}" class="form-control" id="startdate" placeholder="mm/dd/yyyy">
                                @if ($errors->has('startdate'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('startdate') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="enddate" class="col-sm-4 col-form-label">End Date</label>
                            <div class="col-sm-8">
                                <input type="text" name="enddate" value="{{ isset($_POST['enddate']) ? $_POST['enddate'] :'' }}" class="form-control" id="enddate" placeholder="mm/dd/yyyy">
                                @if ($errors->has('enddate'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('enddate') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-sm-12">
                                <button type="submit" class="btn btn-primary" onclick="return validate();">Filter</button>
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-sm-12">
                                <button type="button" class="btn btn-danger" onclick="resetForm();">Reset</button>
                            </div>
                        </div>

                    </form>
                </div>
            </section>
        </div>
        </div>
    </div> 


    <!-- Row start -->
    <div class="row gutters">                       
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
            <div class="table-container">
                <div class="t-header">Events</div>
                <div class="alert alert-success alert-block" style="display:none;"></div> 
                <div class="table-responsive">                              
                    <table id="copy-print-csv" class="table custom-table">
                        <thead>                            
                            <tr>
                                <th>S/n</th>                               
                                <th>Event</th>
                                <th>Title</th>                                    
                                <th>Start Date</th> 
                                <th>Start Time</th>      
                                <th>End Date</th>   
                                <th>End Time</th>                      
                                <th>Action</th>                                                                                         
                            </tr>
                        </thead>
                        <tbody id="productlist">
                        @if(is_array($events) && count($events) > 0)                            
                            @foreach($events as $key => $event)                                                                                   
                            <tr>                            
                                <td>{{ ($key + 1) }}</td>                         
                                <td><a href="{{ route('events.show',[$event->event_key]) }}" onclick="showloader()">
                                    <img src="{{ url($event->image) }}" height="50px" width="50px">
                                </a></td>
                                <td>{{ $event->title }}</td>                                                                 
                                <td>{{ $event->start_date }}</td> 
                                <td>{{ date('h:i:s a',strtotime($event->start_time)) }}</td> 
                                <td>{{ $event->end_date }}</td>  
                                <td>{{ date('h:i:s a',strtotime($event->end_time)) }}</td>  
                                @php
                                    $today = strtotime(date('Y-m-d'));
                                    $curtime = strtotime(date('h:i:s'));
                                    $enddate = strtotime($event->end_date);
                                    $endtime = strtotime($event->end_time);   
                                    //&& $curtime > $endtime
                                @endphp                                                                      
                                <td> 
                                    @if($today > $enddate )
                                        <button class="btn btn-warning btn-sm">Expired</button>
                                    @else                                    
                                        <a href="{{ route('events.edit',[$event->event_key]) }}" class="btn btn-dark btn-sm" onclick="showloader()">Edit</a>                                                                         
                                    @endif 

                                    @if(Session::get('user_type') == 1)
                                        <form action="{{ route('events.destroy', $event->event_key) }}" method="POST" onsubmit="return confirm('Are You sure want to delete this ?');" style="display: inline-block;">
                                            <input type="hidden" name="_method" value="DELETE">
                                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                            <input type="submit" class="btn btn-warning btn-sm" value="Delete">
                                        </form> 
                                    @endif                                      
                                </td>                                                                                                                                                     
                            </tr>
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

@push('after-scripts')
<script type="text/javascript" src="{{ asset('assets/js/bootstrap-datepicker.min.js') }}"></script>
<link rel="stylesheet" href="{{ asset('assets/css/bootstrap-datepicker3.css') }}"/>

<script type="text/javascript">
$(document).ready(function(){
	$('#startdate').datepicker({ 
        dateFormat: 'dd-mm-yy' 
    });
	$('#enddate').datepicker({ 
        dateFormat: 'dd-mm-yy' 
    });
});


function resetForm(){
    showloader(); 
    document.getElementById("myForm").reset();
    document.getElementById("startdate").value = '';
    document.getElementById("enddate").value='';	
	location.reload(true); 
    location.href="{{ route('events.index') }}";
}
</script>
@endpush   
    
@endsection