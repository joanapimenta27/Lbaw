document.addEventListener("DOMContentLoaded", function () {
    const posts = document.querySelectorAll(".post");
    let truncationApplied = false; //for truncate text function

    initializePosts();

    function initializePosts() {
        const posts = document.querySelectorAll(".post:not([data-initialized])"); // Only process new posts
        let truncationApplied = false; // For truncate text function

        posts.forEach(post => {
            // Mark the post as initialized
            post.setAttribute("data-initialized", "true");

            // Parse media files from the data attribute
            const mediaFiles = JSON.parse(post.getAttribute('data-media-files') || "[]");

            const descriptionContainer = post.querySelector('.post-description-container');
            const descriptionElement = descriptionContainer.querySelector('.post-description');

            if (!descriptionElement) {
                console.error("Description element not found for post:", post);
                return;
            }

            if (mediaFiles.length > 0) {
                // If media files are present, ensure description stays on one line and gets truncated
                descriptionElement.style.whiteSpace = "nowrap";
                truncateText(descriptionElement, descriptionContainer, true);
            } else {
                // If no media files are present, allow normal wrapping
                descriptionElement.style.whiteSpace = "normal";
                truncateText(descriptionElement, descriptionContainer, false);
                return;
            }

            const mediaContainer = post.querySelector(".post-media-container");
            const slideContainer = post.querySelector(".post-slide-container");
            let currentMediaIndex = 0;

            // Initial rendering
            renderMedia(post, mediaContainer, mediaFiles, currentMediaIndex);
            createDots(post, slideContainer, mediaFiles, currentMediaIndex);

            // Store the current media index as a data attribute for future use
            post.setAttribute('data-current-index', currentMediaIndex);
        });
    }
    window.initializePosts = initializePosts;

    // Render the current media (image or video) based on the current index
    function renderMedia(post, mediaContainer, mediaFiles, currentMediaIndex) {
        mediaContainer.innerHTML = '';
        const mediaFile = mediaFiles[currentMediaIndex];
        if (!mediaFile) {
            console.error("Invalid media file:", mediaFile);
            return;
        }

        let mediaElement;
        if (mediaFile.type === 'image') {
            mediaElement = document.createElement('img');
            mediaElement.className = 'post-media';
            mediaElement.src = mediaFile.url;
        } else if (mediaFile.type === 'video') {
            mediaElement = document.createElement('video');
            mediaElement.className = 'post-media';
            mediaElement.controls = true;
            mediaElement.src = mediaFile.url;
        } else {
            console.error("Unsupported media type:", mediaFile.type);
            return;
        }

        mediaContainer.appendChild(mediaElement);
        createNavigationButtons(mediaContainer, post, mediaFiles);
    }

    // Create navigation buttons for each post
    function createNavigationButtons(mediaContainer, post, mediaFiles) {
        const leftArrow = document.createElement('button');
        leftArrow.className = 'nav-arrow';
        leftArrow.textContent = '<';
        leftArrow.onclick = (event) => {
            event.stopPropagation();
            handleMediaNavigation(post, 'left', mediaFiles);
        };

        const rightArrow = document.createElement('button');
        rightArrow.className = 'nav-arrow';
        rightArrow.textContent = '>';
        rightArrow.onclick = (event) => {
            event.stopPropagation();
            handleMediaNavigation(post, 'right', mediaFiles);
        };

        if (mediaFiles.length > 1) {
            mediaContainer.appendChild(leftArrow);
            mediaContainer.appendChild(rightArrow);
        }
    }

    // Create dots to indicate the current media file and allow navigation
    function createDots(post, slideContainer, mediaFiles, currentMediaIndex) {
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
            dot.onclick = () => handleDotClick(post, index);
            dotsContainer.appendChild(dot);
        });

        slideContainer.appendChild(dotsContainer);
        updateDots(dotsContainer, currentMediaIndex);
    }

    // Update which dot is active based on the current media index
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

    // Handle navigation when clicking on the arrows
    function handleMediaNavigation(post, direction, mediaFiles) {
        const mediaContainer = post.querySelector(".post-media-container");
        const slideContainer = post.querySelector(".post-slide-container");
        let currentMediaIndex = parseInt(post.getAttribute('data-current-index')) || 0;

        // Update index based on navigation direction
        if (direction === 'left') {
            currentMediaIndex = (currentMediaIndex - 1 + mediaFiles.length) % mediaFiles.length;
        } else if (direction === 'right') {
            currentMediaIndex = (currentMediaIndex + 1) % mediaFiles.length;
        }

        // Update the current media index in the post data attribute
        post.setAttribute('data-current-index', currentMediaIndex);
        // Re-render media and update dots
        renderMedia(post, mediaContainer, mediaFiles, currentMediaIndex);
        const dotsContainer = slideContainer.querySelector('.dots-container');
        updateDots(dotsContainer, currentMediaIndex);
    }

    // Handle dot click to navigate directly to the selected media
    function handleDotClick(post, index) {
        const mediaContainer = post.querySelector(".post-media-container");
        const slideContainer = post.querySelector(".post-slide-container");
        const mediaFiles = JSON.parse(post.getAttribute('data-media-files'));
        post.setAttribute('data-current-index', index);

        renderMedia(post, mediaContainer, mediaFiles, index);
        const dotsContainer = slideContainer.querySelector('.dots-container');
        updateDots(dotsContainer, index);
    }

    function truncateText(descriptionElement, containerElement, hasMedia) {
        let text = descriptionElement.textContent;
    
        descriptionElement.textContent = text;
    
        if (hasMedia) {
            if (descriptionElement.scrollWidth <= containerElement.clientWidth) {
                truncationApplied = false;
                return;
            }
            if (truncationApplied && text.length <= lastTruncatedLength) {
                return;
            }
            let start = 0;
            let end = text.length;
            let truncatedText = text;
    
            while (start <= end) {
                const mid = Math.floor((start + end) / 2);
                truncatedText = text.slice(0, mid) + "...";
                descriptionElement.textContent = truncatedText;
    
                if (descriptionElement.scrollWidth > containerElement.clientWidth) {
                    end = mid - 1; // Too long, reduce the length
                } else {
                    start = mid + 1; // Fits, try to include more characters
                }
            }
            let fineTuneIndex = end;
            truncatedText = text.slice(0, fineTuneIndex) + "...";
            descriptionElement.textContent = truncatedText;
    
            while (descriptionElement.scrollWidth > containerElement.clientWidth && fineTuneIndex > 0) {
                fineTuneIndex--;
                truncatedText = text.slice(0, fineTuneIndex) + "...";
                descriptionElement.textContent = truncatedText;
            }
            lastTruncatedLength = truncatedText.length;
            truncationApplied = true;
    
        } else {
            
            if (descriptionElement.offsetHeight <= containerElement.offsetHeight) {
                truncationApplied = false;
                return;
            }
    
            if (truncationApplied && text.length <= lastTruncatedLength) {
                return;
            }
    
            // Start truncation process to fit within container height
            let start = 0;
            let end = text.length;
            let truncatedText = text;
    
            while (start <= end) {
                const mid = Math.floor((start + end) / 2);
                truncatedText = text.slice(0, mid) + "...";
                descriptionElement.textContent = truncatedText;
    
                if (descriptionElement.offsetHeight > containerElement.offsetHeight) {
                    end = mid - 1; // Too long, reduce the length
                } else {
                    start = mid + 1; // Fits, try to include more characters
                }
            }
    
            // Fine-tune the final truncation to ensure an exact fit
            let fineTuneIndex = end;
            truncatedText = text.slice(0, fineTuneIndex) + "...";
            descriptionElement.textContent = truncatedText;
    
            while (descriptionElement.offsetHeight > containerElement.offsetHeight && fineTuneIndex > 0) {
                fineTuneIndex--;
                truncatedText = text.slice(0, fineTuneIndex) + "...";
                descriptionElement.textContent = truncatedText;
            }
    
            // Record the truncated state
            lastTruncatedLength = truncatedText.length;
            truncationApplied = true;
        }
    }
});

function handlePostMenuClick(button) {

    // Find the post-menu-options container related to the button clicked
    button.classList.add('button-spin');

    // Remove the spin animation class after the animation ends
    setTimeout(() => {
        button.classList.remove('button-spin');
    }, 500);

    const postElement = button.closest('.post-header');
    const optionsContainer = postElement.querySelector('.post-menu-options');

    const icon = button.querySelector('.menu-icon');

    if (icon.classList.contains('active')) {
        icon.classList.remove('active');
        icon.classList.add('inactive');
    } else {
        icon.classList.remove('inactive');
        icon.classList.add('active');
    }
    // Toggle visibility
    if (optionsContainer.classList.contains('hidden')) {
        button.classList.add('button-right-spin');

        setTimeout(() => {
            button.classList.remove('button-right-spin');
        }, 500);
        // Open the current menu
        optionsContainer.classList.remove('hidden');
        optionsContainer.classList.remove('retreating');
        optionsContainer.classList.add('visible');
        button.parentElement.style.marginLeft = 0;
    } else {
        button.classList.add('button-left-spin');

        setTimeout(() => {
            button.classList.remove('button-left-spin');
        }, 500);
        optionsContainer.classList.add('retreating');
        setTimeout(() => {
            optionsContainer.classList.remove('retreating');
            optionsContainer.classList.remove('visible');
            optionsContainer.classList.add('hidden');
            button.parentElement.style.marginLeft = 'auto';
        }, 100);
    }
}

function handleDeletePost(deleteUrl) {
    const confirmed = confirm("Are you sure you want to delete this post?");
    
    if (confirmed) {
        fetch(deleteUrl, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json'
            }
        })
        .then(response => {
            if (response.ok) {
                window.location.reload();
            } else {
                alert('Failed to delete post. Please try again.');
            }
        })
        .catch(error => {
            console.error('Error deleting post:', error);
            alert('An error occurred. Please try again.');
        });
    }
}

function handleEditPost(editUrl) {
    window.location.href = editUrl;
}




let likeRequestInProgress = {}; // Track in-progress requests per post

function toggleLike(event, postId) {
    event.preventDefault();

    const button = event.currentTarget;
    const likeCountSpan = button.querySelector(".like-count");
    const likeIcon = button.querySelector(".like-icon");
    const currentLikes = parseInt(likeCountSpan.textContent, 10) || 0;

    // Prevent multiple requests for the same post
    if (likeRequestInProgress[postId]) {
        console.warn(`Request already in progress for post ${postId}`);
        return;
    }

    // Lock the button
    likeRequestInProgress[postId] = true;

    const isModalLike = button.dataset.type === "modal";

    sendLikeRequest(postId)
        .then((data) => {

            if (data.status === "liked") {
                console.log("Post liked successfully");
                likeCountSpan.textContent = currentLikes + 1;
                button.classList.remove("unliked");
                button.classList.add("liked");
                likeIcon.classList.remove("far");
                likeIcon.classList.remove("fa-heart");
                likeIcon.classList.add("fas");
                likeIcon.classList.add("fa-heart");
                if (isModalLike) {
                    updatePostLike(postId, "like");
                }
            }
            else if (data.status === "unliked") {
                console.log("Post unliked successfully");
                likeCountSpan.textContent = currentLikes - 1;
                button.classList.remove("liked");
                button.classList.add("unliked");
                likeIcon.classList.remove("fas");
                likeIcon.classList.remove("fa-heart");
                likeIcon.classList.add("far");
                likeIcon.classList.add("fa-heart");
                if (isModalLike) {
                    updatePostLike(postId, "unlike");
                }
            }
            
        })
        .catch((error) => {
            if (error.redirect) {
                updateSessionAndRedirect(window.location.href, 'Log in to like posts :)', '/login');
            } else {
                console.error("Error updating like status:", error);
                alert("An error occurred. Please try again.");
            }
        })
        .finally(() => {
            // Unlock the button
            likeRequestInProgress[postId] = false;
        });
}

function updatePostLike(postId, action){
    const post = document.querySelector(`.post[data-post-id="${postId}"]`);
    const postActions = post.querySelector(".post-actions");
    const likeButton = postActions.querySelector(".like-button")
    const likeCountSpan = likeButton.querySelector(".like-count");
    const likeIcon = likeButton.querySelector(".like-icon");
    const currentLikes = parseInt(likeCountSpan.textContent, 10) || 0;
    if (action == "like"){
        likeCountSpan.textContent = currentLikes + 1;
        likeButton.classList.remove("unliked");
        likeButton.classList.add("liked");
        likeIcon.classList.remove("far");
        likeIcon.classList.remove("fa-heart");
        likeIcon.classList.add("fas");
        likeIcon.classList.add("fa-heart");
    }
    else {
        likeCountSpan.textContent = currentLikes - 1;
        likeButton.classList.remove("liked");
        likeButton.classList.add("unliked");
        likeIcon.classList.remove("fas");
        likeIcon.classList.remove("fa-heart");
        likeIcon.classList.add("far");
        likeIcon.classList.add("fa-heart");
    }
}

function sendLikeRequest(postId) {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute("content");

    return fetch(`/posts/${postId}/like`, {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": csrfToken,
        },
        body: JSON.stringify({}),
    })
    .then((response) => {
        if (response.ok) {
            return response.json(); // Parse JSON response
        } else if (response.status === 401) {
            return response.json().then((data) => {
                throw { redirect: data.redirect_url, message: data.message };
            });
        } else {
            throw new Error(`Unexpected response status: ${response.status}`);
        }
    });
}




