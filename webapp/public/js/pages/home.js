document.addEventListener("DOMContentLoaded", function () {
    const sidebar = document.querySelector(".sidebar");
    const sidebarContent = sidebar.querySelector(".sidebar-content")
    const header = document.querySelector("header");

    function adjustSidebarHeight() {
        const headerHeight = header.offsetHeight;
        const viewportHeight = window.innerHeight;

        if (window.scrollY > headerHeight) {
            sidebar.style.height = `${viewportHeight}px`;
            sidebar.style.top = "0";
        } else {
            sidebar.style.height = `${viewportHeight - headerHeight + window.scrollY}px`;
            sidebar.style.top = `${headerHeight - window.scrollY}px`;
        }
    }

    adjustSidebarHeight();
    window.addEventListener("resize", adjustSidebarHeight);
    window.addEventListener("scroll", adjustSidebarHeight);


    // ------------------------------- BUTTON FOR SIDEBAR -------------------------------------- //
    const toggleButton = sidebar.querySelector(".sidebar-toggle");

    toggleButton.addEventListener("click", function () {
        sidebar.classList.toggle("sidebar-hidden");
        const icon = toggleButton.querySelector("i");

        if (sidebar.classList.contains("sidebar-hidden")) {
            icon.classList.replace("fa-chevron-left", "fa-chevron-right");
            toggleButton.classList.remove("pulse-left");
            toggleButton.classList.add("pulse-right");
        } else {
            icon.classList.replace("fa-chevron-right", "fa-chevron-left");
            toggleButton.classList.remove("pulse-right");
            toggleButton.classList.add("pulse-left");
        }
    });
    //---------------------------------------------------------------------------------------------//




    // ------------------------------- Lazy Loading -------------------------------------- //

    let page = 1;
    const feedType = document.body.dataset.feedType || "public";
    const postsContainer = document.querySelector(".posts");
    let isLoading = false;

    window.addEventListener("scroll", function () {
        if (isNearBottom() && !isLoading) {
            loadMorePosts();
        }
    });

    function isNearBottom() {
        return window.innerHeight + window.scrollY >= document.body.offsetHeight - 200;
    }

    function loadMorePosts() {
        isLoading = true;
        page++;

        fetch(`/home/${feedType}?page=${page}`, {
            headers: {
                "X-Requested-With": "XMLHttpRequest",
            },
        })
            .then((response) => {
                if (!response.ok) {
                    throw new Error("Failed to fetch posts.");
                }
                return response.text();
            })
            .then((html) => {
                if (html.trim() === "") {
                    return;
                }
                postsContainer.innerHTML += html;
                isLoading = false;
                window.initializePosts();
            })
            .catch((error) => {
                console.error("Error loading posts:", error);
                isLoading = false;
            });
    }
    // ----------------------------------------------------------------------------------- //
});
