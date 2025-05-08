@extends('layouts.app')

@section('content')

<div class="view-modal hidden">
    <div class="view-content"></div>
</div>


@push('scripts')
    <script src="{{ asset('js/message/message.js') }}" defer></script>
    <script src="{{ asset('js/message/pagination.js') }}" defer></script>
    <script src="https://js.pusher.com/7.0/pusher.min.js"></script>
    @endpush

<meta name="csrf-token" content="{{ csrf_token() }}">
<meta name="reciver-id" content="{{ $recipient }}">
<meta name="user-id" content="{{ auth()->id() }}">
<meta name="pusher-key" content="{{ $pusherKey }}">
<meta name="pusher-cluster" content="{{ $pusherCluster }}">

@if(isset($group))
    <meta name="group" content="{{ json_encode($group) }}">
@endif
@if(isset($group) && $group->owner->id == Auth::id())
    @include('partials.editGroup', ['groupName' => $group->name])
@elseif(isset($group))
    <form action="{{ route('groups.leave', ['group' => $group->id]) }}" method="POST">
        @csrf
        @method('POST')
        <button type="submit" class="button-add">Leave Group</button>
    </form>
@endif


<div class="container-M">
    <!-- CSRF Token -->
   
    
    
    @if(isset($otherUser))
        <h1>{{ $otherUser->name }} Chat</h1>
    @else
        <h1>{{ $group->name }} Chat</h1>
    @endif


    <div class="messages">
        @if ($messagesCount < $maxMessages)
        <form method="GET" action="{{ url()->current() }}" style="text-align: center; margin-bottom: 10px;">
            <label for="messagesCount"></label>
            <input type="hidden" name="messagesCount" value="{{ min($messagesCount + 10, $maxMessages) }}">
            <button type="submit" class="btn btn-primary">Load More Messages</button>
        </form>
    @endif  

    @foreach (array_reverse($messages) as $message)
    @if (isset($otherUser))  
   
    
        @if ($message->receiver_id == $mainUser->id)
            @include('layouts.receive', [
                'content' => $message->content,
                'date' => $message->date,
                'user' => $otherUser])
        @else
            @include('layouts.broadcast', [
                'content' => $message->content,
                'date' => $message->date,
                'user' => $mainUser,
                'pic' =>  asset('storage/' . $mainUser->profile_picture) ])
        @endif
    @else  <!-- If group chat -->
        @php
            $sender = \App\Models\User::find($message->sender_id);

        @endphp

        @if ($message->sender_id == $mainUser->id)
            @if ($message->post_id !== null)
            @php
                $post = \App\Models\Post::find($message->post_id);

            @endphp

        {{-- Include broadcastPost layout for messages with a post --}}
                @include('layouts.broadcastPost', [
                'post' => $post, 
                'date' => $message->date,
                'user' => $mainUser,
                ])
            @else
        {{-- Include regular broadcast layout --}}
                @include('layouts.broadcast', [
                'content' => $message->content,
                'date' => $message->date,
                'user' => $mainUser,
                'pic' => $sender->profile_picture 
                ? asset('storage/' . $sender->profile_picture) 
                : asset('default-profile.png')
                ])
            @endif
        @else
            @if ($message->post_id !== null)
        {{-- Include receivePost layout for messages with a post --}}
                @php
                $post = \App\Models\Post::find($message->post_id);
                @endphp
            @include('layouts.receivePost', [
                'post' => $post, 
                'date' => $message->date,
                'user' => $sender,
                'pic' => $sender->profile_picture 
                ? asset('storage/' . $sender->profile_picture) 
                : asset('default-profile.png')])
            @else
    {{-- Include regular receive layout --}}
            @include('layouts.receive', [
            'content' => $message->content,
            'date' => $message->date,
            'user' => $sender,
            'pic' => $sender->profile_picture 
                ? asset('storage/' . $sender->profile_picture) 
                : asset('default-profile.png')
        ])
        @endif
    @endif

    @endif
@endforeach
    </div>
    
    
        
    </div>
    <div id="message-error" class="alert alert-danger" style="display: none;">Message cannot be empty.</div>

    <form id="sendMessageForm">
        <div class="input-container">
            <label for="message"></label>
            <input type="text" id="message" name="message" placeholder="Enter message..." autocomplete="off">
            <button type="submit">
                <i class="fa fa-paper-plane"></i> 
            </button>
        </div>
    </form>

    
    
</div>
@endsection
