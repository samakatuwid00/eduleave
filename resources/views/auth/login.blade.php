<!DOCTYPE html>
  <html lang="en">
    <head>
      <meta charset="UTF-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <title>Log In</title>
    <link rel="icon" href="{{ asset('assets/images/icons8-leave-48.png') }}" type="image/png">
    </head>
    <body>
        @include('auth.login_css')
        @include('admin.loader')
        <div class="wrapper">
            <!-- Title and Icon Back Button -->
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h2 style="margin: 0;">Log In</h2>
                <a 
                    href="{{ route('welcome') }}" 
                    style="
                        display: inline-flex; 
                        align-items: center; 
                        justify-content: center; 
                        width: 40px; 
                        height: 40px; 
                        background-color: #007BFF; 
                        color: white; 
                        border-radius: 50%; 
                        text-decoration: none; 
                        font-size: 18px;
                    "
                >
                    <i class="fas fa-arrow-left"></i>
                </a>
            </div>
            <form method="POST" action="{{ route('login') }}"  id="loginForm">
                @csrf
                @if (session('status'))
                    <span class="status-message">
                        {{ session('status') }}
                    </span>
                @endif
                @if(session('status'))
                    <script>
                        alertify.set('notifier', 'position', 'top-right'); // Set the position to top-right
                        alertify.success('{{ session('status') }}'); // Display the success message
                    </script>
                @endif
                @error('email')
                <span class="error">
                    {{ $message }} 
                    @if ($message === 'Your account was rejected. Contact support for assistance.')
                    <a href="{{ route('welcome') }}" id="contact-link">Contact Us</a>                    @endif
                </span>
                @enderror
                <!-- Email Address -->
                <div class="input-box">
                    <input 
                        type="email" 
                        name="email" 
                        placeholder="example@gmail.com"
                        value="{{ old('email') }}" 
                        required 
                        autofocus 
                    >
                </div>
                <!-- Password -->
                <div class="input-box" style="position: relative;">
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        placeholder="Example: SecurePass1!"
                        value="{{ old('password') }}" 
                        required 
                        autofocus 
                    >
                    <i 
                        id="togglePasswordIcon" 
                        class="fas fa-eye" 
                        style="position: absolute; top: 50%; right: 10px; transform: translateY(-50%); cursor: pointer;"
                    ></i>
                    @error('password')
                        <span class="error">{{ $message }}</span>
                    @enderror
                </div>
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" id="forgotPasswordLink">{{ __('Forgot your password?') }}</a>
                @endif

                <!-- Submit Button -->
                <div class="input-box button">
                    <input type="submit" value="Log In">
                </div>

                <!-- Forgot Password and Register Link -->
                <div class="text">
                    <h3>Don't have an account? 
                        <a href="{{ route('register') }}">Register Now!</a>
                    </h3>
                </div>
            </form>
        </div>
    </body>
</html>
<script>
    // Get the password input and the eye icon
    const togglePasswordIcon = document.getElementById('togglePasswordIcon');
    const passwordInput = document.getElementById('password');

    // Add event listener to toggle the password visibility
    togglePasswordIcon.addEventListener('click', function() {
        // Toggle the type attribute between 'password' and 'text'
        const type = passwordInput.type === 'password' ? 'text' : 'password';
        passwordInput.type = type;

        // Toggle the icon between eye and eye-slash
        this.classList.toggle('fa-eye');
        this.classList.toggle('fa-eye-slash');
    });

    // Handle form submission
    document.getElementById('loginForm').addEventListener('submit', function(e) {
        // Show the loader
        document.querySelector('.loader').style.display = 'flex';

        // Prevent the default behavior (form submission)
        e.preventDefault();

        // Simulate a delay before submitting the form
        setTimeout(() => {
            // Submit the form after the delay
            e.target.submit();
        }, 300); // Adjust the delay as needed (in milliseconds)
    });
</script>
