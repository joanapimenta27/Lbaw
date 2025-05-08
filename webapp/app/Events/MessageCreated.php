<?php

namespace App\Events;

use App\Models\Message;
use App\Models\Group;
use App\Models\User;
use App\Models\Post;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class MessageCreated implements ShouldBroadcast
{
    public $message;
    public $group;
   

    public function __construct(Message $message,?Group $group = null)
    {
        $this->message = $message;
        $this->group = $group;
    }

    public function broadcastOn(): Channel{
        if ($this->group) {
                
            $channelName = 'group.' . preg_replace('/[^a-zA-Z0-9_\-]/', '_', $this->group->name);
        } else {
               
            $senderId = $this->message->sender_id;
            $receiverId = $this->message->receiver_id;
            $channelName = 'private-chat.' . min($senderId, $receiverId) . '.' . max($senderId, $receiverId);
        }
    
        error_log($channelName);
        return new Channel($channelName);
    }
    public function broadcastAs()
    {

        return 'chat';
    }

    public function broadcastWith(): array
    {
        $user = User::find($this->message->sender_id);

        $data = [
            'sender' => $user,   
            'message' => $this->message,
        ];
        
        if ($this->group) {
            $data['group'] = $this->group;
        }
        

        return $data;
    }
    
}
