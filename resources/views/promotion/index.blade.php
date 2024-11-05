@extends("layouts.app2")

@section("content")

<!-- Page header start -->
<div class="page-header">
    <ol class="breadcrumb">
        <li class="breadcrumb-item">Home</li>
        <li class="breadcrumb-item active">Promotions</li>
    </ol>   
    <div>         
    <a href="{{ route('promotions.create') }}">
        <button type="button" class="btn btn-primary btn-rounded" onclick="showloader()">Add New</button>
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
                <div class="t-header">Promotions and eCoupan</div>
                <div class="alert alert-success alert-block" style="display:none;"></div> 
                <div class="table-responsive">                              
                    <table id="copy-print-csv" class="table custom-table">
                        <thead>                            
                            <tr>
                                <th>S/n</th>                                                                                              
                                <th>Image</th>   
                                <th>Title</th>                                 
                                <th>Created Date</th>                     
                                <th>Action</th>                                                                                         
                            </tr>
                        </thead>
                        <tbody id="productlist">
                        @if(is_array($promotions) && count($promotions) > 0)                            
                            @foreach($promotions as $key => $promotion)                                                                                   
                            <tr>                            
                                <td>{{ ($key + 1) }}</td>                                                                                          
                                <td>
                                    <img src="{{ url($promotion->image) }}" height="50px" width="50px">                                   
                                </td> 
                                <td>{{ $promotion->title }}</td>                                                               
                                <td>{{ $promotion->created_at }}</td>                                                                                                   
                                <td>    
                                    <a href="{{ route('promotions.show',[$promotion->promotion_key]) }}" class="btn btn-primary btn-sm" onclick="showloader()">View</a>                                                                   
                                    <a href="{{ route('promotions.edit',[$promotion->promotion_key]) }}" class="btn btn-dark btn-sm" onclick="showloader()">Edit</a>                                                                         
                                
                                    @if(Session::get('user_type') == 1)
                                        <form action="{{ route('promotions.destroy', $promotion->promotion_key) }}" method="POST" onsubmit="return confirm('Are You sure want to delete this ?');" style="display: inline-block;">
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

@endpush   
    
@endsection