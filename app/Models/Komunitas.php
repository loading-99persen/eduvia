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

    protected $casts = [
        'dibuat_pada' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $komunitas) {
            if (empty($komunitas->dibuat_pada)) {
                $komunitas->dibuat_pada = now();
            }
        });
    }

    public function scopeAktif($query)
    {
        return $query->where('status', 'aktif');
    }

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

    /** Ambil group chat komunitas, buat bila belum ada. */
    public function chatroomAtauBuat(): Chatroom
    {
        return $this->chatroom()->firstOrCreate(
            ['id_komunitas' => $this->id_komunitas],
            ['nama' => 'Group chat ' . $this->nama_komunitas]
        );
    }

    public function getJumlahMemberAttribute(): int
    {
        return (int) ($this->attributes['member_komunitas_count']
            ?? $this->memberKomunitas()->count());
    }

    public function getSudahGabungAttribute(): bool
    {
        if (!auth()->check()) {
            return false;
        }

        if (isset($this->attributes['gabung_count'])) {
            return (int) $this->attributes['gabung_count'] > 0;
        }

        return $this->memberKomunitas()
            ->where('id_user', auth()->id())
            ->exists();
    }

    public function getSayaLeaderAttribute(): bool
    {
        return auth()->check()
            && (int) $this->id_leader === (int) auth()->id();
    }
}
