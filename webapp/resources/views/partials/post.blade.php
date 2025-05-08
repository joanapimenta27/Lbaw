<div class="post" 
    data-media-files="{{ json_encode($post->media->sortBy('order')->values()->map(function ($media) {
        return [
            'url' => asset('storage/' . $media->file_path),
            'type' => $media->file_type,
        ];
    })) }}"
    data-post-id="{{ $post->id }}">
    <div class="post-header">
        <div class="stackable">
            <div class="post-header-part" id="name-part" onclick="redirectToProfile({{ $post->author->id }})">
                <img src="{{ $post->author->profile_picture ? asset('storage/' . $post->author->profile_picture) : asset('default-profile.png') }}" alt="Profile pic" class="mini-profile-pic">
                <span class="post-name">{{ $post->author->name }}</span>
            </div>
            <div class="post-header-part" id="date-part">
                <span class="post-date">{{ $post->date->format('d/m/y') }}</span>
                <img src="{{ asset($post->is_public ? 'images/icon/public.png' : 'images/icon/lock.png') }}" alt="{{ $post->is_public ? 'Public' : 'Private' }}" id="state-icon" width="40" height="40">
            </div>
        </div>
        @if(auth()->check())
            @if((auth()->user()->id === $post->author->id) || (Auth::user()->isAdmin()))
                <div class="stackable" id="config-stack">
                    <div class="post-header-part" id="post-owner-header-part">
                        <span class="tooltip-container">
                            <button class="blank-button" onclick="handlePostMenuClick(this)">
                                <img src="{{ asset('images/icon/settings.png') }}" alt="Post Menu" class="menu-icon" id="post-menu-icon" width="33" height="33">
                            </button>
                            <span class="tooltip-text">Post Settings</span>
                        </span>
                    </div>
                    <div class="post-header-part post-menu-options hidden" id="edit-part">
                        <span class="tooltip-container">
                            <button onclick="handleEditPost(&quot;{{ route('edit-post', ['id' => $post->id]) }}&quot;)" class="blank-button">
                                <img src="{{ asset('images/icon/edit.png') }}" alt="Edit Post" class="menu-icon" id="post-menu-icon" width="40" height="40">
                            </button>
                            <span class="tooltip-text">Edit Post</span>
                        </span>
                        <span class="tooltip-container">
                            <button onclick="handleDeletePost(&quot;{{ route('delete-post', ['id' => $post->id]) }}&quot;)" class="blank-button">
                                <img src="{{ asset('images/icon/trash.png') }}" alt="Delete Post" class="menu-icon" id="post-menu-icon" width="40" height="40">
                            </button>
                            <span class="tooltip-text">Delete Post</span>
                        </span>
                    </div>
                </div>
            @endif
        @endif
    </div>
    <div class="post-content">
        <h2 class="post-title" onclick="viewPost({{ $post->id }}, 'post')">{{ $post->title }}</h2>
        <div class="post-slide-container">
            <div class="post-media-container" onclick="viewPost({{ $post->id }}, 'post')">
                <!-- Media preview will be added here by JavaScript -->
                <button id="left-arrow" class="nav-arrow" style="display:none;">&lt;</button>
                <button id="right-arrow" class="nav-arrow" style="display:none;">&gt;</button>
            </div>
        </div>
        @if($post->media->isNotEmpty())
            <div class="sepLine"></div>
        @endif
        <div class="post-description-container" onclick="viewPost({{ $post->id }}, 'post')">
            <p class="post-description">{{ $post->description }}</p>
        </div>
    </div>
    <div class="post-actions">
        <span class="tooltip-container">
            <button data-type="post" class="like-button {{ $post->isLiked ? 'liked' : 'unliked' }}"  onclick="toggleLike(event, {{ $post->id }})">
                <i class="like-icon {{ $post->isLiked ? 'fas fa-heart' : 'far fa-heart' }}"></i>
                <span class="like-count">{{ $post->like_num}}</span>
            </button>
            <span class="tooltip-text">Like this Post</span>
        </span>
        <span class="tooltip-container">
            <button class="comment-button" onclick="viewPost({{ $post->id }}, 'comment')">
                <i class="fas fa-comment"></i> {{$post->comment_num}}
            </button>
            <span class="tooltip-text">Comment on this post</span>
        </span>
        <span class="tooltip-container">
            <button class="share-button" onclick="viewPost({{ $post->id }}, 'share')"> <i class="fas fa-share"></i> {{$post->share_num}}</button>
            <span class="tooltip-text">Share post with group</span>
        </span>
        <span class="tooltip-container">
            <button class="repost-button"> <i class="fas fa-retweet"></i> {{$post->flick_num}}</button>
            <span class="tooltip-text">Flick (under maintenence)</span>
        </span>
    </div>
</div>