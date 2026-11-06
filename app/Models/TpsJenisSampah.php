<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * Pivot tps_jenis_sampah (primary key komposit: tps_id + jenis_id).
 *
 * Eloquent tidak mendukung PK komposit, jadi untuk update gunakan:
 *   $tps->jenisSampah()->updateExistingPivot($jenisId, ['terpakai_kg' => 0]);
 */
class TpsJenisSampah extends Pivot
{
    protected $table = 'tps_jenis_sampah';
    public $incrementing = false;

    const CREATED_AT = null;
    const UPDATED_AT = 'updated_at';
    public $timestamps = true;

    protected $fillable = [
        'tps_id',
        'jenis_id',
        'kapasitas_maks_kg',
        'terpakai_kg',
        'diterima',
        'catatan',
    ];

    protected function casts(): array
    {
        return [
            'kapasitas_maks_kg' => 'decimal:2',
            'terpakai_kg' => 'decimal:2',
            'diterima' => 'boolean',
            'updated_at' => 'datetime',
        ];
    }

    /** Agar save()/delete() pada instance memakai kedua kolom kunci. */
    protected function setKeysForSaveQuery($query): Builder
    {
        return $query->where('tps_id', $this->getAttribute('tps_id'))
            ->where('jenis_id', $this->getAttribute('jenis_id'));
    }

    public function tps(): BelongsTo
    {
        return $this->belongsTo(Tps::class, 'tps_id', 'tps_id');
    }

    public function jenisSampah(): BelongsTo
    {
        return $this->belongsTo(JenisSampah::class, 'jenis_id', 'jenis_id');
    }

    public function getSisaKgAttribute(): float
    {
        return (float) $this->kapasitas_maks_kg - (float) $this->terpakai_kg;
    }
}
