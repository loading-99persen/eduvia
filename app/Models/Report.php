<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Report extends Model
{
    use HasFactory;

    protected $table = 'reports';
    protected $primaryKey = 'id_reports';

    public $timestamps = false;

    protected $fillable = [
        'id_user',
        'tipe_target',
        'id_target',
        'alasan',
        'status',
        'tanggapan_admin',
        'dibuat_pada',
        'diproses_pada'
    ];

    protected $casts = [
        'dibuat_pada'   => 'datetime',
        'diproses_pada' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $report) {
            if (empty($report->dibuat_pada)) {
                $report->dibuat_pada = now();
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
}