<?php

namespace App\Models;

use App\Enums\KasJenis;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TransaksiKas extends Model
{
    protected $table = 'transaksi_kas';

    protected $fillable = ['proyek_id', 'tanggal', 'jenis', 'nominal', 'keterangan'];

    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
            'jenis' => KasJenis::class,
            'nominal' => 'decimal:2',
        ];
    }

    public function proyek(): BelongsTo
    {
        return $this->belongsTo(Proyek::class);
    }

    protected function getProyekNamaAttribute(): ?string
    {
        return $this->proyek?->nama;
    }

    public function scopeMasuk(Builder $query): Builder
    {
        return $query->where('jenis', KasJenis::MASUK->value);
    }

    public function scopeKeluar(Builder $query): Builder
    {
        return $query->where('jenis', KasJenis::KELUAR->value);
    }
}
