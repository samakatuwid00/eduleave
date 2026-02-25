<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="{{ asset('assets/images/icons8-leave-48.png') }}" type="image/png">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <title>Email Verification</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(to bottom right, #e3f2fd, #90caf9);
            color: #333;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .email-container {
            background: #ffffff;
            border-radius: 15px;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
            padding: 30px;
            max-width: 600px;
            width: 90%;
            text-align: center;
        }

        h1 {
            font-size: 28px;
            color: #007BFF;
            margin-bottom: 15px;
            font-weight: bold;
        }

        p {
            margin: 15px 0;
            line-height: 1.7;
            font-size: 16px;
        }

        a.btn {
            display: inline-block;
            margin: 20px 0;
            padding: 12px 25px;
            background-color: #007BFF;
            color: white;
            border-radius: 8px;
            font-size: 16px;
            font-weight: bold;
            text-decoration: none;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            transition: background-color 0.3s ease, transform 0.2s ease;
        }

        a.btn:hover {
            background-color: #0056b3;
            transform: translateY(-2px);
        }

        a.btn:focus {
            outline: none;
            box-shadow: 0 0 0 4px rgba(0, 123, 255, 0.5);
        }

        footer {
            margin-top: 20px;
            font-size: 14px;
            color: #555;
        }

        footer a {
            color: #007BFF;
            text-decoration: none;
        }

        footer a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <h1>Email Verification</h1>
        <p>Hi <strong>{{ $user->name }}</strong>,</p>
        <p>
            Thank you for registering with us! Please verify your email address to activate your account.
            Just click the button below:
        </p>
        <a href="{{ $url }}" class="btn">Verify Email</a>
        <p>If you didn't sign up, don't worry—just ignore this email.</p>
        <p>Warm regards,<br><strong>EduLeave Team</strong></p>
        <footer>
            Need help? <a href="mailto:support@eduleave.com">Contact Support</a>
        </footer>
    </div>
</body>
</html>
