<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    public const ROLE_ADMIN = 'admin';
    public const ROLE_PENGGUNA = 'pengguna';
    public const ROLE_PENGELOLA_TPS = 'pengelola_tps';
    public const ROLE_KURIR = 'kurir';

    protected $table = 'users';
    protected $primaryKey = 'user_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'role',
        'nama',
        'email',
        'no_hp',
        'password_hash',
        'foto_url',
        'status',
        'last_login_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password_hash',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'last_login_at' => 'datetime',
            'password_hash' => 'hashed',
        ];
    }

    // ---- Auth: kolom password bernama password_hash ----

    public function getAuthPasswordName(): string
    {
        return 'password_hash';
    }

    public function getAuthPassword(): string
    {
        return $this->password_hash;
    }

    // ---- Helper role ----

    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function isPengguna(): bool
    {
        return $this->role === self::ROLE_PENGGUNA;
    }

    public function isPengelolaTps(): bool
    {
        return $this->role === self::ROLE_PENGELOLA_TPS;
    }

    public function isKurir(): bool
    {
        return $this->role === self::ROLE_KURIR;
    }

    public function scopeRole(Builder $query, string $role): Builder
    {
        return $query->where('role', $role);
    }

    // ---- Relasi ----

    public function alamat(): HasMany
    {
        return $this->hasMany(AlamatPengguna::class, 'user_id', 'user_id');
    }

    public function alamatDefault(): HasOne
    {
        return $this->hasOne(AlamatPengguna::class, 'user_id', 'user_id')->where('is_default', true);
    }

    public function kurirProfil(): HasOne
    {
        return $this->hasOne(KurirProfil::class, 'user_id', 'user_id');
    }

    /** TPS yang dikelola (1 akun pengelola_tps = 1 TPS). */
    public function tps(): HasOne
    {
        return $this->hasOne(Tps::class, 'pengelola_id', 'user_id');
    }

    public function tpsDikelola(): HasOne
    {
        return $this->hasOne(Tps::class, 'pengelola_id', 'user_id');
    }

    /** Setoran yang dibuat (role pengguna). */
    public function setoran(): HasMany
    {
        return $this->hasMany(Setoran::class, 'pengguna_id', 'user_id');
    }

    public function notifikasi(): HasMany
    {
        return $this->hasMany(Notifikasi::class, 'penerima_id', 'user_id');
    }

    /** Konten yang ditulis (role admin). */
    public function konten(): HasMany
    {
        return $this->hasMany(Konten::class, 'admin_id', 'user_id');
    }

    public function ulasan(): HasMany
    {
        return $this->hasMany(Ulasan::class, 'pengguna_id', 'user_id');
    }

    public function transaksiPoin(): HasMany
    {
        return $this->hasMany(TransaksiPoin::class, 'pengguna_id', 'user_id');
    }

    public function logAktivitasAdmin(): HasMany
    {
        return $this->hasMany(LogAktivitasAdmin::class, 'admin_id', 'user_id');
    }

    /** Saldo poin = total masuk - total keluar. */
    public function saldoPoin(): int
    {
        return (int) $this->transaksiPoin()
            ->selectRaw("COALESCE(SUM(CASE WHEN tipe = 'masuk' THEN jumlah ELSE -jumlah END), 0) AS saldo")
            ->value('saldo');
    }
}
