<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeletedUser extends Model
{
    use HasFactory;

    protected $table = 'deleted_users';

    protected $fillable = [
        'user_id',
        'deleted_at',
    ];

    public $timestamps = false;
}