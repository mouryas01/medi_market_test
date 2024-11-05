@extends("layouts.app2")

@section("content")

<!-- Page header start -->
<div class="page-header">
    <ol class="breadcrumb">
        <li class="breadcrumb-item">Home</li>
        <li class="breadcrumb-item">Inventory List</li>        
    </ol>  
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
                    <form class="form-inline" action="{{ route('inventoryReportFilterByDate') }}" method="post" autocomplete="off" id="myForm">
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
                <div class="t-header">Inventory List</div>
                <div class="alert alert-success alert-block" style="display:none;"></div> 
                <div class="table-responsive">                                
                    <table id="copy-print-csv" class="table custom-table">
                        <thead>                            
                            <tr>
                                <th>S/n</th>  
                                <th>Product</th>                                                               
                                <th>Product Code</th>
                                <th>Product Name</th>
                                <th>Created On</th>
                                {{-- <th>In Stock</th> --}}
                                <th>Action</th>                                                                                                                                                               
                            </tr>
                        </thead>
                        <tbody id="cartlist">                        
                        @if(is_array($products) && count($products) > 0)
                            @foreach($products as $key => $product)                            
                            <tr>
                                <td>{{ ($key + 1) }}</td>                         
                                <td>
                                    <img src="{{ url($product->picture) }}" height="50px" width="50px">
                                </td>
                                <td>{{ $product->product_code }}</td>
                                <td>{{ $product->name }}</td>
                                <td>{{ $product->created_at ?? '' }}</td>                                
                                {{-- <td>$product->stock</td> --}}
                                <td>
                                    <a href="{{ route('products.show',[$product->product_key]) }}"  class="btn btn-primary btn-sm" target="_blank">View</a>
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

function validate(){
    var startdate = $('#startdate').val();
    var enddate = $('#enddate').val();
    if(startdate == ''){
        alert('Start Date field Required');        
    }
    if(enddate == ''){
        alert('End Date field Required');        
    }

    if(startdate != '' && enddate != ''){
        showloader(); 
        return true;
    }  
    return false; 
}

function resetForm(){
    showloader(); 
    document.getElementById("myForm").reset();
    document.getElementById("startdate").value = '';
    document.getElementById("enddate").value='';	
	location.reload(true); 
    location.href="{{ route('inventory-report') }}";
}
</script>
@endpush   

@endsection