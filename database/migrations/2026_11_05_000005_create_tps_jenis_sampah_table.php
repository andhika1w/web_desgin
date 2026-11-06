<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Jenis sampah yang diterima tiap TPS + kapasitas per jenis
        Schema::create('tps_jenis_sampah', function (Blueprint $table) {
            $table->unsignedBigInteger('tps_id');
            $table->unsignedBigInteger('jenis_id');
            $table->decimal('kapasitas_maks_kg', 12, 2);
            $table->decimal('terpakai_kg', 12, 2)->default(0);
            $table->boolean('diterima')->default(true);
            $table->string('catatan', 255)->nullable();
            $table->dateTime('updated_at')->useCurrent()->useCurrentOnUpdate();

            $table->primary(['tps_id', 'jenis_id']);

            $table->foreign('tps_id', 'fk_tjs_tps')
                ->references('tps_id')->on('tps')
                ->cascadeOnDelete();
            $table->foreign('jenis_id', 'fk_tjs_jenis')
                ->references('jenis_id')->on('jenis_sampah');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tps_jenis_sampah');
    }
};

