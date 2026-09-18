<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PartisipasiWebinar extends Model
{
    use HasFactory;

    protected $table = 'partisipasi_webinar';
    protected $primaryKey = 'id_parnar';

    public $timestamps = false;

    protected $fillable = [
        'id_webinar',
        'id_user',
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

    public function webinar()
    {
        return $this->belongsTo(
            Webinar::class,
            'id_webinar',
            'id_webinar'
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