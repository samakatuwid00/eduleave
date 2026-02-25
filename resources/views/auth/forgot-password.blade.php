<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="{{ asset('assets/images/icons8-leave-48.png') }}" type="image/png">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <title>Forgot Password</title>
</head>
<body>
    @include('auth.login_css')
    @include('admin.loader')
    <div class="wrapper">
        <!-- Title and Icon Back Button -->
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h2 style="margin: 0;">Forgot Password</h2>
            <a 
                href="{{ route('login') }}" 
                id="backButton" 
                style="display: inline-flex; align-items: center; justify-content: center; width: 40px; height: 40px; background-color: #007BFF; color: white; border-radius: 50%; text-decoration: none; font-size: 18px;">
                <i class="fas fa-arrow-left"></i>
            </a>
        </div>
        <form method="POST" action="{{ route('pass.request') }}" id="forgotPasswordForm">
            @csrf
            @if (session('status'))
                <div class="status-message">{{ session('status') }}</div>
            @endif
            @error('email')
                <span class="error">{{ $message }}</span>
            @enderror
            <div class="input-box">
                <input type="email" name="email" placeholder="Enter your email" value="{{ old('email') }}" required autofocus>
            </div>
            <div class="input-box button">
                <input type="submit" value="Send Reset Link">
            </div>
        </form>
    </div>

    <script>
        // Only handle loader for form submission
        function handleLoaderAndAction(e, actionType) {
            if (actionType === 'submit') {
                // Show the loader for form submission
                document.querySelector('.loader').style.display = 'flex';
                e.preventDefault();
                setTimeout(() => {
                    e.target.submit(); // Submit form after a delay
                }, 300); // Adjust the delay as needed
            }
        }

        // Add event listeners for form submission
        document.getElementById('forgotPasswordForm').addEventListener('submit', function(e) {
            handleLoaderAndAction(e, 'submit'); // Handle form submission
        });

        // Back Button should redirect immediately without loader interference
        document.getElementById('backButton').addEventListener('click', function(e) {
            // Just let the back button work normally without interference
            window.location.href = e.target.getAttribute('href');
        });

        // Hide the loader once the page is fully loaded (for form submissions)
        window.addEventListener('load', () => {
            document.querySelector('.loader').style.display = 'none';
        });
    </script>
</body>
</html>
