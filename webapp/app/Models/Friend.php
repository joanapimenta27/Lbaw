<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;



class Friend extends user {
    use HasFactory;
    protected $table = 'friend';
    public $timestamps = false; 
    protected $fillable = [
        'user_id',
        'friend_id'
    ];
}