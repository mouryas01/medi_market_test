@extends('layouts.app2')

@section('content')

<!-- Page header start -->
<div class="page-header">
    <ol class="breadcrumb">
        <li class="breadcrumb-item">Home</li>
        <li class="breadcrumb-item">Forms</li>
        <li class="breadcrumb-item active">Input Fields</li>
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
                
                @if(Session::has('message1'))
                <p id="existmsg" class="alert {{ Session::get('alert-class', 'alert-info') }}">{{ Session::get('message1') }}</p>
                @endif
                
                <h4>Create New User</h4><br>            
                    <form class="form-horizontal" method="POST" action="{{ route('vendors.store') }}" enctype="multipart/form-data">
                        {{ csrf_field() }}
                        <div class="box-body">
                            <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                                <label for="name" class="col-md-4 control-label">Name</label>

                                <div class="col-md-12">
                                    <input id="name" type="text" class="form-control" name="name" value="{{ old('name') }}" autofocus>

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
                                    <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" autocomplete="off">

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
                                    <input id="picture" type="file" class="form-control" name="picture" >
                                    @if ($errors->has('picture'))
                                        <span class="help-block">
                                                <strong>{{ $errors->first('picture') }}</strong>
                                            </span>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="user-type" class="col-md-4 control-label">User Type</label>

                                <div class="col-md-12">                                    
                                    <select name="user_type" id="user-type" class="form-control">
                                        <option value="1">Admin</option>
                                        <option value="2">Vendor</option>                                        
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-md-12 col-md-offset-4">
                                    <button type="submit" class="btn btn-primary">Register</button>
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
<section class="col-lg-6 connectedSortable new-category hidden">    
</section>
<script type="text/javascript">
    $(document).ready(function() {

        $("form").attr('autocomplete', 'off');

		setInterval(function(){ 
			$('#existmsg').hide();
		}, 5000);
    })
</script>
@endpush   

@endsection

