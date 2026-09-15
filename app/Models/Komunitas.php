<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Komunitas extends Model
{
    use HasFactory;

    protected $table = 'komunitas';
    protected $primaryKey = 'id_komunitas';

    public $timestamps = false;

    protected $fillable = [
        'id_leader',
        'nama_komunitas',
        'deskripsi',
        'kategori',
        'gambar',
        'status',
        'dibuat_pada'
    ];

    public function leader()
    {
        return $this->belongsTo(
            User::class,
            'id_leader',
            'id_user'
        );
    }

    public function members()
    {
        return $this->belongsToMany(
            User::class,
            'member_komunitas',
            'id_komunitas',
            'id_user'
        )->withPivot(
            'id_memkom',
            'role',
            'bergabung_pada'
        );
    }

    public function memberKomunitas()
    {
        return $this->hasMany(
            MemberKomunitas::class,
            'id_komunitas',
            'id_komunitas'
        );
    }

    public function posts()
    {
        return $this->hasMany(
            Post::class,
            'id_komunitas',
            'id_komunitas'
        );
    }

    public function chatroom()
    {
        return $this->hasOne(
            Chatroom::class,
            'id_komunitas',
            'id_komunitas'
        );
    }

    public function webinar()
    {
        return $this->hasMany(
            Webinar::class,
            'id_komunitas',
            'id_komunitas'
        );
    }

    public function pengajuan()
    {
        return $this->hasMany(
            Pengajuan::class,
            'id_komunitas',
            'id_komunitas'
        );
    }
}