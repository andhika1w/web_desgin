<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('setoran_status_log', function (Blueprint $table) {
            $table->id('log_id');
            $table->foreignId('setoran_id')
                ->constrained('setoran', 'setoran_id', 'fk_log_setoran')
                ->cascadeOnDelete();
            $table->string('status_lama', 20)->nullable();
            $table->string('status_baru', 20);
            $table->foreignId('diubah_oleh')->nullable()
                ->constrained('users', 'user_id', 'fk_log_user')
                ->nullOnDelete();
            $table->string('catatan', 255)->nullable();
            $table->dateTime('created_at')->useCurrent();

            $table->index(['setoran_id', 'created_at'], 'idx_log_setoran');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('setoran_status_log');
    }
};

