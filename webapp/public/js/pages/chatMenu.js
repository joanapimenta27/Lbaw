document.addEventListener('DOMContentLoaded', function () {
    const blockedChatLinks = document.querySelectorAll('.blocked-chat');

    blockedChatLinks.forEach(link => {
        link.addEventListener('click', function (event) {
            event.preventDefault();
            alert('You cannot open this chat because you have been blocked by this user.');
        });
    });
});