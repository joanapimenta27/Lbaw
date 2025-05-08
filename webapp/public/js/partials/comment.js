let editingCommentId = null;
let replyingToCommentId = null;

function initializeLazyLoad(postId){
    const commentsContainer = document.querySelector(".comments");
    const loadingIndicator = document.querySelector(".loading-indicator");
    limit = 10;
    let hasMoreComments = true;
    let isLoading = false;
    let offset = 10;

    function loadMoreComments() {
        if (isLoading || !hasMoreComments) return;

        isLoading = true;
        loadingIndicator.classList.remove("hidden");

        fetch(`/comments/${postId}?limit=${limit}&offset=${offset}`)
            .then(response => {
                if (!response.ok) throw new Error("Failed to load comments");
                return response.json();
            })
            .then(data => {
                commentsContainer.innerHTML += data.html;
                offset += limit;

                if (!data.hasMore) {
                    hasMoreComments = false;
                }
            })
            .catch(error => {
                console.error("Error loading comments:", error);
                alert("Failed to load more comments. Please try again.");
            })
            .finally(() => {
                isLoading = false;
                loadingIndicator.classList.add("hidden");
            });
    }

    commentsContainer.addEventListener("scroll", () => {
        const bottomReached =
            commentsContainer.scrollTop + commentsContainer.clientHeight >= commentsContainer.scrollHeight - 50;

        if (bottomReached) {
            loadMoreComments();
        }
    });

    loadMoreComments();
}

function editComment(commentId) {
    editingCommentId = commentId;
    const commentElement = document.querySelector(`[data-id="${commentId}"]`);
    const commentTextElement = commentElement.querySelector(".comment-text");

    const commentContent = commentTextElement.textContent.trim();
    const commentInput = document.getElementById("new-comment-input");
    const editNotifier = document.getElementById("edit-notifier");
    if (editNotifier) {
        editNotifier.classList.remove("hidden");
    }
    if (commentInput) {
        commentInput.value = commentContent;
        commentInput.focus();
    }
    setupShowMoreForComment(commentElement);
}


function cancelEdit() {
    editingCommentId = null;

    const commentInput = document.getElementById("new-comment-input");
    commentInput.value = "";

    const editNotifier = document.getElementById("edit-notifier");
    editNotifier.classList.add("hidden");
}

function toggleCommentLike(commentId, button) {
    fetch(`/comments/${commentId}/like`, {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
        },
    })
        .then((response) => {
            if (!response.ok) throw new Error("Failed to toggle like.");
            return response.json();
        })
        .then((data) => {
            const likeIcon = button.querySelector(".like-icon");
            const likeCount = button.querySelector(".like-count");

            if (data.status === "liked") {
                button.classList.remove("unliked");
                button.classList.add("liked");
                likeIcon.classList.remove("far");
                likeIcon.classList.add("fas");
                likeCount.textContent = parseInt(likeCount.textContent) + 1;
            } else if (data.status === "unliked") {
                button.classList.remove("liked");
                button.classList.add("unliked");
                likeIcon.classList.remove("fas");
                likeIcon.classList.add("far");
                likeCount.textContent = parseInt(likeCount.textContent) - 1;
            }
        })
        .catch((error) => console.error("Error toggling like:", error));
}

function addComment(postId) {
    const commentInput = document.getElementById('new-comment-input');
    const commentContent = commentInput.value.trim();

    if (!commentContent) {
        alert('Please write something before posting.');
        return;
    }

    if (editingCommentId) {
        url = `/comments/${editingCommentId}`;
        method = "PUT";
    } else if (replyingToCommentId) {
        url = `/comments/${replyingToCommentId}/reply`;
        method = "POST";
    } else {
        url = `/comments/`;
        method = "POST";
    }   

    fetch(url, {
        method: method,
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        },
        body: JSON.stringify({ 
            content: commentContent,
            post_id: postId,
            parent_id: replyingToCommentId,
        }),
    })
        .then(response => {
            if (!response.ok) throw new Error('Failed to add comment.');
            return response.json();
        })
        .then(data => {
            commentInput.value = "";

            if (editingCommentId) {
                const commentElement = document.querySelector(`[data-id="${editingCommentId}"] .comment-content p`);
                const comment = document.querySelector(`[data-id="${editingCommentId}"]`);
                if (commentElement) {
                    commentElement.textContent = commentContent;
                }
                const actionsContainer = comment.querySelector(".comment-actions");
                if (!actionsContainer.querySelector(".comment-edited-label")) {
                    const editedLabel = document.createElement("span");
                    editedLabel.className = "comment-edited-label";
                    editedLabel.textContent = "Edited";
                    actionsContainer.appendChild(editedLabel);
                }
                cancelEdit();
                setupShowMoreForComment(comment);
            } else if (replyingToCommentId) {
                let parentComment = document.querySelector(`[data-id="${replyingToCommentId}"]`);
            
                while (parentComment && parentComment.dataset.parentId) {
                    parentComment = document.querySelector(`[data-id="${parentComment.dataset.parentId}"]`);
                }
                const repliesContainer = parentComment.querySelector(".replies-container");
            
                if (repliesContainer) {
                    repliesContainer.insertAdjacentHTML("afterbegin", data.html);
                    repliesContainer.classList.remove("hidden");
                } else {
                    const newRepliesContainer = document.createElement("div");
                    newRepliesContainer.className = "replies-container";
                    newRepliesContainer.innerHTML = data.html;
                    parentComment.appendChild(newRepliesContainer);
                }            
                const replyCountElement = parentComment.querySelector(".reply-count");
                if (replyCountElement) {
                    replyCountElement.textContent = parseInt(replyCountElement.textContent) + 1;
                }
                cancelReply();
            }
            else {
                const commentsContainer = document.querySelector(".comments.description-container");
                commentsContainer.insertAdjacentHTML("afterbegin", data.html);
                const newComment = commentsContainer.firstElementChild;
                const placeholderMessage = commentsContainer.querySelector(".comment-placeholder");
                if (placeholderMessage) {
                    placeholderMessage.remove();
                }
                const commentCountElement = document.querySelector(".comment-button .comment-count");
                if (commentCountElement) {
                    commentCountElement.textContent = parseInt(commentCountElement.textContent) + 1;
                }
                commentsContainer.scrollTo({ top: 0, behavior: "smooth" });
                setupShowMoreForComment(newComment);
            }        
        })
        .catch(error => console.error('Error adding comment:', error));
}

function deleteComment(commentId) {
    if (!confirm("Are you sure you want to delete this comment?")) {
        return;
    }

    fetch(`/comments/${commentId}`, {
        method: "DELETE",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
        },
    })
        .then((response) => {
            if (!response.ok) {
                throw new Error("Failed to delete the comment.");
            }
            return response.json();
        })
        .then((data) => {
            const commentElement = document.querySelector(`.comment[data-id="${commentId}"]`);
            if (commentElement) {
                commentElement.remove();
            }

            if (editingCommentId === commentId) {
                cancelEdit();
            }

            const commentCountElement = document.querySelector(".comment-button .comment-count");
            if (commentCountElement) {
                commentCountElement.textContent = Math.max(0, parseInt(commentCountElement.textContent) - 1);
            }

            const commentsContainer = document.querySelector(".comments.description-container");
            if (commentsContainer.children.length === 0) {
                commentsContainer.innerHTML = "<p class='comment-placeholder'>No comments yet. Be the first to comment!..</p>";
            }
        })
        .catch((error) => {
            console.error("Error deleting comment:", error);
            alert("Failed to delete the comment. Please try again.");
        });
}

function initializeShowMore() {
    const commentWrappers = document.querySelectorAll(".comment-content");

    commentWrappers.forEach(wrapper => {
        const text = wrapper.querySelector(".comment-text");
        const showMoreButton = wrapper.querySelector(".show-more-button");
        const originalHeight = text.style.maxHeight;
        text.style.maxHeight = "none";
        if (text.scrollHeight > 140) {
            text.style.maxHeight = "140px";
            text.style.overflow = "hidden";
            showMoreButton.classList.remove("hidden");
        }

        text.style.maxHeight = originalHeight;
    });
}

function toggleShowMore(button) {
    const wrapper = button.closest(".comment-content");
    const text = wrapper.querySelector(".comment-text");

    if (text.classList.contains("expanded")) {
        text.classList.remove("expanded");
        button.textContent = "Show More";
    } else {
        text.classList.add("expanded");
        button.textContent = "Show Less";
    }
}

function setupShowMoreForComment(commentElement) {
    const contentWrapper = commentElement.querySelector(".comment-content");
    const text = contentWrapper.querySelector(".comment-text");
    const showMoreButton = contentWrapper.querySelector(".show-more-button");

    const originalHeight = text.style.maxHeight;
    text.style.maxHeight = "none";
    if (text.scrollHeight > 140) {
        text.style.maxHeight = "140px";
        text.style.overflow = "hidden";
        showMoreButton.classList.remove("hidden");
    }
    else {
        showMoreButton.classList.add("hidden");
    }

    text.style.maxHeight = originalHeight;
}

function replyToComment(commentId, username) {
    replyingToCommentId = commentId;
    const commentInput = document.getElementById("new-comment-input");
    commentInput.value = `@${username} `;
    if (commentInput) {
        commentInput.focus();
    }
    const editNotifier = document.getElementById("edit-notifier");
    editNotifier.querySelector("span").textContent = `Replying to ${username}...`;
    editNotifier.classList.remove("hidden");
}

function cancelReply() {
    replyingToCommentId = null;
    const commentInput = document.getElementById("new-comment-input");
    commentInput.value = "";
    const editNotifier = document.getElementById("edit-notifier");
    editNotifier.classList.add("hidden");
}

function showReplies(commentId, button) {
    const parentComment = document.querySelector(`.comment[data-id="${commentId}"]`);
    const repliesContainer = parentComment.querySelector(".replies-container");
    const hideRepliesButton = parentComment.querySelector(".hide-replies-button");

    const alreadyLoaded = repliesContainer.querySelectorAll(".comment-reply").length;

    fetch(`/comments/${commentId}/replies?limit=5&offset=${alreadyLoaded}`)
        .then((response) => {
            if (!response.ok) throw new Error("Failed to load replies.");
            return response.json();
        })
        .then((data) => {
            repliesContainer.innerHTML += data.html;
            repliesContainer.classList.remove("hidden");

            hideRepliesButton.classList.remove("hidden");
            if (data.hasMoreReplies) {
                button.innerHTML = '<i class="fa-solid fa-angle-down"></i> Load More';
            } else {
                button.classList.add("hidden");
            }
        })
        .catch((error) => console.error("Error loading replies:", error));
}

function hideReplies(commentId, button) {
    const parentComment = document.querySelector(`.comment[data-id="${commentId}"]`);
    const repliesContainer = parentComment.querySelector(".replies-container");
    const showRepliesButton = parentComment.querySelector(".show-replies-button");

    repliesContainer.innerHTML = '';

    repliesContainer.classList.add("hidden");
    showRepliesButton.classList.remove("hidden");

    button.classList.add("hidden");
    showRepliesButton.innerHTML = '<i class="fa-solid fa-angle-down"></i> See Replies';
}
