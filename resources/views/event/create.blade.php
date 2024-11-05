@extends("layouts.app2")

@section("content")

<!-- Page header start -->
<div class="page-header">
    <ol class="breadcrumb">
        <li class="breadcrumb-item">Home</li>
        <li class="breadcrumb-item"><a href="{{ route('events.index') }}">Event</a></li>
        <li class="breadcrumb-item active">Add New</li>
    </ol>           
</div>
<!-- Page header end -->

<!-- Main container start -->
<div class="main-container">

    <!-- Row start -->
    <div class="row gutters">
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
            <div class="card">
                <div class="card-body">
                    <div class="box-header">
                        <h4 class="card-title">Add Event</h4>                    
                    </div>  
                    <div>                        
                        <div class="alert alert-success successmsg" style="display:none;">
                            <a href="#" class="close" data-dismiss="alert" aria-label="close">×</a>
                        </div>
                        <div class="alert alert-warning print-error-msg" style="display:none;">
                            <a href="#" class="close" data-dismiss="alert" aria-label="close">×</a>
                            <ul></ul>
                        </div>
                    </div>
                    <hr>

                    <form id="myform" method="POST" action="{{ route('events.store') }}" autocomplete="off" enctype="multipart/form-data">
                        {{ csrf_field() }}

                        <div class="row gutters">
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group{{ $errors->has('title') ? ' has-error' : '' }}">
                                    <label for="title">Event Title</label>                                
                                    <input id="title" type="text" class="form-control" name="title" value="{{ old('title') }}" required autofocus>
                                    @if ($errors->has('title'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('title') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="col-xl-2 col-lg-2 col-md-2 col-sm-2 col-12">
                                <div class="form-group{{ $errors->has('start_date') ? ' has-error' : '' }}p">
                                    <label for="start_date">Start Date</label>                               
                                    <input id="start_date" type="text" class="form-control" name="start_date" value="{{ old('start_date') }}" required autofocus>
                                    @if ($errors->has('start_date'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('start_date') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="col-xl-2 col-lg-2 col-md-2 col-sm-2 col-12">
                                <div class="form-group{{ $errors->has('start_time') ? ' has-error' : '' }}p">
                                    <label for="start_time">Start Time</label>                               
                                    <input id="start_time" type="time" class="form-control" name="start_time" value="{{ old('start_time') }}" required autofocus>
                                    @if ($errors->has('start_time'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('start_time') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="col-xl-2 col-lg-2 col-md-2 col-sm-2 col-12">
                                <div class="form-group{{ $errors->has('end_date') ? ' has-error' : '' }}p">
                                    <label for="end_date">End Date</label>                               
                                    <input id="end_date" type="text" class="form-control" name="end_date" value="{{ old('end_date') }}" required autofocus>
                                    @if ($errors->has('end_date'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('end_date') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="col-xl-2 col-lg-2 col-md-2 col-sm-2 col-12">
                                <div class="form-group{{ $errors->has('end_time') ? ' has-error' : '' }}p">
                                    <label for="end_time">End Time</label>                               
                                    <input id="end_time" type="time" class="form-control" name="end_time" value="{{ old('end_time') }}" required autofocus>
                                    @if ($errors->has('end_time'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('end_time') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                                <div class="form-group{{ $errors->has('description') ? ' has-error' : '' }}">
                                    <label for="description">Description</label>                                
                                    <textarea id="description" class="form-control" name="description" required>{{ old('description') }}</textarea>
                                   @if ($errors->has('description'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('description') }}</strong>
                                        </span>
                                    @endif
                                </div>                            
                            </div>                        


                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group{{ $errors->has('picture') ? ' has-error' : '' }}">
                                    <label for="address">Event Location</label>
                                    <input  type="text" name="address" id="address" class="form-control" required>
                                    @if ($errors->has('address'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('address') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                            
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group{{ $errors->has('picture') ? ' has-error' : '' }}">
                                    <label for="picture">Event Picture</label>
                                    <input  type="file" onchange="loadFile(event)" name="picture" id="picture" accept="image/x-png,image/gif,image/jpeg" class="form-control" required>
                                    @if ($errors->has('picture'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('picture') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <img id="uploaded_image" width="50px" height="50px" style="display:none;">
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                    {{-- blank --}}
                            </div>

                            
                            
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-6">                                                                    
                                <button type="button" class="btn btn-primary" onclick="formSubmit();">Add</button> 
                                <a href="{{ route('events.index') }}" class="btn btn-secondary" onclick="showloader()"> Cancel</a>                                  
                            </div>
                        </div>                                            
                    </form>
                </div>               
            </div>
        </div>
    </div>
    <!-- Row end -->
     <div id ="test"></div>
</div>
<!-- Main container end -->


@push('after-scripts')

<script type="text/javascript">
$(function(){
    $("#start_date").datepicker({ minDate: 0});
    $("#end_date").datepicker({ minDate: 0});       
});

/* Print validation error on page */
function printErrorMsg(msg) {
    $(".print-error-msg").find("ul").html('');
    $(".print-error-msg").css('display', 'block');
    $.each(msg, function(key, value) {
        $(".print-error-msg").find("ul").append('<li>' + value + '</li>');
    });
}

/* Add store info */
function formSubmit(){  
    $('#loading-wrapper').show();    
    var form_data = new FormData(document.getElementById("myform"));            
    $.ajax({
        url: "{{ route('events.store') }}",
        type: "post",
        data: form_data,
        dataType: 'JSON',
        contentType: false,
        cache: false,
        processData: false,
        headers: {
            'X-CSRF-Token': '{{ csrf_token() }}',
        },
        success: function(data) {
            $('#loading-wrapper').hide();  
            if ($.isEmptyObject(data.error)) {
                $(".print-error-msg").hide();
                $(".successmsg").html(data.success).show().delay(3000).fadeOut(800);
                $("#uploaded_image").html(data.uploaded_image);
                window.location = "{{ route('events.index') }}";
            } else {
                printErrorMsg(data.error);
                $(".successmsg").hide();
                $('.print-error-msg').delay(10000).fadeOut(800);
            }
        }
    });
}

var loadFile = function(event) {
    var output = document.getElementById('uploaded_image');
    output.src = URL.createObjectURL(event.target.files[0]);
    $("#uploaded_image").show();
    output.onload = function() {
      URL.revokeObjectURL(output.src) // free memory
    }
};
</script>
@endpush
@endsection