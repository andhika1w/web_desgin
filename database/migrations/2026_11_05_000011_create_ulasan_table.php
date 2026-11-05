<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ulasan', function (Blueprint $table) {
            $table->id('ulasan_id');
            $table->foreignId('setoran_id')->unique()
                ->constrained('setoran', 'setoran_id', 'fk_ulasan_setoran')
                ->cascadeOnDelete();
            $table->foreignId('pengguna_id')
                ->constrained('users', 'user_id', 'fk_ulasan_pengguna');
            $table->tinyInteger('rating_kurir')->nullable(); // 1-5
            $table->tinyInteger('rating_tps')->nullable();   // 1-5
            $table->string('komentar', 500)->nullable();
            $table->dateTime('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ulasan');
    }
};

