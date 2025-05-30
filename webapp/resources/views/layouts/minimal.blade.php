<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <!-- CSRF Token -->
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>@yield('title')</title>

        <!-- Styles -->
        <link href="{{ url('css/app.css') }}" rel="stylesheet">
        @stack('styles')

        <!-- Scripts -->
        @stack('scripts')
    </head>
    <body>
        @yield('content')
    </body>
</html>