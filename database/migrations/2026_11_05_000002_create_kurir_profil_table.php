<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kurir_profil', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->primary();
            $table->string('jenis_kendaraan', 30);
            $table->string('plat_nomor', 15);
            $table->string('no_sim', 30)->nullable();
            $table->decimal('kapasitas_angkut_kg', 8, 2)->nullable();
            $table->enum('status_ketersediaan', ['offline', 'online', 'bertugas'])->default('offline');
            $table->decimal('lat_terakhir', 10, 7)->nullable();
            $table->decimal('lng_terakhir', 10, 7)->nullable();
            $table->dateTime('lokasi_update_at')->nullable();
            $table->decimal('rating_rata', 3, 2)->default(0);

            $table->foreign('user_id', 'fk_kurir_user')
                ->references('user_id')->on('users')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kurir_profil');
    }
};

