@extends('layouts.app')


@section('content')
<button class="btn btn-back" onclick="window.history.back()">Go Back</button>

<div class="edit-group-frame">
    <h2>Edit Group Options</h2>

    <div class="edit-group-options">
        
        <form action="{{ route('groups.update', ['group' => $group->id]) }}" method="POST" class="edit-group-form">
            @csrf
            @method('PUT')
            <label for="groupName">Edit Group Name:</label>
            <input type="text" id="groupName" name="group_name" value="{{ $group->name }}" required>
            <button type="submit" class="btn btn-save">Save Changes</button>
        </form>

       
        <form action="{{ route('groups.destroy', ['group' => $group->id]) }}" method="POST" class="delete-group-form">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-delete" onclick="return confirm('Are you sure you want to delete this group? This action cannot be undone.')">
                Delete Group
            </button>
        </form>
    </div>
</div>

@endsection