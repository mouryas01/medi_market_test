@extends("layouts.app2")

@section("content")

<!-- Page header start -->
<div class="page-header">
    <ol class="breadcrumb">
        <li class="breadcrumb-item">Home</li>
        <li class="breadcrumb-item active">Orders</li>
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
                    <form class="form-inline" action="{{ route('orderFilterByDate') }}" method="post" autocomplete="off" id="myForm">
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
                <div class="t-header">Orders</div>
                <div class="alert alert-success alert-block" style="display:none;"></div> 
                <div class="table-responsive">            
                    <table id="copy-print-csv-orders" class="table custom-table">
                        <thead>                            
                            <tr>
                                <th>S/n</th>                               
                                <th>Order ID</th>
                                <th>Order Date</th>                                
                                <th>Total Amount</th>   
                                <th>Order Status</th>
                                @if(Session::get('user_type') == 1)  
                                    <th>Store Name</th>
                                @else 
                                    <th>Payment Method</th>    
                                @endif  
                                @if(Session::get('user_type') == 2)                            
                                    <th>Action</th>
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

                              @if(in_array($value->store_id, $storeIds) || Session::get('user_type') == 1)                             
                                <tr>
                                    <td>{{ $sr }}</td>                                                                                                                           
                                    <td>
                                    <a href="{{ route('orders.show', ['customer_id' => $value->customer_id, 'order_key' => $value->order_key]) }}" target="_blank" class="badge badge-primary badge-pill">
                                        {{ $value->order_key }}
                                    </a></td>

                                    <td>{{ $value->order_date.' '. $value->order_time }}</td>
                                    <td>€ {{ $value->total_amount }}</td>                                       
                                    
                                    <td id="orderStatus{{ $value->order_key }}">
                                        @if($value->order_status == 4)
                                            <span>Delivered</span>
                                        @elseif($value->order_status == 3)                                     
                                            <span>Cancelled</span>
                                        @elseif($value->order_status == 2)                            
                                            <span>Shipped</span>
                                        @elseif($value->order_status == 1)                               
                                            <span>Packed</span>
                                        @else                                         
                                            <span>Placed</span>
                                        @endif 
                                    </td>
                                    <td>  
                                        {{-- 1 = ADMIN --}}
                                        @if(Session::get('user_type') == 1)                                              
                                            @php                                            
                                                $storeinfo = OrdersController::get_storeInfo($value->store_id); 
                                                echo !empty($storeinfo['store_name']) ? $storeinfo['store_name']:'';
                                            @endphp 
                                        @else 
                                            @php
                                                $disabled = '';
                                                if($value->order_status == 0 || $value->order_status == 3 || $value->payment_status == 1){
                                                    $disabled = 'Disabled';
                                                }                                                                                        
                                            @endphp
                                            <select id="payment_method{{ $value->order_key }}" onchange="paymentProcess('{{ $value->customer_id }}','{{ $value->order_key }}', this.value);" {{ $disabled }}>
                                                <option value="">Select</option>
                                                <option value="cash" {{ ($value->payment_type == 'cash') ? 'selected':'' }}>Cash</option>
                                                <option value="online" {{ ($value->payment_type == 'online') ? 'selected':'' }}>Online</option>
                                            </select> 
                                        @endif       
                                    </td>

                                    {{-- 2 = Vendor --}}
                                    @if(Session::get('user_type') == 2) 
                                    @php
                                        $orderkey = $value->order_key;
                                        $customer_id = $value->customer_id;
                                    @endphp                                     
                                    <td id="ar_btn{{ $orderkey }}">                                        
                                        <button id="delivered{{ $orderkey }}" class="btn btn-info btn-sm" style="display:none;">Delivered</button>
                                        <button id="rejected{{ $orderkey }}" class="btn btn-warning btn-sm" style="display:none;">Rejected</button>
                                        <button id="approved{{ $orderkey }}" class="btn btn-success btn-sm" style="display:none;">Approved</button>
                                        <button id="shipped{{ $orderkey }}" style="display:none;" onclick="orderStatus('{{ $value->customer_id }}','{{ $value->order_key }}','shipped')" type="button" class="btn btn-primary btn-sm">
                                            Shipped</button>                                         
                                        <button id="reject{{ $orderkey }}" style="display:none;" data-orderid="{{ $value->order_key }}" data-customerid="{{ $value->customer_id }}" type="button" data-target="#rejectModal" data-toggle="modal" class="btn btn-warning btn-sm reject">
                                            Reject</button>
                                        <button id="approve{{ $orderkey }}" style="display:none;" onclick="orderStatus('{{ $value->customer_id }}','{{ $value->order_key }}','approve')" type="button" class="btn btn-primary btn-sm">
                                            Approve</button>

                                        @if($value->order_status == 4)    
                                            <button class="btn btn-info btn-sm">Delivered</button>

                                        @elseif($value->order_status == 3)  
                                            <button class="btn btn-warning btn-sm">Rejected</button> 

                                        @elseif($value->order_status == 1 || $value->order_status == 2)
                                            <button class="btn btn-success btn-sm">Approved</button>                                            
                                            @if($value->order_status != 2)
                                            <button id="mshipped{{ $orderkey }}" type="button" onclick="orderStatus('{{ $customer_id }}','{{ $orderkey }}','shipped')" class="btn btn-primary btn-sm">
                                                Shipped</button>
                                            @endif                                                        
                                        @else
                                            @if($value->order_status == 0)                                                 
                                            <button id="mapprove{{ $orderkey }}" type="button" onclick="orderStatus('{{ $customer_id }}','{{ $orderkey }}','approve')" class="btn btn-primary btn-sm">
                                                Approve</button>         
                                            @endif                                                                                                                                                
                                        @endif  

                                        @if($value->order_status != 3 && $value->order_status != 4)                                                                                               
                                            <button type="button" data-orderid="{{ $orderkey }}" data-customerid="{{ $customer_id }}" data-target="#rejectModal" data-toggle="modal" class="btn btn-warning btn-sm reject">
                                                Reject</button>
                                        @endif                                             
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

                <!-- Reject order Modal -->
                <div class="modal fade" id="rejectModal" role="dialog">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close closebtn" data-dismiss="modal">&times;</button>
                                <h4 class="modal-title"></h4>
                            </div>
                            <div class="modal-body">
                                <div id="cmtform">
                                    <form action="" id="frmComment" method="post">
                                        <div class="form-group">
                                            <label for="comments">Comments</label>
                                            <textarea class="form-control" name="reason" id="reason" col="112" rows="3"></textarea> 
                                            <span id="error_msg"></span>                                           
                                            <input type="hidden" name="order_id" id="order_id">
                                            <input type="hidden" name="customer_id" id="customer_id"> 
                                        </div>                                                    
                                        <button type="submit" name="submit" id="submit" class="btn btn-warning">Reject</button>                        
                                        <img src="{{ asset('assets/img/LoaderIcon.gif') }}" id="modal_loader" style="display:none;" />
                                        <br>
                                    </form>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-default closebtn" data-dismiss="modal">Close</button>
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

    //Set order id and customer id in model
    $('.reject').on('click', function(){  
        $('#reason').val('');       
        $("#cmtform").show();
        var order_id = $(this).data("orderid");
        $("#order_id").val(order_id);
        var customer_id = $(this).data("customerid");
        $("#customer_id").val(customer_id);        
    });   

    //form submit of model
    $("#frmComment").on("submit", function(e){                                
        $('#reason').removeClass("error").css({"border": ""});
        e.preventDefault();            
        var reason = $('#reason').val();  
        var oid = $("#order_id").val();                  
        if (reason == "") {
            $('#reason').addClass("error").css({"border": "1px solid red"});
        }
        $("#error_msg").text("Please enter a reason for cancel this order").css({"color": "red"});
        if(reason){    
            $("#error_msg").hide();        
            $('#modal_loader').show();
            $.ajax({
                type: "POST",
                url: "{{ route('orders.approve') }}",
                data: $(this).serialize(),
                headers: {
                    'X-CSRF-Token': '{{ csrf_token() }}',
                },                
                success: function(data) {                          
                    if($.isEmptyObject(data.error)) {
                        $('.alert').html(data.success).show().delay(2000).fadeOut(800);
                        $('#ar_btn'+oid).empty();
                        $('#ar_btn'+oid).html(data.arbtn);                        
                        $('#orderStatus'+oid).empty();
                        $('#orderStatus'+oid).html(data.ostatus);
                        if(data.status == 3){
                            $('#payment_method'+oid).prop('disabled', true);  
                        } 	                
                    } else {
                        alert(data.error);
                    }                    
                    $('#rejectModal').modal('hide');  
                    $('#modal_loader').hide();							             
                }
            });
        }
    });
});


function paymentProcess(cstid, oid, pmtype){
    if(pmtype == ''){
        alert('Please select payment method');
        return false;
    }
    else
    {
        $('.loading').show();
        formData = {	
            order_id: oid,	
            customer_id:cstid,
            payment_type:pmtype,
        };    
        $.ajax({
            url: "{{ route('orders.payment') }}",
            type: "POST",
            data: formData,
            headers: {
                'X-CSRF-Token': '{{ csrf_token() }}',
            },
            success: function(data) {
                $('.loading').hide();                      
                if($.isEmptyObject(data.error)) {
                    $('.alert').html(data.success).show().delay(2000).fadeOut(800);
                    $('#ar_btn'+oid).empty();
                    $('#ar_btn'+oid).html(data.arbtn);
                    $('#orderStatus'+oid).empty();
                    $('#orderStatus'+oid).html(data.ostatus);
                    $('#payment_method'+oid).prop('disabled', true);	                
                } else {
                    alert(data.error);
                } 			             
            }
        });
    }
}


function orderStatus(cstid, oid, action){
    formData = {        		
        order_id: oid,	
        customer_id:cstid,	
		action:action,
    };  
    $('.loading').show();  
    $.ajax({
        url: "{{ route('orders.approve') }}",
        type: "POST",
        data: formData,
        headers: {
            'X-CSRF-Token': '{{ csrf_token() }}',
        },
        success: function(data) { 
            $('.loading').hide();                         
            if($.isEmptyObject(data.error)) {
                $('.alert').html(data.success).show().delay(2000).fadeOut(800);
                $('#mapprove'+oid).hide();                                                               
                if(data.status == 1){                     
                    $('#approve'+oid).hide();                        
                    $('#approved'+oid).show();                                    
                    $('#shipped'+oid).show();                                        
                    $('#payment_method'+oid).prop('disabled', false);
                }
                if(data.status == 2){                       
                    $('#approve'+oid).hide();  
                    $('#shipped'+oid).hide();                                                          
                    $('#mshipped'+oid).hide();                                      
                    $('#payment_method'+oid).prop('disabled', false);
                }                
                $('#orderStatus'+oid).empty();
                $('#orderStatus'+oid).html(data.ostatus);                           
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
    location.href="{{ route('orders.index') }}";
}

function getds(){
    alert('a');
}
</script>
@endpush   
    
@endsection