<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Like extends Model
{
    use HasFactory;

    protected $table = 'likes';
    protected $primaryKey = 'id_like';

    public $timestamps = false;

    protected $fillable = [
        'id_post',
        'id_user',
        'dibuat_pada'
    ];

    public function post()
    {
        return $this->belongsTo(
            Post::class,
            'id_post',
            'id_post'
        );
    }

    public function user()
    {
        return $this->belongsTo(
            User::class,
            'id_user',
            'id_user'
        );
    }
}