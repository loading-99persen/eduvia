<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Minat extends Model
{
    use HasFactory;

    protected $table = 'minat';
    protected $primaryKey = 'id_minat';

    public $timestamps = false;

    protected $fillable = [
        'nama_minat'
    ];

    public function users()
    {
        return $this->belongsToMany(
            User::class,
            'minat_user',
            'id_minat',
            'id_user'
        )->withPivot('id_userminat');
    }

    public function minatUser()
    {
        return $this->hasMany(
            MinatUser::class,
            'id_minat',
            'id_minat'
        );
    }
}