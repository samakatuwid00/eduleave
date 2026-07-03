<div class="header-outer">
    <div class="header">
        <button id="mobile_btn" class="mobile_btn admin-icon-button float-left" type="button" aria-controls="sidebar" aria-expanded="false" aria-label="Open navigation">
            <i class="fas fa-bars" aria-hidden="true"></i>
        </button>
        <button id="toggle_btn" class="admin-icon-button float-left" type="button" aria-controls="sidebar" aria-expanded="true" aria-label="Collapse navigation">
            <i class="fas fa-angle-left" aria-hidden="true"></i>
        </button>
        <ul class="nav float-left">
            <li>
                <a href="{{ url('admin/dashboard') }}" class="mobile-logo d-md-block d-lg-none d-block">
                    <img src="{{ asset('assets/images/icons8-leave-48.png') }}" alt="Edu Leave" width="30" height="30">
                </a>
            </li>
        </ul>
        <button id="theme_toggle" class="admin-icon-button theme-toggle float-right" type="button" aria-pressed="false" aria-label="Enable dark mode" title="Toggle dark mode">
            <i class="fas fa-moon" aria-hidden="true"></i>
        </button>
        <ul class="nav user-menu float-right">
        <!-- <li class="nav-item dropdown has-arrow">
            <a href="javascript:void(0);" id="contactModalToggle" class="nav-link user-link" data-toggle="dropdown">
                <span class="user-img">
                <i class="fa-solid fa-phone"></i>
                </span>
                <span>Your Contact</span>
            </a>
        </li> -->
        <li class="nav-item dropdown has-arrow">
            <a href="#" class="nav-link user-link" data-toggle="dropdown">
                <span class="user-img">
                    <img class="rounded-circle" src="{{ asset('admincss/assets/img/user.png') }}" width="30" alt="Admin">
                    <span class="status online"></span>
                </span>
                <span>Admin</span>
            </a>
            <div class="dropdown-menu">
                <!-- <a class="dropdown-item" href="#">My Profile</a>
                <a class="dropdown-item" href="#">Edit Profile</a> -->
                <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                    @csrf
                    <button class="dropdown-item" type="submit" onclick="event.preventDefault(); this.closest('form').submit();">
                        {{ __('Logout') }}
                    </button>
                </form>
            </div>
        </li>
        </ul>
        <div class="dropdown mobile-user-menu float-right"> 
            <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown" aria-expanded="false"><i class="fas fa-ellipsis-v"></i></a>
            <div class="dropdown-menu dropdown-menu-right">
                <!-- <a class="dropdown-item" href="#">My Profile</a>
                <a class="dropdown-item" href="#">Edit Profile</a>
                <a class="dropdown-item" href="inbox.html">Settings</a> -->
                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>
                <a class="dropdown-item" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    Logout
                </a>
            </div>
        </div>
    </div>
</div>
@include('admin.contact')
