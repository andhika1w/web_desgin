<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('setoran_detail', function (Blueprint $table) {
            $table->id('detail_id');
            $table->foreignId('setoran_id')
                ->constrained('setoran', 'setoran_id', 'fk_sd_setoran')
                ->cascadeOnDelete();
            $table->foreignId('jenis_id')
                ->constrained('jenis_sampah', 'jenis_id', 'fk_sd_jenis');
            $table->decimal('berat_estimasi_kg', 10, 2);
            // diisi pengelola TPS saat menimbang
            $table->decimal('berat_aktual_kg', 10, 2)->nullable();
            $table->string('foto_url', 255)->nullable();
            $table->string('catatan', 255)->nullable();

            $table->unique(['setoran_id', 'jenis_id'], 'uq_setoran_jenis');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('setoran_detail');
    }
};

