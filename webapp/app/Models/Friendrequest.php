<?php

namespace App\Models;   

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Friendrequest extends Model
{
    use HasFactory;
    
    protected $table = 'friend_request';
    public $timestamps = false;

    protected $fillable = [
        'sender_id',
        'receiver_id',
    ];


    protected $primaryKey = ['req_id', 'rcv_id'];
    public $incrementing = false;
    protected $keyType = 'string';
}