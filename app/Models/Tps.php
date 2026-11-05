<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tps extends Model
{
    protected $table = 'tps';
    protected $primaryKey = 'tps_id';

    protected $fillable = [
        'pengelola_id',
        'nama_tps',
        'alamat',
        'latitude',
        'longitude',
        'kapasitas_total_kg',
        'kapasitas_terpakai_kg',
        'jam_buka',
        'jam_tutup',
        'no_izin',
        'izin_limbah_medis',
        'status',
        'is_verified',
    ];

    protected function casts(): array
    {
        return [
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
            'kapasitas_total_kg' => 'decimal:2',
            'kapasitas_terpakai_kg' => 'decimal:2',
            'izin_limbah_medis' => 'boolean',
            'is_verified' => 'boolean',
        ];
    }

    public function pengelola(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pengelola_id', 'user_id');
    }

    /** Jenis sampah yang diterima TPS + kapasitas per jenis (pivot tps_jenis_sampah). */
    public function jenisSampah(): BelongsToMany
    {
        return $this->belongsToMany(JenisSampah::class, 'tps_jenis_sampah', 'tps_id', 'jenis_id')
            ->using(TpsJenisSampah::class)
            ->withPivot(['kapasitas_maks_kg', 'terpakai_kg', 'diterima', 'catatan', 'updated_at']);
    }

    public function kapasitasJenis(): HasMany
    {
        return $this->hasMany(TpsJenisSampah::class, 'tps_id', 'tps_id');
    }

    public function setoran(): HasMany
    {
        return $this->hasMany(Setoran::class, 'tps_id', 'tps_id');
    }

    public function getSisaKapasitasKgAttribute(): float
    {
        return (float) $this->kapasitas_total_kg - (float) $this->kapasitas_terpakai_kg;
    }
}

