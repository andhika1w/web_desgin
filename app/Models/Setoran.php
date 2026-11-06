<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * Alur status:
 *   dijemput      : menunggu_kurir -> kurir_ditugaskan -> diangkut -> tiba_di_tps -> selesai
 *   antar_sendiri : menuju_tps -> tiba_di_tps -> selesai
 *   cabang        : ditolak_tps / dibatalkan
 */
class Setoran extends Model
{
    public const METODE_DIJEMPUT = 'dijemput';
    public const METODE_ANTAR_SENDIRI = 'antar_sendiri';

    protected $table = 'setoran';
    protected $primaryKey = 'setoran_id';

    protected $fillable = [
        'kode_setoran',
        'pengguna_id',
        'tps_id',
        'metode',
        'status',
        'estimasi_tiba',
        'catatan_pengguna',
        'alasan_penolakan',
        'total_berat_aktual_kg',
        'total_poin',
        'selesai_at',
    ];

    protected function casts(): array
    {
        return [
            'estimasi_tiba' => 'datetime',
            'total_berat_aktual_kg' => 'decimal:2',
            'total_poin' => 'integer',
            'selesai_at' => 'datetime',
        ];
    }

    public function pengguna(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pengguna_id', 'user_id');
    }

    public function tps(): BelongsTo
    {
        return $this->belongsTo(Tps::class, 'tps_id', 'tps_id');
    }

    public function detail(): HasMany
    {
        return $this->hasMany(SetoranDetail::class, 'setoran_id', 'setoran_id');
    }

    public function penjemputan(): HasOne
    {
        return $this->hasOne(Penjemputan::class, 'setoran_id', 'setoran_id');
    }

    public function ulasan(): HasOne
    {
        return $this->hasOne(Ulasan::class, 'setoran_id', 'setoran_id');
    }

    public function notifikasi(): HasMany
    {
        return $this->hasMany(Notifikasi::class, 'setoran_id', 'setoran_id');
    }

    public function transaksiPoin(): HasMany
    {
        return $this->hasMany(TransaksiPoin::class, 'setoran_id', 'setoran_id');
    }

    public function statusLog(): HasMany
    {
        return $this->hasMany(SetoranStatusLog::class, 'setoran_id', 'setoran_id')->orderBy('created_at');
    }
}

