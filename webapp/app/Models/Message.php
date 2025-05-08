<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;
    
    public $timestamps = false;


    protected $table = 'message';

    protected $fillable = [
        'content',
        'date',
        'sender_id',
        'receiver_id',
        'group_id',
        'post_id',
    ];

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    public function group()
    {
        return $this->belongsTo(Group::class, 'group_id');
    }

    public function isGroupMessage()
    {
        return $this->group_id !== null;
    }

    public function isPrivateMessage()
    {
        return $this->receiver_id !== null && $this->group_id === null;
    }

    public static function getPrivateMessagebetween2Users($mainUser, $recipient, $perPage)//esta a dar load ao contrario 
    {
        return Message::where(function ($query) use ($mainUser, $recipient) {
                $query->where('sender_id', $mainUser)
                      ->where('receiver_id', $recipient);
            })
            ->orWhere(function ($query) use ($mainUser, $recipient) {
                $query->where('sender_id', $recipient)
                      ->where('receiver_id', $mainUser);
            })
            ->orderBy('date', 'desc') // Most recent messages first
            ->paginate($perPage);
    }

    public static function countMessagesBetweenUsers($userA, $userB)
{
    return self::where(function ($query) use ($userA, $userB) {
        $query->where('sender_id', $userA)
              ->where('receiver_id', $userB);
    })->orWhere(function ($query) use ($userA, $userB) {
        $query->where('sender_id', $userB)
              ->where('receiver_id', $userA);
    })->count();
}

public static function getMessagesByGroup($group, $perPage)
{
    return Message::where('group_id', $group->id)
        ->orderBy('date', 'desc') 
        ->paginate($perPage);
}
public static function getTotalMessagesByGroup($group)
{
    return Message::where('group_id', $group->id)->count();
}




    
}
