@extends('layouts.app')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/pages/profile.css') }}">
@endpush

@push('scripts')
    <script src="{{ asset('js/partials/post.js') }}"></script>
    <script src="{{ asset('js/pages/profile.js') }}"></script>
@endpush

@section('content')
    <div class="view-modal hidden">
        <div class="view-content"></div>
    </div>
    <div class="profile-page">
        <h1 class="profile-section">{{ $user->name }}</h1>
        @if (Auth::check())
            @if ((( Auth::user()->isAdmin() && !$user->isAdmin()) || Auth::user()->isSuper()) && (!$user->isBlockedByAdmin() &&  Auth::user()->id != $user->id) )	
                
            <button onclick="handleBlockFromFlick({{ $user->id }})" class="button primary-btn profile-btn">
                Block From Flick
            </button>
            @elseif ((( Auth::user()->isAdmin() && !$user->isAdmin()) || Auth::user()->isSuper()) && ($user->isBlockedByAdmin() && Auth::user()->id != $user->id) )
                
                <button onclick="handleUnblockFromFlick({{ $user->id }})" class="button primary-btn profile-btn">
                    Unblock From Flick
                </button>
        
            @endif

            @if (Auth::user()->id == $user->id || ( Auth::user()->isAdmin() && !$user->isAdmin()) || Auth::user()->isSuper())
                
                <button onclick="handleDeleteAccount({{ $user->id }})" class="trash-button">
                    <img src="{{ asset('images/icon/trash.png') }}" class="trash-icon" width="20" height="20">
                    Delete account
                </button>
        
            @endif
        @endif
        <img src="{{ $user->profile_picture ? asset('storage/' . $user->profile_picture) : asset('default-profile.png') }}" alt="Profile pic" class="profile-pic">
        <p class="profile-section">Age: {{ $user->age }}</p>
        <p class="profile-section">
            <a href="{{ route('friends.list', $user->id) }}">
                Friends: {{ $user->friends()->count() }}
            </a>
        </p>
        
        <div class="basic-center-container">
            @if (Auth::check())
                @if (Auth::user()->id == $user->id ||  Auth::user()->isAdmin())
                    <a href="{{ route('profile.edit', $user->id) }}" class="button primary-btn profile-btn" >
                       Edit Profile
                    </a>
                @endif
                @if (Auth::user()->id == $user->id)
                    <a href="{{ route('logout') }}" class="button primary-btn profile-btn">
                        Log Out
                    </a>
                @endif
                <section id="friend-button">
                    @if (Auth::check())
                        @if (Auth::user()->id != $user->id)
                            <button id="state{{$user->id}}" class="button primary-btn profile-btn" onclick="updatestate({{$user->id}}, {{Auth::user()->id}})">
                                @if (Auth::user()->isFriendWith($user))
                                    Remove Friend
                                @elseif (Auth::user()->hasSentRequest($user))
                                    Cancel request
                                @elseif (Auth::user()->hasReceivedRequest($user))
                                    Accept Request
                                @elseif (Auth::user()->hasReceivedRequest($user))
                                    Reject Request
                                @else
                                    Add friend
                                @endif
                            </button>
                        @endif
                    @endif
                </section>
                <section class="block-button">
                    @if (Auth::check() && Auth::user()->id != $user->id && !$user->isAdmin())
                        @if (Auth::user()->hasBlocked($user))
                            <button id="block-state{{$user->id}}" class="button primary-btn profile-btn1" onclick="blockUser({{$user->id}}, {{Auth::user()->id}})">
                                Unblock
                            </button>
                        @elseif (Auth::user()->isBlocked($user))
                            <button id="block-state{{$user->id}}" class="button primary-btn profile-btn1" onclick="blockUser({{$user->id}}, {{Auth::user()->id}})">
                                Block
                            </button>
                        @endif
                    @endif
                </section>

                <section class="Message-button">
                    @if (Auth::check() && Auth::user()->id != $user->id)
                        @php
                            $userId1 = min(Auth::user()->id, $user->id);
                            $userId2 = max(Auth::user()->id, $user->id);
                        @endphp

                        <a href="{{ route('messages.index', ['recipient' => $userId1 . '.' . $userId2]) }}" class="button primary-btn profile-btn1">
                            Message
                        </a>
                    @endif
                </section>
            @endif
        </div>

        <div class="profile-post-container">
                <div class="horizontal-container">
                    <h3 class="smaller-title">POSTS</h3>
                    <a href="{{ route('add-post') }}" class="button primary-btn">+</a>
                </div>
                <div class="posts multiple-post-container">
                    @if($posts->isEmpty())
                        <p>You have no posts. Start creating one now!</p>
                    @else
                        @foreach($posts as $post)
                            @include('partials.post', ['post' => $post])
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

