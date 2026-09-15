<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'users';
    protected $primaryKey = 'id_user';

    public $timestamps = false;

    protected $fillable = [
        'username',
        'email',
        'password',
        'id_role',
        'status'
    ];

    protected $hidden = [
        'password'
    ];

    public function role()
    {
        return $this->belongsTo(Role::class, 'id_role', 'id_role');
    }

    public function profil()
    {
        return $this->hasOne(Profil::class, 'id_user', 'id_user');
    }

    public function minat()
    {
        return $this->belongsToMany(
            Minat::class,
            'minat_user',
            'id_user',
            'id_minat'
        )->withPivot('id_userminat');
    }

    public function minatUser()
    {
        return $this->hasMany(
            MinatUser::class,
            'id_user',
            'id_user'
        );
    }

    public function komunitasDipimpin()
    {
        return $this->hasMany(
            Komunitas::class,
            'id_leader',
            'id_user'
        );
    }

    public function komunitas()
    {
        return $this->belongsToMany(
            Komunitas::class,
            'member_komunitas',
            'id_user',
            'id_komunitas'
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
            'id_user',
            'id_user'
        );
    }

    public function posts()
    {
        return $this->hasMany(
            Post::class,
            'id_user',
            'id_user'
        );
    }

    public function komentar()
    {
        return $this->hasMany(
            Komentar::class,
            'id_user',
            'id_user'
        );
    }

    public function likes()
    {
        return $this->hasMany(
            Like::class,
            'id_user',
            'id_user'
        );
    }

    public function pesan()
    {
        return $this->hasMany(
            Pesan::class,
            'id_user',
            'id_user'
        );
    }

    public function webinarDibuat()
    {
        return $this->hasMany(
            Webinar::class,
            'id_leader',
            'id_user'
        );
    }

    public function webinar()
    {
        return $this->belongsToMany(
            Webinar::class,
            'partisipasi_webinar',
            'id_user',
            'id_webinar'
        )->withPivot(
            'id_parnar',
            'bergabung_pada'
        );
    }

    public function partisipasiWebinar()
    {
        return $this->hasMany(
            PartisipasiWebinar::class,
            'id_user',
            'id_user'
        );
    }

    public function pengajuan()
    {
        return $this->hasMany(
            Pengajuan::class,
            'id_user',
            'id_user'
        );
    }

    public function reports()
    {
        return $this->hasMany(
            Report::class,
            'id_user',
            'id_user'
        );
    }

    public function notifikasi()
    {
        return $this->hasMany(
            Notifikasi::class,
            'id_user',
            'id_user'
        );
    }
}