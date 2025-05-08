<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Admin extends Model
{
    use HasFactory;

    protected $table = 'admin';

    protected $fillable = ['user_id', 'is_super'];
    public $timestamps = false; 
    
    protected $primaryKey = 'user_id'; 
    public $incrementing = false; 
    protected $keyType = 'string'; 
}