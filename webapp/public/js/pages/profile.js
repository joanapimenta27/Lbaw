function handleDeleteAccount(userId) {
    const confirmed = confirm("Are you sure you want to delete this account?");
    if (confirmed) {
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        console.log('CSRF Token:', csrfToken); // Log the CSRF token

        // Send DELETE request to delete the user account
        fetch(`/deleteUser/${userId}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.message === 'User anonymized successfully') {
                alert("Account has been deleted and anonymized.");
                // Check if the authenticated user is deleting their own account
                if (data.authUserId === userId || !data.isAdmin) {
                    // Log the user out
                    if (data.isAdmin && !data.isSuper) {
                        fetch(`/removeAdmin/${userId}`, {
                            method: 'DELETE',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrfToken
                            }
                        });
                    }
                    return fetch('/logout', {
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        }
                    });
                } else {
                    // If the user is an admin deleting someone else's account, do not log out or redirect
                    return Promise.resolve({ ok: true });
                }
            } else {
                console.error('Error response:', data);
                alert("Failed to delete the account. Please try again.");
                throw new Error('Failed to delete the account');
            }
        })
        .then(response => {
            if (response.ok) {
                // Redirect to the home page if the user is not an admin or is deleting their own account
                window.location.href = '/';
            } else {
                console.error('Logout failed');
                alert("An error occurred during logout. Please try again.");
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert("An error occurred. Please try again.");
        });
    }
}
function handleBlockFromFlick(userId) {
    const confirmed = confirm("Are you sure you want to block this user?");
    if (confirmed) {
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        console.log('CSRF Token:', csrfToken); // Log the CSRF token

        // Send POST request to block the user
        fetch(`/AdminBlockUser/${userId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.message === 'User blocked by admin successfully') {
                alert("User has been blocked.");
                // Optionally, you can refresh the page or update the UI to reflect the change
                location.reload();
            } else {
                console.error('Error response:', data);
                alert("Failed to block the user. Please try again.");
                throw new Error('Failed to block the user');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert("An error occurred. Please try again.");
        });
    }
}

function handleUnblockFromFlick(userId) {
    const confirmed = confirm("Are you sure you want to block this user?");
    if (confirmed) {
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        console.log('CSRF Token:', csrfToken); // Log the CSRF token

        // Send POST request to block the user
        fetch(`/AdminUnblockUser/${userId}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.message === 'User unblocked by admin successfully') {
                alert("User has been unblocked.");
                // Optionally, you can refresh the page or update the UI to reflect the change
                location.reload();
            } else {
                console.error('Error response:', data);
                alert("Failed to unblock the user. Please try again.");
                throw new Error('Failed to unblock the user');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert("An error occurred. Please try again.");
        });
    }
}