<?php

namespace App\Enums;

enum Role: string
{
    case BOS = 'bos';
    case OPERATOR = 'operator';

    public function label(): string
    {
        return match ($this) {
            self::BOS => 'Bos / Admin',
            self::OPERATOR => 'Operator',
        };
    }
}
