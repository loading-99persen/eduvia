<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Notifikasi extends Model
{
    use HasFactory;

    protected $table = 'notifikasi';
    protected $primaryKey = 'id_notifikasi';

    public $timestamps = false;

    protected $fillable = [
        'id_user',
        'judul',
        'pesan',
        'tipe',
        'dibaca',
        'dibuat_pada'
    ];

    protected $casts = [
        'dibaca'      => 'boolean',
        'dibuat_pada' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $notifikasi) {
            if (empty($notifikasi->dibuat_pada)) {
                $notifikasi->dibuat_pada = now();
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

    /** Isi notifikasi tanpa bagian tautan. */
    public function getIsiAttribute(): string
    {
        return trim(explode(' ||', (string) $this->pesan, 2)[0]);
    }

    /** Tautan tujuan yang disimpan di belakang pesan. */
    public function getTautanAttribute(): ?string
    {
        $bagian = explode(' ||', (string) $this->pesan, 2);

        return isset($bagian[1]) ? trim($bagian[1]) : null;
    }
}
