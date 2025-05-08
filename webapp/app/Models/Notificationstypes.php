<?php

use App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo; 


class Notificationstypes extends Model
{
    use HasFactory;

    protected $fillable = 'notifications_types';
    public $timestamps = false;

    public function User() {
        $this->belongsTo('App\Model\User');
    }
}