<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Styles -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="{{ url('css/app.css') }}" rel="stylesheet">
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

    @stack('styles')
    <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">

    <!-- Scripts -->
    @stack('scripts')
    <script src="{{ asset('js/app.js') }}" defer></script>
    <script src="{{ asset('js/layouts/header.js') }}" defer></script>
    <script src="{{ asset('js/partials/post.js') }}"></script>
    <script src="{{ asset('js/partials/postView.js') }}"></script>
    <script src="{{ asset('js/partials/comment.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>    
    <script src="https://js.pusher.com/7.0/pusher.min.js" defer></script>

    
    

</head>
<body>
    <main>
        @include('layouts.header')
        
        <section id="content">
            @yield('content') <!-- Content section -->
        </section>


    
    </main>

    @include('partials.toast.recoverPassword')
</body>
</html>
