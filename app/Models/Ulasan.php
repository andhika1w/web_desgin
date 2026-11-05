<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Ulasan extends Model
{
    protected $table = 'ulasan';
    protected $primaryKey = 'ulasan_id';

    const UPDATED_AT = null;

    protected $fillable = [
        'setoran_id',
        'pengguna_id',
        'rating_kurir',
        'rating_tps',
        'komentar',
    ];

    protected function casts(): array
    {
        return [
            'rating_kurir' => 'integer',
            'rating_tps' => 'integer',
        ];
    }

    public function setoran(): BelongsTo
    {
        return $this->belongsTo(Setoran::class, 'setoran_id', 'setoran_id');
    }

    public function pengguna(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pengguna_id', 'user_id');
    }
}

