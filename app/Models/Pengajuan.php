<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Pengajuan extends Model
{
    use HasFactory;

    protected $table = 'pengajuan';
    protected $primaryKey = 'id_pengajuan';

    public $timestamps = false;

    protected $fillable = [
        'id_user',
        'id_komunitas',
        'tipe',
        'judul',
        'deskripsi',
        'kategori',
        'nama_komunitas',
        'gambar',
        'alasan',
        'pembicara',
        'tanggal',
        'waktu',
        'foto',
        'link_meeting',
        'status',
        'catatan_admin',
        'tanggal_pengajuan',
        'diproses_pada'
    ];

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