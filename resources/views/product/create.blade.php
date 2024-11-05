@extends("layouts.app2")

@section("content")

<!-- Page header start -->
<div class="page-header">
    <ol class="breadcrumb">
        <li class="breadcrumb-item">Home</li>
        <li class="breadcrumb-item"><a href="{{ route('products.index') }}">Products</a></li>
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
                        <h4 class="card-title">Add Product</h4>                    
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

                    <form id="myform" method="POST" action="{{ route('products.store') }}" enctype="multipart/form-data">
                        {{ csrf_field() }}

                        <div class="row gutters">
                            <div class="col-xl-6 col-lglg-6 col-md-6 col-sm-6 col-12">
                                <div class="form-group{{ $errors->has('product_code') ? ' has-error' : '' }}">
                                    <label for="product_code">Product Code</label>                                
                                    <input id="product_code" type="text" class="form-control" name="product_code" value="{{ old('product_code') }}" required autofocus>
                                    @if ($errors->has('product_code'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('product_code') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="col-xl-6 col-lglg-6 col-md-6 col-sm-6 col-12">
                                <div class="form-grou{{ $errors->has('name') ? ' has-error' : '' }}p">
                                    <label for="name">Product Name</label>                               
                                    <input id="name" type="text" class="form-control" name="name" value="{{ old('name') }}" required autofocus>
                                    @if ($errors->has('name'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('name') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="col-xl-12 col-lglg-12 col-md-12 col-sm-12 col-12">
                                <div class="form-group{{ $errors->has('description') ? ' has-error' : '' }}">
                                    <label for="description">Product Description</label>                                
                                    <textarea id="description" class="form-control" name="description" required>{{ old('description') }}</textarea>
                                   @if ($errors->has('description'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('description') }}</strong>
                                        </span>
                                    @endif
                                </div>                            
                            </div>                        

                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group{{ $errors->has('price') ? ' has-error' : '' }}">
                                    <label for="price">Product Price</label>
                                    <input id="price" type="text" class="form-control" name="price" value="{{ old('price') }}" required autofocus>
                                    @if ($errors->has('price'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('price') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group{{ $errors->has('sale_price') ? ' has-error' : '' }}">
                                    <label for="sale_price">Product Sale Price</label>
                                    <input id="sale_price" type="text" class="form-control" name="sale_price" value="{{ old('sale_price') }}" required autofocus>
                                    @if ($errors->has('sale_price'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('sale_price') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group{{ $errors->has('vat') ? ' has-error' : '' }}">
                                    <label for="vat">VAT</label>
                                    <input id="vat" type="text" class="form-control" name="vat" value="{{ old('vat') }}" required autofocus>
                                    @if ($errors->has('vat'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('vat') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>                            

                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group{{ $errors->has('category') ? ' has-error' : '' }}">
                                    <label for="category">Category</label>
                                    <select class="form-control" name="category" id="category" onChange="getSubCategory(this.value);">
                                        <option value="">Select Category</option>
                                        @foreach($categories as $value)
                                            <option value="{{ $value->category_id }}">{{ $value->name }} ({{ $value->category_id }})</option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('category'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('category') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group{{ $errors->has('sub_category') ? ' has-error' : '' }}">
                                    <label for="sub_category">Sub Category</label>
                                    <select class="form-control" name="sub_category" id="sub_category" onChange="getSubSubCategory(this.value);">
                                        <option value="0">Select Subcategory</option>                                        
                                    </select>
                                    @if ($errors->has('sub_category'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('sub_category') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>


                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group{{ $errors->has('sub_subcategory') ? ' has-error' : '' }}">
                                    <label for="sub_subcategory">Sub SubCategory</label>
                                    <select class="form-control" name="sub_subcategory" id="sub_subcategory">
                                        <option value="0">Select Sub Subcategory</option>                                        
                                    </select>
                                    @if ($errors->has('sub_subcategory'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('sub_subcategory') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group{{ $errors->has('supplier') ? ' has-error' : '' }}">
                                    <label for="supplier">Product Supplier</label>
                                    <select class="form-control" name="supplier[]" id="supplier" required autofocus multiple="multiple">
                                        <option value="">Select Supplier</option>
                                        @foreach($stores as $key => $value)
                                            <!-- If vendor -->
                                            @if(Session::get('user_type') == 2)
                                                @if(Session::get('uid') == $value['vendor_id'])
                                                    <option value="{{ $value['store_code'] }}">({{ $value['store_code'] }}) {{ $value['store_name'] }}</option>
                                                @endif
                                            @else  
                                            <!-- Admin -->
                                                <option value="{{ $value['store_code'] }}">({{ $value['store_code'] }}) {{ $value['store_name'] }}</option>
                                            @endif  
                                        @endforeach
                                    </select>                                    
                                    @if ($errors->has('supplier'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('supplier') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group{{ $errors->has('stock') ? ' has-error' : '' }}">
                                    <label for="stock">In Stock</label>
                                    <input id="stock" type="text" class="form-control" name="stock" value="{{ old('stock') }}" required autofocus>
                                    @if ($errors->has('stock'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('stock') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                            

                            {{-- <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group{{ $errors->has('picture') ? ' has-error' : '' }}">
                                    <label for="picture">Picture</label>
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
                            </div> --}}
                            
                            <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 col-12">                                                                    
                                <button type="button" class="btn btn-primary" onclick="formSubmit();">Add</button> 
                                <a href="{{ route('products.index') }}" class="btn btn-secondary" onclick="showloader()"> Cancel</a>                                  
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

/* Update Product info */
function formSubmit(){ 
    $('#loading-wrapper').show();   
    var form_data = new FormData(document.getElementById("myform"));        
    $.ajax({
        url: "{{ route('products.store') }}",
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
            //console.log(data);
            $('#loading-wrapper').hide();             
            if ($.isEmptyObject(data.error)) {               
                $(".print-error-msg").hide();
                $(".successmsg").html(data.success).show().delay(3000).fadeOut(800);
                $("#uploaded_image").html(data.uploaded_image).hide();                
                $('#myform')[0].reset();                            
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


function getSubCategory(val) {    
    $('#loading-wrapper').show();
    formData = {
        category: val,
    };
    $.ajax({
        url: "{{ route('subcategory') }}",
        type: "POST",
        data: formData,
        headers: {
            'X-CSRF-Token': '{{ csrf_token() }}',
        },
        success: function(data) {
           $('#loading-wrapper').hide();
           $('#sub_category').empty();
           $('#sub_category').html(data);
        }
    });
}


function getSubSubCategory(val) { 
    $('#loading-wrapper').show();   
    formData = {
        category: val,
    };
    $.ajax({
        url: "{{ route('sub-subcategory') }}",
        type: "POST",
        data: formData,
        headers: {
            'X-CSRF-Token': '{{ csrf_token() }}',
        },
        success: function(data) {
           $('#loading-wrapper').hide();
           $('#sub_subcategory').empty();
           $('#sub_subcategory').html(data);
        }
    });
}
</script>
@endpush
@endsection