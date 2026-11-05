<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'role' => User::ROLE_PENGGUNA,
            'nama' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'no_hp' => fake()->unique()->numerify('08##########'),
            'password_hash' => static::$password ??= Hash::make('password'),
            'status' => 'aktif',
            'remember_token' => Str::random(10),
        ];
    }

    public function admin(): static
    {
        return $this->state(fn (array $attributes) => ['role' => User::ROLE_ADMIN]);
    }

    public function pengelolaTps(): static
    {
        return $this->state(fn (array $attributes) => ['role' => User::ROLE_PENGELOLA_TPS]);
    }

    public function kurir(): static
    {
        return $this->state(fn (array $attributes) => ['role' => User::ROLE_KURIR]);
    }

    /**
     * Akun menunggu verifikasi admin (pengelola_tps & kurir).
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => ['status' => 'pending']);
    }
}
