@extends('layouts.app')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/pages/login.css') }}">
@endpush

@section('content')
<div class="auth-form-container">
    <form class="auth-form" method="POST" action="{{ route('login') }}">
        <h2 class='general-title'>Welcome Back !</h2>
        {{ csrf_field() }}
        @if ($redirectReason)
            <p class="info">{{ $redirectReason }}</p>
        @endif

        <label for="email">E-mail</label>
        <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus>
        
        @if ($errors->has('email'))
            <span class="error">
            {{ $errors->first('email') }}
            </span>
        @endif

        <label for="password" >Password</label>
        <input id="password" type="password" name="password" required>
        @if ($errors->has('password'))
            <span class="error">
                {{ $errors->first('password') }}
            </span>
        @endif
        <!-- 
        <label>
            <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}> Remember Me
        </label>
        -->
        <button class="log_reg_button" type="submit">
            Login
        </button>
        <a class="button button-outline" href="{{ route('register') }}">Register</a>



        <div class="forgot-password-link">
            <a href="{{ route('recoverPassword') }}" class="forgot-password">Forgot Password?</a>
        </div>

        <div class="anony-link">
            <a href="{{ route('home', ['type' => 'public']) }}" class="explore-anony">Explore Anonymously</a>
        </div>
        
        @if (session('success'))
            <p class="success">
                {{ session('success') }}
            </p>
        @endif

    

    </form>
</div>
@endsection