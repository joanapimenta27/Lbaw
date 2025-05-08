

let groupMeta = document.querySelector('meta[name="group-id"]');
let groupValue = groupMeta ? groupMeta.getAttribute('content') : null;
let remove= document.querySelector('meta[name="remove"]');
let boolRemove=remove.getAttribute('content');

console.log( groupValue);


function fetchUsers(query = '') {
    const resultsContainer = document.getElementById('user-list');
    if (!resultsContainer) {
        console.log('Error: User container not found.');
        return;
    }

    const url = new URL('/search-liveG', window.location.origin);
    url.searchParams.append('group', groupValue);
    url.searchParams.append('remove',boolRemove);

    if (query) url.searchParams.append('search', query);
    
    fetch(url)
        .then(response => response.json())
        .then(data => {
            resultsContainer.innerHTML = '';  
            if (data.length > 0) {
                data.forEach(user => {
                    const listItem = document.createElement('li');
                    const userLink = document.createElement('a');
                    userLink.href = '#';
                    userLink.textContent = user.username;
                    userLink.onclick = () => selectUser(user);
                    listItem.appendChild(userLink);
                    resultsContainer.appendChild(listItem);
                });
            } else {
                const listItem = document.createElement('li');
                listItem.textContent = 'No results found';
                resultsContainer.appendChild(listItem);
            }
        })
        .catch(error => console.error('Error fetching search results:', error));
}

document.getElementById('search').addEventListener('input', function(event) {
    const searchQuery = event.target.value.trim();
    fetchUsers(searchQuery);
});