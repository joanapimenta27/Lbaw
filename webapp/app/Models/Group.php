<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\User;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;


class Group extends Model
{
    use HasFactory;
    public $timestamps = false; // Disable automatic timestamps

    protected $fillable = [
        'name',
        'owner_id',
    ];

    
    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function members()
    {
        return $this->belongsToMany(User::class, 'group_members');
    }

    public function addMember(User $user)
    {
        return $this->members()->attach($user);
    }

    public function removeMember(User $user)
    {
        return $this->members()->detach($user);
    }

    public function hasMember(User $user)
    {
        return $this->members()->where('user_id', $user->id)->exists();
    }

    public static function findByName(string $name): ?self
{
    return self::where('name', $name)->first();
}

public function sendInvite(User $user)
{
    \DB::table('group_invite')->insert([
        'user_id' => $user->id,
        'group_id' => $this->id,
    ]);
}

public function getFriendsNotInGroupAndNoInvite($groupId, $userId, $query = '')
{
    $queryBuilder = User::whereIn('id', function ($queryBuilder) use ($userId) {
        $queryBuilder->select('friend_id')
            ->from('friend')
            ->where('user_id', $userId)
            ->union(
                DB::table('friend')
                    ->select('user_id')
                    ->where('friend_id', $userId)
            );
    })
    ->whereNotIn('id', function ($queryBuilder) use ($groupId) {
        $queryBuilder->select('user_id')
            ->from('group_members')
            ->where('group_id', $groupId);
    })
    ->whereNotIn('id', function ($queryBuilder) use ($groupId) {
        $queryBuilder->select('user_id')
            ->from('group_invite')
            ->where('group_id', $groupId);
    });

    if (!empty($query)) {
        $queryBuilder->where('username', 'LIKE', '%' . $query . '%');
    }

    return $queryBuilder->get();
}


public function getMembersByQuery(string $query = null)
{
    $queryBuilder = $this->members();

   
    $ownerId = $this->owner_id; 
    $queryBuilder->where('id', '!=', $ownerId);

    if (!empty($query)) {
        $queryBuilder->where('username', 'like', '%' . $query . '%');
    }

    return $queryBuilder->get(['id', 'username']);
}

public function deleteGroup()
{
    $this->members()->detach();

    DB::table('group_invite')->where('group_id', $this->id)->delete();

    return $this->delete();
}

public function leaveGroup(User $user)
{
    $this->members()->detach($user);
    return true; 
}



}
