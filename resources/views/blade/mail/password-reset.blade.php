<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .button {
            display: inline-block;
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>Hello {{ $username }},</h2>

    <p>We received a request to reset your password. If you didn't make this request, you can safely ignore this email.</p>

    <p>To reset your password, click the button below:</p>

    <a href="{{ $resetUrl }}" class="button">Reset Password</a>

    <p>This link will expire in {{ $expiresIn }}.</p>

    <p>If you're having trouble clicking the button, copy and paste this URL into your browser:</p>
    <p>{{ $resetUrl }}</p>

    <p>Best regards,<br>Your Application Team</p>
</div>
</body>
</html>