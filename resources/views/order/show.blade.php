@extends("layouts.app2")

@section("content")

<!-- Page header start -->
<div class="page-header">
    <ol class="breadcrumb">
        <li class="breadcrumb-item">Home</li>
        <li class="breadcrumb-item"><a href="{{ route('orders.index') }}">Orders</a></li>
        <li class="breadcrumb-item active">View Order</li>
    </ol>  

    {{-- <button class="btn btn-secondary" onclick="history.back();">Back</button> --}}
</div>
<!-- Page header end -->

<!-- Main container start -->
<div class="main-container">

@php
use App\Http\Controllers\OrdersController;
@endphp

    <!-- Row start -->
    <div class="row gutters">

        <div class="col-xl-8 col-lg-8 col-md-12 col-sm-12 col-12">
            <div class="table-container">
                <div class="t-header">Order Items</div>
                <div class="alert alert-success alert-block" style="display:none;"></div> 
                <div class="table-responsive">                                
                    <table class="table custom-table">
                        <thead>                            
                            <tr>
                                <th>Product</th>                               
                                <th>Cart ID</th>
                                <th>Product Code</th>
                                <th>Quantity</th>
                                <th>Price</th>        
                                <th>Total</th>                                                                             
                            </tr>
                        </thead>
                        <tbody id="cartlist">                        
                        @if(is_array($orders['cart_items']) && count($orders['cart_items']) > 0)                        
                            @foreach($orders['cart_items'] as $cart_key => $value)                                                             
                            <tr>
                                <td><img src="{{ asset($value['image']) }}" width="50px" height="50px"></td>  
                                <td>{{ $value['cart_id'] }}</td>                                                                                                                         
                                <td>{{ $value['product_code'] }}</td>
                                <td>{{ $value['quantity'] }}</td>     
                                <td>€ {{ $value['sale_price'] }}</td>  
                                <td>€ {{ $value['quantity'] * $value['sale_price'] }}</td>                                                                                                                                                                                                                          
                            </tr>                                
                            @endforeach  
                            <tr>
                                <td colspan="5">Total Amount</td>                                  
                                <td>€ {{ $orders['total_amount'] }}</td>                                                                                                                                                                                                                          
                            </tr> 
                        @endif                      
                        </tbody>
                    </table>
                </div>                
            </div>
        </div>

        <div class="col-xl-4 col-lg-4 col-md-12 col-sm-12 col-12">
            <div class="card h-100">
                <div class="card-body"> 
                    <div>
                        @if(!empty($orders['payment_status']) && $orders['payment_status']  == 1)
                            <table class="table custom-table">                    
                                <tr><td>Delivery Status</td></tr>
                                <tr>
                                    <td><b>Delivered to : </b>{{ !empty($orders['address_info']['name']) ? ucwords($orders['address_info']['name']):$orders['address_info'] }} <br>
                                        <b>Payment method : </b>{{ ucwords($orders['payment_type']) }}<br>
                                        @if(!empty($orders['address_info']['name']))
                                        <b>Address : </b><br>
                                            {{ !empty($orders['address_info']['address']) ? ucwords($orders['address_info']['address']):'' }}, 
                                            {{ !empty($orders['address_info']['area']) ? ucwords($orders['address_info']['area']):'' }},  <br>
                                            {{ !empty($orders['address_info']['landmark']) ? ucwords($orders['address_info']['landmark']):'' }}, <br>
                                            {{ !empty($orders['address_info']['city']) ? ucwords($orders['address_info']['city']):'' }},
                                            {{ !empty($orders['address_info']['state']) ? ucwords($orders['address_info']['state']):'' }}                                
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        @endif                        
                    </div>

                    <ul class="list-group">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Order ID
                            <span class="badge-pill">{{ $orders['order_key'] }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Order Date
                            <span class="badge badge-primary badge-pill">{{ $orders['order_date'].' '.$orders['order_time']  }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Order Status
                            @php
                            $order_status = '';
                            if(!empty($orders['order_status'])){
                                if($orders['order_status'] == 4){
                                    $order_status = 'Delivered'; 
                                }elseif($orders['order_status'] == 3){
                                    $order_status = 'Cancelled';
                                }elseif($orders['order_status'] == 2){
                                    $order_status = 'Shipped';
                                }elseif($orders['order_status'] == 1){
                                    $order_status = 'Packed';
                                }else{
                                    $order_status = 'Placed';
                                }
                            }   
                            @endphp
                            <span class="badge badge-primary badge-pill">{{ $order_status }}</span>
                        </li>

                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Store Name
                            @php                                            
                            $storeinfo = OrdersController::get_storeInfo($orders['store_id']); 
                            $store_name = !empty($storeinfo['store_name']) ? $storeinfo['store_name']:'';
                            @endphp
                            <span class="badge badge-primary badge-pill">{{ ucwords($store_name) }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Total Products
                            <span class="badge badge-primary badge-pill">{{ $orders['total_items'] }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Total Amount
                            <span class="badge badge-primary badge-pill">€ {{ $orders['total_amount'] }}</span>
                        </li> 
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Delivery Address
                            <span class="badge badge-primary badge-pill">
                            @if(!empty($orders['address_info']['name']))                            
                                 {{ !empty($orders['address_info']['address']) ? ucwords($orders['address_info']['address']):'' }}, 
                                {{ !empty($orders['address_info']['area']) ? ucwords($orders['address_info']['area']):'' }},  <br>
                                {{ !empty($orders['address_info']['landmark']) ? ucwords($orders['address_info']['landmark']):'' }}, <br>
                                {{ !empty($orders['address_info']['city']) ? ucwords($orders['address_info']['city']):'' }},
                                {{ !empty($orders['address_info']['state']) ? ucwords($orders['address_info']['state']):'' }}                                  
                            @endif
                            </span>
                        </li>                                                                                            
                    </ul> 

                    @if(!empty($orders['reason']) && $orders['order_status']  == 3)
                        <table class="table custom-table">                    
                            <tr><td>Cancel Reason</td></tr>
                            <tr>
                                <td>{{ $orders['reason'] ?? '' }}</td>
                            </tr>
                        </table>
                    @endif
                    
                </div>
            </div>
        </div>
    </div>
    <!-- Row end -->

</div>
<!-- Main container end -->

@endsection