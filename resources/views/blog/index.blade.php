@extends("layouts.app2")

@section("content")

<!-- Page header start -->
<div class="page-header">
    <ol class="breadcrumb">
        <li class="breadcrumb-item">Home</li>
        <li class="breadcrumb-item active">Blogs</li>
    </ol>   
    <div>         
    <a href="{{ route('blogs.create') }}">
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
                <div class="t-header">Blogs</div>
                <div class="alert alert-success alert-block" style="display:none;"></div> 
                <div class="table-responsive">                              
                    <table id="copy-print-csv" class="table custom-table">
                        <thead>                            
                            <tr>
                                <th>S/n</th>  
                                {{-- <th>Image</th> --}}
                                <th>Title</th>                                    
                                {{-- <th>Description</th> --}}
                                <th>Created Date</th>                      
                                <th>Action</th>                                                                                         
                            </tr>
                        </thead>
                        <tbody id="productlist">
                        @if(is_array($blogs) && count($blogs) > 0)                            
                            @foreach($blogs as $key => $blog)                                                                                   
                            <tr>                            
                                <td>{{ ($key + 1) }}</td>                         
                                {{-- <td><a href="{{ route(blogs.show',[$blog->blog_key]) }}" onclick="showloader()">
                                    <img src="{{ url($blog->image) }}" height="50px" width="50px">
                                </a></td> --}}
                                <td>{{ $blog->title }}</td>                                                                 
                                {{-- <td>{{ $blog->description }}</td>  --}}
                                <td>{{ date('Y-m-d h:i:s a',strtotime($blog->created_at)) }}</td>                                                                                                    
                                <td>           
                                    <a href="{{ route('blogs.show',[$blog->blog_key]) }}" class="btn btn-primary btn-sm" onclick="showloader()">View</a>                                                                                                                                      
                                    <a href="{{ route('blogs.edit',[$blog->blog_key]) }}" class="btn btn-dark btn-sm" onclick="showloader()">Edit</a>                                                                         
                                   
                                    @if(Session::get('user_type') == 1)
                                        <form action="{{ route('blogs.destroy', $blog->blog_key) }}" method="POST" onsubmit="return confirm('Are You sure want to delete this ?');" style="display: inline-block;">
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
</script>
@endpush   
    
@endsection