<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TransaksiPoin extends Model
{
    protected $table = 'transaksi_poin';
    protected $primaryKey = 'transaksi_id';

    const UPDATED_AT = null;

    protected $fillable = [
        'pengguna_id',
        'setoran_id',
        'tipe',
        'jumlah',
        'keterangan',
    ];

    protected function casts(): array
    {
        return [
            'jumlah' => 'integer',
        ];
    }

    public function pengguna(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pengguna_id', 'user_id');
    }

    public function setoran(): BelongsTo
    {
        return $this->belongsTo(Setoran::class, 'setoran_id', 'setoran_id');
    }
}

