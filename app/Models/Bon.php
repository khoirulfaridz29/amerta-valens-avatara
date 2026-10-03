<?php

namespace App\Models;

use App\Enums\KasJenis;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Bon extends Model
{
    protected $table = 'bon';

    protected $fillable = ['nomor', 'customer', 'proyek_id', 'tanggal', 'jatuh_tempo', 'total', 'keterangan'];

    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
            'jatuh_tempo' => 'date',
            'total' => 'decimal:2',
        ];
    }

    public function proyek(): BelongsTo
    {
        return $this->belongsTo(Proyek::class);
    }

    public function pembayaran(): HasMany
    {
        return $this->hasMany(TransaksiKas::class, 'bon_id');
    }

    public function getProyekNamaAttribute(): ?string
    {
        return $this->proyek?->nama;
    }

    public function getDibayarAttribute(): float
    {
        return (float) $this->pembayaran()->where('jenis', KasJenis::MASUK->value)->sum('nominal');
    }

    public function getSisaAttribute(): float
    {
        return (float) $this->total - $this->getDibayarAttribute();
    }

    public function getLunasAttribute(): bool
    {
        return $this->getSisaAttribute() <= 0;
    }
}
