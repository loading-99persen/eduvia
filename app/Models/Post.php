<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Post extends Model
{
    use HasFactory;

    protected $table = 'post';
    protected $primaryKey = 'id_post';

    public $timestamps = false;

    protected $fillable = [
        'id_user',
        'id_komunitas',
        'konten',
        'gambar',
        'file',
        'dibuat_pada',
        'diperbarui_pada'
    ];

    public function user()
    {
        return $this->belongsTo(
            User::class,
            'id_user',
            'id_user'
        );
    }

    public function komunitas()
    {
        return $this->belongsTo(
            Komunitas::class,
            'id_komunitas',
            'id_komunitas'
        );
    }

    public function komentar()
    {
        return $this->hasMany(
            Komentar::class,
            'id_post',
            'id_post'
        );
    }

    public function likes()
    {
        return $this->hasMany(
            Like::class,
            'id_post',
            'id_post'
        );
    }
}