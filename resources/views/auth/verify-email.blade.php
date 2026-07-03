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
    <style>
        #resend-verification-button:disabled {
            cursor: not-allowed;
            opacity: 0.65;
        }
    </style>
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
        <form id="resend-verification-form" method="POST" action="{{ route('verification.send') }}" style="margin-bottom: 10px;">
            @csrf
            <div class="input-box button">
                <input
                    id="resend-verification-button"
                    type="submit"
                    value="{{ ($cooldownSeconds ?? 0) > 0 ? __('Resend Verification Email (:seconds s)', ['seconds' => $cooldownSeconds]) : __('Resend Verification Email') }}"
                    @disabled(($cooldownSeconds ?? 0) > 0)>
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
        @if (session('status') === 'verification-link-sent')
            alertify.success("{{ __('A new verification link has been sent to your email address.') }}");
        @elseif (session('errors'))
            alertify.error("An error occurred while sending the verification email. Please try again.");
        @endif

        const resendForm = document.getElementById('resend-verification-form');
        const resendButton = document.getElementById('resend-verification-button');
        const resendLabel = @json(__('Resend Verification Email'));
        let cooldownSeconds = Number(@json($cooldownSeconds ?? 0));

        const updateResendButton = () => {
            if (cooldownSeconds > 0) {
                resendButton.disabled = true;
                resendButton.value = `${resendLabel} (${cooldownSeconds}s)`;
                return;
            }

            resendButton.disabled = false;
            resendButton.value = resendLabel;
        };

        updateResendButton();

        if (cooldownSeconds > 0) {
            const countdown = window.setInterval(() => {
                cooldownSeconds--;
                updateResendButton();

                if (cooldownSeconds <= 0) {
                    window.clearInterval(countdown);
                }
            }, 1000);
        }

        resendForm.addEventListener('submit', () => {
            resendButton.disabled = true;
            resendButton.value = @json(__('Sending...'));
        });
    </script>
</body>
</html>
