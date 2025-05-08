<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    use HasFactory;

    protected $table = 'tag';
    protected $fillable = ['name'];

    public function getTagName($tagId){
    
    $tag = Tag::find($tagId);

    if ($tag) {
        return $tag->name; 
    }

    return null;
    }
    
    public function posts(){
        return $this->belongsToMany(Post::class, 'post_tag', 'tag_id', 'post_id');
    }

}
