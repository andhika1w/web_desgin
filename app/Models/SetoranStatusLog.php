<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SetoranStatusLog extends Model
{
    protected $table = 'setoran_status_log';
    protected $primaryKey = 'log_id';

    const UPDATED_AT = null;

    protected $fillable = [
        'setoran_id',
        'status_lama',
        'status_baru',
        'diubah_oleh',
        'catatan',
    ];

    public function setoran(): BelongsTo
    {
        return $this->belongsTo(Setoran::class, 'setoran_id', 'setoran_id');
    }

    public function pengubah(): BelongsTo
    {
        return $this->belongsTo(User::class, 'diubah_oleh', 'user_id');
    }
}

