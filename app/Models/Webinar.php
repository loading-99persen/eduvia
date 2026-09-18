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

    /** Tautan meeting dibuka sekian menit sebelum acara dimulai. */
    const MENIT_AKSES_AWAL = 30;

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

    protected $casts = [
        'tanggal'     => 'date',
        'dibuat_pada' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $webinar) {
            if (empty($webinar->dibuat_pada)) {
                $webinar->dibuat_pada = now();
            }
        });
    }

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

    /** Waktu mulai gabungan tanggal + jam. */
    public function getMulaiAttribute(): \Illuminate\Support\Carbon
    {
        $tanggal = \Illuminate\Support\Carbon::parse($this->attributes['tanggal'] ?? now())
            ->toDateString();

        $waktu = $this->attributes['waktu'] ?? '00:00:00';

        return \Illuminate\Support\Carbon::parse($tanggal . ' ' . $waktu);
    }

    public function sedangBerlangsung(): bool
    {
        if ($this->status === 'dibatalkan') {
            return false;
        }

        if ($this->status === 'berlangsung') {
            return true;
        }

        return now()->between($this->mulai, $this->mulai->copy()->addHours(2));
    }

    public function sudahLewat(): bool
    {
        return $this->status === 'selesai'
            || $this->mulai->copy()->addHours(2)->isPast();
    }

    /** Tautan meeting dibuka 30 menit sebelum acara sampai acara selesai. */
    public function bolehAksesLink(): bool
    {
        return filled($this->link_meeting)
            && $this->status !== 'dibatalkan'
            && !$this->sudahLewat()
            && now()->gte($this->mulai->copy()->subMinutes(self::MENIT_AKSES_AWAL));
    }

    /** Apakah user (default: yang sedang login) sudah mendaftar. */
    public function diikutiOleh($idUser = null): bool
    {
        $idUser = $idUser ?: auth()->id();

        if (!$idUser) {
            return false;
        }

        return $this->partisipasi()
            ->where('id_user', $idUser)
            ->exists();
    }

    public function getJumlahPesertaAttribute(): int
    {
        return (int) ($this->attributes['partisipasi_count']
            ?? $this->partisipasi()->count());
    }

    public function getSudahDaftarAttribute(): bool
    {
        return $this->diikutiOleh();
    }

    public function getLabelStatusAttribute(): string
    {
        return [
            'akan_datang' => 'Akan datang',
            'berlangsung' => 'Berlangsung',
            'selesai'     => 'Selesai',
            'dibatalkan'  => 'Dibatalkan',
        ][$this->status] ?? 'Akan datang';
    }
}