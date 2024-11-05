<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <title>{{ config('app.name', 'medimarket') }}| Log in</title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <!-- Bootstrap 3.3.6 -->
    <link rel="stylesheet" href="{{ asset('bootstrap/css/bootstrap.min.css') }}">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css">
    <!-- Ionicons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="{{ asset('dist/css/Admin.min.css') }}">
    <!-- iCheck -->
    <link rel="stylesheet" href="{{ asset('plugins/iCheck/square/blue.css') }}">       
</head>
<body class="hold-transition login-page">
<div class="login-box">
    <div class="login-logo">
        <img src="{{ asset('assets/img/logo.png') }}" alt="Login" width="100%" height="15%"/>        
        {{-- @include('layouts.messages') --}}
    </div>
    <!-- /.login-logo -->
    <div class="login-box-body">
        <p class="login-box-msg">Reset Password</p>

        <form class="form-horizontal" method="POST" action="{{ route('password.request') }}">
            {{ csrf_field() }}

            {{-- <input type="hidden" name="token" value="{{ $token }}"> --}}

            @if ($errors->has('status'))
                <center><span class="alert-success">
                    <strong>{{ $errors->first('status') }}</strong>
                </span></center><br>
            @endif

            @if (\Session::has('success'))
                <div class="alert alert-success">
                    <ul>
                        <li>{!! \Session::get('success') !!}</li>
                    </ul>
                </div>
            @endif

            <div class="form-group has-feedback{{ $errors->has('email') ? ' has-error' : '' }}">            
                <input id="email" type="email" class="form-control" placeholder="E-Mail Address" name="email" value="{{ $email or old('email') }}" autofocus>
                <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
                @if ($errors->has('email'))
                    <span class="help-block">
                        <strong>{{ $errors->first('email') }}</strong>
                    </span>
                @endif               
            </div>

            <div class="form-group has-feedback{{ $errors->has('password') ? ' has-error' : '' }}">            
                <input id="password" type="password" class="form-control" name="password" placeholder="Password" required>
                @if ($errors->has('password'))
                    <span class="help-block">
                        <strong>{{ $errors->first('password') }}</strong>
                    </span>
                @endif            
            </div>

            <div class="form-group has-feedback{{ $errors->has('confirm_password') ? ' has-error' : '' }}">            
                <input id="confirm_password" type="password" class="form-control" name="confirm_password"  placeholder="Confirm password" required>

                @if ($errors->has('confirm_password'))
                    <span class="help-block">
                        <strong>{{ $errors->first('confirm_password') }}</strong>
                    </span>
                @endif           
            </div>

            <div class="row">                                 
                <div class="col-md-6 col-md-offset-4">
                    <button type="submit" class="btn btn-primary">
                        Reset Password
                    </button>
                </div>                                
            </div>        
            
        </form>
    </div>
    <!-- /.login-box-body -->
</div>
<!-- /.login-box -->

<!-- jQuery 2.2.3 -->
<script src="{{ asset('plugins/jQuery/jquery-2.2.3.min.js') }}"></script>
<!-- Bootstrap 3.3.6 -->
<script src="{{ asset('bootstrap/js/bootstrap.min.js') }}"></script>
<!-- iCheck -->
<script src="{{ asset('plugins/iCheck/icheck.min.js') }}"></script>
</body>
</html>



