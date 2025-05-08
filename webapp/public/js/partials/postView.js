let activeContext;
let activePostId;

function activateEnter(postId){
    const commentInput = document.getElementById("new-comment-input");

    commentInput.addEventListener("keydown", (event) => {
        if (event.key === "Enter" && commentInput.value.trim() !== "") {
            event.preventDefault();
            addComment(postId);
        }
    });
}

function hide(element) {
    if (!element) {
        console.error("Element not found.");
        return;
    }
    element.classList.add("hidden");
}

function viewPost(postId, context) {
    fetch(`/post/view/${postId}`)
        .then(response => {
            if (!response.ok) throw new Error("Failed to fetch post view.");
            return response.text();
        })
        .then(html => {
            const modal = document.querySelector(".view-modal");
            const modalContent = document.querySelector(".view-content");

            modalContent.innerHTML = html;

            const mediaFiles = JSON.parse(
                modalContent
                    .querySelector(".post-view-content")
                    .getAttribute("data-media-files") || "[]"
            );
            const mediaContainer = modalContent.querySelector(".post-media-container");
            const slideContainer = modalContent.querySelector(".post-slide-container");
            let currentMediaIndex = 0;

            if (mediaFiles.length > 0) {
                renderMedia(mediaContainer, mediaFiles, currentMediaIndex);
                createDots(mediaContainer, slideContainer, mediaFiles, currentMediaIndex);
            } else {
                mediaContainer.innerHTML = "<p class='no-media'>This post has no media</p>";
            }

            // Display the modal
            modal.classList.remove("hidden");
            document.body.classList.add("modal-open");

            // Close modal on background click
            modal.addEventListener("click", function (event) {
                if (event.target === event.currentTarget) {
                    closePostView();
                }
            });

            commentContainer = modalContent.querySelector(".comments-container");
            shareContainer = modalContent.querySelector(".share-container");
            postContainer = modalContent.querySelector(".post-view-info");
            activateEnter(postId);

            if (activeContext == context){
                context = 'post';
                activeContext = context;
            }
            else{
                activeContext = context;
            }

            if (context === 'share') {
                toggleShare(shareContainer, postId);
            }
            else {
                hide(shareContainer);
            }
            if (context === 'comment') {
                toggleComment(commentContainer, postId);
            }
            else {
                hide(commentContainer); 
            }
            if (context == 'post') {
                togglePost(postContainer);
            }
            else {
                hide(postContainer); 
            }

            activePostId = postId;
        })
        .catch((error) => console.error("Error fetching post view:", error));
}

function closePostView() {
    const modal = document.querySelector(".view-modal");
    modal.classList.add("hidden");
    document.body.classList.remove("modal-open");
    activeContext = null;
    editingCommentId = null;
    replyingToCommentId = null;

    const postViewActions = modal.querySelector(".post-view-actions");
    const commentCount = postViewActions.querySelector(".comment-count").textContent.trim();
    const shareCount = postViewActions.querySelector(".share-button").textContent.trim();
    const repostCount = postViewActions.querySelector(".repost-button").textContent.trim();

    const post = document.querySelector(`.post[data-post-id="${activePostId}"]`);
    const postActions = post.querySelector(".post-actions");

    if (postActions) {
        const commentButtonMain = postActions.querySelector(".comment-button");
        const shareButtonMain = postActions.querySelector(".share-button");
        const repostButtonMain = postActions.querySelector(".repost-button");

        commentButtonMain.innerHTML = `
            <i class="fas fa-comment"></i>
            <span class="comment-count">${commentCount}</span>
        `;
        shareButtonMain.innerHTML = `<i class="fas fa-share"></i> ${shareCount}`;
        repostButtonMain.innerHTML = `<i class="fas fa-retweet"></i> ${repostCount}`;
    }
}

function createNavigationButtons(mediaContainer, mediaFiles) {
    const leftArrow = document.createElement("button");
    leftArrow.className = "nav-arrow";
    leftArrow.textContent = "<";
    leftArrow.onclick = () =>
        handleMediaNavigation(mediaContainer, "left", mediaFiles);

    const rightArrow = document.createElement("button");
    rightArrow.className = "nav-arrow";
    rightArrow.textContent = ">";
    rightArrow.onclick = () =>
        handleMediaNavigation(mediaContainer, "right", mediaFiles);

    if (mediaFiles.length > 1) {
        mediaContainer.appendChild(leftArrow);
        mediaContainer.appendChild(rightArrow);
    }
}

function handleMediaNavigation(mediaContainer, direction, mediaFiles) {
    const slideContainer = document.querySelector(".post-slide-container");

    let currentMediaIndex = parseInt(
        mediaContainer.getAttribute("data-current-index"),
        10
    ) || 0;

    // Update index based on navigation direction
    if (direction === "left") {
        currentMediaIndex = (currentMediaIndex - 1 + mediaFiles.length) % mediaFiles.length;
    } else if (direction === "right") {
        currentMediaIndex = (currentMediaIndex + 1) % mediaFiles.length;
    }

    // Update the current media index in the post data attribute
    mediaContainer.setAttribute("data-current-index", currentMediaIndex);

    // Re-render media
    renderMedia(mediaContainer, mediaFiles, currentMediaIndex);
    const dotsContainer = slideContainer.querySelector('.dots-container');
    updateDots(dotsContainer, currentMediaIndex);
}

function renderMedia(mediaContainer, mediaFiles, currentMediaIndex) {
    mediaContainer.innerHTML = "";
    const mediaFile = mediaFiles[currentMediaIndex];

    let mediaElement;
    if (mediaFile.type === "image") {
        mediaElement = document.createElement("img");
        mediaElement.className = "post-media";
        mediaElement.src = mediaFile.url;
    } else if (mediaFile.type === "video") {
        mediaElement = document.createElement("video");
        mediaElement.className = "post-media";
        mediaElement.controls = true;
        mediaElement.src = mediaFile.url;
    } else {
        console.error("Unsupported media type:", mediaFile.type);
        return;
    }

    mediaContainer.appendChild(mediaElement);
    createNavigationButtons(
        mediaContainer,
        mediaFiles
    );
}

function createDots(mediaContainer, slideContainer, mediaFiles, currentMediaIndex) {
    // Remove any existing dots container to avoid duplication
    const existingDotsContainer = slideContainer.querySelector('.dots-container');
    if (existingDotsContainer) {
        slideContainer.removeChild(existingDotsContainer);
    }

    const dotsContainer = document.createElement('div');
    dotsContainer.className = 'dots-container';

    mediaFiles.forEach((file, index) => {
        const dot = document.createElement('span');
        dot.className = 'dot';
        dot.dataset.index = index;
        dot.onclick = () => handleDotClick(mediaContainer, index, mediaFiles);
        dotsContainer.appendChild(dot);
    });

    slideContainer.appendChild(dotsContainer);
    updateDots(dotsContainer, currentMediaIndex);
}

function updateDots(dotsContainer, currentMediaIndex) {
    const dots = dotsContainer.getElementsByClassName('dot');
    for (let i = 0; i < dots.length; i++) {
        if (i === currentMediaIndex) {
            dots[i].classList.add('active');
        } else {
            dots[i].classList.remove('active');
        }
    }
}

function handleDotClick(mediaContainer, index, mediaFiles) {
    const slideContainer = document.querySelector(".post-slide-container");
    mediaContainer.setAttribute('data-current-index', index);
    renderMedia(mediaContainer, mediaFiles, index);
    const dotsContainer = slideContainer.querySelector('.dots-container');
    updateDots(dotsContainer, index);
}


//---------------------------- COMMENTS RELATED STUFF HERE ----------------------------------//
//-------------------------------------------------------------------------------------------//

function toggleComment(commentContainer, postId) {
    if (!commentContainer) {
        console.error("Comment container not found.");
        return;
    }

    // Toggle the 'hidden' class
    const isHidden = commentContainer.classList.toggle("hidden");
    const commentButton = commentContainer.querySelector(".comment-button");
    commentButton.classList.toggle("active");

    // If the container is now active (not hidden), load the comments
    if (!isHidden) {
        loadComments(commentContainer, postId);
    }
}

function loadComments(commentContainer, postId) {
    fetch(`/comments/${postId}`, {
        headers: {
            Accept: "application/json",
        },
    })
        .then(async (response) => {
            if (!response.ok) {
                if (response.status === 401) {
                    const errorData = await response.json();
                    if (errorData.redirect_url) {
                        updateSessionAndRedirect(
                            window.location.href,
                            errorData.message || "Log in to comment on posts :)",
                            errorData.redirect_url
                        );
                    } else {
                        throw new Error("Unauthorized access");
                    }
                } else {
                    throw new Error("Failed to fetch comments.");
                }
            }
            return response.json();
        })
        .then((data) => {
            const comments = commentContainer.querySelector(".comments");
            if (data.html.trim() === "") {
                comments.innerHTML = "<p class='comment-placeholder'>No comments yet. Be the first to comment!..</p>";
            } else {
                comments.innerHTML = data.html;
                initializeShowMore();
                initializeLazyLoad(postId);
            }
        })
        .catch((error) => {
            console.error("Error loading comments:", error);
            const comments = commentContainer.querySelector(".comments");
            comments.innerHTML = "<p>Failed to load comments. Please try again later.</p>";
        });
}
//----------------------------------------------------------------------------------------------//

//---------------------------- POST INFO RELATED STUFF HERE ---------------------------------//
function togglePost(postContainer) {
    if (!postContainer) {
        console.error("post container not found.");
        return;
    }

    // Toggle the 'hidden' class
    const isHidden = postContainer.classList.toggle("hidden");
}

//-------------------------------------------------------------------------------------------//


//------------------------------ SHARE RELATED STUFF HERE -----------------------------------//
function toggleShare(shareContainer, postId) {
    if (!shareContainer) {
        console.error("post container not found.");
        return;
    }

    // Toggle the 'hidden' class
    const isHidden = shareContainer.classList.toggle("hidden");
    const shareButton = shareContainer.querySelector(".share-button");
    shareButton.classList.toggle("active");
}

function handleShare(postId) {
    const selectedGroup = document.querySelector('input[name="group"]:checked');
    const pusherKey = document.querySelector('meta[name="pusher-key"]').content;
    const pusherCluster = document.querySelector('meta[name="pusher-cluster"]').content;
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    console.log('Selected Group:', selectedGroup ? selectedGroup.value : 'No group selected');
    console.log('Pusher Key:', pusherKey);
    console.log('Pusher Cluster:', pusherCluster);
    console.log('CSRF Token:', csrfToken);
    if (!selectedGroup) {
        alert('Please select a group to share the post.');
        return;
    }

    const groupName = selectedGroup.value;
    console.log(`Sharing post ${postId} to group ${groupName}`);

    const channelName = `group.${groupName}`;
    const pusher = new Pusher(pusherKey, {
        cluster: pusherCluster,
        authEndpoint: '/pusher/auth',
        auth: {
            headers: {
                'X-CSRF-TOKEN': csrfToken,
            },
        },
    });

    console.log(`groupName: ${groupName}, postId: ${postId}`);

    const channel = pusher.subscribe(channelName);
    Pusher.logToConsole = true;

    fetch('/sharePost', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
        },
        body: JSON.stringify({
            post: { id: postId },
            group: { name: groupName },
        }),
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            console.log('Post shared successfully');
        } else {
            console.error('Failed to share the post');
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
}




//-------------------------------------------------------------------------------------------//
