<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Pesan extends Model
{
    use HasFactory;

    protected $table = 'pesan';
    protected $primaryKey = 'id_pesan';

    public $timestamps = false;

    protected $fillable = [
        'id_rc',
        'id_user',
        'pesan',
        'dikirim_pada'
    ];

    protected $casts = [
        'dikirim_pada' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $model) {
            if (empty($model->dikirim_pada)) {
                $model->dikirim_pada = now();
            }
        });
    }

    public function chatroom()
    {
        return $this->belongsTo(
            Chatroom::class,
            'id_rc',
            'id_rc'
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