<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Tag;


use Illuminate\Database\Eloquent\Relations\HasMany;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Post extends Model
{
    use HasFactory;

    protected $table = 'post';
    public $timestamps = false;

    protected $fillable = [
        'author_id',
        'date',
        'is_public',
        'title',
        'description',
        'like_num',
        'flick_num',
        'share_num',
    ];


    protected $casts = [
        'date' => 'datetime',
    ];

    // ------------- Define relationships -------------- //
    
    // Each post belongs to one user (author)

    // Relationships

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }


    // Each post has many media files associated with it
    public function media(): HasMany
    {
        return $this->hasMany(PostMedia::class, 'post_id');
    }

    // Get public posts
    public static function getPublicPosts()
    {
        return Post::where('is_public', true)
        ->orderByDesc('date');
    }

    // Get private posts by user
    public static function getPrivatePosts($userId)
    {
        return Post::where('is_public', false)
        ->where('author_id', $userId)
        ->orderByDesc('date');
    }
    public static function getPrivateFeed($userId){
    
        $friendIds = User::find($userId)->friends()->pluck('id');

        
        return self::whereIn('author_id', $friendIds)
            ->where('author_id', '!=', $userId)
            ->orderBy('date', 'desc');
    }


    // Get feed posts (friends + public posts)
    public static function getFeedPosts($filter=null)
    {
        $user = Auth::user();
        $friendIds = $user ? $user->friends->pluck('id')->toArray() : [];

        
        $query = Post::where(function ($query) use ($friendIds) {
            $query->where('is_public', true);
            if (!empty($friendIds)) {
                $query->orWhereIn('author_id', $friendIds);
            }
        });

        if ($filter === 'older') {

            $query->orderBy('date', 'asc');
        } else {
            $query->orderBy('date', 'desc');
        }

        $posts = $query->get();

        return $posts;
    }   
    
    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'post_tag', 'post_id', 'tag_id');
    }


    public static function searchPosts($searchTerm)
    {
        $posts = self::whereRaw("tsvectors @@ plainto_tsquery('portuguese', ?)", [$searchTerm])
        ->selectRaw("*, ts_rank(tsvectors, plainto_tsquery('portuguese', ?)) AS rank", [$searchTerm])//rank para calcular a relevancia do search
        ->orderByDesc('rank')  
        ->get();

    

        return $posts;
    }


    public static function getPostByFullTextSearch($query, $filter = null) {
        $posts = self::getFeedPosts();
    
        $postIds = $posts->pluck('id')->toArray();
    
        
        $queryBuilder = self::whereRaw("tsvectors @@ plainto_tsquery('portuguese', ?)", [$query])
            ->selectRaw("*, ts_rank(tsvectors, plainto_tsquery('portuguese', ?)) AS rank", [$query])
            ->whereIn('id', $postIds);
    
        
        if ($filter == 'older') {//if filter order by date asc
            $queryBuilder->orderBy('date', 'asc'); 
        } else {
            $queryBuilder->orderByDesc('rank'); 
        }
    
        $results = $queryBuilder->get();
    
        return $results;
    }
    


    public static function searchPostsByQuery($searchTerm, $filter = null) {
        $posts = self::getFeedPosts($filter);
    
        if (!$searchTerm) {
            return $posts;
        }
    
        $posts = collect($posts);
    
        $filteredPosts = $posts->filter(function ($post) use ($searchTerm, $filter) {
            if ($filter === 'description') {
                return stripos($post->description, $searchTerm) !== false;
            }
            elseif ($filter ==='title') {
                return stripos($post->title, $searchTerm) !== false;

            }else{
                return stripos($post->title, $searchTerm) !== false || stripos($post->description, $searchTerm) !== false;
            }
        });
    
        return $filteredPosts->values();
    }
    

    public static function getTagsBySearchTerm($searchTerm,$post){
        foreach ($post->tags as $tag) {
            if (stripos($tag->name, $searchTerm) !== false) {
                return true;
            }
        }
        return false; 

    }

    public static function searchPostsByTags($searchTerm){
        $posts = self::getFeedPosts();
        if (!$searchTerm) {
            return $posts;
        }
        $filteredPosts = $posts->filter(function ($post) use ($searchTerm) {
            return self::getTagsBySearchTerm($searchTerm, $post);
        });
        return $filteredPosts->sortByDesc('date')->values();
    }      


    public function comments()
    {
        return $this->hasMany('App\Models\Comment');
    }

    public function likes(){
        return $this->belongsToMany(User::class, 'post_likes', 'post_id', 'user_id');
    }


    public function isLikedByUser() {
        $userId = Auth::id();
        return $userId && $this->likes()->where('user_id', $userId)->exists();
    }

    public function getIsLikedAttribute(){
        return $this->isLikedByUser();
    }
}
