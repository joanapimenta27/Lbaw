
<meta name="csrf-token" content="{{ csrf_token() }}">
<meta name="pusher-key" content="{{ config('broadcasting.connections.pusher.key') }}">
<meta name="pusher-cluster" content="{{ config('broadcasting.connections.pusher.options.cluster') }}">


<div class="close-button-container">
    <button class="close-modal blank-button" onclick="closePostView()">
        <img src="{{ asset('images/icon/cross.png') }}" alt="Edit Post" class="menu-icon" id="post-menu-icon" width="25" height="25">
    </button>
</div>
<div class="post-view-content"
    data-media-files="{{ json_encode($post->media->sortBy('order')->values()->map(function ($media) {
        return [
            'url' => asset('storage/' . $media->file_path),
            'type' => $media->file_type,
        ];
    })) }}">
    <div class="post-view-details">
            <div class="post-view-media">
                <div class="post-slide-container">
                    <div class="post-media-container">
                        <!-- Media preview will be added here by JavaScript -->
                        <button id="left-arrow" class="nav-arrow" style="display:none;">&lt;</button>
                        <button id="right-arrow" class="nav-arrow" style="display:none;">&gt;</button>
                    </div>
                </div>
            </div>
        <!--                                      COMMENTS                                               -->
        <div class="comments-container hidden">
            <div class="toggle-down-button-container">
                <button class="button primary-btn toggle-down-button" onclick="viewPost({{ $post->id }}, 'post')">Switch to Post Info</button>
            </div>
            <div class="titleption-container">
                <div class="title-container">
                    <h2 class="post-view-title">Comments</h2>
                </div>
                <div class="comments description-container">
                    <!-- comments will be added here by JavaScript -->
                </div>
                <div class="loading-indicator hidden">Loading more comments...</div>
            </div>
            <div class="edit-notifier-wrapper">
                <div id="edit-notifier" class="edit-notifier hidden">
                    <span>Editing your comment...</span>
                    <div class="cancel-container">
                        <button class="cancel-edit-button blank-button" onclick="cancelEdit()">
                            <img src="{{ asset('images/icon/cross_dark.png') }}" alt="Edit Comment" class="menu-icon" id="post-menu-icon" width="15" height="15">
                        </button>
                    </div>
                </div>
            </div>
            <div class="add-comment-container">
                <label for="add-comment"></label>

                <input type="text" class="add-comment-input" placeholder="Write a comment..." id="new-comment-input" maxlength="1500">
                <button class="button primary-btn add-comment-button" onclick="addComment({{ $post->id }})">
                    <i class="fas fa-arrow-up"></i>
                </button>
            </div>
            <div class="post-view-actions">
                <button class="like-button {{ $post->isLiked ? 'liked' : 'unliked' }}"  onclick="toggleLike(event, {{ $post->id }})">
                    <i class="like-icon {{ $post->isLiked ? 'fas fa-heart' : 'far fa-heart' }}"></i>
                    <span class="like-count">{{ $post->like_num}}</span>
                </button>
                <button class="comment-button" onclick="viewPost({{ $post->id }}, 'comment')">
                    <i class="fas fa-comment"></i>
                    <span class="comment-count">{{ $post->comment_num }}</span>
                </button>
                <button class="share-button" onclick="viewPost({{ $post->id }}, 'share')"> <i class="fas fa-share"></i> {{$post->share_num}}</button>
                <button class="repost-button"> <i class="fas fa-retweet"></i> {{$post->flick_num}}</button>
            </div>
        </div>
        <!--                                     POST INFO                                               -->
        <div class="post-view-info hidden">
            <div class="personal-info" onclick="redirectToProfile({{ $post->author->id }})">
                <img src="{{ $post->author->profile_picture ? asset('storage/' . $post->author->profile_picture) : asset('default-profile.png') }}" alt="Profile pic" class="mini-profile-pic">
                <span class="post-view-name">{{ $post->author->name }}</span>
                <span class="post-view-date">{{ $post->date->format('d/m/y') }}</span>
                <img src="{{ asset($post->is_public ? 'images/icon/public.png' : 'images/icon/lock.png') }}" alt="{{ $post->is_public ? 'Public' : 'Private' }}" id="view-state-icon" width="35" height="35">
            </div>
            <div class="titleption-container">
                <div class="title-container">
                    <h2 class="post-view-title">{{ $post->title }}</h2>
                </div>
                @if ($post->description)
                    <div class="description-container">
                        <span class="post-view-description">{{ $post->description }}</span>
                    </div>
                @endif
            </div>
            <div class="post-view-actions">
                <button data-type="modal" class="like-button {{ $post->isLiked ? 'liked' : 'unliked' }}"  onclick="toggleLike(event, {{ $post->id }})">
                    <i class="like-icon {{ $post->isLiked ? 'fas fa-heart' : 'far fa-heart' }}"></i>
                    <span class="like-count">{{ $post->like_num}}</span>
                </button>
                <button class="comment-button" onclick="viewPost({{ $post->id }}, 'comment')">
                    <i class="fas fa-comment"></i>
                    <span class="comment-count">{{ $post->comment_num }}</span>
                </button>
                <button class="share-button" onclick="viewPost({{ $post->id }}, 'share')"> <i class="fas fa-share"></i> {{$post->share_num}}</button>
                <button class="repost-button"> <i class="fas fa-retweet"></i> {{$post->flick_num}}</button>
            </div>
        </div>
        <!--                                      SHARE                                              -->
        <div class="share-container hidden">
            <div class="toggle-down-button-container">
                <button class="button primary-btn toggle-down-button" onclick="viewPost({{ $post->id }}, 'post')">Switch to Post Info</button>
            </div>
            <div class="titleption-container">
                <div class="title-container">
                    <h2 class="post-view-title">Share to Group ...</h2>
                </div>
                <div class="comments description-container">
                    @foreach (Auth::user()->getAllGroups(Auth::user()->id) as $group)
                    <label>
                        <div class="group-entry">
                            <label for="group"></label>

                            <input type="radio" name="group" value="{{ $group->name }}" class="group-selection">

                            <span>{{ $group->name }}</span>
                        </div>
                    </label>
                    @endforeach

                    @if (Auth::user()->getAllGroups(Auth::user()->id)->isEmpty())
                        <p class="no-media">You are not part of any groups yet. Join or create a group to get started!</p>
                    @endif 
                </div>
            </div>
            <div class="share-button-container">
                <button class="button primary-btn share-btn" onclick="handleShare({{ $post->id }})"> Share </button>
            </div>
            <div class="post-view-actions">
                <button class="like-button {{ $post->isLiked ? 'liked' : 'unliked' }}"  onclick="toggleLike(event, {{ $post->id }})">
                    <i class="like-icon {{ $post->isLiked ? 'fas fa-heart' : 'far fa-heart' }}"></i>
                    <span class="like-count">{{ $post->like_num}}</span>
                </button>
                <button class="comment-button" onclick="viewPost({{ $post->id }}, 'comment')">
                    <i class="fas fa-comment"></i>
                    <span class="comment-count">{{ $post->comment_num }}</span>
                </button>
                <button class="share-button" onclick="viewPost({{ $post->id }}, 'share')"> <i class="fas fa-share"></i> {{$post->share_num}}</button>
                <button class="repost-button"> <i class="fas fa-retweet"></i> {{$post->flick_num}}</button>
            </div>
        </div>
    </div>
</div>
