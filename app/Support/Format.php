<?php

namespace App\Support;

use Carbon\Carbon;
use DateTimeInterface;
use Throwable;

class Format
{
    public static function rupiah(mixed $n): string
    {
        $v = (int) round((float) ($n ?? 0));

        return ($v < 0 ? '-Rp ' : 'Rp ').number_format(abs($v), 0, ',', '.');
    }

    public static function tgl(mixed $s): string
    {
        if (! $s) {
            return '-';
        }

        try {
            $d = $s instanceof DateTimeInterface ? Carbon::instance($s) : Carbon::parse($s);
        } catch (Throwable) {
            return (string) $s;
        }

        $hari = ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'];
        $bln = ['', 'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];

        return $hari[$d->dayOfWeek].', '.$d->day.' '.$bln[(int) $d->month].' '.$d->year;
    }

    public static function hariIni(): string
    {
        return Carbon::now()->format('Y-m-d');
    }

    public static function greeting(): string
    {
        $h = (int) Carbon::now()->format('G');

        return match (true) {
            $h < 11 => 'Selamat pagi',
            $h < 15 => 'Selamat siang',
            $h < 19 => 'Selamat sore',
            default => 'Selamat malam',
        };
    }

    public static function hm(mixed $v): string
    {
        $n = (float) ($v ?? 0);
        $str = rtrim(rtrim(number_format($n, 2, '.', ''), '0'), '.');

        return ($str === '' ? '0' : $str).' HM';
    }

    public static function initials(?string $name): string
    {
        $parts = preg_split('/\s+/', trim((string) $name)) ?: [];

        return strtoupper(substr(collect($parts)->map(fn ($w) => $w[0] ?? '')->join(''), 0, 2) ?: '?');
    }
}
