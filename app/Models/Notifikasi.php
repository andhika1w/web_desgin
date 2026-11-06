<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notifikasi extends Model
{
    protected $table = 'notifikasi';
    protected $primaryKey = 'notif_id';

    const UPDATED_AT = null;

    protected $fillable = [
        'penerima_id',
        'setoran_id',
        'tipe',
        'judul',
        'pesan',
        'is_read',
        'read_at',
    ];

    protected function casts(): array
    {
        return [
            'is_read' => 'boolean',
            'read_at' => 'datetime',
        ];
    }

    public function penerima(): BelongsTo
    {
        return $this->belongsTo(User::class, 'penerima_id', 'user_id');
    }

    public function setoran(): BelongsTo
    {
        return $this->belongsTo(Setoran::class, 'setoran_id', 'setoran_id');
    }

    public function scopeBelumDibaca(Builder $query): Builder
    {
        return $query->where('is_read', false);
    }

    public function tandaiDibaca(): bool
    {
        return $this->update(['is_read' => true, 'read_at' => now()]);
    }
}

