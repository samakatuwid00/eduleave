<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="{{ asset('assets/images/icons8-leave-48.png') }}" type="image/png">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <title>Account Approval</title>
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
        <h1>Congratulations!</h1>
        <p>Your account has been successfully approved.</p>
        <p>You can now log in and view your leave card information.</p>
        <footer>
            Need help? <a href="mailto:support@eduleave.com">Contact Support</a>
        </footer>
    </div>
</body>
</html>
