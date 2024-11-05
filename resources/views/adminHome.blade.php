@extends("layouts.app2")

@section("content")

<!-- Page header start -->
<div class="page-header">
    <ol class="breadcrumb">
        <li class="breadcrumb-item">Home</li>
        <li class="breadcrumb-item active">Admin Dashboard</li>
    </ol>

    {{-- <ul class="app-actions">
        <li>
            <a href="#" id="reportrange">
                <span class="range-text"></span>
                <i class="icon-chevron-down"></i>	
            </a>
        </li>
        <li>
            <a href="#">
                <i class="icon-export"></i>
            </a>
        </li>
    </ul> --}}
</div>
<!-- Page header end -->

<!-- Main container start -->
<div class="main-container">

    <!-- Row start -->
    <div class="row gutters">
        <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6 col-12">
            <div class="info-stats4">
                <div class="info-icon">
                    <i class="icon-eye1"></i>
                </div>
                <a class="sale-num" href="{{ route('vendors.index') }}">
                    <h3>{{ $vendors }}</h3>
                    <p>Vendors</p>
                </a>
            </div>
        </div>
        <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6 col-12">
            <div class="info-stats4">
                <div class="info-icon">
                    <i class="icon-activity"></i>
                </div>
                <a class="sale-num" href="{{ route('inventory-report') }}">
                    <h3>{{ $product }}</h3>
                    <p>Total Inventory</p>
                </a>
            </div>
        </div>
        <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6 col-12">
            <div class="info-stats4">
                <div class="info-icon">
                    <i class="icon-shopping-cart1"></i>
                </div>
                <a class="sale-num" href="{{ route('sales-report') }}">
                    <h3>€ {{ $total_sales }}</h3>
                    <p>Total Sales</p>
                </a>
            </div>
        </div>
        <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6 col-12">
            <div class="info-stats4">
                <div class="info-icon">
                    <i class="icon-shopping-bag1"></i>
                </div>
                <a class="sale-num" href="{{ route('return-report') }}">
                    <h3>{{ $cancel_orders }}</h3>
                    <p>Cancel Orders</p>
                </a>
            </div>
        </div>
        
    </div>
    <!-- Row end -->

    <!-- Row start -->
    {{-- <div class="row gutters">
        <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6 col-12">
            <div class="info-stats4">
                <div class="info-icon">
                    <i class="icon-eye1"></i>
                </div>
                <div class="sale-num">
                    <h3>{{ $users }}</h3>
                    <p>Customers</p>
                </div>
            </div>
        </div>                
    </div> --}}
    <!-- Row end -->



    <!-- Row start -->
    {{-- <div class="row gutters">
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
            <div class="card">
                <div class="card-header">
                    <div class="card-title">Visitors</div>
                </div>
                <div class="card-body pt-0">
                    <div id="visitors"></div>
                </div>
            </div>
        </div>
    </div> --}}
    <!-- Row end -->

    <!-- Row start -->
    {{-- <div class="row gutters">
        <div class="col-xl-4 col-lg-6 col-md-6 col-sm-12 col-12">
            <div class="card">
                <div class="card-header">
                    <div class="card-title">Customers</div>
                </div>
                <div class="card-body">
                    <div id="customers"></div>
                    <!-- Row starts -->
                    <div class="row gutters">
                        <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-6">
                            <div class="info-stats3 shade-one-a">
                                <i class="icon-opacity"></i>
                                <h6>New</h6>
                                <h3>450</h3>
                            </div>
                        </div>
                        <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-6">
                            <div class="info-stats3 shade-one-b">
                                <i class="icon-opacity"></i>
                                <h6>Returned</h6>
                                <h3>900</h3>
                            </div>
                        </div>
                    </div>
                    <!-- Row end -->
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-lg-6 col-md-6 col-sm-12 col-12">
            <div class="card">
                <div class="card-header">
                    <div class="card-title">Deals</div>
                </div>
                <div class="card-body pt-0 pb-0">
                    <div id="deals"></div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-lg-12 col-md-12 col-sm-12 col-12">
            <div class="card">
                <div class="card-header">
                    <div class="card-title">Logs</div>
                </div>
                <div class="card-body">
                    <div class="customScroll5">
                        <div class="activity-logs">
                            <div class="activity-log-list">
                                <div class="sts"></div>
                                <div class="log">New item sold</div>
                                <div class="log-time">10:10</div>
                            </div>
                            <div class="activity-log-list">
                                <div class="sts"></div>
                                <div class="log">Notification from bank</div>
                                <div class="log-time">05:25</div>
                            </div>
                            <div class="activity-log-list">
                                <div class="sts red"></div>
                                <div class="log">Transaction success alert</div>
                                <div class="log-time">09:45</div>
                            </div>
                            <div class="activity-log-list">
                                <div class="sts orange"></div>
                                <div class="log">Your item has been updated</div>
                                <div class="log-time">06:50</div>
                            </div>
                            <div class="activity-log-list">
                                <div class="sts"></div>
                                <div class="log">New fffer</div>
                                <div class="log-time">12:30</div>
                            </div>
                            <div class="activity-log-list">
                                <div class="sts orange"></div>
                                <div class="log">Item bought</div>
                                <div class="log-time">04:22</div>
                            </div>
                            <div class="activity-log-list">
                                <div class="sts"></div>
                                <div class="log">New sale: Zyan Ferris</div>
                                <div class="log-time">10:10</div>
                            </div>
                            <div class="activity-log-list">
                                <div class="sts orange"></div>
                                <div class="log">Order Received</div>
                                <div class="log-time">12:55</div>
                            </div>
                            <div class="activity-log-list">
                                <div class="sts"></div>
                                <div class="log">Service information</div>
                                <div class="log-time">09:12</div>
                            </div>
                            <div class="activity-log-list">
                                <div class="sts"></div>
                                <div class="log">Message from Reisnz</div>
                                <div class="log-time">09:27</div>
                            </div>
                            <div class="activity-log-list">
                                <div class="sts red"></div>
                                <div class="log">New item sale: Ali Sayed</div>
                                <div class="log-time">02:39</div>
                            </div>
                            <div class="activity-log-list">
                                <div class="sts orange"></div>
                                <div class="log">Product update</div>
                                <div class="log-time">08:22</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div> --}}
    <!-- Row end -->

    <!-- Row start -->
    {{-- <div class="row gutters">
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
            <div class="card">
                <div class="card-header">
                    <div class="card-title">Overall Income</div>
                </div>
                <div class="card-body pt-0 pb-0">
                    <div id="income"></div>
                </div>
            </div>
        </div>
    </div> --}}
    <!-- Row end -->

    <!-- Row start -->
    {{-- <div class="row gutters">
        <div class="col-xl-4 col-lg-6 col-md-6 col-sm-6 col-12">
            <div class="card">
                <div class="card-header">
                    <div class="card-title">Quick Stats</div>
                </div>
                <div class="card-body">
                    <div class="customScroll5">
                        <div class="quick-analytics">
                            <a href="#">
                                <i class="icon-shopping-cart1"></i> 500,000 New Orders
                            </a>
                            <a href="#">
                                <i class="icon-shopping-bag1"></i> 950,000 Products
                            </a>
                            <a href="#">
                                <i class="icon-package"></i> 325,010 Retail Stores
                            </a>
                            <a href="#">
                                <i class="icon-play-circle"></i> 780,500 Movies Downloaded
                            </a>
                            <a href="#">
                                <i class="icon-share1"></i> 250,000 Images Uploaded
                            </a>
                            <a href="#">
                                <i class="icon-eye1"></i> 870,000 Monthly Visits
                            </a>
                            <a href="#">
                                <i class="icon-bell"></i> 350,500 Tickets Booked
                            </a>
                            <a href="#">
                                <i class="icon-shopping-cart1"></i> 500,000 New Orders
                            </a>
                            <a href="#">
                                <i class="icon-shopping-bag1"></i> 950,000 Products
                            </a>
                            <a href="#">
                                <i class="icon-package"></i> 325,010 Retail Stores
                            </a>
                            <a href="#">
                                <i class="icon-play-circle"></i> 780,500 Movies Downloaded
                            </a>
                            <a href="#">
                                <i class="icon-share1"></i> 250,000 Images Uploaded
                            </a>
                            <a href="#">
                                <i class="icon-eye1"></i> 870,000 Monthly Visits
                            </a>
                            <a href="#">
                                <i class="icon-bell"></i> 350,500 Tickets Booked
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-lg-6 col-md-6 col-sm-6 col-12">
            <div class="card">
                <div class="card-header">
                    <div class="card-title">Activity</div>
                </div>
                <div class="card-body">
                    <div class="customScroll5">
                        <ul class="project-activity">
                            <li class="activity-list">
                                <div class="detail-info">
                                    <p class="date">Today</p>
                                    <p class="info">Messages accepted with attachments.</p>
                                </div>
                            </li>
                            <li class="activity-list success">
                                <div class="detail-info">
                                    <p class="date">Yesterday</p>
                                    <p class="info">Send email notifications of subscriptions and deletions to list owner.</p>
                                </div>
                            </li>
                            <li class="activity-list danger">
                                <div class="detail-info">
                                    <p class="date">10th December</p>
                                    <p class="info">Required change logs activity reports.</p>
                                </div>
                            </li>
                            <li class="activity-list warning">
                                <div class="detail-info">
                                <p class="date">15th December</p>
                                    <p class="info">Strategic partnership plan.</p>
                                </div>
                            </li>
                            <li class="activity-list success">
                                <div class="detail-info">
                                    <p class="date">21st December</p>
                                    <p class="info">Send email notifications of subscriptions and deletions to list owner.</p>
                                </div>
                            </li>
                            <li class="activity-list danger">
                                <div class="detail-info">
                                    <p class="date">25th December</p>
                                    <p class="info">Required change logs activity reports.</p>
                                </div>
                            </li>
                            <li class="activity-list warning">
                                <div class="detail-info">
                                <p class="date">28th December</p>
                                    <p class="info">Strategic partnership plan.</p>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-lg-12 col-md-12 col-sm-12 col-12">
            <div class="card">
                <div class="card-header">
                    <div class="card-title">Order History</div>
                </div>
                <div class="card-body">
                    <div class="customScroll5">
                        <ul class="user-messages">
                            <li class="clearfix">
                                <div class="customer">AM</div>
                                <div class="delivery-details">
                                    <span class="badge badge-primary">Ordered</span>
                                    <h5>Aaleyah Malik</h5>
                                    <p>We are pleased to inform that the following ticket no. <b>Le Rouge510</b> have been booked.</p>
                                </div>
                            </li>
                            <li class="clearfix">
                                <div class="customer">AS</div>
                                <div class="delivery-details">
                                    <span class="badge badge-primary">Delivered</span>
                                    <h5>Ali Sayed</h5>
                                    <p>The carrier successfully delivered the message to the end user.</p>
                                </div>
                            </li>
                            <li class="clearfix">
                                <div class="customer">ZR</div>
                                <div class="delivery-details">
                                    <span class="badge badge-primary">Cancelled</span>
                                    <h5>Zaira Raheem</h5>
                                    <p>The following describe the status codes and messages states for delivery receipts.</p>
                                </div>
                            </li>
                            <li class="clearfix">
                                <div class="customer">LJ</div>
                                <div class="delivery-details">
                                    <span class="badge badge-primary">Returned</span>
                                    <h5>Lily Jordan</h5>
                                    <p>Status codes and descriptions are returned in the following OpenMarket-specific TLVs When a delivery receipt is received.</p>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div> --}}
    <!-- Row end -->

</div>
<!-- Main container end -->

@endsection