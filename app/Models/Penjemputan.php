<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Khusus metode 'dijemput' (1 setoran = 1 penjemputan).
 *
 * Agar tidak rebutan, kurir menerima tugas lewat update bersyarat:
 *   Penjemputan::where('setoran_id', $id)->whereNull('kurir_id')
 *       ->update(['kurir_id' => $kurirId, 'status' => 'diterima', 'diterima_at' => now()]) === 1
 */
class Penjemputan extends Model
{
    protected $table = 'penjemputan';
    protected $primaryKey = 'penjemputan_id';
    public $timestamps = false;

    protected $fillable = [
        'setoran_id',
        'alamat_id',
        'kurir_id',
        'jadwal_jemput',
        'status',
        'diterima_at',
        'diangkut_at',
        'tiba_at',
        'foto_bukti_ambil',
        'foto_bukti_serah',
        'catatan_kurir',
    ];

    protected function casts(): array
    {
        return [
            'jadwal_jemput' => 'datetime',
            'diterima_at' => 'datetime',
            'diangkut_at' => 'datetime',
            'tiba_at' => 'datetime',
        ];
    }

    public function setoran(): BelongsTo
    {
        return $this->belongsTo(Setoran::class, 'setoran_id', 'setoran_id');
    }

    public function alamat(): BelongsTo
    {
        return $this->belongsTo(AlamatPengguna::class, 'alamat_id', 'alamat_id');
    }

    public function kurir(): BelongsTo
    {
        return $this->belongsTo(KurirProfil::class, 'kurir_id', 'user_id');
    }

    public function kurirUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'kurir_id', 'user_id');
    }
}

