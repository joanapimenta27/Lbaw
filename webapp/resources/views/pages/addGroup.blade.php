@extends('layouts.app')


@section('content')
@push('scripts')
    <script src="{{ asset('js/groups/addUser.js') }}" defer></script>
    <script src="{{ asset('js/groups/groupSearch.js') }}" defer></script>

@endpush
<meta name="group-id" content="{{ $groupID }}">
<meta name="remove" content="0">

    <title>User Search and Invite</title>
   


    <div id="user-search-container">
        <h1>Search and Invite Users</h1>
        
      
        <label for="search"></label>
        <input type="text" id="search" placeholder="Search users by username" />

        
        <ul id="user-list"></ul>

       
        <h3>Selected Users:</h3>
        <ul id="selected-users-list"></ul>

        <button id="send-invites-button">Send Invites</button>
    </div>

    



@endsection