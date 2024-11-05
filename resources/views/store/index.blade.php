@extends("layouts.app2")

@section("content")

<!-- Page header start -->
<div class="page-header">
    <ol class="breadcrumb">
        <li class="breadcrumb-item">Home</li>
        <li class="breadcrumb-item active">Stores</li>
    </ol>   
    <div>         
    <a href="{{ route('stores.create') }}">
        <button type="button" class="btn btn-primary btn-rounded" onclick="showloader()">Add New</button>
    </a>    
    </div>
</div>
<!-- Page header end -->

<!-- Main container start -->
<div class="main-container">

    @if(Session::has('success'))
        <p class="alert {{ Session::get('alert-class', 'alert-success') }}">{{ Session::get('success') }}</p>
    @endif

    <!-- Row start -->
    <div class="row gutters">                       
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
            <div class="table-container">
                <div class="t-header">Stores</div>
                <div class="alert alert-success alert-block" style="display:none;"></div> 
                <div class="table-responsive">                              
                    <table id="copy-print-csv" class="table custom-table">
                        <thead>                            
                            <tr>
                                <th>S/n</th>                               
                                <th>Store</th>
                                <th>Store Code</th>
                                <th>Store Name</th>
                                <th>Store Type</th>
                                <th>Phone</th>      
                                <th>Created Date</th>                              
                                {{-- <th>Vendor Name</th>  --}}                                
                                <th>Action</th>                                                                                         
                            </tr>
                        </thead>
                        <tbody id="productlist">
                        @if(is_array($stores) && count($stores) > 0)                            
                            @foreach($stores as $key => $store)                                                                                   
                            <tr>                            
                                <td>{{ ($key + 1) }}</td>                         
                                <td>
                                    <img src="{{ url($store->image) }}" height="50px" width="50px">
                                </td>
                                <td>{{ $store->store_code }}</td>
                                <td>{{ $store->store_name }}</td>
                                <td>{{ ($store->store_type == 1) ? 'Parafarmacie':'Parashop' }}</td>
                                <td>{{ $store->phone }}</td>
                                <td>{{ $store->created_at ?? '' }}</td>                                                                                                
                                <td>
                                    <a href="{{ route('stores.show',[$store->store_key]) }}" class="btn btn-primary btn-sm" onclick="showloader()">View</a>   
                                    <a href="{{ route('stores.edit',[$store->store_key]) }}" class="btn btn-dark btn-sm" onclick="showloader()">Edit</a>                                     
                                    @if(Session::get('user_type') == 1)
                                    <form action="{{ route('stores.destroy', $store->store_key) }}" method="POST" onsubmit="return confirm('Are You sure want to delete this ?');" style="display: inline-block;">
                                        <input type="hidden" name="_method" value="DELETE">
                                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                        <input type="submit" class="btn btn-warning btn-sm" value="Delete">
                                    </form> 
                                    @endif                                  
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
<script type="text/javascript">
 $(document).ready(function() {
    $("form").attr('autocomplete', 'off');

    setInterval(function(){ 
        $('.alert').hide();
    }, 3000);				
})
</script>
@endpush   
    
@endsection