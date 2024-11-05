@extends("layouts.app2")

@section("content")
@php
    ini_set('max_execution_time', 180); //3 minutes
@endphp
<!-- Page header start -->
<div class="page-header">
    <ol class="breadcrumb">
        <li class="breadcrumb-item">Home</li>
        <li class="breadcrumb-item active">Products</li>
    </ol>   
    <div>    
    <a href="{{ route('importexport') }}" target="_blank">
        <button type="button" class="btn btn-primary btn-rounded">Import CSV</button>
    </a>
    <a href="{{ route('products.create') }}" target="_blank">
        <button type="button" class="btn btn-primary btn-rounded">Add New</button>
    </a>
    </div>
</div>
<!-- Page header end -->

<!-- Main container start -->
<div class="main-container">

    <!-- Row start -->
    <div class="row gutters">                       
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
            <div class="table-container">
                <div class="t-header">Products</div>
                <div class="alert alert-success alert-block" style="display:none;"></div> 
                <div class="table-responsive">                              
                    <table id="copy-print-csv" class="table custom-table">
                        <thead>                            
                            <tr>
                                <th>S/n</th>                               
                                <th>Product</th>
                                <th>Product Code</th>
                                <th>Product Name</th>
                                <th>Product Price</th>
                                <th>Category</th>                                   
                                <th>Action</th>
                                @if(Session::get('user_type') == 1)
                                    <th>Approve / Reject</th>  
                                @endif                               
                            </tr>
                        </thead>
                        <tbody id="productlist">
                        @if(is_array($products) && count($products) > 0)
                            @foreach($products as $key => $product)                            
                            <tr>
                                <td>{{ ($key + 1) }}</td>                         
                                <td><img src="{{ url($product->picture) }}" height="50px" width="50px"></td>
                                <td>{{ $product->product_code }}</td>
                                <td>{{ $product->name }}</td>
                                <td>€ {{ $product->price }}</td>                               
                                <td>{{ $product->category_name }}</td>                                
                                <td>
                                    <a href="{{ route('products.show',[$product->product_key]) }}" target="_blank" class="btn btn-primary btn-sm">View</a> 
                                    <a href="{{ route('products.edit',[$product->product_key]) }}" target="_blank" class="btn btn-dark btn-sm">Edit</a> 
                                    @if(Session::get('user_type') == 1)
                                    <form action="{{ route('products.destroy', $product->product_key) }}" method="POST" onsubmit="return confirm('Are You sure want to delete this ?');" style="display: inline-block;">
                                        <input type="hidden" name="_method" value="DELETE">
                                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                        <input type="submit" class="btn btn-warning btn-sm" value="Delete">
                                    </form>
                                    @endif
                                </td>  
                                @if(Session::get('user_type') == 1)
                                <td id="ar_btn{{ $product->product_key }}">                                    
                                    @if($product->status == 1)
                                        <button class="btn btn-success btn-sm">Approved</button> 
                                    @elseif($product->status == 2)  
                                        <button class="btn btn-warning btn-sm">Rejected</button> 
                                    @else
                                        <button class="btn btn-primary btn-sm" onclick="changeStatus('{{ $product->product_key }}','approve');">
                                        Approve</button> 
                                    @endif
                                    @if($product->status == 0)
                                        <button class="btn btn-warning btn-sm" onclick="changeStatus('{{ $product->product_key }}','reject');">
                                        Reject</button>
                                    @endif                                                                        
                                </td> 
                                @endif                            
                            </tr>
                            @endforeach  
                        @endif                        
                        </tbody>
                    </table>
                </div>
                <div id="test"></div>
            </div>
        </div>
    </div>
    <!-- Row end -->
</div>
<!-- Main container end -->

@push('after-scripts')
<script type="text/javascript">
function changeStatus(pid, action){
    $('#loading-wrapper').show();
    formData = {
        product_id: pid,		
		action:action,
    };
    $.ajax({
        url: "{{ route('products.approve') }}",
        type: "POST",
        data: formData,
        headers: {
            'X-CSRF-Token': '{{ csrf_token() }}',
        },
        success: function(data){ 
            $('#loading-wrapper').hide();                            
            if($.isEmptyObject(data.error)) {
                $('.alert').html(data.success).show().delay(2000).fadeOut(800);
                $('#ar_btn'+pid).empty();
                $('#ar_btn'+pid).html(data.arbtn);	                
            } else {
                alert(data.error);
            }							             
        }
    });
}
</script>
@endpush   
    
@endsection