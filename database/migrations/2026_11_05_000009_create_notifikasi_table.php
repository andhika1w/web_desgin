<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 4. NOTIFIKASI, KONTEN, ULASAN, POIN, LOG
        Schema::create('notifikasi', function (Blueprint $table) {
            $table->id('notif_id');
            $table->foreignId('penerima_id')
                ->constrained('users', 'user_id', 'fk_notif_user')
                ->cascadeOnDelete();
            $table->foreignId('setoran_id')->nullable()
                ->constrained('setoran', 'setoran_id', 'fk_notif_setoran')
                ->nullOnDelete();
            $table->enum('tipe', [
                'tugas_baru', 'kurir_ditugaskan', 'sampah_akan_diantar', 'sampah_tiba',
                'setoran_selesai', 'setoran_ditolak', 'akun_diverifikasi', 'pengumuman',
            ]);
            $table->string('judul', 150);
            $table->string('pesan', 500);
            $table->boolean('is_read')->default(false);
            $table->dateTime('read_at')->nullable();
            $table->dateTime('created_at')->useCurrent();

            $table->index(['penerima_id', 'is_read', 'created_at'], 'idx_notif_inbox');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifikasi');
    }
};

