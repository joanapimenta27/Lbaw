<div class="message-container left">

  <!-- User's Name -->
  <div class="message-user">
    @if (isset($user) && is_object($user)) 
        <span class="user-name">{{ $user->name ?? 'Unknown User' }}</span>
    @else
        <span class="user-name">{{ $user ?? 'Unknown User' }}</span>
    @endif
  </div>

  <div class="message-content">
    @if (isset($pic))
    <div class="user-profile-pic">
        <img src="{{ $pic }}" alt="Profile Picture" />
    </div>
    @endif
    
    <span class="content-m">{{ $content ?? 'No content' }}</span>
  </div>

  <!-- Message Date -->
  <div class="message-date">
      <span class="date">{{ \Carbon\Carbon::parse($date)->format('d F') ?? now()->format('d F') }}</span>
  </div>

</div>
