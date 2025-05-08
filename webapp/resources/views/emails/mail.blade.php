<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Reset Request</title>
    <link href="{{ url('css/others/mail.css') }}" rel="stylesheet"></head>
<body>
    <div class="email-container">
        <h3>Hi,</h3>
        <h4>We received a request to reset your Flick password. If you did not make this request, please contact our support team.</h4>
        <h4><strong>"This is your code to reset your password:"</strong></h4>
        <h1>{{ $mailData['code'] }}</h1>
        <h5>Flick Staff</h5>
    </div>

    <div class="footer">
        <p>If you have any issues, please contact support at <a href="mailto:support@flick.com" class="highlight">support@flick.com</a></p>
    </div>
</body>
</html>
