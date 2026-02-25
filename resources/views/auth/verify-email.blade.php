<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="{{ asset('assets/images/icons8-leave-48.png') }}" type="image/png">
    <title>Email Verification</title>
    <!-- Include Alertify CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/alertify.min.css"/>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/themes/default.min.css"/>
</head>
<body>
    @include('auth.login_css')
    @include('admin.loader')

    <div class="wrapper">
        <!-- Title -->
        <div style="margin-bottom: 20px;">
            <h2 style="margin: 0;">Email Verification</h2>
        </div>

        <p style="margin-bottom: 20px; text-align: justify; color: #228B22">
            {{ __('Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you? If you didn\'t receive the email, we will gladly send you another.') }}
        </p>

        <!-- Buttons -->
        <form method="POST" action="{{ route('verification.send') }}" style="margin-bottom: 10px;">
            @csrf
            <div class="input-box button">
                <input type="submit" value="{{ __('Resend Verification Email') }}">
            </div>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <div class="input-box button">
                <input type="submit" value="{{ __('Log Out') }}" style="background-color: #DC3545;">
            </div>
        </form>
    </div>

    <!-- Include Alertify JS -->
    <script src="https://cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/alertify.min.js"></script>

    <script>
        // Set Alertify position to top-right
        alertify.set('notifier', 'position', 'top-right');

        // Display alert based on session message
        @if (session('message'))
            alertify.success("{{ session('message') }}");
        @elseif (session('errors'))
            alertify.error("An error occurred while sending the verification email. Please try again.");
        @endif
    </script>
</body>
</html>
