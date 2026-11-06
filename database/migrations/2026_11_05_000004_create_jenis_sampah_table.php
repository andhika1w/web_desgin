<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jenis_sampah', function (Blueprint $table) {
            $table->id('jenis_id');
            $table->string('nama', 80)->unique();
            $table->enum('kategori', ['organik', 'anorganik', 'b3', 'elektronik', 'medis']);
            $table->string('deskripsi', 255)->nullable();
            $table->boolean('perlu_penanganan_khusus')->default(false);
            $table->integer('poin_per_kg')->default(0);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jenis_sampah');
    }
};

