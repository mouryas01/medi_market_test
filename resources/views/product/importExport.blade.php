@extends("layouts.app2")

@section("content")

<!-- Page header start -->
<div class="page-header">
    <ol class="breadcrumb">
        <li class="breadcrumb-item">Home</li>
        <li class="breadcrumb-item"><a href="{{ route('products.index') }}">Products</a></li>
        <li class="breadcrumb-item active">Import</li>
    </ol>
</div>
<!-- Page header end -->

<!-- Main container start -->
<div class="main-container">

    <!-- Row start -->
    <div class="row gutters">
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
            <div class="card">

                <!-- <a href="{{ url('downloadExcel/xls') }}"><button class="btn btn-success">Download Excel xls</button></a>
                <a href="{{ url('downloadExcel/xlsx') }}"><button class="btn btn-success">Download Excel xlsx</button></a>
                <a href="{{ url('downloadExcel/csv') }}"><button class="btn btn-success">Download CSV</button></a> -->

                <div class="card-body">
                    <div class="box-header">
                        <h3 class="card-title">Import Excel,CSV and Images</h3>
                    </div>
                    <hr>

                    @if($errors->any())
                        <div class="alert alert-danger">
                            <a href="#" class="close" data-dismiss="alert" aria-label="close">×</a>
                            <ul>
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    
                    @if(Session::has('success'))
                        <div class="alert alert-success">
                            <a href="#" class="close" data-dismiss="alert" aria-label="close">×</a>
                            <p>{{ Session::get('success') }}</p>
                        </div>
                    @endif                    

                    <div class="row gutters">                        
                        <form method="post" action="{{ route('importexcel') }}" enctype="multipart/form-data" 
                            class="form-horizontal" >
                            @csrf 

                            <div class="col-xl-12 col-lglg-12 col-md-12 col-sm-12 col-12">                            
                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label for="inputEmail4">Product</label>
                                        <input type="file" name="product_file" class="form-control" value="{{ old('product_file') }}"/>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="inputPassword4">Product Quentity</label>
                                        <input type="file" name="quentity_file" class="form-control"/>                                    
                                    </div>
                                </div>                            
                            </div>   

                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-6">
                                <button class="btn btn-primary" onclick="showloader()">Import</button>                            
                                <a href="{{ route('products.index') }}" class="btn btn-secondary" onclick="showloader()">
                                    Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Row end -->
</div>
<!-- Main container end -->

@endsection