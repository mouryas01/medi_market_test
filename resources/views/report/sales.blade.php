@extends("layouts.app2")

@section("content")

<!-- Page header start -->
<div class="page-header">
    <ol class="breadcrumb">
        <li class="breadcrumb-item">Home</li>
        <li class="breadcrumb-item active">Sales Report</li>
    </ol>   
</div>
<!-- Page header end -->

<!-- Main container start -->
<div class="main-container">

@php
use App\Http\Controllers\OrdersController;
@endphp

    <!-- Row start -->
    <div class="row gutters">                       
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
        <div class="table-container">
            <section class="panel">
                <div class="t-header">Search By</div>
                <div class="panel-body">
                    <form class="form-inline" action="{{ route('salesReportFilterByDate') }}" method="post" autocomplete="off" id="myForm">
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
                <div class="t-header">Sales Report</div>
                <div class="alert alert-success alert-block" style="display:none;"></div> 
                <div class="table-responsive">
                
                    <table id="copy-print-csv" class="table custom-table">
                        <thead>                            
                            <tr>
                                <th>S/n</th>                               
                                <th>Order ID</th>
                                <th>Order Date</th>
                                <th>Total Items</th>
                                <th>Total Amount</th>
                                @if(Session::get('user_type') == 1)                                                   
                                <th>Store Name</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody id="productlist">
                        @if(is_array($orders) && count($orders) > 0)

                            @php
                                $sr = 1;
                                // sort array with given user-defined function
                                function compareByTimeStamp($a, $b) 
                                { 
                                    $aorder_datetime = $a['order_date'].' '.$a['order_time'];
                                    $border_datetime = $b['order_date'].' '.$b['order_time'];
                                    if (strtotime($aorder_datetime) < strtotime($border_datetime)) 
                                        return 1; 
                                    else if (strtotime($aorder_datetime) > strtotime($border_datetime))  
                                        return -1; 
                                    else
                                        return 0; 
                                } 
                                usort($orders, "compareByTimeStamp"); 
                            @endphp

                            @foreach($orders as $key => $value)
                                @if($value->order_status == 4)    {{-- 4 = Delivered --}}                    
                                <tr>
                                    <td>{{ $sr }}</td>                                                                                                                           
                                    <td>
                                    <a href="{{ route('orders.show', ['customer_id' => $value->customer_id, 'order_key' => $value->order_key]) }}" target="_blank" class="badge badge-primary badge-pill">
                                        {{ $value->order_key }}
                                    </a></td>

                                    <td>{{ $value->order_date.' '. $value->order_time }}</td>
                                    <td>{{ $value->total_items }}</td>                                       
                                    <td>€ {{ $value->total_amount }}</td>                                                                          
                                    @if(Session::get('user_type') == 1)  {{-- 1 = ADMIN --}}
                                    <td>                                             
                                        @php                                            
                                            $storeinfo = OrdersController::get_storeInfo($value->store_id); 
                                            echo !empty($storeinfo['store_name']) ? ucwords($storeinfo['store_name']):'';
                                        @endphp                                                                                                                
                                    </td>
                                    @endif  
                                </tr>
                                @php $sr++; @endphp
                                @endif
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

function orderStatus(uid, oid, action){
    formData = {
        user_id: uid,		
        order_id: oid,		
		action:action,
    };    
    $.ajax({
        url: "{{ route('orders.approve') }}",
        type: "POST",
        data: formData,
        headers: {
            'X-CSRF-Token': '{{ csrf_token() }}',
        },
        success: function(data) {                          
            if($.isEmptyObject(data.error)) {
                $('.alert').html(data.success).show().delay(2000).fadeOut(800);
                $('#ar_btn'+oid).empty();
                $('#ar_btn'+oid).html(data.arbtn);	                
            } else {
                alert(data.error);
            }							             
        }
    });
}

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
    location.href="{{ route('sales-report') }}";
}
</script>
@endpush   
    
@endsection