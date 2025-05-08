window.onload = function() {
    var container = document.querySelector('.messages');
    if (container) {
        container.scrollTop = container.scrollHeight;
    }
};
