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

    protected $casts = [
        'tanggal'           => 'date',
        'tanggal_pengajuan' => 'datetime',
        'diproses_pada'     => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $pengajuan) {
            if (empty($pengajuan->tanggal_pengajuan)) {
                $pengajuan->tanggal_pengajuan = now();
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

    public function getLabelStatusAttribute(): string
    {
        return [
            'proses'    => 'Menunggu',
            'disetujui' => 'Disetujui',
            'ditolak'   => 'Ditolak',
        ][$this->status] ?? 'Menunggu';
    }

    /**
 * Scope untuk mengambil pengajuan yang masih menunggu proses admin.
 */
public function scopeProses($query)
{
    return $query->where($this->getTable() . '.status', 'proses');
}
}
