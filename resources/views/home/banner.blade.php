<section class="section main-banner" id="top" data-section="section1">
    <video autoplay muted loop id="bg-video">
        <source src="{{ asset('assets/images/banner-video.mp4') }}" type="video/mp4" />
    </video>

    <div class="video-overlay header-text">
        <div class="caption">
            
            <h2 class ="mb-2"><em>Time Off</em> Hassle Free</h2>
            <h6 class ="mb-4">For Teaching and Non Teaching Leave Card Monitoring</h6>
            <div class="main-button mt-4">
                <div class="scroll-to-section">
                @if (auth()->check() && auth()->user()->usertype == 'admin')
                    <a href="{{ route('admin.dashboard') }}">Dashboard</a>
                @elseif (auth()->check() && auth()->user()->status == 'active')
                    <a href="{{ route('user/dashboard') }}">Dashboard</a>
                @elseif (auth()->check() && auth()->user()->status == 'pending' && auth()->user()->email_verified_at != null)
                    <a href="{{ route('/user/dashboard/warning') }}">Dashboard</a>
                @elseif (auth()->check() && auth()->user()->status == 'rejected')
                <a href="#" id="contact-link" onclick="alert('Your account has been rejected. Please contact the admin.');">Dashboard</a>
                @else
                    <a href="{{ route('register') }}">REGISTER NOW!</a>
                @endif
            </div>
            </div>
        </div>
    </div>
</section>

<style>
    html, body {
        margin: 0;
        overflow: hidden; /* Prevent scrolling */
    }

    /* Disable overflow: hidden on smaller screens (mobile) */
    @media (max-width: 768px) {
        html, body {
            overflow: auto; /* Enable scrolling */
        }
    }
</style>

