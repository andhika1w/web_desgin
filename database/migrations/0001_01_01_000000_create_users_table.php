<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. AKUN — role: admin, pengguna, pengelola_tps, kurir
        Schema::create('users', function (Blueprint $table) {
            $table->id('user_id');
            $table->enum('role', ['admin', 'pengguna', 'pengelola_tps', 'kurir']);
            $table->string('nama', 100);
            $table->string('email', 120)->unique();
            $table->string('no_hp', 20)->unique();
            $table->string('password_hash', 255);
            $table->string('foto_url', 255)->nullable();
            // pengguna: langsung 'aktif'; pengelola_tps & kurir: 'pending' sampai diverifikasi admin
            $table->enum('status', ['pending', 'aktif', 'nonaktif', 'ditolak'])->default('aktif');
            $table->dateTime('last_login_at')->nullable();
            $table->rememberToken(); // tambahan untuk fitur "remember me" Laravel
            $table->dateTime('created_at')->useCurrent();
            $table->dateTime('updated_at')->useCurrent()->useCurrentOnUpdate();

            $table->index(['role', 'status'], 'idx_users_role_status');
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
