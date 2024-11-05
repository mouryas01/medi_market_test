@extends("layouts.app2")

@section("content")

<!-- Page header start -->
<div class="page-header">
    <ol class="breadcrumb">
        <li class="breadcrumb-item">Home</li>
        <li class="breadcrumb-item active">Vendor Dashboard</li>
    </ol>    
</div>
<!-- Page header end -->

<!-- Main container start -->
<div class="main-container">

    <!-- Row start -->
    <div class="row gutters">
        <div class="col-xl-4 col-lg-6 col-md-6 col-sm-6 col-12">
            <div class="card">
                <div class="card-header">
                    <div class="card-title">Quick Stats</div>
                </div>
                <div class="card-body">
                    <div class="customScroll5">
                        <div class="quick-analytics">
                            <a href="{{ route('products.index') }}">
                                <i class="icon-shopping-bag1"></i> {{ $total_products }} Products
                            </a>
                            <a href="{{ route('orders.index') }}">                                
                                <i class="icon-shopping-cart1"></i> {{ $total_orders }} Orders
                            </a>
                            <a href="{{ route('return-report') }}">
                                <i class="icon-package"></i> {{ $cancel_orders }} Cancel Orders
                            </a>
                            <a href="{{ route('sales-report') }}">
                                <i class="icon-shopping-bag1"></i>(€ {{ $total_sales }}) Sales   
                            </a>
                            {{-- <a href="#">
                                <i class="icon-share1"></i> 250,000 Images Uploaded
                            </a>
                            <a href="#">
                                <i class="icon-eye1"></i> 870,000 Monthly Visits
                            </a>
                            <a href="#">
                                <i class="icon-bell"></i> 350,500 Tickets Booked
                            </a> --}}                            
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--<div class="col-xl-4 col-lg-6 col-md-6 col-sm-6 col-12">-->
        <!--    <div class="card">-->
        <!--        <div class="card-header">-->
        <!--            <div class="card-title">Activity</div>-->
        <!--        </div>-->
        <!--        <div class="card-body">-->
        <!--            <div class="customScroll5">-->
        <!--                <ul class="project-activity">-->
        <!--                    <li class="activity-list">-->
        <!--                        <div class="detail-info">-->
        <!--                            <p class="date">Today</p>-->
        <!--                            <p class="info">Messages accepted with attachments.</p>-->
        <!--                        </div>-->
        <!--                    </li>-->
        <!--                    <li class="activity-list success">-->
        <!--                        <div class="detail-info">-->
        <!--                            <p class="date">Yesterday</p>-->
        <!--                            <p class="info">Send email notifications of subscriptions and deletions to list owner.</p>-->
        <!--                        </div>-->
        <!--                    </li>-->
        <!--                    <li class="activity-list danger">-->
        <!--                        <div class="detail-info">-->
        <!--                            <p class="date">10th December</p>-->
        <!--                            <p class="info">Required change logs activity reports.</p>-->
        <!--                        </div>-->
        <!--                    </li>-->
        <!--                    <li class="activity-list warning">-->
        <!--                        <div class="detail-info">-->
        <!--                        <p class="date">15th December</p>-->
        <!--                            <p class="info">Strategic partnership plan.</p>-->
        <!--                        </div>-->
        <!--                    </li>-->
        <!--                    <li class="activity-list success">-->
        <!--                        <div class="detail-info">-->
        <!--                            <p class="date">21st December</p>-->
        <!--                            <p class="info">Send email notifications of subscriptions and deletions to list owner.</p>-->
        <!--                        </div>-->
        <!--                    </li>-->
        <!--                    <li class="activity-list danger">-->
        <!--                        <div class="detail-info">-->
        <!--                            <p class="date">25th December</p>-->
        <!--                            <p class="info">Required change logs activity reports.</p>-->
        <!--                        </div>-->
        <!--                    </li>-->
        <!--                    <li class="activity-list warning">-->
        <!--                        <div class="detail-info">-->
        <!--                        <p class="date">28th December</p>-->
        <!--                            <p class="info">Strategic partnership plan.</p>-->
        <!--                        </div>-->
        <!--                    </li>-->
        <!--                </ul>-->
        <!--            </div>-->
        <!--        </div>-->
        <!--    </div>-->
        <!--</div>-->
        
        <!--<div class="col-xl-4 col-lg-12 col-md-12 col-sm-12 col-12">-->
        <!--    <div class="card">-->
        <!--        <div class="card-header">-->
        <!--            <div class="card-title">Order History</div>-->
        <!--        </div>-->
        <!--        <div class="card-body">-->
        <!--            <div class="customScroll5">-->
        <!--                <ul class="user-messages">-->
        <!--                    <li class="clearfix">-->
        <!--                        <div class="customer">AM</div>-->
        <!--                        <div class="delivery-details">-->
        <!--                            <span class="badge badge-primary">Ordered</span>-->
        <!--                            <h5>Aaleyah Malik</h5>-->
        <!--                            <p>We are pleased to inform that the following ticket no. <b>Le Rouge510</b> have been booked.</p>-->
        <!--                        </div>-->
        <!--                    </li>-->
        <!--                    <li class="clearfix">-->
        <!--                        <div class="customer">AS</div>-->
        <!--                        <div class="delivery-details">-->
        <!--                            <span class="badge badge-primary">Delivered</span>-->
        <!--                            <h5>Ali Sayed</h5>-->
        <!--                            <p>The carrier successfully delivered the message to the end user.</p>-->
        <!--                        </div>-->
        <!--                    </li>-->
        <!--                    <li class="clearfix">-->
        <!--                        <div class="customer">ZR</div>-->
        <!--                        <div class="delivery-details">-->
        <!--                            <span class="badge badge-primary">Cancelled</span>-->
        <!--                            <h5>Zaira Raheem</h5>-->
        <!--                            <p>The following describe the status codes and messages states for delivery receipts.</p>-->
        <!--                        </div>-->
        <!--                    </li>-->
        <!--                    <li class="clearfix">-->
        <!--                        <div class="customer">LJ</div>-->
        <!--                        <div class="delivery-details">-->
        <!--                            <span class="badge badge-primary">Returned</span>-->
        <!--                            <h5>Lily Jordan</h5>-->
        <!--                            <p>Status codes and descriptions are returned in the following OpenMarket-specific TLVs When a delivery receipt is received.</p>-->
        <!--                        </div>-->
        <!--                    </li>-->
        <!--                </ul>-->
        <!--            </div>-->
        <!--        </div>-->
        <!--    </div>-->
        <!--</div>-->
    </div>
    <!-- Row end -->

</div>
<!-- Main container end -->

@endsection