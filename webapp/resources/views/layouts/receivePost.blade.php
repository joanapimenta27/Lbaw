
<div class="message-container left">

  <div class="message-user">
    @if (isset($user) && is_object($user)) 
        <span class="user-name">{{ $user->name ?? 'Unknown User' }}</span>
    @else
        <span class="user-name">{{ $user ?? 'Unknown User' }}</span>
    @endif
  </div>

  <div class="message-content">
    
        @include('partials.post', ['post' => $post])    
  </div>

  <!-- Message Date -->
  <div class="message-date">
      <span class="date">{{ \Carbon\Carbon::parse($date)->format('d F') ?? now()->format('d F') }}</span>
  </div>

</div>
