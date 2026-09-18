<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MemberKomunitas extends Model
{
    use HasFactory;

    protected $table = 'member_komunitas';
    protected $primaryKey = 'id_memkom';

    public $timestamps = false;

    protected $fillable = [
        'id_komunitas',
        'id_user',
        'role',
        'bergabung_pada'
    ];

    protected $casts = [
        'bergabung_pada' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $model) {
            if (empty($model->bergabung_pada)) {
                $model->bergabung_pada = now();
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
}