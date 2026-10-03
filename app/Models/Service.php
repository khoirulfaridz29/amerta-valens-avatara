<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Service extends Model
{
    protected $table = 'service';

    protected $fillable = ['alat_id', 'tanggal', 'hm', 'jenis', 'keterangan', 'biaya'];

    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
            'hm' => 'decimal:2',
            'biaya' => 'decimal:2',
        ];
    }

    public function alat(): BelongsTo
    {
        return $this->belongsTo(Alat::class);
    }

    protected function getAlatKodeAttribute(): ?string
    {
        return $this->alat?->kode;
    }

    protected function getAlatNamaAttribute(): ?string
    {
        return $this->alat?->nama;
    }
}
