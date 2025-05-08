<?php


namespace App\Policies;


use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Auth;


class UserPolicy
{
    use HandlesAuthorization;

    /**
     * Determine if the given user can send a friend request to another user.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\User  $targetUser
     * @return bool
     */
    public function sendFriendRequest(User $user, User $targetUser)
    {
        return Auth::check() && $user->id !== $targetUser->id;
    }

    /**
     * Determine if the given user can accept a friend request from another user.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\User  $targetUser
     * @return bool
     */
    public function acceptFriendRequest(User $user, User $targetUser)
    {
        return Auth::check() && $user->id === $targetUser->id;
    }

    /**
     * Determine if the given user can reject a friend request from another user.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\User  $targetUser
     * @return bool
     */
    public function rejectFriendRequest(User $user, User $targetUser)
    {
        return Auth::check() && $user->id === $targetUser->id;
    }

    /**
     * Determine if the given user can remove a friend request sent to another user.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\User  $targetUser
     * @return bool
     */
    public function removeFriendRequest(User $user, User $targetUser)
    {
        
        return Auth::check() && $user->id !== $targetUser->id;

    }

    public function removeFriend(User $user, User $targetUser)
    {
        return Auth::check() && $user->id !== $targetUser->id;
    }

    public function blockUser(User $user, User $targetUser)
    {
        return Auth::check() && $user->id !== $targetUser->id;
    }
    
    public function unblockUser(User $user, User $targetUser)
    {
        return Auth::check() && $user->id !== $targetUser->id;
    }
}