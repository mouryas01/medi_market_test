<!-- Sidebar wrapper start -->
<nav id="sidebar" class="sidebar-wrapper">
				
    <!-- Sidebar brand start  -->
    <div class="sidebar-brand">        
        @if(Session::get('user_type') == 1)
            <a href="{{ route('admin.home') }}" class="logo">                      
        @else            
            <a href="{{ route('home') }}" class="logo">
        @endif               
            <img src="{{ asset('assets/img/logo.png') }}" alt="Le Rouge Admin Dashboard" />
        </a>
    </div>
    <!-- Sidebar brand end  -->

    <!-- Sidebar content start -->
    <div class="sidebar-content">
        <!-- sidebar menu start -->
        <div class="sidebar-menu">						
            <ul>{{ (in_array($path, ['/'])) ? 'active': '' }}
                <li class="header-menu">General</li>
                
                <li class="{{ (in_array($path, ['home'])) ? 'active': '' }}">
                    @if(Session::get('user_type') == 1)                      
                        <a href="{{ route('admin.home') }}" class="{{ (in_array($path, ['home'])) ? 'current-page': '' }}" onclick="showloader()">
                            <i class="icon-devices_other"></i>
                            <span class="menu-text">Dashboard</span>
                        </a>                            
                    @else
                        <a href="{{ route('home') }}" class="{{ (in_array($path, ['home'])) ? 'current-page': '' }}" onclick="showloader()">
                            <i class="icon-devices_other"></i>
                            <span class="menu-text">Dashboard</span>
                        </a>
                    @endif
                </li>                  

                <li class="sidebar-dropdown {{ (in_array($path, ['reports'])) ? 'active': '' }}">
                    <a href="#">
                        <i class="icon-devices_other"></i>
                        <span class="menu-text">Reports</span>
                    </a>
                    <div class="sidebar-submenu">                    
                        <ul>
                            <li class="{{ (in_array($path, ['sales'])) ? 'active': '' }}">
                                <a href="{{ route('sales-report') }}" class="{{ (in_array($path, ['sales'])) ? 'current-page': '' }}" onclick="showloader()">Sales Report</a>
                            </li> 
                            <li class="{{ (in_array($path, ['inventory'])) ? 'active': '' }}">
                                <a href="{{ route('inventory-report') }}" class="{{ (in_array($path, ['inventory'])) ? 'current-page': '' }}" onclick="showloader()">Inventory Report</a>
                            </li> 
                            <li class="{{ (in_array($path, ['return'])) ? 'active': '' }}">
                                <a href="{{ route('return-report') }}" class="{{ (in_array($path, ['return'])) ? 'current-page': '' }}" onclick="showloader()">Cancel Orders</a>
                            </li> 
                        </ul>
                    </div>
                </li> 
                @if(Session::get('user_type') == 1)         
                <li class="{{ Request::segment(2) == 'categories' ? 'active': '' }}">
                    <a href="{{ route('categories.index') }}" onclick="showloader()">
                        <i class="icon-list2"></i>
                        <span class="menu-text">Categories</span>
                    </a>
                </li>      
                @endif
                <li class="{{ (in_array($path, ['products'])) ? 'active': '' }}">
                    <a href="{{ route('products.index') }}" onclick="showloader()">
                        <i class="icon-border_all"></i>
                        <span class="menu-text">Products</span>
                    </a>
                </li>   
                <li class="{{ (in_array($path, ['orders'])) ? 'active': '' }}">
                    <a href="{{ route('orders.index') }}" onclick="showloader()">
                        <i class="icon-check-circle"></i>
                        <span class="menu-text">Orders</span>
                    </a>
                </li>                                                               
                @if(Session::get('user_type') == 1)                          
                <li class="{{ Request::segment(2) == 'events' ? 'active': '' }}">
                    <a href="{{ route('events.index') }}" onclick="showloader()">
                        <i class="icon-border_all"></i>
                        <span class="menu-text">Event</span>
                    </a>
                </li>
                @endif                                 
                @if(Session::get('user_type') == 1)                          
                <li class="{{ Request::segment(2) == 'promotions' ? 'active': '' }}">
                    <a href="{{ route('promotions.index') }}" onclick="showloader()">
                        <i class="icon-border_all"></i>
                        <span class="menu-text">Promotions & eCoupan</span>
                    </a>
                </li>
                @endif 
                @if(Session::get('user_type') == 1)                          
                <li class="{{ Request::segment(2) == 'blogs' ? 'active': '' }}">
                    <a href="{{ route('blogs.index') }}" onclick="showloader()">
                        <i class="icon-border_all"></i>
                        <span class="menu-text">Blogs</span>
                    </a>
                </li>
                @endif 
                <li class="{{ (in_array($path, ['stores'])) ? 'active': '' }}">
                    <a href="{{ route('stores.index') }}" onclick="showloader()">
                        <i class="icon-list2"></i>
                        <span class="menu-text">Stores</span>
                    </a>
                </li> 
                @if(Session::get('user_type') == 1)                          
                <li class="{{ Request::segment(2) == 'users' ? 'active': '' }}">
                    <a href="{{ route('users.index') }}" onclick="showloader()">
                        <i class="icon-user1"></i>
                        <span class="menu-text">Customers</span>
                    </a>
                </li>
                @endif            
                @if(Session::get('user_type') == 1)                          
                <li class="{{ Request::segment(2) == 'vendors' ? 'active': '' }}">
                    <a href="{{ route('vendors.index') }}" onclick="showloader()">
                        <i class="icon-user1"></i>
                        <span class="menu-text">Vendors</span>
                    </a>
                </li>
                @endif                  
                @if(Session::get('user_type') == 1)                          
                <li class="{{ Request::segment(2) == 'send-nofiication' ? 'active': '' }}">
                    <a href="{{ route('notification.index') }}" onclick="showloader()">
                        <i class="icon-border_all"></i>
                        <span class="menu-text">Send Notification</span>
                    </a>
                </li>
                @endif
                @if(Session::get('user_type') == 1)                          
                <li class="{{ Request::segment(2) == 'enquiries' ? 'active': '' }}">
                    <a href="{{ route('enquiries') }}" onclick="showloader()">
                        <i class="icon-list2"></i>
                        <span class="menu-text">Enquiry</span>
                    </a>
                </li>
                @endif 
                @if(Session::get('user_type') == 1)                          
                <li class="{{ (in_array($path, ['menus'])) ? 'active': '' }}">
                    <a href="{{ route('menus') }}" onclick="showloader()">
                        <i class="icon-list2"></i>
                        <span class="menu-text">App Menu</span>
                    </a>
                </li>
                @endif                                                       
            </ul>

        </div>
            <!-- sidebar menu end -->
    </div>
    <!-- Sidebar content end -->
    
</nav>
<!-- Sidebar wrapper end -->                        