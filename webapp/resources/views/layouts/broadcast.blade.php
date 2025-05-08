<div class="message-container right">

  <div class="message-user">
    <span class="user-name">{{ $user->name }}</span>
  </div>

  <div class="message-content">
    <span class="content-m">{{ $message->content }}</span>
    @if (isset($pic))
    <div class="user-profile-pic">
        <img src="{{ $pic }}" alt="Profile Picture" />
    </div>
    @endif

    
  </div>

  <!-- Message Date -->
  <div class="message-date">
    <span class="date">
        {{ \Carbon\Carbon::parse($message->date)->format('d F') ?? now()->format('d F') }}
    </span>
  </div>

</div>

