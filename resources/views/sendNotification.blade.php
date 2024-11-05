@extends("layouts.app2")

@section("content")

<!-- Page header start -->
<div class="page-header">
    <ol class="breadcrumb">
        <li class="breadcrumb-item">Home</li>
        <li class="breadcrumb-item active">Send Notification</li>
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
                        <h4 class="card-title">{{ $title }}</h4>                    
                    </div>  
                    <div>                        
                        <div class="alert alert-success successmsg" style="display:none;">
                            <a href="#" class="close" data-dismiss="alert" aria-label="close">×</a>
                        </div>
                        <div class="alert alert-warning print-error-msg" style="display:none;">
                            <a href="#" class="close" data-dismiss="alert" aria-label="close">×</a>
                            <ul></ul>
                        </div>
                        <?php if(!empty($msg)){ ?> 
                        <div class="alert alert-success notificationmsg">{{ $msg }}</div>
                        <?php } ?> 
                    </div>
                    <hr>

                    <form id="myform" method="POST" action="{{ route('notification.send') }}" enctype="multipart/form-data">
                        {{ csrf_field() }}

                        <div class="row">    
                            <div class="col-md-4">                            
                                <div class="card border-primary mb-3" style="max-width: 20rem;">                                    
                                    <label for="title">Customers</label>              
                                    <div class="card-body">                                         
                                        <input type="checkbox" id="ckbCheckAll" />      
                                        <span class="badge badge-primary badge-pill">Select All</span>                                  
                                        <br><br>
                                        @foreach ($customers as $customer) 
                                            <input type="checkbox" name="device_token[]" value="{{ $customer['device_token'] }}" class="checkBoxClass"/> 
                                            {{ $customer['name']}}
                                            @php echo "<br>"; @endphp
                                        @endforeach 
                                    </div>
                                </div>
                            </div>                            
                        
                            <div class="col-md-8">
                                <div class="col-md-8">
                                    <div class="form-group{{ $errors->has('title') ? ' has-error' : '' }}">
                                        <label for="title">Title</label>                                
                                        <input id="title" type="text" class="form-control" name="title" value="{{ old('title') }}" required autofocus>
                                        @if ($errors->has('title'))
                                            <span class="help-block">
                                                <strong>{{ $errors->first('title') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <div class="col-xl-12 col-lglg-12 col-md-12 col-sm-12 col-12">
                                    <div class="form-group{{ $errors->has('description') ? ' has-error' : '' }}">
                                        <label for="description">Message</label>                                
                                        <textarea id="description" class="form-control" name="message" required>{{ old('message') }}</textarea>
                                        @if ($errors->has('message'))
                                            <span class="help-block">
                                                <strong>{{ $errors->first('message') }}</strong>
                                            </span>
                                        @endif
                                    </div>                            
                                </div> 

                                 <div class="col-sm-12">                                                                    
                                    <button type="submit" class="btn btn-primary">Send</button>                                 
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
$(document).ready(function () {
    $("#ckbCheckAll").click(function () {
        $(".checkBoxClass").prop('checked', $(this).prop('checked'));
    });

    setInterval(function(){ 
        $('.notificationmsg').hide();
    }, 5000);
});
</script>
@endpush
@endsection