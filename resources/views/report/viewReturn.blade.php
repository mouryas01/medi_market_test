@extends("layouts.app2")

@section("content")

<!-- Page header start -->
<div class="page-header">
    <ol class="breadcrumb">
        <li class="breadcrumb-item">Home</li>
        <li class="breadcrumb-item"><a href="{{ route('return-report') }}">Cancel Order</a></li>
        <li class="breadcrumb-item active">View Cancel Order</li>
    </ol>  
</div>
<!-- Page header end -->

<!-- Main container start -->
<div class="main-container">

    <!-- Row start -->
    <div class="row gutters">

        <div class="col-xl-8 col-lg-8 col-md-12 col-sm-12 col-12">
            <div class="table-container">
                <div class="t-header">Cancel Order Items</div>
                <div class="alert alert-success alert-block" style="display:none;"></div> 
                <div class="table-responsive">                                
                    <table class="table custom-table">
                        <thead>                            
                            <tr>
                                <th>Product</th>                               
                                <th>Cart ID</th>
                                <th>Product Code</th>
                                <th>Quentity</th>
                                <th>Price</th>                                                                                  
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
                            </tr>
                            @endforeach  
                        @endif                      
                        </tbody>
                    </table>
                </div>                
            </div>
        </div>

        <div class="col-xl-4 col-lg-4 col-md-12 col-sm-12 col-12">
            <div class="card h-100">
                <div class="card-body">
                    <ul class="list-group">  
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Order Status
                            <span class="badge-pill">{{ $orders['order_status'] }}</span>
                        </li>   
                    </ul>
                              
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
                            Total Products
                            <span class="badge badge-primary badge-pill">{{ $orders['total_items'] }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Total Amount
                            <span class="badge badge-primary badge-pill">€ {{ $orders['total_amount'] }}</span>
                        </li>                             
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Created Date:
                            <span class="badge-pill">{{ $orders['created_at'] }}</span>
                        </li>                                          
                    </ul>                     
                    
                </div>
            </div>
        </div>
    </div>
    <!-- Row end -->

</div>
<!-- Main container end -->

@endsection