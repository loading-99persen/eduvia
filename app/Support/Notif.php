<?php

namespace App\Support;

use App\Models\Notifikasi;

class Notif
{
    public static function kirim(
        int $idUser,
        string $tipe,
        string $judul,
        string $pesan,
        ?string $tautan = null
    ): ?Notifikasi {
        if (!$idUser) {
            return null;
        }

        return Notifikasi::create([
            'id_user'     => $idUser,
            'judul'       => mb_substr($judul, 0, 100),
            'pesan'       => $tautan ? $pesan . ' ||' . $tautan : $pesan,
            'tipe'        => $tipe,
            'dibaca'      => false,
            'dibuat_pada' => now(),
        ]);
    }

    public static function kirimBanyak(
        array $idUsers,
        string $tipe,
        string $judul,
        string $pesan,
        ?string $tautan = null
    ): void {
        foreach (array_unique(array_filter($idUsers)) as $idUser) {
            self::kirim((int) $idUser, $tipe, $judul, $pesan, $tautan);
        }
    }
}
