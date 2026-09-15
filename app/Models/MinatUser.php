<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MinatUser extends Model
{
    use HasFactory;

    protected $table = 'minat_user';
    protected $primaryKey = 'id_userminat';

    public $timestamps = false;

    protected $fillable = [
        'id_user',
        'id_minat'
    ];

    public function user()
    {
        return $this->belongsTo(
            User::class,
            'id_user',
            'id_user'
        );
    }

    public function minat()
    {
        return $this->belongsTo(
            Minat::class,
            'id_minat',
            'id_minat'
        );
    }
}