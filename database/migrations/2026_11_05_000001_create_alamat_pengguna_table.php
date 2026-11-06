<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('alamat_pengguna', function (Blueprint $table) {
            $table->id('alamat_id');
            $table->foreignId('user_id')
                ->constrained('users', 'user_id', 'fk_alamat_user')
                ->cascadeOnDelete();
            $table->string('label', 50)->default('Rumah');
            $table->text('alamat_lengkap');
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);
            $table->boolean('is_default')->default(false);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alamat_pengguna');
    }
};

