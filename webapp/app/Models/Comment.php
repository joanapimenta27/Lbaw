<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;

    protected $table = 'comment';
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'post_id',
        'parent_id',
        'content',
        'date',
        'like_num',
        'reply_num',
        'edited',
    ];

    // Cast `date` column to Carbon
    protected $casts = [
        'date' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function post()
    {
        return $this->belongsTo(Post::class, 'post_id');
    }
    public function likes()
    {
        return $this->belongsToMany(User::class, 'comment_like', 'comment_id', 'user_id');
    }
    public function getIsLikedAttribute()
    {
        $user = auth()->user();
        if (!$user) {
            return false;
        }
        return $this->likes->contains($user->id);
    }
    public function replies()
    {
        return $this->hasMany(Comment::class, 'parent_id')->orderBy('date', 'desc');
    }
}
