<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class UserNotifications extends Model
{
    use HasFactory;

    protected $table = 'user_notifications';
    public $timestamps = false;

    protected $fillable = [
        notification_id,
        user_id,
        notification_type,
    ];

    
}