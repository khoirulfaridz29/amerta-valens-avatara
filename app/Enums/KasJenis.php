<?php

namespace App\Enums;

enum KasJenis: string
{
    case MASUK = 'masuk';
    case KELUAR = 'keluar';

    public function label(): string
    {
        return match ($this) {
            self::MASUK => 'Kas Masuk',
            self::KELUAR => 'Kas Keluar',
        };
    }
}
