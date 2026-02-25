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
        </div>
        <form method="POST" action="{{ route('pass.update') }}" id="resetPasswordForm">
            @csrf
            @error('email')
            <span class="error">{{ $message }}</span>
            @enderror

            @error('password')
                    <span class="error">{{ $message }}</span>
            @enderror
            
            @error('password_confirmation')
                <span class="error">{{ $message }}</span>
            @enderror

            <input type="hidden" name="token" value="{{ $token }}">

            <!-- Email Address -->
            <div class="input-box">
                <input 
                    type="email" 
                    name="email" 
                    placeholder="Enter your email" 
                    value="{{ old('email') }}" 
                    required 
                    autofocus 
                >
            </div>
            <!-- Password -->
            <div class="input-box" style="position: relative;">
                <input 
                    type="password" 
                    name="password" 
                    placeholder="Enter your password" 
                    required 
                >
            </div>
            <!-- Confirm Password -->
            <div class="input-box" style="position: relative;">
                <input 
                    type="password" 
                    id="conPassword" 
                    name="password_confirmation" 
                    placeholder="Confirm your password" 
                    required 
                >
                <i 
                    id="togglePassword" 
                    class="fas fa-eye" 
                    style="position: absolute; top: 50%; right: 10px; transform: translateY(-50%); cursor: pointer;"
                ></i>
            </div>
            <!-- Submit Button -->
            <div class="input-box button">
                <input type="submit" value="Reset Password">
            </div>
        </form>
    </div>

    <script>
        // Toggle password visibility
        document.getElementById('togglePassword').addEventListener('click', function () {
            const passwordField = document.getElementById('conPassword');
            const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordField.setAttribute('type', type);

            // Toggle the icon class
            this.classList.toggle('fa-eye');
            this.classList.toggle('fa-eye-slash');
        });

        // Common function to show the loader and apply delay before submitting
        function handleLoaderAndAction(e, actionType) {
            // Show the loader
            document.querySelector('.loader').style.display = 'flex';

            // Prevent the default behavior (form submit or link click)
            e.preventDefault();

            // Delay the action (form submission or page redirection)
            setTimeout(() => {
                if (actionType === 'submit') {
                    // Submit the form if it's a form submit event
                    e.target.submit();
                }
            }, 300); // Adjust the delay as needed
        }

        // Add event listener to the form for submit action
        document.getElementById('resetPasswordForm').addEventListener('submit', function(e) {
            handleLoaderAndAction(e, 'submit'); // Handle form submission with loader
        });

        // Hide the loader once the page is fully loaded
        window.addEventListener('load', () => {
            document.querySelector('.loader').style.display = 'none';
        });
    </script>
</body>
</html>
