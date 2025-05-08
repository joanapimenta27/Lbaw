<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Notification;
use Illuminate\Auth\Access\HandlesAuthorization;


class NotificationsPolicy
{   
    use HandlesAuthorization;

    public function get_notifications(User $user)
    {
        return $user->id === $notification->receiver_id;
    }

    public function view(User $user, Notification $notification)
    {
        return $user->id === $notification->receiver_id;
    }

    public function delete(User $user, Notification $notification)
    {
        return $user->id === $notification->receiver_id;
    }

    public function update(User $user, Notification $notification)
    {
        return $user->id === $notification->receiver_id;
    }

    public function create(User $user)
    {
        return $user->id === $notification->receiver_id;
    }


}