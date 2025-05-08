
function fetchValuesByTag(query = '', tag = '', filter = '') {  
    const resultsContainer = document.getElementById('user-listS');
    if (!resultsContainer) {
        console.log('Error: User container not found.');
        return;
    }

    const url = new URL('/search-live', window.location.origin);
    
    if (query) url.searchParams.append('search', query);
    if (tag) url.searchParams.append('tag', tag);
    if (filter) url.searchParams.append('filter', filter);  

    fetch(url)
        .then(response => response.json())
        .then(data => {
            resultsContainer.innerHTML = '';  
            if (data.length > 0) {
                if (tag === ' ' || tag === 'accounts' || tag == null) {
                    data.forEach(user => {
                        const listItem = document.createElement('li');
                        const userLink = document.createElement('a');
                        userLink.href = `/users/${user.id}/profile/`; 
                        userLink.textContent = user.username; 
                        listItem.appendChild(userLink);
                        resultsContainer.appendChild(listItem);
                    });
                } 
                else if (tag === 'titles' || tag === 'tags') {
                    data.forEach(post => {
                        const postItem = document.createElement('li');
                        postItem.classList.add('post-item');
        
                        fetch(`/posts/${post.id}`)
                            .then(response => response.text()) 
                            .then(html => {
                                postItem.innerHTML = html; 
                                resultsContainer.appendChild(postItem); 
                            })
                            .catch(error => console.error('Error fetching post view:', error));
                    });
                }
                else {
                    data.forEach(item => {
                        const listItem = document.createElement('li');
                        listItem.textContent = item.name || item.title || 'No Title Available';
                        resultsContainer.appendChild(listItem);
                    });
                }
            } else {
                const listItem = document.createElement('li');
                listItem.textContent = 'No results found';
                resultsContainer.appendChild(listItem);
            }
        })
        .catch(error => console.error('Error fetching search results:', error));
}


function handleSearchInput(event) {
    const searchQuery = event.target.value.trim(); 

    const tag = getCurrentTag();
    const filter=getCurrentFilter();


    fetchValuesByTag(searchQuery, tag,filter);  
}

function setupSearchListeners() {
    const searchInput = document.getElementById('searchD');
    if (searchInput) {
        searchInput.addEventListener('input', handleSearchInput);
    }
    const filter=getCurrentFilter();
    const tag = getCurrentTag();  
    fetchValuesByTag('', tag,filter); 
}

function getCurrentTag() {
    const urlParams = new URLSearchParams(window.location.search);
    return urlParams.get('tag'); 
}
function getCurrentFilter() {
    const urlParams = new URLSearchParams(window.location.search);
    return urlParams.get('filter'); 
}

//buguer display
function toggleAdvancedFiltersMenu() {
    const hamburgerButton = document.getElementById('hamburgerButton');
    const advancedFiltersMenu = document.getElementById('advancedFiltersMenu');

    
    hamburgerButton.addEventListener('click', function () {
        
        advancedFiltersMenu.classList.toggle('hidden');
       
    });
}

function addEventListeners() {
    if (typeof setupSearchListeners === 'function') {
        setupSearchListeners();
    }
}
document.addEventListener('DOMContentLoaded', function() {
    addEventListeners();  
    toggleAdvancedFiltersMenu();
});


