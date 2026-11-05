<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AlamatPengguna extends Model
{
    protected $table = 'alamat_pengguna';
    protected $primaryKey = 'alamat_id';
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'label',
        'alamat_lengkap',
        'latitude',
        'longitude',
        'is_default',
    ];

    protected function casts(): array
    {
        return [
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
            'is_default' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function penjemputan(): HasMany
    {
        return $this->hasMany(Penjemputan::class, 'alamat_id', 'alamat_id');
    }
}

