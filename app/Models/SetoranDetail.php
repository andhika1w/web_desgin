<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SetoranDetail extends Model
{
    protected $table = 'setoran_detail';
    protected $primaryKey = 'detail_id';
    public $timestamps = false;

    protected $fillable = [
        'setoran_id',
        'jenis_id',
        'berat_estimasi_kg',
        'berat_aktual_kg',
        'foto_url',
        'catatan',
    ];

    protected function casts(): array
    {
        return [
            'berat_estimasi_kg' => 'decimal:2',
            'berat_aktual_kg' => 'decimal:2',
        ];
    }

    public function setoran(): BelongsTo
    {
        return $this->belongsTo(Setoran::class, 'setoran_id', 'setoran_id');
    }

    public function jenisSampah(): BelongsTo
    {
        return $this->belongsTo(JenisSampah::class, 'jenis_id', 'jenis_id');
    }
}

