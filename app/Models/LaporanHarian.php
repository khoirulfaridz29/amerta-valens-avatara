<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LaporanHarian extends Model
{
    protected $table = 'laporan_harian';

    protected $fillable = [
        'alat_id',
        'proyek_id',
        'user_id',
        'tanggal',
        'hm_awal',
        'hm_akhir',
        'solar_jerigen',
        'solar_liter',
        'foto_hm_awal',
        'foto_hm_akhir',
        'foto_lokasi',
        'keterangan',
    ];

    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
            'hm_awal' => 'decimal:2',
            'hm_akhir' => 'decimal:2',
            'solar_jerigen' => 'decimal:2',
            'solar_liter' => 'decimal:2',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (LaporanHarian $laporan): void {
            $laporan->solar_liter = ((float) $laporan->solar_jerigen) * 35;
        });
    }

    public function alat(): BelongsTo
    {
        return $this->belongsTo(Alat::class);
    }

    public function proyek(): BelongsTo
    {
        return $this->belongsTo(Proyek::class);
    }

    public function operator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    protected function getAlatKodeAttribute(): ?string
    {
        return $this->alat?->kode;
    }

    protected function getAlatNamaAttribute(): ?string
    {
        return $this->alat?->nama;
    }

    protected function getProyekNamaAttribute(): ?string
    {
        return $this->proyek?->nama;
    }

    protected function getOperatorNamaAttribute(): ?string
    {
        return $this->operator?->name;
    }
}
