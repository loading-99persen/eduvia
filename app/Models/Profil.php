<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Profil extends Model
{
    use HasFactory;

    protected $table = 'profil';
    protected $primaryKey = 'id_profil';

    public $timestamps = false;

    protected $fillable = [
        'id_user',
        'nama_lengkap',
        'photo',
        'bio',
        'tanggal_lahir',
        'jenis_kelamin',
        'tingkat_pendidikan',
        'institusi',
        'media_sosial',
        'preferensi_belajar'
    ];

    public function user()
    {
        return $this->belongsTo(
            User::class,
            'id_user',
            'id_user'
        );
    }
}   