<div class="edit-frame">
    <button id="hamburgerButton-edit" class="hamburger-button">Edit Group</button>
    <div id="manageUsersMenu" class="advanced-filters hidden">
        <h3>Manage users in group:</h3>

        <ul>
            @php
                $currentRoute = request()->route()->getName();
                $groupName = $groupName ?? 'defaultGroup';
                $routes = [
                    ['name' => 'groups.add', 'label' => 'Add Users', 'url' => route('groups.add', $groupName)],
                    ['name' => 'groups.remove', 'label' => 'Remove Users', 'url' => route('groups.remove', $groupName)],
                    ['name' => 'groups.edit', 'label' => 'Edit Group', 'url' => route('groups.edit', $groupName)],
                ];
            @endphp

            @forelse ($routes as $route)
            <li class="edit-option">
                <a href="{{ $route['url'] }}" class="button-option">
                    <span class="edit-text">{{ $route['label'] }}</span>
                    @if($currentRoute == $route['name'])
                        <span class="checkmark">&#10003;</span>
                    @endif
                </a>
            </li>
            @empty
                <li class="edit-item">No actions available</li>
            @endforelse
        </ul>
    </div>
</div>
