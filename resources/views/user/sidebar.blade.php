<div class="sidebar" id="sidebar" aria-label="User navigation">
  <button id="sidebar_close" class="sidebar-close" type="button" aria-label="Close navigation">
    <i class="fas fa-times" aria-hidden="true"></i>
  </button>
  <div class="sidebar-inner slimscroll">
    <div id="sidebar-menu" class="sidebar-menu">
      <div class="header-left">
        <a href="#" class="logo">
          <span class="text-uppercase">EDU LEAVE</span>
          <img src="{{ asset('assets/images/icons8-leave-48.png') }}" width="40" height="40" alt="">
        </a>
      </div>
      <ul class="sidebar-ul">
        <li class="menu-title">Menu</li>
        <li class="active">
          <a href="{{url('user/dashboard')}}">
            <img src="{{ asset('admincss/assets/img/sidebar/icon-1.png') }}" alt="icon">
            <span>Dashboard</span>
          </a>
        </li>
      </ul>
    </div>
  </div>
</div>
