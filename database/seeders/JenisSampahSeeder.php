<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * DATA AWAL: JENIS SAMPAH
 */
class JenisSampahSeeder extends Seeder
{
    public function run(): void
    {
        $kolom = ['nama', 'kategori', 'deskripsi', 'perlu_penanganan_khusus', 'poin_per_kg'];

        $data = [
            ['Sisa makanan',        'organik',    'Sisa dapur dan makanan',                         0, 5],
            ['Daun dan ranting',    'organik',    'Sampah kebun',                                   0, 5],
            ['Plastik',             'anorganik',  'Botol, kemasan, kantong plastik',                0, 20],
            ['Kertas dan kardus',   'anorganik',  'Kertas bekas, kardus, koran',                    0, 15],
            ['Logam',               'anorganik',  'Kaleng, besi, aluminium',                        0, 30],
            ['Kaca',                'anorganik',  'Botol dan pecahan kaca',                         1, 10],
            ['Baterai bekas',       'b3',         'Baterai dan aki kecil',                          1, 10],
            ['Lampu bekas',         'b3',         'Lampu neon dan LED',                             1, 10],
            ['Elektronik bekas',    'elektronik', 'Ponsel, kabel, peralatan elektronik',            1, 25],
            ['Medis - infeksius',   'medis',      'Perban, sarung tangan, masker bekas pakai',      1, 0],
            ['Medis - benda tajam', 'medis',      'Jarum suntik, pisau bedah, ampul pecah',         1, 0],
            ['Medis - farmasi',     'medis',      'Obat kedaluwarsa dan sisa obat',                 1, 0],
            ['Medis - patologis',   'medis',      'Jaringan dan cairan tubuh dari fasilitas medis', 1, 0],
        ];

        DB::table('jenis_sampah')->upsert(
            array_map(fn (array $row) => array_combine($kolom, $row), $data),
            ['nama'],
            ['kategori', 'deskripsi', 'perlu_penanganan_khusus', 'poin_per_kg']
        );
    }
}

