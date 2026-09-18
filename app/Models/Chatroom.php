<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Chatroom extends Model
{
    use HasFactory;

    protected $table = 'chatroom';
    protected $primaryKey = 'id_rc';

    public $timestamps = false;

    protected $fillable = [
        'id_komunitas',
        'nama',
        'dibuat_pada'
    ];

    protected $casts = [
        'dibuat_pada' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $model) {
            if (empty($model->dibuat_pada)) {
                $model->dibuat_pada = now();
            }
        });
    }

    public function komunitas()
    {
        return $this->belongsTo(
            Komunitas::class,
            'id_komunitas',
            'id_komunitas'
        );
    }

    public function pesan()
    {
        return $this->hasMany(
            Pesan::class,
            'id_rc',
            'id_rc'
        );
    }
}