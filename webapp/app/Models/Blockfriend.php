<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Blockfriend extends Model
{
    use HasFactory;

    protected $table = 'block_friend';
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'blocked_id',
    ];

}