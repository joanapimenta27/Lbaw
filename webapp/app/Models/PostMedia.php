<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PostMedia extends Model
{
    use HasFactory;

    // Specify the table name if it's not pluralized by default
    protected $table = 'post_media';

    // Disable timestamps as your table doesn't have `created_at` or `updated_at`
    public $timestamps = false;

    // Specify fillable attributes for mass assignment
    protected $fillable = [
        'post_id',
        'file_path',
        'file_type',
        'order',
    ];

    // Each media file belongs to a post
    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class, 'post_id');
    }
}