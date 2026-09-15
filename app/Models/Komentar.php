<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Komentar extends Model
{
    use HasFactory;

    protected $table = 'komentar';
    protected $primaryKey = 'id_komen';

    public $timestamps = false;

    protected $fillable = [
        'id_post',
        'id_user',
        'komentar',
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