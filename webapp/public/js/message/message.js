// Get CSRF token and other meta data
const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
const userId = document.querySelector('meta[name="user-id"]').getAttribute('content');
const reciverId = document.querySelector('meta[name="reciver-id"]').getAttribute('content');
const pusherKey = document.querySelector('meta[name="pusher-key"]').content;
const pusherCluster = document.querySelector('meta[name="pusher-cluster"]').content;
let groupMeta = document.querySelector('meta[name="group"]');
let groupValue = groupMeta ? groupMeta.getAttribute('content') : null;
let groupName;
if (groupValue) {
    const groupObject = JSON.parse(groupValue);
    groupName = groupObject.name;
    console.log(groupName); 
} else {
    console.log("Meta tag not found or no content.");
}

let channelName;
let recipient;
if(groupValue==null){

    recipient=`${Math.min(userId, reciverId)}.${Math.max(userId, reciverId)}`;
   channelName = `private-chat.${recipient}`;
   groupValue=null;
   console.log("n   group"); 



}
else{
    recipient=sanitizeGroupName(groupName);
    channelName = `group.${recipient}`;
    console.log("y   group"); 


}

const pusher = new Pusher(pusherKey, {
    cluster:  pusherCluster,
    authEndpoint: '/pusher/auth',
    auth: {
        headers: {
            'X-CSRF-TOKEN': csrfToken,
        },
    },
});

console.log(channelName);

const channel = pusher.subscribe(channelName);
Pusher.logToConsole = true;
channel.bind('chat', (data) => {
    console.log('Event received:', data);
    if ((data.sender.id != userId)) { 
        fetch('/receive', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
            },
            body: JSON.stringify({
                message: {
                    content: data.message.content,
                    date: data.message.date,
                },
                sender:{
                    name:data.sender.name,
                    pic:data.sender.profile_picture,
                },
                postId:data.message.post_id,
            }),
        })
            .then(response => response.text())
            .then(res => {
                const messagesContainer = document.querySelector('.messages');
                if (!messagesContainer) {
                    console.error('Messages container not found');
                    return;
                }

                messagesContainer.insertAdjacentHTML('beforeend', res);
                messagesContainer.scrollTop = messagesContainer.scrollHeight;
            })
            .catch(error => console.error('Error receiving message:', error));
    }
});

document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('#sendMessageForm');
    const messageInput = form.querySelector('#message');
    const errorAlert = document.querySelector('#message-error');

    form.addEventListener('submit', function(event) {
        event.preventDefault();

       
        if (!messageInput.value.trim()) {
            errorAlert.style.display = 'block';
            errorAlert.classList.remove('alert-hide');
            errorAlert.classList.add('alert-show');

            setTimeout(function() {
                errorAlert.classList.remove('alert-show');
                errorAlert.classList.add('alert-hide');
            }, 1000);

            return; 
        }

        fetch(`/broadcast/${recipient}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'X-Socket-Id': pusher.connection.socket_id,
            },
            body: JSON.stringify({
                message: messageInput.value,
                group: groupName,
            }),
        })
        .then(response => response.text())
        .then(res => {
            const messagesContainer = document.querySelector('.messages');
                if (!messagesContainer) {
                    console.error('Messages container not found');
                    return;
                }
                messageInput.value = '';
                messagesContainer.insertAdjacentHTML('beforeend', res);
                messagesContainer.scrollTop = messagesContainer.scrollHeight;
    
        })
        .catch(error => console.error('Error broadcasting message:', error));
    });
});


function sanitizeGroupName(groupName) {
    console.log(groupName);
    return groupName.replace(/[^a-zA-Z0-9_\-]/g, '_');
}