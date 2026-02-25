<header class="main-header clearfix" role="header">
    <div class="logo">
        <a href=""><em>Edu</em> Leave</a>
    </div>
    <a href="#menu" class="menu-link"><i class="fa fa-bars"></i></a>
    <nav id="menu" class="main-nav" role="navigation">
        <ul class="main-menu">
            <li><a href="">Home</a></li>
            <li class="has-submenu">
                <a href="#">About Us</a>
                <ul class="sub-menu">
                    <li><a href="#what-we-do">What we do?</a></li>
                    <li><a href="#who-we-are">Who we are?</a></li>
                    <li><a href="#how-it-works">How it works?</a></li>
                </ul>
            </li>
            <li><a href="#" id="contact-link">Contact Us</a></li>
            
            <!-- Conditional Logic for Authenticated Users -->
            @if (auth()->check())
                @if (auth()->user()->usertype == 'admin')
                    <li>
                        <a href="{{ route('admin.dashboard') }}">Dashboard</a>
                    </li>
                @elseif (auth()->user()->status == 'active')
                    <li>
                        <a href="{{ route('user/dashboard') }}">Dashboard</a>
                    </li>
                @elseif (auth()->user()->status == 'pending' && auth()->user()->email_verified_at != null)
                <li>
                    <a href="{{ route('/user/dashboard/warning') }}">Dashboard</a>
                </li>
                @elseif (auth()->check() && auth()->user()->status == 'rejected')
                    <li>
                        <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Logout</a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                            @csrf
                        </form>
                    </li>
                @endif
                @else
                <li class="has-submenu">
                    <a href="#">Log in / Register</a>
                    <ul class="sub-menu">
                        <li><a href="#" id="login-link">Log in</a></li>
                        <li><a href="#" id="register-link">Register</a></li>
                    </ul>
                </li>
            @endif
        </ul>
    </nav>
</header>

@include('home.contactus')
