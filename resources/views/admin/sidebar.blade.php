<div class="sidebar" id="sidebar" aria-label="Admin navigation">
    <button id="sidebar_close" class="sidebar-close" type="button" aria-label="Close navigation">
        <i class="fas fa-times" aria-hidden="true"></i>
    </button>
    <div class="sidebar-inner slimscroll">
        <div id="sidebar-menu" class="sidebar-menu">
            <div class="header-left">
                <a href="{{ url('admin/dashboard') }}" class="logo">
                    <span class="text-uppercase">EDULEAVE</span>
                    <img src="{{ asset('assets/images/icons8-leave-48.png') }}" width="40" height="40" alt="">
                </a>
            </div>
            <ul class="sidebar-ul">
                <li class="menu-title">Menu</li>
                <li class="{{ Request::is('admin/dashboard') ? 'active' : '' }}">
                    <a href="{{ url('admin/dashboard') }}">
                        <img src="{{ asset('admincss/assets/img/sidebar/icon-27.png') }}" alt="icon">
                        <span>Dashboard</span>
                    </a>
                </li>
                @if (auth()->user()->hasAdminPermission('view_analytics'))
                <li class="{{ Request::is('admin/action-center') ? 'active' : '' }}">
                    <a href="{{ route('admin.action-center') }}">
                        <img src="{{ asset('admincss/assets/img/sidebar/icon-19.png') }}" alt="icon">
                        <span>Action Center</span>
                        @isset($actionCount)
                            @if ($actionCount > 0)<span class="action-center-badge">{{ $actionCount }}</span>@endif
                        @endisset
                    </a>
                </li>
                <li class="{{ Request::is('admin/leave-analytics') ? 'active' : '' }}">
                    <a href="{{ route('admin.leave-analytics') }}">
                        <img src="{{ asset('admincss/assets/img/sidebar/icon-20.png') }}" alt="icon">
                        <span>Leave Analytics</span>
                    </a>
                </li>
                @endif
                @if (auth()->user()->hasAdminPermission('manage_imports'))
                <li class="{{ Request::is('admin/import-center*') ? 'active' : '' }}">
                    <a href="{{ route('admin.import-center') }}">
                        <img src="{{ asset('admincss/assets/img/sidebar/icon-7.png') }}" alt="icon">
                        <span>Import Center</span>
                    </a>
                </li>
                @endif
                @if (auth()->user()->hasAdminPermission('manage_automation'))
                <li class="{{ Request::is('admin/automation*') ? 'active' : '' }}">
                    <a href="{{ route('admin.automation') }}">
                        <img src="{{ asset('admincss/assets/img/sidebar/icon-14.png') }}" alt="icon">
                        <span>Automation</span>
                    </a>
                </li>
                @endif
                @if (auth()->user()->hasAdminPermission('export_reports'))
                <li class="{{ Request::is('admin/reports*') ? 'active' : '' }}">
                    <a href="{{ route('admin.reports') }}">
                        <img src="{{ asset('admincss/assets/img/sidebar/icon-22.png') }}" alt="icon">
                        <span>Reports</span>
                    </a>
                </li>
                @endif
                @if (auth()->user()->hasAdminPermission('view_audit'))
                <li class="{{ Request::is('admin/audit*') ? 'active' : '' }}">
                    <a href="{{ route('admin.audit') }}">
                        <img src="{{ asset('admincss/assets/img/sidebar/icon-23.png') }}" alt="icon">
                        <span>Audit Log</span>
                    </a>
                </li>
                @endif
                @if (auth()->user()->hasAdminPermission('manage_users'))
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
                @endif
                @if (auth()->user()->hasAdminPermission('manage_leave_cards'))
                <li class="{{ Request::is('admin/teacher_leave_cards') || Request::is('admin/leave_card/*') ? 'active' : '' }}">
                    <a href="{{ url('/admin/teacher_leave_cards') }}">
                        <img src="{{ asset('admincss/assets/img/sidebar/icon-6.png') }}" alt="icon">
                        <span>Leave Cards</span>
                    </a>
                </li>
                @endif
            </ul>
        </div>
    </div>
</div>
