<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Khusus metode 'dijemput' (1 setoran = 1 penjemputan)
        Schema::create('penjemputan', function (Blueprint $table) {
            $table->id('penjemputan_id');
            $table->foreignId('setoran_id')->unique()
                ->constrained('setoran', 'setoran_id', 'fk_pj_setoran')
                ->cascadeOnDelete();
            $table->foreignId('alamat_id')
                ->constrained('alamat_pengguna', 'alamat_id', 'fk_pj_alamat');
            // NULL sampai ada kurir yang menerima
            $table->foreignId('kurir_id')->nullable()
                ->constrained('kurir_profil', 'user_id', 'fk_pj_kurir');
            $table->dateTime('jadwal_jemput')->nullable();
            $table->enum('status', ['menunggu', 'diterima', 'diangkut', 'tiba_di_tps', 'dibatalkan'])
                ->default('menunggu');
            $table->dateTime('diterima_at')->nullable();
            $table->dateTime('diangkut_at')->nullable();
            $table->dateTime('tiba_at')->nullable();
            $table->string('foto_bukti_ambil', 255)->nullable();
            $table->string('foto_bukti_serah', 255)->nullable();
            $table->string('catatan_kurir', 255)->nullable();

            $table->index(['kurir_id', 'status'], 'idx_pj_kurir_status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penjemputan');
    }
};

