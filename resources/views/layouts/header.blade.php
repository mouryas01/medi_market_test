<!-- Header start -->
<header class="header">
    <div class="toggle-btns">
        <a id="toggle-sidebar" href="#">
            <i class="icon-list"></i>
        </a>
        <a id="pin-sidebar" href="#">
            <i class="icon-list"></i>
        </a>
    </div>
    <div class="header-items">
        <!-- Custom search start -->
        <div class="custom-search">
            {{-- <input type="text" class="search-query" placeholder="Search here ...">
            <i class="icon-search1"></i> --}}
        </div>
        <!-- Custom search end -->

        <!-- Header actions start -->
        <ul class="header-actions">
            
            <li class="dropdown">
                <?php
                use App\Http\Controllers\HomeController;                
                $userinfo = HomeController::get_userinfo();              
                ?>
                <a href="#" id="userSettings" class="user-settings" data-toggle="dropdown" aria-haspopup="true">
                    <span class="user-name">{{-- ucfirst($userinfo['name']) --}}</span>
                    <span class="avatar">
                        <img src="{{ !empty($userinfo['picture']) ? asset($userinfo['picture']) : asset('assets/img/fav.png') }}" alt="avatar">
                        <span class="status busy"></span>
                    </span>
                </a>
                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="userSettings">
                    <div class="header-profile-actions">

                        <div class="header-user-profile">
                            <div class="header-user">
                                <img src="{{ !empty($userinfo['picture']) ? asset($userinfo['picture']) : asset('assets/img/fav.png') }}" alt="Admin Template">
                            </div>
                            <h5>{{ ucfirst($userinfo['name']) }}</h5>
                            <p>{{ ($userinfo['user_type'] == 1) ? 'Admin':'Vendor' }}</p>                            
                        </div>

                        <a href="{{ route('get-profile') }}"><i class="icon-user1"></i> My Profile</a>

                        @if(Session::get('user_type') == 1)  
                            {{-- <a href="account-settings.html"><i class="icon-settings1"></i> Account Settings</a> --}}
                        @endif

                        <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            <i class="fa fa-power-off"></i> <span>Sign out</span>
                        </a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                            {{ csrf_field() }}
                        </form>                                                    
                    </div>
                </div>
            </li>
        </ul>						
        <!-- Header actions end -->
    </div>
</header>
<!-- Header end -->