@extends('layouts.app')

@section('content')
<div class="section">
    <div class="friends-page">
        @if ($user->id === Auth::id())
            <h1 class="tittle-friendpage">Your Friends</h1>
        @else
            <h1 class="tittle-friendpage">{{ $user->name }}'s Friends</h1>
        @endif
        <ul>
            @if ($user->friends->isEmpty())
                @if ($user->id === Auth::id())
                    <p>You have no friends yet. Start connecting with people!</p>
                @else
                    <p>This User has no friends</p>
                @endif
            @else
                @foreach ($user->friends as $friend)
                    <li>
                        <a href="{{ route('profile', $friend->id) }}">
                            <img src="{{ $friend->profile_picture ? asset('storage/' . $friend->profile_picture) : asset('default-profile.png') }}" alt="Profile pic" class="profile-pic">
                            <p>{{ $friend->name }}</p>
                        </a>
                    </li>
                @endforeach
            @endif
        </ul>
    </div>
</div>
@endsection