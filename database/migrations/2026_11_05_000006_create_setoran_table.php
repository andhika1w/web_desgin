<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * 3. SETORAN (transaksi pembuangan sampah)
     *
     * Alur status:
     *   dijemput      : menunggu_kurir -> kurir_ditugaskan -> diangkut -> tiba_di_tps -> selesai
     *   antar_sendiri : menuju_tps -> tiba_di_tps -> selesai
     *   cabang        : ditolak_tps / dibatalkan
     */
    public function up(): void
    {
        Schema::create('setoran', function (Blueprint $table) {
            $table->id('setoran_id');
            // contoh: STR-20261105-0001 (dibuat aplikasi)
            $table->string('kode_setoran', 25)->unique();
            $table->foreignId('pengguna_id')
                ->constrained('users', 'user_id', 'fk_setoran_pengguna');
            $table->foreignId('tps_id')
                ->constrained('tps', 'tps_id', 'fk_setoran_tps');
            $table->enum('metode', ['dijemput', 'antar_sendiri']);
            $table->enum('status', [
                'diajukan', 'menunggu_kurir', 'kurir_ditugaskan', 'diangkut',
                'menuju_tps', 'tiba_di_tps', 'selesai', 'ditolak_tps', 'dibatalkan',
            ])->default('diajukan');
            $table->dateTime('estimasi_tiba')->nullable(); // untuk antar_sendiri
            $table->string('catatan_pengguna', 255)->nullable();
            $table->string('alasan_penolakan', 255)->nullable();
            $table->decimal('total_berat_aktual_kg', 10, 2)->nullable();
            $table->integer('total_poin')->default(0);
            $table->dateTime('created_at')->useCurrent();
            $table->dateTime('updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->dateTime('selesai_at')->nullable();

            $table->index(['tps_id', 'status'], 'idx_setoran_tps_status');
            $table->index(['pengguna_id', 'created_at'], 'idx_setoran_pengguna');
            $table->index('status', 'idx_setoran_status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('setoran');
    }
};

