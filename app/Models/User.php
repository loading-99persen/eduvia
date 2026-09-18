<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'users';

    protected $primaryKey = 'id_user';

    public $timestamps = false;

    protected $fillable = [
        'email',
        'password',
        'google_id',
        'id_role',
        'status'
    ];

    protected $hidden = [
        'password'
    ];

    // =========================
    // ROLE
    // =========================
    public function role()
    {
        return $this->belongsTo(
            Role::class,
            'id_role',
            'id_role'
        );
    }


    public function isAdmin(): bool
{
    return $this->id_role === 1;
}

    // =========================
    // PROFIL
    // =========================
    public function profil()
    {
        return $this->hasOne(
            Profil::class,
            'id_user',
            'id_user'
        );
    }

    // =========================
    // MINAT
    // =========================
    public function minat()
    {
        return $this->belongsToMany(
            Minat::class,
            'minat_user',
            'id_user',
            'id_minat'
        )->withPivot('id_userminat');
    }

    // =========================
    // MINAT USER
    // =========================
    public function minatUser()
    {
        return $this->hasMany(
            MinatUser::class,
            'id_user',
            'id_user'
        );
    }

    // =========================
    // KOMUNITAS YANG DIPIMPIN
    // =========================
    public function komunitasDipimpin()
    {
        return $this->hasMany(
            Komunitas::class,
            'id_leader',
            'id_user'
        );
    }

    // =========================
    // KOMUNITAS YANG DIIKUTI
    // =========================
    public function komunitas()
    {
        return $this->belongsToMany(
            Komunitas::class,
            'member_komunitas',
            'id_user',
            'id_komunitas'
        )->withPivot(
            'id_memkom',
            'role',
            'bergabung_pada'
        );
    }

    // =========================
    // MEMBER KOMUNITAS
    // =========================
    public function memberKomunitas()
    {
        return $this->hasMany(
            MemberKomunitas::class,
            'id_user',
            'id_user'
        );
    }

    // =========================
    // POST
    // =========================
    public function posts()
    {
        return $this->hasMany(
            Post::class,
            'id_user',
            'id_user'
        );
    }

    // =========================
    // KOMENTAR
    // =========================
    public function komentar()
    {
        return $this->hasMany(
            Komentar::class,
            'id_user',
            'id_user'
        );
    }

    // =========================
    // LIKE
    // =========================
    public function likes()
    {
        return $this->hasMany(
            Like::class,
            'id_user',
            'id_user'
        );
    }

    // =========================
    // PESAN
    // =========================
    public function pesan()
    {
        return $this->hasMany(
            Pesan::class,
            'id_user',
            'id_user'
        );
    }

    // =========================
    // WEBINAR YANG DIBUAT
    // =========================
    public function webinarDibuat()
    {
        return $this->hasMany(
            Webinar::class,
            'id_leader',
            'id_user'
        );
    }

    // =========================
    // WEBINAR YANG DIIKUTI
    // =========================
    public function webinar()
    {
        return $this->belongsToMany(
            Webinar::class,
            'partisipasi_webinar',
            'id_user',
            'id_webinar'
        )->withPivot(
            'id_parnar',
            'bergabung_pada'
        );
    }

    // =========================
    // PARTISIPASI WEBINAR
    // =========================
    public function partisipasiWebinar()
    {
        return $this->hasMany(
            PartisipasiWebinar::class,
            'id_user',
            'id_user'
        );
    }

    // =========================
    // PENGAJUAN
    // =========================
    public function pengajuan()
    {
        return $this->hasMany(
            Pengajuan::class,
            'id_user',
            'id_user'
        );
    }

    // =========================
    // REPORT
    // =========================
    public function reports()
    {
        return $this->hasMany(
            Report::class,
            'id_user',
            'id_user'
        );
    }

    // =========================
    // NOTIFIKASI
    // =========================
    public function notifikasi()
    {
        return $this->hasMany(
            Notifikasi::class,
            'id_user',
            'id_user'
        );
    }

    // =========================
    // HELPER
    // =========================

    /** Nama tampilan: diambil dari profil, jatuh ke bagian depan email. */
    public function getNamaAttribute(): string
    {
        $nama = $this->profil->nama_lengkap ?? null;

        return $nama ?: (string) strstr($this->email . '@', '@', true);
    }

    public function aktif(): bool
    {
        return $this->status === 'aktif';
    }

    /** Profil dianggap lengkap bila data wajib sudah diisi. */
    public function profilLengkap(): bool
    {
        $profil = $this->profil;

        return (bool) $profil
            && filled($profil->nama_lengkap)
            && $profil->nama_lengkap !== 'Pengguna Baru'
            && filled($profil->tingkat_pendidikan);
    }

    /** Onboarding selesai bila minat belajar sudah dipilih. */
    public function onboardingSelesai(): bool
    {
        return $this->minat()->exists();
    }
}
