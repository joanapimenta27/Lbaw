@extends('layouts.app')

@section('content')
    <div class="verify-container">
        <h2 class="verify-title">Verify Code and Set New Password</h2>

        <form method="POST" action="{{ route('verifyCode') }}" class="verify-form">
            @csrf

            <div class="verify-group">
                <label for="code">Enter Verification Code</label>
                <input type="text" id="code" name="code" autocomplete="off" required>
                @error('code')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>

            <div class="verify-group">
                <label for="newPassword">New Password</label>
                <input type="password" id="newPassword" name="newPassword" autocomplete="new-password" required>
                @error('newPassword')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>

            <div class="verify-group">
                <label for="newPassword_confirmation">Confirm New Password</label>
                <input type="password" id="newPassword_confirmation" name="newPassword_confirmation" autocomplete="new-password" required>
            </div>

            <button type="submit" class="verify-button">Reset Password</button>
        </form>

        <div class="recover-password-redirect">
            <p>Didn't receive the code? Try again or use a different email address.</p>
            <a href="{{ route('recoverPassword') }}" class="recover-password-verify-button">Go to Verify Code</a>
        </div>

    </div>

@endsection
