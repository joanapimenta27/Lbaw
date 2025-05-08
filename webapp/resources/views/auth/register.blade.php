@extends('layouts.app')

@section('content')
<div class="form-container">
  <form class="register-form" method="POST" action="{{ route('register') }}">
    {{ csrf_field() }}
    <h2 class='general-title'>Join Us</h2>

    <div class="form-group">
      <label for="username">Username</label>
      <input id="username" class="text_input" type="text" name="username" value="{{ old('username') }}" maxlength=20 placeholder="..." required autofocus>
      @if ($errors->has('username'))
        <span class="error">
            {{ $errors->first('username') }}
        </span>
      @endif
    </div>

    <div class="form-group">
      <label for="name">Name</label>
      <input id="name" type="text" class="text_input" name="name" value="{{ old('name') }}" maxlength=20 placeholder="..." required>
      @if ($errors->has('name'))
        <span class="error">
            {{ $errors->first('name') }}
        </span>
      @endif
    </div>

    <div class="form-group">
      <label for="email">E-Mail Address</label>
      <input id="email" type="email" class="text_input" name="email" value="{{ old('email') }}" maxlength=50 placeholder="..." required>
      @if ($errors->has('email'))
        <span class="error">
            {{ $errors->first('email') }}
        </span>
      @endif
    </div>

    <div class="form-group">
      <label for="age">Age</label>
      <input id="age" type="number" class="text_input" name="age" placeholder="..." required min="13" max="120">
      @if ($errors->has('age'))
        <span class="error">
            {{ $errors->first('age') }}
        </span>
      @endif
    </div>

    <div class="form-group">
      <label for="password">Password</label>
      <input id="password" type="password" class="text_input" name="password" placeholder="..." maxlength=30 required>
      @if ($errors->has('password'))
        <span class="error">
            {{ $errors->first('password') }}
        </span>
      @endif
    </div>

    <div class="form-group">
      <label for="password-confirm">Confirm Password</label>
      <input id="password-confirm" type="password" class="text_input" name="password_confirmation" maxlength=30 placeholder="..." required>
    </div>
    @if(!auth()->check())
      <button class="button primary-btn log_reg_button" type="submit">Register</button>
      <a class="button button-outline" href="{{ route('login') }}">Login</a>
    @elseif(Auth::user()->isAdmin())
      <span class="fake-label">Permissions 
          <span class="tooltip-container">
              <img src="{{ asset('images/icon/helper.png') }}" width="15" height="15" alt="Help Icon">
              <span class="tooltip-text">This controls whether the user is admin or not.</span>
          </span>
      </span>
      <div class="form-group checkbox-wrapper-10 padding-left-10px">
          <input class="tgl tgl-flip " id="admin_checkbox" name="admin_checkbox" type="checkbox">
          <label class="tgl-btn" data-tg-off="User" data-tg-on="Admin" for="admin_checkbox"></label>
      </div>
      <button class="button primary-btn log_reg_button" log_reg_button" type="submit">Create a New User</button>
    @endif
  </form>
</div>
@endsection
  