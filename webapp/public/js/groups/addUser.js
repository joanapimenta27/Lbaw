let selectedUsers = [];

function selectUser(user) {
    if (!selectedUsers.includes(user.id)) {
        selectedUsers.push(user.id);
        const selectedUsersList = document.getElementById('selected-users-list');
        const listItem = document.createElement('li');
        listItem.textContent = user.username;
        selectedUsersList.appendChild(listItem);
    }
}

function sendInvites() {
    if (selectedUsers.length === 0) {
        alert("Please select at least one user to send an invite.");
        return;
    }

    fetch('/send-invites', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ 
            users: selectedUsers,
            group: groupValue,

        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Invites sent successfully!');
            selectedUsers = []; 
            document.getElementById('selected-users-list').innerHTML = '';
            console.log("brooo1");

            if (window.history.length > 2) {
                console.log("brooo");
                window.history.go(-2); 
            } else {
                console.log("brooo2");

                window.location.href = '/chatMenu'; 
            }

        } else {
            alert('Failed to send invites. Please try again.');
        }
    })
    .catch(error => console.error('Error sending invites:', error));
}

document.getElementById('send-invites-button').addEventListener('click', sendInvites);

document.addEventListener('DOMContentLoaded', () => {
    fetchUsers('');
});
