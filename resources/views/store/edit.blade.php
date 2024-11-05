@extends("layouts.app2")

@section("content")

<!-- Page header start -->
<div class="page-header">
    <ol class="breadcrumb">
        <li class="breadcrumb-item">Home</li>
        <li class="breadcrumb-item"><a href="{{ route('products.index') }}">Store</a></li>
        <li class="breadcrumb-item active">Edit</li>
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
                        <h4 class="card-title">Edit Store</h4>                    
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

                    <form id="myform" method="POST" action="" enctype="multipart/form-data">
                        {{ csrf_field() }}
                        <input name="_method" type="hidden" value="PATCH">
                        <div class="row gutters">
                            <div class="col-xl-4 col-lglg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group{{ $errors->has('store_code') ? ' has-error' : '' }}">
                                    <label for="store_code">Store Code</label>                                
                                    <input id="store_code" type="text" class="form-control" name="store_code" value="{{ $stores['store_code'] }}" required autofocus>
                                    @if ($errors->has('store_code'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('store_code') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                               
                            <div class="col-xl-4 col-lglg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-grou{{ $errors->has('store_name') ? ' has-error' : '' }}p">
                                    <label for="store_name">Store Name</label>                               
                                    <input id="store_name" type="text" class="form-control" name="store_name" value="{{ $stores['store_name'] }}" required autofocus>
                                    @if ($errors->has('store_name'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('store_name') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group{{ $errors->has('price') ? ' has-error' : '' }}">
                                    <label for="store_type">Store Type</label>                                    
                                    <select name="store_type" id="store_type" class="form-control" required autofocus>                                         
                                        <option value='1' {{ ($stores['store_type'] == 1) ? 'selected':'' }}>Parafarmacie</option>
                                        <option value='2' {{ ($stores['store_type'] == 2) ? 'selected':'' }}>Parashop</option>
                                    </select> 
                                    @if ($errors->has('store_type'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('store_type') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>

                                                 

                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group{{ $errors->has('vendor') ? ' has-error' : '' }}">
                                    <label for="vendor">Vendor</label>
                                    <select name="vendor" id="vendor" class="form-control" required autofocus>
                                        <option value=''>Select Vendor</option>
                                        @foreach($users as $key => $value)
                                            <!-- If vendor -->
                                            @if(Session::get('user_type') == 2)                                                 
                                                 @if(Session::get('uid') == $key)                                              
                                                    <option value='{{ $key }}' selected>{{ $value['name'] }}</option>
                                                 @endif                                                 
                                            @else
                                            <!-- Admin -->
                                                @if($key == $stores['vendor_id'])
                                                    <option value='{{ $key }}' selected>{{ $value['name'] }}</option>
                                                @else
                                                    <option value='{{ $key }}'>{{ $value['name'] }}</option>
                                                @endif 
                                            @endif       
                                        @endforeach                                        
                                    </select>                                    
                                    @if ($errors->has('vendor'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('vendor') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group{{ $errors->has('phone') ? ' has-error' : '' }}">
                                    <label for="phone">Contact No.</label>
                                    <input id="phone" type="text" class="form-control" name="phone" value="{{ $stores['phone'] }}" maxlength="10" required autofocus>
                                    @if ($errors->has('phone'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('phone') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group{{ $errors->has('payment_link') ? ' has-error' : '' }}">
                                    <label for="payment_link">Payment Link</label>
                                    <input id="payment_link" type="text" class="form-control" name="payment_link" value="{{ $stores['paylink'] }}">
                                    @if ($errors->has('phone'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('payment_link') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>


                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12 col-12">
                                <div class="form-group{{ $errors->has('address') ? ' has-error' : '' }}">
                                    <label for="address">Store Address</label>                                
                                    <textarea id="address" class="form-control" name="address" required>{{ $stores['address'] }}</textarea>
                                   @if ($errors->has('address'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('address') }}</strong>
                                        </span>
                                    @endif
                                </div>                            
                            </div>   


                            <div class="col-xl-2 col-lg-2 col-md-2 col-sm-6 col-6">
                                <div class="form-group{{ $errors->has('latitude') ? ' has-error' : '' }}">
                                    <label for="latitude">Latitude</label>  
                                    <input id="latitude" type="text" class="form-control" name="latitude" value="{{ $stores['latitude'] }}">                                                                  
                                   @if ($errors->has('latitude'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('latitude') }}</strong>
                                        </span>
                                    @endif
                                </div>                            
                            </div> 

                            <div class="col-xl-2 col-lg-2 col-md-2 col-sm-6 col-6">
                                <div class="form-group{{ $errors->has('longitude') ? ' has-error' : '' }}">
                                    <label for="longitude">Longitude</label> 
                                    <input id="longitude" type="text" class="form-control" name="longitude" value="{{ $stores['logitude'] }}">                                                                   
                                   @if ($errors->has('longitude'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('longitude') }}</strong>
                                        </span>
                                    @endif
                                </div>                            
                            </div>


                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">   
                                <label for="payment_link">Select Option</label>                             
                                <div class="form-group">
                                    <input type="checkbox" name="option[]" value="deliverto" class="checkBoxClass checkbox" {{ ($stores['deliverto'] == 1) ? 'checked':'' }} /> Deliver to
                                    <br>
                                    <input type="checkbox" name="option[]" value="self_pickup" class="checkBoxClass checkbox" {{ ($stores['selfpickup'] == 1) ? 'checked':'' }}/> Self Pickup                                 
                                </div>
                            </div>

                        
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group{{ $errors->has('picture') ? ' has-error' : '' }}">
                                    <label for="picture">Store Picture</label>
                                    <input  type="file" onchange="loadFile(event)" name="picture" id="picture" accept="image/x-png,image/gif,image/jpeg" class="form-control">
                                    <input type="hidden" name="old_picture" id="old_picture" class="form-control" value="{{ $stores['image'] }}">
                                    @if ($errors->has('picture'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('picture') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                            
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <img src="{{ url($stores['image']) }}" id="uploaded_image" height="50px" width="50px">
                                </div>
                            </div>
                            
                            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">                                    
                                <button type="button" class="btn btn-primary" onclick="formUSubmit('{{ $id }}');">Update</button>                                    
                                <a href="{{ route('stores.index') }}" class="btn btn-secondary" onclick="showloader()"> Cancel</a> 
                            </div>
                        </div>         
                                   
                    </form>
                    </div id="test"></div>
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

/* Update store info */
function formUSubmit(){ 
    $('#loading-wrapper').show();      
    var form_data = new FormData(document.getElementById("myform"));        
    $.ajax({
        url: "{{ route('stores.update',$id) }}",
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
                window.location = "{{ route('stores.index') }}";
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
    output.onload = function() {
      URL.revokeObjectURL(output.src) // free memory
    }
};

</script>
@endpush
@endsection