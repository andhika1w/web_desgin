<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transaksi_poin', function (Blueprint $table) {
            $table->id('transaksi_id');
            $table->foreignId('pengguna_id')
                ->constrained('users', 'user_id', 'fk_poin_pengguna');
            $table->foreignId('setoran_id')->nullable()
                ->constrained('setoran', 'setoran_id', 'fk_poin_setoran')
                ->nullOnDelete();
            $table->enum('tipe', ['masuk', 'keluar']);
            $table->integer('jumlah'); // > 0
            $table->string('keterangan', 200)->nullable();
            $table->dateTime('created_at')->useCurrent();

            $table->index(['pengguna_id', 'created_at'], 'idx_poin_pengguna');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transaksi_poin');
    }
};

