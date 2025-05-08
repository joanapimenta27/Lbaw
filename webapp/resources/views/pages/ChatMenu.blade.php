@extends('layouts.app')

@push('scripts')
    <script src="{{ asset('js/pages/chatMenu.js') }}"></script>
@endpush

@section('content')
<a href="{{ route('groups.create') }}" class="button-create">Create Group</a>

<div class="chat-group-container">

    
    <div class="chat-menu-container">
        <h1>Your Chats</h1>
        @if($chats->isEmpty())
            <p>You have no chats yet.</p>
        @else
            <ul class="chat-list">
                @foreach($chats as $chat)
                    <li class="chat-item">
                        @if ($chat->hasBlocked(Auth::user()))
                            <a href="#" class="chat-link blocked-chat" data-chat-id="{{ $chat->id }}">
                        @else
                            <a href="{{ $chat->route }}" class="chat-link">
                        @endif
                            <div class="chat-info"> 
                                <span class="chat-name">{{ $chat->name }}</span>
                            </div>
                        </a>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>

    <div class="group-menu-container">
        <h2>Your Groups</h2>
        @if($groups->isEmpty())
            <p>No groups available.</p> 
        @else
            <ul class="group-list">
                @foreach($groups as $group)
                    <li class="group-item">
                        <a href="{{  $group->route }}" class="group-link">
                            {{ $group->name }}
                        </a>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>
</div>
@endsection
