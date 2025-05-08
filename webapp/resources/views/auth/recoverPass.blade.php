@extends('layouts.app')

@section('content')
    <div class="recover-password-container">
        <h2 class="recover-password-heading">Recover Your Password</h2>
        <form method="POST" action="{{ route('recoverPassword') }}">
            @csrf

            <div class="recover-password-form-group">
                <label for="email" class="recover-password-label">Email Address</label>
                <input type="email" id="email" name="email" class="recover-password-input" value="{{ old('email') }}" required>
                @error('email')
                    <div class="recover-password-text-danger">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="recover-password-submit">Send Recovery Code</button>
        </form>

        

    </div>
@endsection
