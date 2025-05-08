
<div id="notification-container">
    @if($notifications->isEmpty())
        <div class="no-notifications">
            <p>You have no notifications.</p>
        </div>
    @else
        <ul class="list-group">
            @foreach($notifications as $notification)
                <li class="list-group-item">
                    <div class="notification-header">
                        <div class="notification-user">
                            @if ($notification->user)
                                <a href="{{ route('profile', ['userId' => $notification->user->id]) }}">
                                    <span>{{ $notification->user->name }}</span>
                                </a>
                            @else
                                <span>User not found</span>
                            @endif
                        </div>
                        <div class="notification-date">
                            @if ($notification->date)
                                <span>{{ $notification->date }}</span>
                            @else
                                <span>Date not available</span>
                            @endif
                        </div>
                    </div>
                    <div class="notification-body">
                        <p>{{ $notification->content }}</p>
                    </div>
                </li>
            @endforeach
        </ul>
    @endif
</div>

