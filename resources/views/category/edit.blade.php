@extends("layouts.app2")

@section("content")       
<!-- Page header start -->
<div class="page-header">
    <ol class="breadcrumb">
        <li class="breadcrumb-item">Home</li>
        <li class="breadcrumb-item"><a href="{{ route('categories.index') }}">Categories</a></li>
        <li class="breadcrumb-item active">Edit</li>
    </ol>
</div>
<!-- Page header end -->

<!-- Main container start -->
<div class="main-container">

	<!-- Row starts -->
    <div class="row gutters">
        <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-6">
            <div class="card">

                <div class="card-header">                   
                    <div class="card-title">Edit category</div>                                           
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

                <div class="card-body">                                          
                        <form id="myform" method="POST" action="{{ route('categories.update',[$id]) }}" enctype="multipart/form-data">
                        <input name="_method" type="hidden" value="PATCH">                                       
                        {{ csrf_field() }}
                        <div class="box-body">
                            <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                                <label for="name" class="col-md-4 control-label">Category</label>
                                <div class="col-md-12">                                      
                                    <input id="name" type="text" class="form-control" name="name" value="{{ $category['name'] }}">                                    
                                    @if ($errors->has('name'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('name') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group{{ $errors->has('parent') ? ' has-error' : '' }}">
                                <label for="parent" class="col-md-4 control-label">Parent Category</label>
                                <div class="col-md-12">                                    
                                    <select id="parent" class="form-control" name="parent">                                        
                                        <option value="0">Select Category</option>
                                        @if(count($categories) > 0)
                                            @foreach($categories as $cate)
                                                @if($cate->category_id == $category['parent'])
                                                <option value="{{ $cate->category_id }}" selected>{{ $cate->name }}</option>
                                                @else
                                                <option value="{{ $cate->category_id }}">{{ $cate->name }}</option>
                                                @endif
                                            @endforeach
                                        @endif
                                    </select>                                    
                                </div>
                            </div>

                            <div class="form-group{{ $errors->has('picture') ? ' has-error' : '' }}">
                                <label for="picture" class="col-md-4 control-label">Category Image</label>
                                <div class="row col-md-12">
                                    <div class="col-md-6">                                                                          
                                        <input type="file" onchange="loadFile(event)" name="picture" id="picture" accept="image/x-png,image/gif,image/jpeg" class="form-control">
                                        <input type="hidden" name="old_picture" id="old_picture" class="form-control" value="{{ $category['image'] }}">
                                        @if ($errors->has('picture'))
                                            <span class="help-block">
                                                <strong>{{ $errors->first('picture') }}</strong>
                                            </span>
                                        @endif                                         
                                    </div>

                                    <div class="col-xl-6">                                                                            
                                        <img id="uploaded_image" width="50px" height="50px" src="{{ url($category['image']) }}">                                                                         
                                    </div>
                                </div>                                
                            </div>
                                                       
                            <div class="form-group">
                                <div class="col-md-6 col-md-offset-4">                                
                                    <button type="button" class="btn btn-primary" onclick="formUSubmit();">Update</button>                                  
                                </div>
                            </div>
                        </div>
                    </form>
                </div>                
            </div>
        </div>
    </div>
    <!-- Row ends -->    
   
</div>
<!-- Main container end -->

@push('after-scripts')
<section class="col-lg-6 connectedSortable new-category hidden">    
</section>
<script type="text/javascript">
    $(document).ready(function() {

        $("form").attr('autocomplete', 'off');

		setInterval(function(){ 
			$('.text-success').hide();
		}, 3000);
		
		var categoryTable = $('#categoryTable').DataTable({
    		dom: 'Bfrtip',
    		buttons: [
    			'copyHtml5',
    			'excelHtml5',
    			'csvHtml5',
    			'pdfHtml5',
    			'print'
    		],
    		'iDisplayLength': 10,
    	});
    })

/* Print validation error on page */
function printErrorMsg(msg) {
    $(".print-error-msg").find("ul").html('');
    $(".print-error-msg").css('display', 'block');
    $.each(msg, function(key, value) {
        $(".print-error-msg").find("ul").append('<li>' + value + '</li>');
    });
}

/* Update Product info */
function formUSubmit(){ 
    $('#loading-wrapper').show();   
    var form_data = new FormData(document.getElementById("myform"));        
    $.ajax({
        url: "{{ route('categories.update',$id) }}",
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
            if ($.isEmptyObject(data.error)) {
                $(".print-error-msg").hide();
                $(".successmsg").html(data.success).show().delay(3000).fadeOut(800);        
                $('#loading-wrapper').hide();
            } else {
                printErrorMsg(data.error);
                $(".successmsg").hide();
                $('.print-error-msg').delay(10000).fadeOut(800);
                $('#loading-wrapper').hide();
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