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
        'dibuat_pada',
        'id_parent'
    ];

    protected $casts = [
        'dibuat_pada' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $komentar) {
            if (empty($komentar->dibuat_pada)) {
                $komentar->dibuat_pada = now();
            }
        });
    }

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

    /** Komentar induk (bila komentar ini berupa balasan). */
    public function induk()
    {
        return $this->belongsTo(
            self::class,
            'id_parent',
            'id_komen'
        );
    }

    /** Balasan dari komentar ini. */
    public function balasan()
    {
        return $this->hasMany(
            self::class,
            'id_parent',
            'id_komen'
        );
    }

    public function milikSaya(): bool
    {
        return auth()->check()
            && (int) $this->id_user === (int) auth()->id();
    }
}
