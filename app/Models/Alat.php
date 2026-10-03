<?php

namespace App\Models;

use App\Enums\AlatStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Alat extends Model
{
    protected $table = 'alat';

    protected $fillable = ['proyek_id', 'operator_id', 'nama', 'jenis', 'kode', 'status', 'active'];

    protected function casts(): array
    {
        return [
            'status' => AlatStatus::class,
            'active' => 'boolean',
        ];
    }

    public function proyek(): BelongsTo
    {
        return $this->belongsTo(Proyek::class);
    }

    public function operator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'operator_id');
    }

    public function laporan(): HasMany
    {
        return $this->hasMany(LaporanHarian::class);
    }

    public function laporanTerakhir(): HasOne
    {
        return $this->hasOne(LaporanHarian::class)->latestOfMany('tanggal');
    }

    public function getProyekNamaAttribute(): ?string
    {
        return $this->proyek?->nama;
    }

    public function getOperatorNamaAttribute(): ?string
    {
        return $this->operator?->name;
    }
}
