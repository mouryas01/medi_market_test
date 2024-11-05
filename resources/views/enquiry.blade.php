@extends("layouts.app2")

@section("content")

<!-- Page header start -->
<div class="page-header">
    <ol class="breadcrumb">
        <li class="breadcrumb-item">Home</li>
        <li class="breadcrumb-item active">Enquiries</li>
    </ol>           
</div>
<!-- Page header end -->

<!-- Main container start -->
<div class="main-container">

    <!-- Row start -->
    <div class="row gutters">
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
            <div class="table-container">
                <div class="t-header">Enquiries</div>
                <div class="table-responsive">
                @if(count($enquiries) > 0)
                    <table id="copy-print-csv" class="table custom-table">
                        <thead>                            
                            <tr>
                                <th>S/n</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Message</th>
                                <th>Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($enquiries as $key => $enquiry)                                        
                            <tr>
                                <td>{{ ($key + 1) }}</td>
                                <td>{{ $enquiry->name }} {{-- $enquiry->advice_id --}}</td>
                                <td>{{ $enquiry->email }}</td>
                                <td>{{ $enquiry->message }}</td>
                                <td>{{ $enquiry->created_at }}</td>    
                                <td><button type="button" class="btn btn-primary btn-lg comments" data-postid="{{ $enquiry->advice_id }}" data-toggle="modal" data-target="#myModal">Reply</button></td>              
                            </tr>
                        @endforeach                        
                        </tbody>
                    </table>
                @else
                    <p class="text text-center text-info">No user.</p>
                @endif   
                </div>

                <!-- Modal -->
                <div class="modal fade" id="myModal" role="dialog">
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
                                            <textarea class="form-control" name="reply_message" id="reply_message" col="112" rows="3"></textarea>
                                            <input type="hidden" name="advice_id" id="advice_id" value="{{ $enquiry->advice_id }}">                                                                                                    
                                        </div>                                                    
                                        <button type="submit" name="submit" id="submit" class="btn btn-primary">Reply</button>                        
                                        <img src="{{ asset('assets/img/LoaderIcon.gif') }}" id="modal_loader" width="50px"/>
                                    </form>
                                </div><br>

                                <div id="comment-count">
                                    <span id="count-number"></span> All Comment(s)
                                </div><br>

                                <div id="response">
                                        
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
<style>
#response{
  width: 98%;
  height: 150px;
  background-color: #eee;
  overflow: scroll;
}
#reply_msg{
    margin: 10px;
}
</style>

@push('after-scripts')
<script type="text/javascript">
$(document).ready(function(){

    $("#frmComment").on("submit", function(e){
        $('#reply_message').removeClass("error").css({"border": ""});
        e.preventDefault();
        $(".error").text("");
        var reply_to = $('#reply_to').val();
        var reply_message = $('#reply_message').val();  
        var advice_id = $('#advice_id').val();        
        if (reply_message == ""){
            $('#reply_message').addClass("error").css({"border": "1px solid red"});
            $(".error").text("required");
        }else{
            $('#modal_loader').show();            
            $("#submit").hide();
            $.ajax({
                type: "POST",
                url: "{{ route('enquiry.reply') }}",
                data: $(this).serialize(),
                headers: {
                    'X-CSRF-Token': '{{ csrf_token() }}',
                },
                success: function (response) {                    
                    $("#frmComment textarea").val("");
                    $('#response').append(response);                                        
                    $("#cmtform").hide();
                    $("#submit").show(); 
                    $('#modal_loader').hide();                   
                }
            });            
        }
    });

    $('.comments').on('click', function(){
        $('#response').empty();        
        $("#cmtform").show();
        var advice_id = $(this).data("postid");
        $("#advice_id").val(advice_id);
        $('#modal_loader').show();
        $.ajax({            
            type: "POST",
            url: "{{ route('enquiry.fetch') }}",
            data: {advice_id: advice_id},
            headers:{
                'X-CSRF-Token': '{{ csrf_token() }}',
            },
            success: function (response){                               
                $('#response').html(response);                
                $("#submit").show();
                $('#modal_loader').hide();
            }
        });
    });   
});
</script>
@endpush
@endsection