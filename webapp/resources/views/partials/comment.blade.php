<div class="{{ $isReply ? 'comment-reply' : 'comment' }}" data-id="{{ $comment->id }}"  
@if ($isReply && $comment->parent_id) data-parent-id="{{ $comment->parent_id }}" @endif>
    <div class="comment-header" onclick="redirectToProfile({{ $comment->user->id }})">
        <img src="{{ $comment->user->profile_picture ? asset('storage/' . $comment->user->profile_picture) : asset('default-profile.png') }}" alt="Profile Picture" class="comment-profile-pic" height=30px width=30px>
        <span class="comment-author">{{ $comment->user->name }}</span>
        <span class="comment-date">{{ $comment->date->diffForHumans(['short' => true]) }}</span>
    </div>
    <div class="comment-content">
        <p class="comment-text">{{ $comment->content }}</p>
        <button class="show-more-button hidden" onclick="toggleShowMore(this)">Show More</button>
    </div>
    <div class="comment-actions">
        <button data-type="comment" class="comment-like-button {{ $comment->isLiked ? 'liked' : 'unliked' }}" onclick="toggleCommentLike({{ $comment->id }}, this)">
            <i class="like-icon {{ $comment->isLiked ? 'fas fa-heart' : 'far fa-heart' }}"></i>
            <span class="like-count">{{ $comment->like_num ?? 0}}</span>
        </button>
        <button class="reply-button blank-button" onclick="replyToComment({{ $comment->id }}, '{{ addslashes($comment->user->name) }}')">
            <i class="fas fa-reply"></i>
            @if(!isset($isReply) || !$isReply)
                <span class="reply-count">{{ $comment->reply_num ?? 0 }}</span>
            @endif
        </button>
        @if(auth()->id() === $comment->user->id || auth()->user()->isAdmin())
            <button onclick="editComment({{ $comment->id }})" class="edit-comment-button blank-button">
                <img src="{{ asset('images/icon/edit.png') }}" alt="Edit Comment" class="menu-icon" width="17" height="17">
            </button>
            <button onclick="deleteComment({{ $comment->id }})" class="blank-button">
                <img src="{{ asset('images/icon/trash.png') }}" alt="Delete Comment" class="menu-icon" width="17" height="17">
            </button>
        @endif
        @if($comment->edited)
            <span class="comment-edited-label">Edited</span>
        @endif
    </div>
    <div class="replies-container">
        
    </div>
    <div class="replies-action-container">
        @if($comment->reply_num > 0)
            <button class="show-replies-button blank-button" onclick="showReplies({{ $comment->id }}, this)">
                <i class="fa-solid fa-angle-down"></i>See Replies
            </button>
            <button class="hide-replies-button hidden blank-button" onclick="hideReplies({{ $comment->id }}, this)">
                <i class="fa-solid fa-angle-up"></i>Hide Replies
            </button>
        @endif
    </div>
</div>