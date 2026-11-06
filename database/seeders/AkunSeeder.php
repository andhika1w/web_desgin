<?php

namespace Database\Seeders;

use App\Models\AlamatPengguna;
use App\Models\KurirProfil;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Akun contoh untuk tiap role. Semua berstatus 'aktif' agar bisa langsung login.
 * Kata sandi semua akun: password
 *
 * Aman dijalankan berulang (updateOrCreate berdasarkan email).
 */
class AkunSeeder extends Seeder
{
    public const PASSWORD = 'password';

    public function run(): void
    {
        // ---- Admin ----
        $this->akun([
            'role' => User::ROLE_ADMIN,
            'nama' => 'Administrator',
            'email' => 'admin@example.com',
            'no_hp' => '081200000001',
        ]);

        // ---- Pengguna ----
        $pengguna = $this->akun([
            'role' => User::ROLE_PENGGUNA,
            'nama' => 'Siti Rahmawati',
            'email' => 'pengguna@example.com',
            'no_hp' => '081200000002',
        ]);

        AlamatPengguna::updateOrCreate(
            ['user_id' => $pengguna->user_id, 'label' => 'Rumah'],
            [
                'alamat_lengkap' => 'Jl. Kenanga No. 12, RT 03/RW 05, Kel. Bandar Lor, Kota Kediri',
                'latitude' => -7.8115000,
                'longitude' => 112.0050000,
                'is_default' => true,
            ]
        );

        // ---- Pengelola TPS ----
        $this->akun([
            'role' => User::ROLE_PENGELOLA_TPS,
            'nama' => 'Budi Santoso',
            'email' => 'pengelola@example.com',
            'no_hp' => '081200000003',
        ]);

        // ---- Kurir ----
        $kurir = $this->akun([
            'role' => User::ROLE_KURIR,
            'nama' => 'Agus Prasetyo',
            'email' => 'kurir@example.com',
            'no_hp' => '081200000004',
        ]);

        KurirProfil::updateOrCreate(
            ['user_id' => $kurir->user_id],
            [
                'jenis_kendaraan' => 'Motor roda tiga',
                'plat_nomor' => 'AG 4512 KR',
                'no_sim' => '1234-5678-000001',
                'kapasitas_angkut_kg' => 150,
                'status_ketersediaan' => 'online',
            ]
        );
    }

    private function akun(array $data): User
    {
        return User::updateOrCreate(
            ['email' => $data['email']],
            $data + [
                'password_hash' => self::PASSWORD, // di-hash otomatis lewat cast 'hashed'
                'status' => 'aktif',
            ]
        );
    }
}

