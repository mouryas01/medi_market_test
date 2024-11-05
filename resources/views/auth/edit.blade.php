@extends('layouts.app2')

@section('content')

<!-- Page header start -->
<div class="page-header">
    <ol class="breadcrumb">
        <li class="breadcrumb-item">Home</li>
        <li class="breadcrumb-item">Forms</li>
        <li class="breadcrumb-item active">Edit User</li>
    </ol>    
</div>
<!-- Page header end -->

<!-- Main container start -->
<div class="main-container">
    <!-- Row start -->
    <div class="row gutters">

        <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-6">
            <div class="card">                
                <div class="card-body">   
                <h4>Edit User</h4><br>            
                    <form class="form-horizontal" method="POST" action="{{ route('vendors.update', [$id]) }}" enctype="multipart/form-data">
                        {{ method_field('patch') }}
                        {{ csrf_field() }}
                        <div class="box-body">
                            <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                                <label for="name" class="col-md-4 control-label">Name</label>

                                <div class="col-md-12">                                    
                                    <input id="name" type="text" class="form-control" name="name" value="{{ $userInfo['name'] ?? '' }}" autofocus>
                                    @if($errors->has('name'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('name') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                                <label for="email" class="col-md-4 control-label">E-Mail</label>

                                <div class="col-md-12">
                                    <input id="email" type="email" class="form-control" name="email" value="{{ $userInfo['email'] ?? '' }}" autocomplete="off">

                                    @if ($errors->has('email'))
                                        <span class="help-block">
                                                <strong>{{ $errors->first('email') }}</strong>
                                            </span>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                                <label for="password" class="col-md-4 control-label">Password</label>
                                <div class="col-md-12">
                                    <input id="password" type="password" class="form-control" name="password" autocomplete="off">
                                    @if ($errors->has('password'))
                                        <span class="help-block">
                                                <strong>{{ $errors->first('password') }}</strong>
                                            </span>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="confirm_password" class="col-md-4 control-label">Confirm Password</label>

                                <div class="col-md-12">
                                    <input id="confirm_password" type="password" class="form-control" name="confirm_password" autocomplete="off">
                                </div>
                            </div>

                            <div class="form-group{{ $errors->has('picture') ? ' has-error' : '' }}">
                                <label for="picture" class="col-md-4 control-label">Profile Pic</label>

                                <div class="col-md-12">
                                    <input  type="file" onchange="loadFile(event)" name="picture" id="picture" accept="image/x-png,image/gif,image/jpeg" class="form-control">
                                    <input type="hidden" name="old_picture" id="old_picture" class="form-control" value="{{ $userInfo['picture'] }}">
                                    @if ($errors->has('picture'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('picture') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <img src="{{ url($userInfo['picture']) }}" id="uploaded_image" height="50px" width="50px">
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-md-12 col-md-offset-4">
                                    <button type="submit" class="btn btn-primary">Update</button>
                                    <button type="button" class="btn btn-secondary" onclick="history.back();">Back</button>                                    
                                </div>
                            </div>
                        </div>
                    </form>

                </div>
            </div>
        </div>
        
    </div>
    <!-- Row end -->
</div>
<!-- Main container end -->

@push('after-scripts')
<script type="text/javascript">
var loadFile = function(event) {
    var output = document.getElementById('uploaded_image');
    output.src = URL.createObjectURL(event.target.files[0]);
    output.onload = function() {
      URL.revokeObjectURL(output.src) // free memory
    }
};
</script>
@endpush

@endsection

