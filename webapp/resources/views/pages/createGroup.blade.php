@extends('layouts.app')

@section('content')
<div class="group-form-container">
    <form class="group-form" action="{{ route('groups.store') }}" method="POST">
        @csrf
        <h1>Create a New Group</h1>

        @if (session('success'))
            <p class="success-message">{{ session('success') }}</p>
        @endif

        @if ($errors->any())
            <p class="error-message">
                {{ implode(', ', $errors->all()) }}
            </p>
        @endif

        <label for="name">Group Name</label>
        <input
            type="text"
            name="name"
            id="name"
            class="form-control"
            placeholder="Enter group name"
            value="{{ old('name') }}"
            required
        >

        <button type="submit" class="create-group-button">Create Group</button>
    </form>
</div>
@endsection
