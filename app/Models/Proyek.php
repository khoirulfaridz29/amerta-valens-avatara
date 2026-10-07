<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Proyek extends Model
{
    protected $table = 'proyek';

    protected $fillable = ['nama', 'lokasi'];

    public function alat(): HasMany
    {
        return $this->hasMany(Alat::class);
    }

    public function laporan(): HasMany
    {
        return $this->hasMany(LaporanHarian::class);
    }

    public function transaksi(): HasMany
    {
        return $this->hasMany(TransaksiKas::class);
    }

    public function bons(): HasMany
    {
        return $this->hasMany(Bon::class);
    }
}
