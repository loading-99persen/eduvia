<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Webinar extends Model
{
    use HasFactory;

    protected $table = 'webinar';
    protected $primaryKey = 'id_webinar';

    public $timestamps = false;

    protected $fillable = [
        'id_leader',
        'id_komunitas',
        'judul',
        'deskripsi',
        'pembicara',
        'tanggal',
        'waktu',
        'foto',
        'kategori',
        'link_meeting',
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

    public function komunitas()
    {
        return $this->belongsTo(
            Komunitas::class,
            'id_komunitas',
            'id_komunitas'
        );
    }

    public function peserta()
    {
        return $this->belongsToMany(
            User::class,
            'partisipasi_webinar',
            'id_webinar',
            'id_user'
        )->withPivot(
            'id_parnar',
            'bergabung_pada'
        );
    }

    public function partisipasi()
    {
        return $this->hasMany(
            PartisipasiWebinar::class,
            'id_webinar',
            'id_webinar'
        );
    }
}