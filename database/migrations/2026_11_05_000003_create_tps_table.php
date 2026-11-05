<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 2. TPS & JENIS SAMPAH
        Schema::create('tps', function (Blueprint $table) {
            $table->id('tps_id');
            // users.role = 'pengelola_tps' (validasi di aplikasi)
            $table->foreignId('pengelola_id')
                ->constrained('users', 'user_id', 'fk_tps_pengelola');
            $table->string('nama_tps', 120);
            $table->string('alamat', 255);
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);
            $table->decimal('kapasitas_total_kg', 12, 2);
            // disinkronkan trigger dari tps_jenis_sampah
            $table->decimal('kapasitas_terpakai_kg', 12, 2)->default(0);
            $table->time('jam_buka')->nullable();
            $table->time('jam_tutup')->nullable();
            $table->string('no_izin', 100)->nullable();
            // 1 = berizin menerima limbah medis
            $table->boolean('izin_limbah_medis')->default(false);
            $table->enum('status', ['buka', 'tutup', 'penuh'])->default('buka');
            // diverifikasi admin
            $table->boolean('is_verified')->default(false);
            $table->dateTime('created_at')->useCurrent();
            $table->dateTime('updated_at')->useCurrent()->useCurrentOnUpdate();

            $table->index(['status', 'is_verified'], 'idx_tps_status');
            $table->index('pengelola_id', 'idx_tps_pengelola');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tps');
    }
};

