<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LogAktivitasAdmin extends Model
{
    protected $table = 'log_aktivitas_admin';
    protected $primaryKey = 'log_id';

    const UPDATED_AT = null;

    protected $fillable = [
        'admin_id',
        'aksi',
        'tabel_target',
        'id_target',
        'detail',
        'ip_address',
    ];

    protected function casts(): array
    {
        return [
            'detail' => 'array',
        ];
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id', 'user_id');
    }
}

