@extends("layouts.app2")

@section("content")       
<!-- Page header start -->
<div class="page-header">
    <ol class="breadcrumb">
        <li class="breadcrumb-item">Home</li>
        <li class="breadcrumb-item active">Categories</li>
    </ol>
    <div>       
        <a href="{{ route('categories.create') }}" target="_blank">
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
                <div class="t-header">Categories</div>
                <div class="table-responsive">
                
                    <table id="categoryTable" class="table custom-table">
                        <thead>                            
                            <tr>        
                                <th>SN</th>                        
                                <th>Category</th>
                                <th>Category ID</th>
                                <th>Category Name</th>
                                <th>Parent Category</th>
                                <th>No of Products</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="category_tbody">
                        @if(is_array($categories) && count($categories) > 0)                             
                            @foreach($categories as $key => $value)                                                        
                                <tr id="row{{$value->category_id}}">    
                                    <td>{{ $key+1 }}</td>                                
                                    <td>
                                        <img src="{{ url($value->image) }}" height="50px" width="50px">
                                    </td>
                                    <td>{{$value->category_id}}</td>
                                    <td><a href="{{ route('categories.show', $value->category_id) }}">{{ $value->name }}</a></td>
                                    <td>{{ App\Http\Controllers\ProductsController::getCategory($value->parent) }}</td>
                                    <td>{{ $value->no_of_product }}</td>
                                    <td>
                                        <a href="{{ route('categories.show', $value->category_id) }}" target="_blank" class="btn btn-primary btn-sm">View</a>
                                        <a href="{{ route('categories.edit', $value->category_id) }}" target="_blank" class="btn btn-dark btn-sm">Edit</a>
                                        <button onclick="return deleteCategory('{{ $value->category_id }}')" class="btn btn-warning btn-sm">Delete</button>
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
<section class="col-lg-6 connectedSortable new-category hidden">    
</section>
<script type="text/javascript">
$(document).ready(function(){

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


function deleteCategory(catid){    
    var confirmalert = confirm("Are you sure?");
    if (confirmalert == true) {
        $('#loading-wrapper').show();
        // AJAX Request
        $.ajax({
            url: "{{ route('category_delete') }}",
            type: 'POST',
            data: { id:catid },
            headers: {
                'X-CSRF-Token': '{{ csrf_token() }}',
            },
            success: function(data){                    
                $('#loading-wrapper').hide();
                if (data.success) { 
                    // Remove row from HTML Table
                    $('#row'+catid).css('background','tomato');                    
                    $('#row'+catid).fadeOut(800,function(){
                        $(this).remove();
                    });              
                    $(".successmsg").html(data.success).show().delay(3000).fadeOut(800);                               
                } else {
                    $(".successmsg").hide();
                    $('.print-error-msg').html('Invalid ID').delay(10000).fadeOut(800);              
                }                             
            }
        });
    }
}
</script>
@endpush
@endsection