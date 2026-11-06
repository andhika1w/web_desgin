<?php

namespace Database\Seeders;

use App\Models\AlamatPengguna;
use App\Models\JenisSampah;
use App\Models\Konten;
use App\Models\KurirProfil;
use App\Models\Tps;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Akun contoh untuk tiap role + contoh TPS & antrean verifikasi untuk panel Admin.
 * Kata sandi semua akun: password
 *
 * Aman dijalankan berulang (updateOrCreate).
 */
class AkunSeeder extends Seeder
{
    public const PASSWORD = 'password';

    public function run(): void
    {
        // ---- 1. Admin ----
        $admin = $this->akun([
            'role' => User::ROLE_ADMIN,
            'nama' => 'Administrator',
            'email' => 'admin@example.com',
            'no_hp' => '081200000001',
        ]);

        // ---- 2. Pengguna (Aktif) ----
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

        // ---- 3. Pengelola TPS (Aktif) + TPS Terverifikasi & TPS Menunggu Verifikasi ----
        $pengelola = $this->akun([
            'role' => User::ROLE_PENGELOLA_TPS,
            'nama' => 'Budi Santoso',
            'email' => 'pengelola@example.com',
            'no_hp' => '081200000003',
        ]);

        $tps1 = Tps::updateOrCreate(
            ['nama_tps' => 'TPS Kenanga Bandar Lor'],
            [
                'pengelola_id' => $pengelola->user_id,
                'alamat' => 'Jl. Kenanga No. 45, Kel. Bandar Lor, Kota Kediri',
                'latitude' => -7.8142000,
                'longitude' => 112.0085000,
                'kapasitas_total_kg' => 2500,
                'kapasitas_terpakai_kg' => 420,
                'jam_buka' => '07:00',
                'jam_tutup' => '17:00',
                'no_izin' => 'IZN-DLHKP/2026/014',
                'izin_limbah_medis' => false,
                'status' => 'buka',
                'is_verified' => true,
            ]
        );

        $tps2 = Tps::updateOrCreate(
            ['nama_tps' => 'TPS Terpadu Mojoroto'],
            [
                'pengelola_id' => $pengelola->user_id,
                'alamat' => 'Jl. Veteran No. 88, Kec. Mojoroto, Kota Kediri',
                'latitude' => -7.8012000,
                'longitude' => 111.9964000,
                'kapasitas_total_kg' => 4000,
                'kapasitas_terpakai_kg' => 0,
                'jam_buka' => '06:30',
                'jam_tutup' => '16:30',
                'no_izin' => 'IZN-DLHKP/2026/029',
                'izin_limbah_medis' => true,
                'status' => 'buka',
                'is_verified' => false, // Contoh untuk antrean verifikasi admin
            ]
        );

        // Isi kapasitas per jenis sampah untuk TPS Kenanga jika belum ada
        $jenisNonMedis = JenisSampah::where('kategori', '!=', 'medis')->get();
        foreach ($jenisNonMedis as $j) {
            $tps1->jenisSampah()->syncWithoutDetaching([
                $j->jenis_id => [
                    'kapasitas_maks_kg' => 250,
                    'terpakai_kg' => $j->nama === 'Plastik' ? 180 : ($j->nama === 'Kertas dan kardus' ? 140 : 25),
                    'diterima' => true,
                ],
            ]);
        }

        // ---- 4. Kurir (Aktif) ----
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

        // ---- 5. Contoh Pendaftar Kurir Pending (Untuk Diuji Verifikasi oleh Admin) ----
        $kurirPending = User::firstOrCreate(
            ['email' => 'kurir.baru@example.com'],
            [
                'role' => User::ROLE_KURIR,
                'nama' => 'Rudi Hartono',
                'no_hp' => '081200000005',
                'password_hash' => self::PASSWORD,
                'status' => 'pending',
            ]
        );

        KurirProfil::updateOrCreate(
            ['user_id' => $kurirPending->user_id],
            [
                'jenis_kendaraan' => 'Mobil pikap',
                'plat_nomor' => 'AG 8821 KB',
                'no_sim' => '1234-5678-000099',
                'kapasitas_angkut_kg' => 450,
                'status_ketersediaan' => 'offline',
            ]
        );

        // ---- 6. Contoh Konten Edukasi Awal ----
        Konten::updateOrCreate(
            ['slug' => 'panduan-memilah-sampah-b3-dan-medis-rumah-tangga'],
            [
                'admin_id' => $admin->user_id,
                'judul' => 'Panduan Memilah Sampah B3 dan Medis Rumah Tangga',
                'jenis' => 'edukasi',
                'ringkasan' => 'Baterai bekas, lampu neon, dan kemasan obat wajib dipisahkan dari sampah plastik dan organik sebelum disetor ke TPS.',
                'isi' => "Sampah Bahan Berbahaya dan Beracun (B3) seperti baterai bekas, aki kecil, dan pecahan lampu tidak boleh dicampur dengan sampah dapur.\n\nKhusus limbah medis (masker bekas perawatan, jarum insulin, obat kedaluwarsa) hanya dapat disetorkan ke TPS yang memiliki tanda Berizin Limbah Medis.",
                'status' => 'terbit',
                'published_at' => now(),
            ]
        );
    }

    private function akun(array $data): User
    {
        return User::updateOrCreate(
            ['email' => $data['email']],
            $data + [
                'password_hash' => self::PASSWORD,
                'status' => 'aktif',
            ]
        );
    }
}
