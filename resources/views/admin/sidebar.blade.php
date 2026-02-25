<a id="mobile_btn" class="mobile_btn float-left" href="#sidebar"><i class="fas fa-bars" aria-hidden="true"></i></a>
<a id="toggle_btn" class="float-left" href="javascript:void(0);">
<img src="assets/img/sidebar/icon-21.png" alt="">
</a>
<div class="sidebar" id="sidebar">
    <div class="sidebar-inner slimscroll">
        <div id="sidebar-menu" class="sidebar-menu">
            <div class="header-left">
                <a href="{{ url('admin/dashboard') }}" class="logo">
                    <span class="text-uppercase">EDU LEAVE</span>
                    <img src="{{ asset('assets/images/icons8-leave-48.png') }}" width="40" height="40" alt="">
                </a>
            </div>
            <ul class="sidebar-ul">
                <li class="menu-title">Menu</li>
                <li class="{{ Request::is('admin/dashboard') ? 'active' : '' }}">
                    <a href="{{ url('admin/dashboard') }}">
                        <img src="{{ asset('admincss/assets/img/sidebar/icon-1.png') }}" alt="icon">
                        <span>Dashboard</span>
                    </a>
                </li>
                <li class="submenu">
                    <a href="#">
                        <img src="{{ asset('admincss/assets/img/sidebar/icon-2.png') }}" alt="icon">
                        <span>Users</span><span class="menu-arrow"></span>
                    </a>
                    <ul class="list-unstyled" style="{{ Request::is('admin/users/*') ? 'display: block;' : 'display: none;' }}">                        
                    <li class="{{ Request::is('admin/users/view-all_users') ? 'active' : '' }}">
                        <a href="{{ url('/admin/users/view-all_users') }}"><span>All Users</span></a>
                    </li>
                    <li class="{{ Request::is('admin/users/view-pending_users') ? 'active' : '' }}">
                        <a href="{{ url('/admin/users/view-pending_users') }}"><span>Pending Users</span></a>
                    </li>
                    <li class="{{ Request::is('admin/users/view-approved_users') ? 'active' : '' }}">
                        <a href="{{ url('/admin/users/view-approved_users') }}"><span>Approved Users</span></a>
                    </li>
                    <li class="{{ Request::is('admin/users/view-rejected_users') ? 'active' : '' }}">
                        <a href="{{ url('/admin/users/view-rejected_users') }}"><span>Rejected Users</span></a>
                    </li>
                    </ul>
                </li>
                <li class="{{ Request::is('admin/teacher_leave_cards') || Request::is('admin/leave_card/*') ? 'active' : '' }}">
                    <a href="{{ url('/admin/teacher_leave_cards') }}">
                        <img src="{{ asset('admincss/assets/img/sidebar/icon-6.png') }}" alt="icon">
                        <span>Leave Cards</span>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</div>
