<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
    </style>
</head>
<body>
<div class="container">
    <h2>Welcome {{ $username }}!</h2>

    <p>Thank you for registering with us. Your account has been successfully created.</p>

    <p>You can now log in to your account and start using our services.</p>

    <p>Best regards,<br>The {{ Config::get('app.name') }} Team</p>
</div>
</body>
</html>