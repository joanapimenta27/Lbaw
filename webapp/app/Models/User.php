<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Relations\HasMany;

use App\Models\Admin;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    // Don't add create and update timestamps in database.
    public $timestamps  = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'username',
        'name',
        'email',
        'age',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Get the Posts for a user.
     */
    public function posts(): HasMany
    {
        return $this->hasMany(Post::class, 'author_id');
    }
    

    /**
     * Get the admin boolean associated with the user.
     */
    public function admin(){
        return $this->hasOne(Admin::class,'user_id');
    }

    public function isAdmin():bool{
        return $this -> admin !== null;
    }

    public function isSuper()
    {
        return Admin::where('user_id', $this->id)->value('is_super');
    }

    public function isBlockedByAdmin()
    {
        return DB::table('blocked_users')->where('user_id', $this->id)->exists();
    }

    public function friends(){
        return $this->belongsToMany(User::class, 'friend','user_id','friend_id');
    }
    public function groups()
    {
        return $this->belongsToMany(Group::class, 'group_members')
                    ->withTimestamps();
    }

    public function isFriendWith(User $user): bool{
        return ($this -> friends()->where('friend_id', $user->id)->exists() || $user -> friends()->where('friend_id', $this->id)->exists() );
    }
    
    public static function getUserByUserName($query)
    {   
    return self::where('username', 'LIKE', '%' . $query . '%')
               ->whereDoesntHave('deletedUser')  // Exclude deleted users
               ->get();
    }

    public static function getUserByName($query)
    {
    return self::where('name', 'LIKE', '%' . $query . '%')
               ->whereDoesntHave('deletedUser')  // Exclude deleted users
               ->get();
    }

    public static function getUserByEmailQuery($query)
    {   
    return self::where('email', 'LIKE', '%' . $query . '%')
               ->whereDoesntHave('deletedUser')  // Exclude deleted users
               ->get();
    }
    public static function findByEmail($email)
    {
        return self::where('email', $email)->first(); 
    }
    
    public function hasSentRequest(User $user){
        return Friendrequest::where('req_id', $this->id)->where('rcv_id', $user->id)->exists();
    }
    public function hasReceivedRequest(User $user){
        return Friendrequest::where('req_id', $user->id)->where('rcv_id', $this->id)->exists();
    }
    public function blockedUsers(){
        return $this->belongsToMany(User::class, 'friend','user_id','friend_id');
    }
    public function isBlocked(User $user): bool{
        return ($this -> blockedUsers()->where('friend_id', $user->id)->exists() || $user->blockedUsers()->where('friend_id', $this->id)->exists() );
    }
    public function hasBlocked(User $user){
        return Blockfriend::where('blocker_id', $this->id)->where('blocked_id', $user->id)->exists();
    }

    public function deletedUser()
    {
        return $this->hasOne(DeletedUser::class, 'user_id');
    }

    public function isDeleted(): bool
    {
        return $this->deletedUser !== null;
    }

    public static  function friendsAndChat($userId)
    {
        return User::whereIn('id', function ($query) use ($userId) {
            $query->select('sender_id')
                ->from('message')
                ->where('receiver_id', $userId)  
                ->whereNull('group_id')
                ->union(
                    Message::select('receiver_id')  
                        ->where('sender_id', $userId)  
                        ->whereNull('group_id')
                );
        })->distinct()->get();
    }
    
    public static  function getAllGroups($userId)
    {
        $groupIds = \DB::table('group_members')
                       ->where('user_id', $userId)
                       ->pluck('group_id'); 
    
        return Group::whereIn('id', $groupIds)->get(); 
    }
    

    
}

   