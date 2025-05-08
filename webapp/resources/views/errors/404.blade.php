<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page Not Found</title>
    <link href="{{ asset('css/errors/404.css') }}" rel="stylesheet">
    <script src="{{ asset('js/errors/404.js') }}" defer></script>
</head>
<body>
    <div class="error-container">
        <h1>404</h1>
        <p class="error-message">Oops! The page you are looking for does not exist.</p>
        <p class="redirect-message">You will be redirected to the main page in 5 seconds...</p>
        <a class="back-link" href="/">Click here to go back now</a>
    </div>
</body>
</html>
