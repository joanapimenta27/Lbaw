function updateSessionAndRedirect(p_intendedUrl, p_redirectReason, redirectUrl) {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute("content");

    fetch('/update-session', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
        },
        body: JSON.stringify({
            intendedUrl: p_intendedUrl,
            redirectReason: p_redirectReason,
        }),
    })
    .then((response) => {
        if (!response.ok) {
            throw new Error('Failed to update session.');
        }
        return response.json();
    })
    .then((data) => {
        console.log(data.message); // Log success message
        window.location.href = redirectUrl; // Redirect after success
    })
    .catch((error) => {
        console.error('Error updating session:', error);
    });
}
window.updateSessionAndRedirect = updateSessionAndRedirect;

function updatestate(userId, authid) {
    const states = document.querySelector('#state' + userId).innerHTML.replace(/\s/g, '');
    const state = states.replace(/(<([^>]+)>)/ig, '').trim();
    
    console.log('Current state:', state);
    switch (state) {
        case 'Addfriend':
                console.log('Sending friend request'); // Adicione este log
                sendAjaxRequest('post', 'sendfriendrequest', { id: userId });
                document.querySelector('#state' + userId).innerHTML = 'Cancel request';
                break;
        case 'Cancelrequest':
            console.log('Cancelling friend request'); // Adicione este log
            sendAjaxRequest('post', 'removefriendrequest', { id: userId });
            document.querySelector('#state' + userId).innerHTML = 'Add friend';
            break;
        case 'AcceptRequest':
            console.log('Accepting friend request'); // Adicione este log
            sendAjaxRequest('post', 'acceptfriendrequest', { id: userId });
            document.querySelector('#state' + userId).innerHTML = 'RemoveFriend';
            break;
        case 'RemoveFriend':
            console.log('Unfriending'); // Adicione este log
            sendAjaxRequest('post', 'removefriend', { id: userId });
            document.querySelector('#state' + userId).innerHTML = 'Add friend';
            break;
        case 'RejectRequest':
            console.log('Rejecting friend request'); // Adicione este log
            sendAjaxRequest('post', 'rejectfriendrequest', { id: userId });
            document.querySelector('#state' + userId).innerHTML = 'Add friend';
            break;
        default:
            console.log('Unknown state'); // Adicione este log
            break;
    }
}

function sendAjaxRequest(method, url, data) {
    console.log('sendAjaxRequest called with method:', method, 'url:', url, 'data:', data); // Adicione este log

    let request = new XMLHttpRequest();
  
    request.open(method, url, true);
    request.setRequestHeader('X-CSRF-TOKEN', document.querySelector('meta[name="csrf-token"]').content);
    request.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    request.send(encodeForAjax(data));

     request.onreadystatechange = function() {
        if (request.readyState === XMLHttpRequest.DONE) {
            console.log('AJAX request completed with status:', request.status); // Adicione este log
            if (request.status === 200) {
                console.log('Response:', request.responseText); // Adicione este log
            } else {
                console.error('Error in AJAX request:', request.statusText); // Adicione este log
            }
        }
    };
}

function blockUser(userId, authId) {
    const states = document.querySelector('#block-state' + userId).innerHTML.replace(/\s/g, '');
    const state = states.replace(/(<([^>]+)>)/ig, '').trim();
    console.log('Current state:', state);
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    switch (state) {
        case 'Block':
            console.log('Blocking user');
            fetch('/blockUser', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({ userId: userId, authId: authId })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.querySelector('#block-state' + userId).innerHTML = 'Unblock';
                } else {
                    console.error('Error blocking user:', data.message);
                }
            })
            .catch(error => console.error('Error:', error));
            break;
        case 'Unblock':
            console.log('Unblocking user');
            fetch('/unblockUser', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({ userId: userId, authId: authId })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.querySelector('#block-state' + userId).innerHTML = 'Block';
                } else {
                    console.error('Error unblocking user:', data.message);
                }
            })
            .catch(error => console.error('Error:', error));
            break;
        default:
            console.log('Unknown state');
            break;
    }
}

function encodeForAjax(data) {
    if (data == null) return null;
    return Object.keys(data).map(function(k){
      return encodeURIComponent(k) + '=' + encodeURIComponent(data[k])
    }).join('&');
}

// Function to simulate Laravel's asset helper
function asset(path) {
    return `/storage/${path}`;
}

document.addEventListener('DOMContentLoaded', function () {
    console.log('DOM fully loaded and parsed');

    async function fetchUnreadNotifications() {
        try {
            console.log('Fetching unread notifications...');
            const response = await fetch('/notifications/unread');
            console.log('Response:', response);
            if (!response.ok) {
                throw new Error('Network response was not ok: ' + response.statusText);
            }
            const notifications = await response.json();
            console.log('Fetched notifications:', notifications);
            updateNotificationUI(notifications);
        } catch (error) {
            console.error('Error fetching notifications:', error);
        }
    }

    function updateNotificationUI(notifications) {
        const notificationContainer = document.getElementById('notification-container');

        if (!notificationContainer) {
            console.error('Notification container not found');
            return;
        }

        // Clear old notifications
        notificationContainer.innerHTML = '';

        if (notifications.length === 0) {
            // Show "no notifications" message
            const noNotifications = document.createElement('div');
            noNotifications.classList.add('no-notifications');
            noNotifications.innerHTML = `<p>You have no notifications.</p>`;
            notificationContainer.appendChild(noNotifications);
            return;
        }

        // Create list for notifications
        const notificationList = document.createElement('ul');
        notificationList.classList.add('list-group');

        notifications.forEach(notification => {
            const listItem = document.createElement('li');
            listItem.classList.add('list-group-item');

            // Create notification header
            const header = document.createElement('div');
            header.classList.add('notification-header');

            // Create user info
            const userDiv = document.createElement('div');
            userDiv.classList.add('notification-user');
            // Add user info to header
           if (notification.user) {
    const userLink = document.createElement('a');
    
    userLink.href = `/users/${notification.user.id}/profile`;


    const userName = document.createElement('span');
    userName.textContent = notification.user.name;

    userLink.appendChild(userName);
    userDiv.appendChild(userLink);
} else {
    userDiv.textContent = 'User not found';
}
            header.appendChild(userDiv);

            // Create date info
            const dateDiv = document.createElement('div');
            dateDiv.classList.add('notification-date');
            if (notification.date) {
                const dateSpan = document.createElement('span');
                dateSpan.textContent = new Date(notification.date).toLocaleString();
                dateDiv.appendChild(dateSpan);
            } else {
                dateDiv.textContent = 'Date not available';
            }

            // Add date info to header
            header.appendChild(dateDiv);

            // Add header to list item
            listItem.appendChild(header);

            // Create notification body
            const bodyDiv = document.createElement('div');
            bodyDiv.classList.add('notification-body');
            const bodyContent = document.createElement('p');
            bodyContent.textContent = notification.content;
            bodyDiv.appendChild(bodyContent);

            // Add body to list item
            listItem.appendChild(bodyDiv);

            // Append list item to notification container
            notificationContainer.appendChild(listItem);
        });

        // Add notification list to container
        notificationContainer.appendChild(notificationList);
    }

    fetchUnreadNotifications();
    setInterval(fetchUnreadNotifications, 3000);
});

function redirectToProfile(userId) {
    window.location.href = '/users/' + userId + '/profile';
}
