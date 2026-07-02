<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="{{ asset('assets/images/icons8-leave-48.png') }}" type="image/png">
    <title>Register</title>
    @if (config('services.turnstile.enabled'))
        <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
    @endif
</head>

<body>
    @include('auth.reg_css')
    @include('admin.loader')
    <div class="container">
        <!-- Header with Back Icon -->
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <header style="margin: 0;">Registration</header>
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
                ">
                <i class="fas fa-arrow-left"></i>
            </a>
        </div>

        <form method="POST" action="{{ route('register') }}" id="registerForm">
            @csrf

            <div class="form first">
                <div class="details personal">
                    <div class="fields">
                        <!-- Full Name -->
                        <div class="input-field">
                            <label>Full Name</label>
                            <input
                                type="text"
                                name="name"
                                placeholder="Enter your name"
                                value="{{ old('name') }}"
                                required>
                            @error('name')
                            <span class="error">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Position -->
                        <div class="input-field">
                            <label>Position/Status</label>
                            <input
                                type="text"
                                name="position"
                                placeholder="Enter position/status"
                                value="{{ old('position') }}"
                                required>
                            @error('position')
                            <span class="error">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Date Employed -->
                        <div class="input-field">
                            <label>Date Employed</label>
                            <input
                                type="date"
                                name="date_employed"
                                value="{{ old('date_employed') }}"
                                required>
                            @error('date_employed')
                            <span class="error">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Sex -->
                        <div class="input-field">
                            <label>Sex</label>
                            <select name="sex" required>
                                <option disabled selected>Select Sex</option>
                                <option value="Male" {{ old('sex') == 'Male' ? 'selected' : '' }}>Male</option>
                                <option value="Female" {{ old('sex') == 'Female' ? 'selected' : '' }}>Female</option>
                            </select>
                            @error('sex')
                            <span class="error">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Date of Birth -->
                        <div class="input-field">
                            <label>Date of Birth</label>
                            <input
                                type="date"
                                name="date_of_birth"
                                value="{{ old('date_of_birth') }}"
                                required>
                            @error('date_of_birth')
                            <span class="error">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Place of Birth -->
                        <div class="input-field">
                            <label>Place of Birth</label>
                            <input
                                type="text"
                                name="place_of_birth"
                                placeholder="Enter place of birth"
                                value="{{ old('place_of_birth') }}"
                                required>
                            @error('place_of_birth')
                            <span class="error">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Employee Number -->
                        <div class="input-field">
                            <label>Employee Number</label>
                            <input
                                type="number"
                                name="employee_number"
                                placeholder="Enter employee number"
                                value="{{ old('employee_number') }}"
                                required>
                            @error('employee_number')
                            <span class="error">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Personnel -->
                        <div class="input-field">
                            <label>Personnel</label>
                            <select name="personnel" required>
                                <option disabled selected>Select Personnel</option>
                                <option value="Teaching" {{ old('personnel') == 'Teaching' ? 'selected' : '' }}>Teaching</option>
                                <option value="Non-Teaching" {{ old('personnel') == 'Non-Teaching' ? 'selected' : '' }}>Non-Teaching</option>
                            </select>
                            @error('personnel')
                            <span class="error">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Station -->
                        <div class="input-field">
                            <label>Station</label>
                            <input
                                type="text"
                                name="station"
                                placeholder="Enter your station"
                                value="{{ old('station') }}"
                                required>
                            @error('station')
                            <span class="error">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Civil Status -->
                        <div class="input-field">
                            <label>Civil Status</label>
                            <input
                                type="text"
                                name="civil_status"
                                placeholder="Enter civil status"
                                value="{{ old('civil_status') }}"
                                required>
                            @error('civil_status')
                            <span class="error">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Email -->
                        <div class="input-field">
                            <label>Email</label>
                            <input
                                type="email"
                                name="email"
                                placeholder="Enter your email"
                                value="{{ old('email') }}"
                                required>
                            @error('email')
                            <span class="error">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Mobile Number -->
                        <div class="input-field">
                            <label>Mobile Number</label>
                            <input
                                type="number"
                                name="phone"
                                placeholder="Enter mobile number"
                                value="{{ old('phone') }}"
                                required>
                            @error('phone')
                            <span class="error">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Password -->
                        <div class="input-field">
                            <label>Password</label>
                            <input
                                type="password"
                                name="password"
                                placeholder="Enter your password"
                                required>
                            @error('password')
                            <span class="error">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Confirm Password -->
                        <div class="input-field" style="position: relative;">
                            <label>Confirm Password</label>
                            <input
                                type="password"
                                id="conPassword"
                                name="password_confirmation"
                                placeholder="Confirm your password"
                                required>
                            <i
                                id="togglePassword"
                                class="fas fa-eye"
                                style="position: absolute; top: 62%; right: 10px; transform: translateY(-50%); cursor: pointer;"></i>
                            @error('password_confirmation')
                            <span class="error">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="text">
                        <h3>Already have an account?
                            <a href="{{ route('login') }}">Log In</a>
                        </h3>
                    </div>
                    @if (config('services.turnstile.enabled'))
                        <div class="center">
                            <div class="cf-turnstile" data-sitekey="{{ config('services.turnstile.site_key') }}"></div>
                        </div>
                        @error('cf-turnstile-response')
                            <div class="center"><span class="error">{{ $message }}</span></div>
                        @enderror
                    @endif
                    <div class="center">
                        <button type="submit" class="submit">
                            <span class="btnText">Submit</span>
                            <i class="uil uil-navigator"></i>
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</body>

</html>

<script>
    document.getElementById('togglePassword').addEventListener('click', function() {
        const passwordField = document.getElementById('conPassword');
        const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordField.setAttribute('type', type);

        // Toggle the icon class
        this.classList.toggle('fa-eye');
        this.classList.toggle('fa-eye-slash');
    });

    // Add event listener for form submission
    document.getElementById('registerForm').addEventListener('submit', function(e) {
        // Show the loader when the submit button is clicked
        document.querySelector('.loader').style.display = 'flex';

        // Prevent the default form submission to show the loader first
        e.preventDefault();

        // Submit the form immediately after showing the loader
        this.submit();
    });
    // Hide the loader when the page has fully loaded
    window.addEventListener('load', () => {
        document.querySelector('.loader').style.display = 'none'; // Hide loader when the page is loaded
    });
</script>
