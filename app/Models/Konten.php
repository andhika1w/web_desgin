<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Konten extends Model
{
    protected $table = 'konten';
    protected $primaryKey = 'konten_id';

    protected $fillable = [
        'admin_id',
        'judul',
        'slug',
        'jenis',
        'ringkasan',
        'isi',
        'gambar_url',
        'status',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id', 'user_id');
    }

    public function scopeTerbit(Builder $query): Builder
    {
        return $query->where('status', 'terbit')->where('published_at', '<=', now());
    }
}

