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

    protected $casts = [
        'dibuat_pada'     => 'datetime',
        'diperbarui_pada' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $post) {
            if (empty($post->dibuat_pada)) {
                $post->dibuat_pada = now();
            }
        });
    }

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

    public function milikSaya(): bool
    {
        return auth()->check()
            && (int) $this->id_user === (int) auth()->id();
    }

    /** Sudah disukai user yang sedang login. */
    public function getDisukaiAttribute(): bool
    {
        if (isset($this->attributes['disukai_count'])) {
            return (int) $this->attributes['disukai_count'] > 0;
        }

        return auth()->check()
            && $this->likes()->where('id_user', auth()->id())->exists();
    }
}
