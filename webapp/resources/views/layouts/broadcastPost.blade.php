<div class="message-container right">

    <div class="message-user">
      <span class="user-name">{{ $user->name }}</span>
    </div>
  
    <div class="message-content">
        @include('partials.post', ['post' => $post])
     
      
    </div>
  
    <!-- Message Date -->
    <div class="message-date">
      <span class="date">
          {{ \Carbon\Carbon::parse($message->date)->format('d F') ?? now()->format('d F') }}
      </span>
    </div>
  
  </div>
  
  