@extends("layouts.app2")

@section("content")

<!-- Page header start -->
<div class="page-header">
    <ol class="breadcrumb">
        <li class="breadcrumb-item">Home</li>
        <li class="breadcrumb-item active">Menu Permission</li>
    </ol>           
</div>
<!-- Page header end -->

<!-- Main container start -->
<div class="main-container">

    <!-- Row start -->
    <div class="row gutters">
        <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12 col-12">
            <div class="card">
                <div class="card-body">
                    <div class="box-header">
                        <h3 class="card-title">{{ $title }}</h3>                    
                    </div>  
                    <div>                        
                        <div class="alert alert-success successmsg" style="display:none;">
                            <a href="#" class="close" data-dismiss="alert" aria-label="close">×</a>
                        </div>
                        <div class="alert alert-warning print-error-msg" style="display:none;">
                            <a href="#" class="close" data-dismiss="alert" aria-label="close">×</a>
                            <ul></ul>
                        </div>
                        <?php if(!empty($msg)){ ?> 
                        <div class="alert alert-success notificationmsg">{{ $msg }}</div>
                        <?php } ?> 
                    </div>
                    <hr>

                    <form id="myform" method="POST" action="{{-- route('setpermission') --}}">
                        {{ csrf_field() }}

                        <div class="row">    
                            <div class="col-md-12">                            
                                <div class="card border-primary mb-3" style="max-width: 20rem;">                                    
                                    {{-- <label for="title"></label>      --}}

                                    @if($errors->has('menu_id'))
                                        <div class="alert alert-warning">
                                            <a href="#" class="close" data-dismiss="alert" aria-label="close">×</a>                                                                                        
                                            {{ $errors->first('menu_id') }}                                                                                 
                                        </div>
                                    @endif

                                    @if(!empty($message))
                                        <div class="alert alert-success" style="display:none;">
                                            <a href="#" class="close" data-dismiss="alert" aria-label="close">×</a>
                                            {{ $message }}
                                        </div>
                                    @endif
                                                                         
                                    <div class="card-body">                                         
                                        <h4><u>App Menu</u>
                                            {{-- <input type="checkbox" id="ckbCheckAll" />      
                                            <span class="badge badge-primary badge-pill">Select All</span>  --}}
                                        </h4> 
                                        <br>                                        
                                        @php
                                        function compareById($a, $b) 
                                        {                                            
                                            $aid = $a['id'];
                                            $bid = $b['id'];
                                            if ($aid > $bid) 
                                                return 1; 
                                            else if ($aid < $aid)  
                                                return -1; 
                                            else
                                                return 0; 
                                        } 
                                        usort($menus, "compareById");                                                                          
                                        @endphp                                                                                

                                        @foreach ($menus as $minfo) 
                                            <h5>
                                                <input type="checkbox" name="menu_id[]" data-id="{{ $minfo->menu_key }}" class="checkBoxClass checkbox" {{ ($minfo->status == 0) ? 'checked':'' }}/> 
                                                {{ $minfo->menu}}
                                            </h5>
                                            <br>
                                        @endforeach                                                                                
                                    </div>
                                </div>

                                 <div class="col-sm-12">                                                                    
                                    <button type="button" class="btn btn-primary" onclick="admenu('activate');">Activate</button> 
                                    <button type="button" class="btn btn-primary" onclick="admenu('deactivate');">De-Activate</button>                                
                                </div>

                                 <hr>
                                 <label for="title"> Note :                                       
                                    <input type="checkbox" class="checkBoxClass"/> Activated   &nbsp;&nbsp; 
                                    <input type="checkbox" class="checkBoxClass" checked/> Deactivated
                                 </label> 
                            </div>                            
                                                                           
                    </form>
                </div>               
            </div>
        </div>
    </div>
    <!-- Row end -->
     <div id ="test"></div>
</div>
<!-- Main container end -->


@push('after-scripts')
<script type="text/javascript">
$(document).ready(function () {
    $("#ckbCheckAll").click(function () {
        $(".checkBoxClass").prop('checked', $(this).prop('checked'));
    });

    setInterval(function(){ 
        $('.notificationmsg').hide();
    }, 5000);    
});

function admenu(action){    
    var idsArr = [];  
    $(".checkbox:checked").each(function() {  
        idsArr.push($(this).attr('data-id'));
    });  

    if(idsArr.length <=0)  
    {  
        if(action == 'activate'){
            alert("Please select atleast one menu to activate.");
        }else{
            alert("Please select atleast one menu to deactivate.");
        }  
    }  
    else
    {              
        if(confirm("Are you sure, you want to activate the selected menus ?")){  
            $('.loading').show();
            var strIds = idsArr.join(","); 
            $.ajax({
                url: "{{ route('setpermission') }}",
                type: 'POST',
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                data: 'ids='+strIds+'&action='+action,
                success: function (data) {                        
                    if (data['status']==true) {  
                        $(".checkbox:checked").each(function() {                              
                            if(action == 'activate'){
                                $(this).prop('checked', false);
                            }else{
                                $(this).prop('checked', true);
                            }                            
                        });                          
                        alert(data['message']);
                        $('.loading').hide();
                    } else {
                        alert('Whoops Something went wrong!!');
                    }
                },
                error: function (data) {
                    alert(data.responseText);
                }
            });
        } 
    }  
   
}
</script>
@endpush
@endsection