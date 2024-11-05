@extends("layouts.app2")

@section("content")

<!-- Page header start -->
<div class="page-header">
    <ol class="breadcrumb">
        <li class="breadcrumb-item">Home</li>
        <li class="breadcrumb-item active">{{ $userinfo->user_type }} Dashboard</li>
    </ol>
</div>
<!-- Page header end -->

<!-- Main container start -->
<div class="main-container">

    <!-- Row start -->
    <div class="row gutters">
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
            <div class="card">
                <div class="card-header">
                    <div class="card-title">{{ $userinfo->user_type }} Profile</div>
                </div>
                <div class="card-body">

                    <!--edit profile -->
                    @if(!empty($edit)) 
                    <form id="myform" method="POST" action="{{ route('update-profile') }}" enctype="multipart/form-data">
                        {{ csrf_field() }}
                    @endif

                        <div class="row">
                            <div class="col-md-4">
                                <div class="profile-img">
                                    <img src="{{ !empty($userinfo->picture) ? url($userinfo->picture):'' }}" id="uploaded_image" width="250px"/>
                                    <br><br>  

                                    <!--edit profile -->
                                    @if(!empty($edit))                                    
                                        <div class="file btn btn-primary">                                         
                                            <input type="file" name="picture" id="picture" onchange="loadFile(event)" accept="image/x-png,image/gif,image/jpeg"/>
                                            <input type="hidden" name="old_picture" id="old_picture" class="form-control" value="{{ $userinfo->picture }}">
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <div class="col-md-8">
                                <!-- first -->
                                <div class="row">
                                    <div class="col-md-10">
                                        <div class="profile-head">
                                            <!--<h5>{{-- $userinfo->name --}}</h5>-->
                                            {{-- <p class="proile-rating">RANKINGS : <span>8/10</span></p> --}}
                                            <ul class="nav nav-tabs" id="myTab" role="tablist">
                                                <li class="nav-item">
                                                    <a class="nav-link active" id="home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="home" aria-selected="true">Profile</a>
                                                </li>
                                                <li class="nav-item">
                                                    <a class="nav-link" id="profile-tab" data-toggle="tab" href="#profile" role="tab" aria-controls="profile" aria-selected="false">Password</a>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <!--edit profile -->
                                        @if(empty($edit))                                             
                                            <a href="{{ route('edit-profile', $userinfo->user_key) }}" class="btn btn-primary btn-rounded" onclick="showloader()"> Edit Profile</a>
                                        @endif    
                                    </div>

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

                                <!-- second -->
                                <div class="row">                                    
                                    <div class="col-md-8">
                                        <div class="tab-content profile-tab" id="myTabContent">
                                            <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">                                                
                                                                                              
                                                <!--edit profile form -->
                                                @if(!empty($edit)) 
                                                    <div>
                                                        <div class="form-group row">
                                                            <div class="col-md-6">
                                                                <label>Name</label>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <input type="hidden" name="user_key" id="user_key" value="{{ $userinfo->user_key }}" class="form-control">
                                                                <input type="text" name="name" id="name" value="{{ $userinfo->name }}" class="form-control">
                                                            </div>
                                                        </div>
                                                        <div class="form-group row">
                                                            @php
                                                                $disabled = '';
                                                                if($userinfo->user_type != 'Admin'){
                                                                    $disabled = 'disabled';
                                                                }
                                                            @endphp
                                                            <div class="col-md-6">
                                                                <label>Email</label>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <input type="text" name="email" value="{{ $userinfo->email }}" class="form-control" {{ $disabled }}>                                                               
                                                            </div>
                                                        </div>                                                                                                                
                                                        <div class="form-group row">
                                                            <div class="col-md-6">
                                                                <label>User Type</label>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <input type="text" name="name" value="{{ $userinfo->user_type }}" class="form-control" disabled>                                                                
                                                            </div>
                                                        </div>
                                                        <button type="button" class="btn btn-primary" onclick="formUSubmit();">Update</button>                                 
                                                    </div>
                                                @else     <!--view profile -->
                                                    <div>                                                        
                                                        <div class="form-group row">
                                                            <div class="col-md-6">
                                                                <label>Name</label>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <p>{{ $userinfo->name }}</p>
                                                            </div>
                                                        </div>
                                                        <div class="form-group  row">
                                                            <div class="col-md-6">
                                                                <label>Email</label>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <p>{{ $userinfo->email }}</p>
                                                            </div>
                                                        </div>                                                        
                                                        <div class="form-group row">
                                                            <div class="col-md-6">
                                                                <label>User Type</label>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <p>{{ $userinfo->user_type }}</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif
                                            @if(!empty($edit)) 
                                            </form> 
                                            @endif 
                                            </div>

                                            <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
                                                <form id="myform1" method="POST" action=" ">
                                                    {{ csrf_field() }}
                                                    <div class="form-group row">
                                                        <div class="col-md-6">
                                                            <label>New Password</label>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <input type="password" name="password" id="password" class="form-control">   
                                                        </div>
                                                    </div> 
                                                    <div class="form-group row"> 
                                                        <div class="col-md-6">
                                                            <label>Confirm New Password</label>
                                                        </div>  
                                                        <div class="col-md-6">
                                                            <input type="password" name="confirm_password" id="confirm_password" class="form-control">   
                                                        </div><br>                                                         
                                                    </div>
                                                     <div class="col-md-6">
                                                            <button type="button" class="btn btn-primary" onclick="formSubmit();">Submit</button>                                 
                                                     </div>  
                                                </form>
                                                
                                            </div>
                                        </div>
                                    </div>
                                </div>

                        </div>      
                    </div>                              
                </div>
            </div>
        </div>            
    </div>
    <!-- Row end -->

</div>
<!-- Main container end -->

@push('after-scripts')

<script type="text/javascript">

/* Print validation error on page */
function printErrorMsg(msg) {
    $(".print-error-msg").find("ul").html('');
    $(".print-error-msg").css('display', 'block');
    $.each(msg, function(key, value) {
        $(".print-error-msg").find("ul").append('<li>' + value + '</li>');
    });
}

/* Update profile info */
function formUSubmit(){  
    $('#loading-wrapper').show();    
    var form_data = new FormData(document.getElementById("myform"));             
    $.ajax({
        url: "{{ route('update-profile') }}",        
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
                window.location = "{{ route('get-profile') }}";
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

function formSubmit(){    
    $('#loading-wrapper').show();    
    var form_data = new FormData(document.getElementById("myform1"));             
    $.ajax({
        url: "{{ route('change-password') }}",        
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
                $('#myform1')[0].reset(); 
            } else {
                printErrorMsg(data.error);
                $(".successmsg").hide();
                $('.print-error-msg').delay(10000).fadeOut(800);
            }
        }
    });
}
</script>
@endpush

@endsection