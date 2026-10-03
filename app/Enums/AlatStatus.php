<?php

namespace App\Enums;

enum AlatStatus: string
{
    case AKTIF = 'aktif';
    case IDLE = 'idle';
    case PERBAIKAN = 'perbaikan';

    public function label(): string
    {
        return match ($this) {
            self::AKTIF => 'Aktif',
            self::IDLE => 'Idle',
            self::PERBAIKAN => 'Perbaikan',
        };
    }
}
