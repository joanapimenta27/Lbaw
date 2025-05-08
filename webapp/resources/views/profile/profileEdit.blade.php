@extends('layouts.app')

@section('content')
    <div class="EditProfile">
        <div class="white-container">
            <div class="title-container">
                <a href="{{  route('profile', ['userId' => $user->id]) }}"><i class="fa-solid fa-arrow-left goBack-arrow"></i></a>
                <h1>Edit Profile</h1>
            </div>

            <form action="{{ route('profile.update', $user->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT') 
                
                <div class="form-group">
                    <label for="username">Edit UserName</label>
                    <input type="text" name="username" id="username" class="text_input" value="{{ old('username', $user->username) }}" required>
                    @error('username')
                        <div class="error-text">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="name">Edit Name</label>
                    <input type="text" name="name" id="name" class="text_input" value="{{ old('name', $user->name) }}" required>
                    @error('name')
                        <div class="error-text">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="email">Edit Email</label>
                    <input type="email" name="email" id="email" class="text_input" value="{{ old('email', $user->email) }}" required>
                    @error('email')
                        <div class="error-text">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="profile_picture">Upload New Profile Picture:</label>
                    <input type="file" name="profile_picture" id="profile_picture" accept="image/*" class="form-control">
                    @error('profile_picture')
                        <div class="error-text">{{ $message }}</div>
                    @enderror
                </div>

            @if(Auth::user()->isAdmin() && !$user->isAdmin() || (Auth::user()->isSuper() && Auth::user()->id != $user->id))
                <span class="fake-label">Permissions 
                    <span class="tooltip-container">
                        <img src="{{ asset('images/icon/helper.png') }}" width="15" height="15" alt="Help Icon">
                        <span class="tooltip-text">This controls whether the user is admin or not.</span>
                    </span>
                    <div class="form-group checkbox-wrapper-10 padding-left-10px">

                        @if($user->isAdmin())
                            <input class="tgl tgl-flip " id="admin_checkbox" name="admin_checkbox" type="checkbox" checked >
                        @else
                            <input class="tgl tgl-flip " id="admin_checkbox" name="admin_checkbox" type="checkbox" >
                        @endif

                        <label class="tgl-btn" data-tg-off="User" data-tg-on="Admin" for="admin_checkbox"></label>

                        <input class="tgl tgl-flip " id="admin_checkbox" name="admin_checkbox" type="checkbox">
                    </div>
                @endif
                @if(!Auth::user()->isAdmin() )
                        <!-- Password section -->
                    <div class="form-group">
                        <label for="current_password">Current Password
                            <span class="tooltip-container">
                                <img src="{{ asset('images/icon/helper.png') }}" width="15" height="15" alt="Help Icon">
                                <span class="tooltip-text">You need to fill this to save any changes!</span>
                            </span>
                        </label>
                        <input type="password" name="current_password" id="current_password" class="text_input" required>
                        @error('current_password')
                            <div class="error-text">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="password">New Password</label>
                        <input type="password" name="password" id="password" class="text_input">
                        @error('password')
                            <div class="error-text">{{ $message }}</div>
                        @enderror
                    </div>
                @endif

                <button type="submit" class="button primary-btn save-button">Save Changes</button>
            </form>
        </div>
    </div>
@endsection
