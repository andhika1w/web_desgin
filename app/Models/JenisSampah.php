<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JenisSampah extends Model
{
    protected $table = 'jenis_sampah';
    protected $primaryKey = 'jenis_id';
    public $timestamps = false;

    protected $fillable = [
        'nama',
        'kategori',
        'deskripsi',
        'perlu_penanganan_khusus',
        'poin_per_kg',
    ];

    protected function casts(): array
    {
        return [
            'perlu_penanganan_khusus' => 'boolean',
            'poin_per_kg' => 'integer',
        ];
    }

    public function tps(): BelongsToMany
    {
        return $this->belongsToMany(Tps::class, 'tps_jenis_sampah', 'jenis_id', 'tps_id')
            ->using(TpsJenisSampah::class)
            ->withPivot(['kapasitas_maks_kg', 'terpakai_kg', 'diterima', 'catatan', 'updated_at']);
    }

    public function setoranDetail(): HasMany
    {
        return $this->hasMany(SetoranDetail::class, 'jenis_id', 'jenis_id');
    }

    public function isMedis(): bool
    {
        return $this->kategori === 'medis';
    }
}

