<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('log_aktivitas_admin', function (Blueprint $table) {
            $table->id('log_id');
            $table->foreignId('admin_id')
                ->constrained('users', 'user_id', 'fk_ladmin_user');
            // mis. 'verifikasi_tps', 'nonaktifkan_akun', 'terbitkan_konten'
            $table->string('aksi', 60);
            $table->string('tabel_target', 60)->nullable();
            $table->unsignedBigInteger('id_target')->nullable();
            $table->json('detail')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->dateTime('created_at')->useCurrent();

            $table->index(['admin_id', 'created_at'], 'idx_ladmin');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('log_aktivitas_admin');
    }
};

