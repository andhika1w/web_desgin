<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KurirProfil extends Model
{
    protected $table = 'kurir_profil';
    protected $primaryKey = 'user_id';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'jenis_kendaraan',
        'plat_nomor',
        'no_sim',
        'kapasitas_angkut_kg',
        'status_ketersediaan',
        'lat_terakhir',
        'lng_terakhir',
        'lokasi_update_at',
        'rating_rata',
    ];

    protected function casts(): array
    {
        return [
            'kapasitas_angkut_kg' => 'decimal:2',
            'lat_terakhir' => 'decimal:7',
            'lng_terakhir' => 'decimal:7',
            'lokasi_update_at' => 'datetime',
            'rating_rata' => 'decimal:2',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function penjemputan(): HasMany
    {
        return $this->hasMany(Penjemputan::class, 'kurir_id', 'user_id');
    }
}

